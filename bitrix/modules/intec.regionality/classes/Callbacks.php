<?php
namespace intec\regionality;

use CCatalogDiscount;
use CCatalogDiscountCoupon;
use CCatalogProduct;
use CCurrencyRates;
use CIBlockElement;
use Bitrix\Main\Loader;
use Bitrix\Catalog\GroupAccessTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Catalog\Product\Price;
use Bitrix\Catalog\Product\Price\Calculation as PriceCalculation;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\Location\LocationTable;
use Exception;
use Bitrix\Main\EventResult;
use Bitrix\Main\Context;
use intec\Core;
use intec\core\base\BaseObject;
use intec\core\base\InvalidConfigException;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\regionality\models\Region;
use intec\regionality\models\region\Value;
use intec\regionality\models\SiteSettings;
use intec\regionality\models\SiteSettingsLocatorExtension;
use intec\regionality\services\locator\Extension as LocatorExtension;
use intec\regionality\services\locator\Service as Locator;
use intec\regionality\tools\Domain as DomainTools;

/**
 * Класс обработчиков событий системы.
 * Class Callbacks
 * @package intec\regionality
 * @author apocalypsisdimon@gmail.com
 */
class Callbacks extends BaseObject
{
    /**
     * Макросы.
     * @var array
     */
    protected static $_macros;

    /**
     * Обработчик. Вызывается после удаления пользовательского свойства.
     * Удаление значений удаляемого пользовательского свойства для всех регионов.
     * @param array $field
     * @throws Exception
     */
    public static function mainOnAfterUserTypeDelete($field)
    {
        if ($field['ENTITY_ID'] !== Region::ENTITY)
            return;

        $code = StringHelper::cut(
            $field['FIELD_NAME'],
            StringHelper::length(Region::PROPERTY_PREFIX_SYSTEM)
        );

        /** @var Value[] $values */
        $values = Value::find()
            ->where(['propertyCode' => $code])
            ->all();

        foreach ($values as $value)
            $value->delete();
    }

    /**
     * Обработчик. Вызывается после отрисовки контента.
     * Замена макросов в контенте сайта.
     * @param string $content Отрисованный контент страницы.
     */
    public static function mainOnEndBufferContent(&$content)
    {
        if (defined('INTEC_REGIONALITY_MACROS_REPLACE') && INTEC_REGIONALITY_MACROS_REPLACE === false)
            return;

        if (!empty(static::$_macros)) {
            $content = StringHelper::replaceMacros(
                $content,
                static::$_macros
            );
        }
    }

    /**
     * Обработчик. Выполняется перед отрисовкой контента.
     * Определение региона перед загрузкой контента.
     */
    public static function mainOnProlog()
    {
        if (defined('INTEC_REGIONALITY_REGION_RESOLVE') && INTEC_REGIONALITY_REGION_RESOLVE === false)
            return;

        try {
            $context = Context::getCurrent();
            $request = $context->getRequest();

            if ($request->isAdminSection())
                return;

            $site = $context->getSite();

            if (empty($site))
                return;

            if (!$request->isAjaxRequest()) {
                /** @var SiteSettings $siteSettings */
                $siteSettings = SiteSettings::getCurrent();

                /** @var Region $region */
                $region = null;
                $regionRemembered = null;
                $isUserAgentIgnored = false;

                if ($siteSettings->regionResolveIgnoreUse) {
                    $ignoredUserAgents = $siteSettings->getRegionResolveIgnoreUserAgents();
                    $userAgent = Core::$app->request->getUserAgent();

                    if (!empty($userAgent) && !empty($ignoredUserAgents))
                        foreach ($ignoredUserAgents as $ignoredUserAgent) {
                            $ignoredUserAgent = preg_quote($ignoredUserAgent);

                            if (preg_match('/' . $ignoredUserAgent . '/i', $userAgent)) {
                                $isUserAgentIgnored = true;

                                break;
                            }
                        }

                    unset($ignoredUserAgents, $ignoredUserAgents, $userAgent);
                }

                /** Если регион из COOKIE восстановлен */
                if (Region::restore()) {
                    $region = Region::getCurrent();
                    $regionRemembered = $region;

                    /** Если регион неактивный, то убираем его, для определения нового региона */
                    if (!$region->active || !$region->isForSites($site))
                        $region = null;
                }

                /** Если регион небыл получен из COOKIE */
                if (empty($region)) {
                    $locatorExtensions = $siteSettings
                        ->getLocatorExtensions(true)
                        ->asArray(function ($index, $extension) {
                            /** @var SiteSettingsLocatorExtension $extension */
                            return [
                                'value' => $extension->extensionCode
                            ];
                        });

                    $locatorExtensions = Locator::getInstance()
                        ->getExtensions(true)
                        ->where(function ($index, $extension) use (&$locatorExtensions) {
                            /** @var LocatorExtension $extension */
                            return ArrayHelper::isIn($extension->code, $locatorExtensions);
                        })
                        ->asArray();

                    /** Если в опции порядка определения домена установлено сначало определять по домену */
                    if ($siteSettings->regionResolveOrder == SiteSettings::REGION_RESOLVE_ORDER_DOMAIN || $isUserAgentIgnored) {
                        /** Если включена опция использования доменов то определяем его по домену */
                        if ($siteSettings->domainsUse)
                            $region = Region::resolveByDomain();

                        /** Если регион найден и он не для сайта, очищаем его */
                        if (!empty($region) && !$region->isForSites($site))
                            $region = null;

                        /** Если регион не определен, определяем по IP адресу */
                        if (empty($region) && !empty($locatorExtensions) && !$isUserAgentIgnored)
                            $region = Region::resolveByAddress(null, $locatorExtensions);
                    } else {
                        /** Определяем его по IP адресу */
                        if (!empty($locatorExtensions))
                            $region = Region::resolveByAddress(null, $locatorExtensions);

                        /** Если регион найден и он не для сайта, очищаем его */
                        if (!empty($region) && !$region->isForSites($site))
                            $region = null;

                        /** Если регион не определен и включена опция использования доменов, то определяем по домену */
                        if (empty($region) && $siteSettings->domainsUse)
                            $region = Region::resolveByDomain();
                    }

                    /** Если регион найден и он не для сайта, очищаем его */
                    if (!empty($region) && !$region->isForSites($site))
                        $region = null;

                    /** Если регион не определен, берем регион по умолчанию для текущих настроек */
                    if (empty($region))
                        $region = Region::getDefault();

                    /** Если регион найден и он не для сайта, очищаем его */
                    if (!empty($region) && !$region->isForSites($site))
                        $region = null;

                    /** Устанавливаем регион только как сессионный */
                    Region::setSessional($region);
                }

                if (!empty($region)) {
                    /** Если опция использования доменов активна и включена опция привязки региона к домену */
                    if ($siteSettings->domainsUse && $siteSettings->domainsLinkingUse) {
                        /** Определяем регион по домену */
                        $regionResolved = Region::resolveByDomain();

                        /** Если регион определен и идентификатор текущего региона не совпадает с регионом, определенным по домену */
                        if (!empty($regionResolved) && $region->id !== $regionResolved->id) {
                            /** Устанавливаем новый регион */
                            $region = $regionResolved;

                            /** Если включена опция неявной смены домена при привязке региона к домену или текущий домен не установлен */
                            if ($siteSettings->domainsLinkingReset || !Region::isCurrentSet()) {
                                /** Устанавливаем регион только как сессионный */
                                Region::setSessional($region);
                            } else {
                                /** Иначе, как текущий */
                                Region::setCurrent($region);
                            }
                        }

                        unset($regionResolved);
                    }

                    if (empty($regionRemembered) || $region->id != $regionRemembered->id)
                        Region::remember($region, $siteSettings->domainsUse, $siteSettings->regionRememberTime);

                    /** Если опция использования доменов активна */
                    if ($siteSettings->domainsUse) {
                        /** Получаем домены региона */
                        $domains = $region->getDomains(true);
                        $domains->sortBy('sort', SORT_ASC);
                        $redirect = false;
                        $url = null;

                        if (!$domains->isEmpty()) {
                            /** Разбираем текущий Url адрес */
                            $url = new Url(Core::$app->request->getAbsoluteUrl());

                            /** Если включена опция перенаправления на домен по умолчанию для сайта */
                            if ($siteSettings->domainsRedirectUse) {
                                $domain = null;

                                /** Производим поиск домена по умолчанию для региона */
                                foreach ($domains as $domain) {
                                    if ($domain->siteId != $site) {
                                        $domain = null;
                                        continue;
                                    }

                                    if ($domain->default)
                                        break;

                                    $domain = null;
                                }

                                /** Если домен определен */
                                if (!empty($domain)) {
                                    /** Если Host текущего Url адреса не совпадает с доменом */
                                    if ($domain->value !== $url->getHost()) {
                                        /** Устанавливаем новый Host в Url адрес */
                                        $url->setHost($domain->value);
                                        $redirect = true;
                                    }
                                }

                                unset($domain);
                            }

                            /** Если нет перенаправления на домен сайта по умолчанию, проверим все домены */
                            if (!$redirect) {
                                $redirect = true;
                                $host = null;

                                foreach ($domains as $domain) {
                                    if (!$domain->active || $domain->siteId != $site)
                                        continue;

                                    if ($host === null)
                                        $host = $domain->value;

                                    if ($domain->value === $url->getHost()) {
                                        $redirect = false;
                                        break;
                                    }
                                }

                                if (empty($host))
                                    $redirect = false;

                                if ($redirect)
                                    $url->setHost($host);

                                unset($domain);
                                unset($host);
                            }
                        }

                        /** Если необходимо перенаправление, то делаем его */
                        if ($redirect) {
                            $domain = $url->getHost();
                            $domain = DomainTools::getRoot($domain);
                            $domainCurrent = Core::$app->request->getHostName();
                            $domainCurrent = DomainTools::getRoot($domainCurrent);

                            if (!empty($domain) && $domain !== $domainCurrent) {
                                $path = new Url($url);
                                $path->setHost(null);
                                $path = $path->build();

                                $url = new Url(Core::$app->request->getHostInfo());
                                $url->setHost($domain);
                                $url->setPathString('/bitrix/admin/regionality_regions_select.php');
                                $url->getQuery()->setRange([
                                    'path' => $path,
                                    'region' => $region->id,
                                    'site' => $site,
                                    'current' => 'Y',
                                    'lang' => LANGUAGE_ID
                                ]);
                            }

                            LocalRedirect($url->build(), true);
                        }

                        unset($domains);
                    }
                }

                if (!isset($_SESSION[Module::VARIABLE]) || !Type::isArray($_SESSION[Module::VARIABLE]))
                    $_SESSION[Module::VARIABLE] = [];

                $_SESSION[Module::VARIABLE]['DOMAIN'] = Core::$app->request->getHostName();
            }

            $region = Region::getCurrent();

            if (!empty($region)) {
                $macros = $region->getFieldsValues(null, null, true, true, true);
                $macros->setRange([
                    Module::VARIABLE . '_DOMAIN' => Core::$app->request->getHostName()
                ]);

                static::$_macros = $macros->asArray();
            }
        } catch (InvalidConfigException $exception) {}
    }

    /**
     * Обработчик. Вызывается для определения оптимальной цены.
     * Определение оптимальной цены.
     * @param integer $productId Идентификатор товара.
     * @param integer $quantity Количество.
     * @param array $userGroups группы пользователя.
     * @param string $renewal Флаг продления подписки (Y|N).
     * @param array $prices Цены.
     * @param string $siteId Идентификатор сайта.
     * @param array $discountCoupons Купоны.
     * @return array|boolean
     */
    public static function catalogOnGetOptimalPrice(
        $productId,
        $quantity,
        $userGroups,
        $renewal,
        $prices,
        $siteId,
        $discountsCoupons
    ) {
        global $APPLICATION;

        if (
            !Loader::includeModule('intec.regionality') ||
            !Loader::includeModule('catalog')
        ) return true;

        $region = Region::getCurrent();

        if (empty($region))
            return true;

        $productId = Type::toInteger($productId);
        $quantity = Type::toFloat($quantity);

        if (empty($userGroups))
            $userGroups = [];

        if (!Type::isArray($userGroups)) {
            if (Type::isString($userGroups)) {
                $userGroups = explode('|', $userGroups);
            } else {
                $userGroups = [];
            }
        }

        if (!ArrayHelper::isIn(2, $userGroups))
            $userGroups[] = 2;

        $renewal = $renewal === 'Y';

        if ($siteId === false)
            $siteId = Context::getCurrent()->getSite();

        $currency = PriceCalculation::getCurrency();

        if (empty($currency))
            return false;

        $iBlockId = CIBlockElement::GetIBlockByID($productId);

        if (empty($iBlockId))
            return false;

        if (!Type::isArray($prices))
            $prices = [];

        if (empty($prices)) {
            $regionPricesTypes = $region->getPricesTypes(true)->asArray();
            $pricesTypes = GroupAccessTable::getList(array(
                'select' => [
                    'CATALOG_GROUP_ID'
                ],
                'filter' => [
                    '@GROUP_ID' => $userGroups,
                    '=ACCESS' => GroupAccessTable::ACCESS_BUY
                ],
                'order' => [
                    'CATALOG_GROUP_ID' => 'ASC'
                ]
            ))->fetchAll();

            foreach ($regionPricesTypes as $key => $regionPriceType)
                $regionPricesTypes[$key] = $regionPriceType->priceTypeId;

            foreach ($pricesTypes as $key => $priceType)
                $pricesTypes[$key] = Type::toInteger($priceType['CATALOG_GROUP_ID']);

            $pricesTypes = array_intersect($pricesTypes, $regionPricesTypes);

            unset($regionPriceType);
            unset($regionPricesTypes);

            if (empty($pricesTypes))
                return false;

            $prices = PriceTable::getList([
                'filter' => [
                    '=PRODUCT_ID' => $productId,
                    '@CATALOG_GROUP_ID' => $pricesTypes, [
                        'LOGIC' => 'OR',
                        '<=QUANTITY_FROM' => $quantity,
                        '=QUANTITY_FROM' => null
                    ], [
                        'LOGIC' => 'OR',
                        '>=QUANTITY_TO' => $quantity,
                        '=QUANTITY_TO' => null
                    ]
                ]
            ])->fetchAll();

            unset($priceType);
            unset($pricesTypes);
        }

        if (empty($prices))
            return false;

        $vat = CCatalogProduct::GetVATInfo($productId)->Fetch();

        if (empty($vat)) {
            $vat['RATE'] = Type::toFloat($vat['RATE'] * 0.01);
            $vat['INCLUDED'] = $vat['VAT_INCLUDED'] === 'Y';

            unset($vat['VAT_INCLUDED']);
        } else {
            $vat = [
                'RATE' => 0,
                'INCLUDED' => false
            ];
        }

        $discountsUse = true;
        $discountsVat = PriceCalculation::isIncludingVat();

        if ($discountsUse && $discountsCoupons === false)
            $discountsCoupons = CCatalogDiscountCoupon::GetCoupons();

        $priceMinimal = null;

        foreach ($prices as $price) {
            $priceValue = Type::toFloat($price['PRICE']);

            if (!$vat['INCLUDED'])
                $priceValue *= (1 + $vat['RATE']);

            if ($price['CURRENCY'] != $currency)
                $priceValue = CCurrencyRates::ConvertCurrency(
                    $priceValue,
                    $price['CURRENCY'],
                    $currency
                );

            $priceValue = PriceCalculation::roundPrecision($priceValue);
            $result = [
                'BASE_PRICE' => $priceValue,
                'COMPARE_PRICE' => $priceValue,
                'PRICE' => $priceValue,
                'CURRENCY' => $currency,
                'DISCOUNT_LIST' => [],
                'RAW_PRICE' => $price
            ];

            if ($discountsUse) {
                $discounts = CCatalogDiscount::GetDiscount(
                    $productId,
                    $iBlockId,
                    $price['CATALOG_GROUP_ID'],
                    $userGroups,
                    $renewal,
                    $siteId,
                    $discountsCoupons
                );

                $discounts = CCatalogDiscount::applyDiscountList(
                    $priceValue,
                    $currency,
                    $discounts
                );

                if ($discounts === false)
                    return false;

                $result['PRICE'] = $discounts['PRICE'];
                $result['COMPARE_PRICE'] = $discounts['PRICE'];
                $result['DISCOUNT_LIST'] = $discounts['DISCOUNT_LIST'];

                unset($discounts);
            }

            if (!$discountsVat) {
                $result['PRICE'] /= (1 + $vat['RATE']);
                $result['COMPARE_PRICE'] /= (1 + $vat['RATE']);
                $result['BASE_PRICE'] /= (1 + $vat['RATE']);
            }

            $result['UNROUND_PRICE'] = $result['PRICE'];
            $result['UNROUND_BASE_PRICE'] = $result['BASE_PRICE'];

            $result['BASE_PRICE'] = Price::roundPrice(
                $price['CATALOG_GROUP_ID'],
                $result['BASE_PRICE'],
                $currency
            );

            $result['PRICE'] = Price::roundPrice(
                $price['CATALOG_GROUP_ID'],
                $result['PRICE'],
                $currency
            );

            if (empty($result['DISCOUNT_LIST']))
                $result['BASE_PRICE'] = $result['PRICE'];

            $result['COMPARE_PRICE'] = $result['PRICE'];

            if (empty($priceMinimal) || $priceMinimal['COMPARE_PRICE'] > $result['COMPARE_PRICE'])
                $priceMinimal = $result;

            unset($result);
            unset($priceValue);
            unset($price);
        }

        unset($vat);

        $result = [
            'PRICE' => $priceMinimal['RAW_PRICE'],
            'RESULT_PRICE' => [
                'PRICE_TYPE_ID' => $priceMinimal['RAW_PRICE']['CATALOG_GROUP_ID'],
                'BASE_PRICE' => $priceMinimal['BASE_PRICE'],
                'DISCOUNT_PRICE' => $priceMinimal['PRICE'],
                'CURRENCY' => $currency,
                'DISCOUNT' => $priceMinimal['BASE_PRICE'] - $priceMinimal['PRICE'],
                'PERCENT' => (
                    $priceMinimal['BASE_PRICE'] > 0 && $priceMinimal > 0 ?
                        roundEx((100 * ($priceMinimal['BASE_PRICE'] - $priceMinimal['PRICE'])) / $priceMinimal['BASE_PRICE'], 0):
                        0
                ),
                'VAT_RATE' => $priceMinimal['RAW_PRICE']['VAT_RATE'],
                'VAT_INCLUDED' => ($discountsVat ? 'Y' : 'N'),
                'UNROUND_BASE_PRICE' => $priceMinimal['UNROUND_BASE_PRICE'],
                'UNROUND_DISCOUNT_PRICE' => $priceMinimal['UNROUND_PRICE']
            ],
            'DISCOUNT_PRICE' => $priceMinimal['PRICE'],
            'DISCOUNT' => [],
            'DISCOUNT_LIST' => [],
            'PRODUCT_ID' => $productId
        ];

        if (!empty($priceMinimal['DISCOUNT_LIST'])) {
            $result['DISCOUNT'] = ArrayHelper::getFirstValue($priceMinimal['DISCOUNT_LIST']);
            $result['DISCOUNT_LIST'] = $priceMinimal['DISCOUNT_LIST'];
        }

        return $result;
    }

    /**
     * Обработчик. Вызывается для обработки свойств заказа компонента.
     * Определение местоположения в соответствии с регионом.
     * @param array $userResult
     * @param \Bitrix\Main\HttpRequest $request
     * @param array $parameters
     * @param array $result
     */
    public static function saleComponentOrderProperties(&$userResult, $request, &$parameters, &$result)
    {
        $site = Context::getCurrent()->getSite();

        if ($request->isAdminSection() || empty($site))
            return;

        $region = Region::getCurrent();
        $siteSettings = SiteSettings::getCurrent();

        if (!empty($region)) {
            if ($siteSettings->regionLocationResolve && Loader::includeModule('sale')) {
                $personType = $userResult['PERSON_TYPE_ID'];

                if (!empty($personType)) {
                    $property = OrderPropsTable::getList([
                        'filter' => [
                            'ACTIVE' => 'Y',
                            'TYPE' => 'LOCATION',
                            'PERSON_TYPE_ID' => $personType
                        ],
                        'limit' => 1,
                        'cache' => [
                            'ttl' => 36000000
                        ]
                    ])->fetch();

                    if (!empty($property)) {
                        $location = LocationTable::getList([
                            'filter' => [
                                '=NAME.NAME' => $region->name,
                                '=NAME.LANGUAGE_ID' => LANGUAGE_ID
                            ],
                            'limit' => 1,
                            'cache' => [
                                'ttl' => 36000000
                            ]
                        ])->fetch();

                        if (!empty($location))
                            $userResult['ORDER_PROP'][$property['ID']] = $location['CODE'];
                    }
                }
            }
        }
    }

    /**
     * Обработчик. Вызывается для сбора дополнительных условий компаний.
     * Добавление ограничения компании по региону.
     * @return EventResult
     */
    public static function saleCompanyRestrictions()
    {
        return new EventResult(EventResult::SUCCESS, [
            '\\intec\\regionality\\platform\\sale\\restrictions\\RegionsRestriction' => '/bitrix/modules/intec.regionality/classes/platform/sale/restrictions/RegionsRestriction.php'
        ]);
    }

    /**
     * Обработчик. Вызывается для сбора дополнительных условий служб доставки.
     * Добавление ограничения доставки по региону.
     * @return EventResult
     */
    public static function saleDeliveryRestrictions()
    {
        return new EventResult(EventResult::SUCCESS, [
            '\\intec\\regionality\\platform\\sale\\restrictions\\DeliveryRegionsRestriction' => '/bitrix/modules/intec.regionality/classes/platform/sale/restrictions/DeliveryRegionsRestriction.php'
        ]);
    }

    /**
     * Обработчик. Вызывается для сбора дополнительных условий платежных систем.
     * Добавление ограничения платежных систем по региону.
     * @return EventResult
     */
    public static function salePaySystemRestrictions()
    {
        return new EventResult(EventResult::SUCCESS, [
            '\\intec\\regionality\\platform\\sale\\restrictions\\RegionsRestriction' => '/bitrix/modules/intec.regionality/classes/platform/sale/restrictions/RegionsRestriction.php'
        ]);
    }
}