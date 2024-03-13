<?php
namespace intec\seo;

use DateTime;
use CDatabase;
use CIBlockElement;
use CCatalogSku;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\HttpRequest;
use Bitrix\Iblock\Template\Engine as TemplateEngine;
use Bitrix\Iblock\Template\Entity\Section as TemplateSection;
use intec\Core;
use intec\core\base\BaseObject;
use intec\core\base\InvalidConfigException;
use intec\core\base\writers\ClosureWriter;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\filter\condition\FilterHelper;
use intec\seo\filter\tags\OfferPropertyTag;
use intec\seo\filter\tags\PropertyTag;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\condition\Site;
use intec\seo\models\filter\Url as FilterUrl;
use intec\seo\models\SiteSettings;

/**
 * Класс обработчиков событий системы.
 * Class Callbacks
 * @package intec\seo
 * @author apocalypsisdimon@gmail.com
 */
class Callbacks extends BaseObject
{
    /**
     * Обработчик. Вызывается вначале загрузки страницы.
     * Определяет адреса фильтра.
     */
    public static function mainOnPageStart()
    {
        try {
            global $APPLICATION, $PAGEN_1;

            $context = Context::getCurrent();
            $server = $context->getServer();
            $request = $context->getRequest();

            if ($request->isAdminSection() || $request->isAjaxRequest())
                return;

            $site = $context->getSite();

            if (empty($site))
                return;

            $settings = SiteSettings::getCurrent();
            $request = Core::$app->request;
            $url = $request->getUrl();
            $url = StringHelper::replace($url, [
                '$' => '%24',
                '+' => '%2B',
                '!' => '%21',
                '*' => '%2A',
                '\'' => '%27',
                '(' => '%28',
                ')' => '%29',
                ',' => '%2C'
            ]);

            $url = new Url($url);

            $page = null;
            $pagination = $settings->filterPaginationPart;

            $path = $url->getPathString();
            $filterPages = $settings->getFilterPages();
            $filterPage = false;

            /** Проверка, является ли страница страницей фильтра */
            foreach ($filterPages as $filterPage) {
                if (strpos($filterPage, '/') !== 0)
                    $filterPage = SITE_DIR.$filterPage;

                if (StringHelper::startsWith($path, $filterPage)) {
                    $filterPage = true;
                    break;
                }

                $filterPage = false;
            }

            unset($path);

            /** Если включена очистка filter/clear/apply/ и это страница фильтра */
            if ($settings->filterClearRedirectUse && $filterPage) {
                $path = $url->getPath();
                $length = $path->getCount();

                if (
                    $path->get($length - 4) === 'filter' &&
                    $path->get($length - 3) === 'clear' &&
                    $path->get($length - 2) === 'apply'
                ) {
                    $path->removeAt($length - 2);
                    $path->removeAt($length - 3);
                    $path->removeAt($length - 4);

                    LocalRedirect($url->build(), true, '301 Moved Permanently');
                }
            }

            /** Если шаблон пагинации задан */
            if (!empty($pagination)) {
                /** Подготавливаем регулярное выражение из шаблона пагинации */
                $matches = [];
                $expression = '/'.preg_quote($pagination, '/').'/';
                $expression = StringHelper::replace($expression, [
                    '%NUMBER%' => '(\d+)'
                ]);

                /** Если есть совпадение */
                if (preg_match($expression, $url->getPathString(), $matches) === 1) {
                    /** Убираем его из основного адреса */
                    $url->setPathString(StringHelper::replace($url->getPathString(), [
                        $matches[0] => ''
                    ]));

                    /** Устанавливаем новый номер страницы */
                    $_GET['PAGEN_1'] = $_REQUEST['PAGEN_1'] = Type::toInteger($matches[1]);
                }

                if (isset($_REQUEST['PAGEN_1'])) {
                    /** Если номер страницы установлен, получаем его */
                    $page = Type::toInteger($_REQUEST['PAGEN_1']);
                    $pagination = StringHelper::replace($pagination, [
                        '%NUMBER%' => $page
                    ]);

                    /** Удаляем параметр запроса из текущего адреса */
                    $url->getQuery()->removeAt('PAGEN_1');

                    $PAGEN_1 = $page;
                } else {
                    $pagination = null;
                }
            }

            /** Поиск адресу назначения */
            /** @var FilterUrl $filterUrl */
            $filterUrl = FilterUrl::resolveByUrl($url->build(), FilterUrl::RESOLVE_MODE_TARGET);

            /** Если адреса назначения нет */
            if (empty($filterUrl)) {
                /** Ищем по адресу источника */
                $filterUrl = FilterUrl::resolveByUrl($url->build(), FilterUrl::RESOLVE_MODE_SOURCE);

                /** Если оригинальный адрес найден */
                if (!empty($filterUrl)) {
                    /** Собираем адрес назначения и перенаправляем */
                    $targetUrl = new Url($filterUrl->target);
                    /** Устанавливаем параметры запроса текущей страницы, сохраняя параметры запроса целевой страницы */
                    $targetUrl->getQuery()->setRange(ArrayHelper::merge(
                        $url->getQuery()->asArray(),
                        $targetUrl->getQuery()->asArray()
                    ));

                    /** Если постраничная навигация, то убираем параметры запроса */
                    if (!empty($pagination)) {
                        $targetUrl->setPathString($targetUrl->getPathString().$pagination);
                        $targetUrl->getQuery()->removeAt('PAGEN_1');
                    }

                    /** Если включена очистка Url адреса от параметров фильтра */
                    if ($settings->filterUrlQueryClean) {
                        /** Собираем параметры страницы без параметров фильтра */
                        $parameters = [];

                        foreach ($targetUrl->getQuery() as $key => $value)
                            /** Если параметр не set_filter и не начинается с arrFilter_, то это общий параметр */
                            if ($key !== 'set_filter' && !StringHelper::startsWith($key, 'arrFilter_'))
                                $parameters[$key] = $value;

                        $targetUrl->getQuery()->removeAll()->setRange($parameters);
                    }

                    LocalRedirect($targetUrl->build(), true, '301 Moved Permanently');
                }
            }

            /** Если адрес назначения был найден */
            if (!empty($filterUrl) && $filterUrl->source !== $filterUrl->target) {
                FilterUrl::setCurrent($filterUrl);

                /** Собираем адрес источника */
                $sourceUrl = new Url($filterUrl->source);

                foreach ($sourceUrl->getQuery() as $key => $value)
                    $_GET[$key] = $value;

                /** Переинициализируем все, что зависит от адреса */
                $serverValues = $server->toArray();
                $_SERVER['REQUEST_URI'] = $serverValues['REQUEST_URI'] = $sourceUrl->build();
                $server->set($serverValues);
                $context->initialize(new HttpRequest($server, $_GET, array(), array(), $_COOKIE), $context->getResponse(), $server);
                $APPLICATION->reinitPath();

                Core::$app->request->setQueryParams($_GET);
                Core::$app->request->setUrl($_SERVER['REQUEST_URI']);
            }
        } catch (InvalidConfigException $exception) {}
    }

    /**
     * Обработчик. Вызывается при индексации поиска.
     * Добавляет условия для индексации.
     * @param array $step Информация о текущем шаге.
     * @param string $class Класс модкля поиска.
     * @param string $method Метод класса модуля поиска.
     * @return boolean|mixed
     */
    public static function searchOnReindex($step, $class, $method)
    {
        if (!Loader::includeModule('iblock'))
            return false;

        if (!isset($step['ID'])) {
            $DB = CDatabase::GetModuleConnection('search');
            $DB->Query('DELETE FROM `b_search_content` WHERE `MODULE_ID` = \'intec.seo\'');
        }

        /** Получаем условия для поиска */
        /** @var Condition $condition */
        $condition = Condition::find()
            ->with(['sites'])
            ->where([
                'active' => 1,
                'searchable' => 1
            ])
            ->orderBy(['id' => SORT_ASC]);

        if (isset($step['ID']))
            $condition->andWhere(['>', 'id', $step['ID']]);

        $condition = $condition->one();
        
        if (empty($condition))
            return false;

        /** @var Condition $condition */
        $itemCombination = null;
        $catalogUse = Loader::includeModule('catalog');
        $sku = false;
        $date = new DateTime();
        $index = 0;

        $writer = new ClosureWriter(function ($url, $combination, $iblock, $section) use (&$condition, &$itemCombination, &$date, &$index, &$class, &$method) {
            /** @var FilterUrl $url */
            $filter = FilterHelper::getFilterFromCombination($combination, $iblock, $section);
            $itemCombination = $combination;

            if (empty($filter))
                return false;

            $filter['ACTIVE'] = 'Y';
            $filter['ACTIVE_DATE'] = 'Y';

            if (isset($filter['ID'])) {
                $filter['ID']->arFilter['ACTIVE'] = 'Y';
                $filter['ID']->arFilter['ACTIVE_DATE'] = 'Y';
            }

            $filter['INCLUDE_SUBSECTIONS'] = $condition->recursive ? 'Y' : 'N';

            $count = CIBlockElement::GetList([
                'SORT' => 'ASC'
            ], $filter, false, [
                'nPageSize' => 1
            ]);

            $count = $count->SelectedRowsCount();

            if ($count < 1)
                return false;

            $entity = new TemplateSection($section['ID']);
            $meta = [
                'title' => $condition->metaSearchTitle,
                'descriptionTop' => $condition->metaDescriptionTop,
                'descriptionBottom' => $condition->metaDescriptionBottom,
                'descriptionAdditional' => $condition->metaDescriptionAdditional
            ];

            if (empty($meta['title']) && !Type::isNumeric($meta['title']))
                $meta['title'] = $condition->metaTitle;

            foreach ($meta as $key => $value) {
                $value = StringHelper::replaceMacros($value, [
                    'SEO_FILTER_PAGINATION_PAGE_NUMBER' => '',
                    'SEO_FILTER_PAGINATION_TEXT' => ''
                ]);

                $meta[$key] = TemplateEngine::process($entity, $value);
            }

            if (empty($meta['title']) && !Type::isNumeric($meta['title']))
                return false;

            $result = [
                'ID' => $condition->id.'|'.($index + 1),
                'DATE_CHANGE' => $date->format('d.m.Y H:i:s'),
                'PERMISSIONS' => [2],
                'SITE_ID' => $condition->getSites(true)->asArray(function ($index, $site) {
                    /** @var Site $site */
                    return [
                        'value' => $site->siteId
                    ];
                }),
                'TITLE' => $meta['title'],
                'BODY' => trim(strip_tags($meta['descriptionTop'].' '.$meta['descriptionBottom'].' '.$meta['descriptionAdditional'])),
                'URL' => $url->source,
                'PARAM1' => $iblock['IBLOCK_TYPE_ID'],
                'PARAM2' => $iblock['ID']
            ];

            if (!call_user_func([
                $class,
                $method
            ], $result))
                return false;

            $index++;

            return true;
        }, false);

        /** Устанавливаем обработчик свойств мета-информации */
        PropertyTag::setHandler(function ($parameters) use (&$condition, &$itemCombination) {
            $values = [];

            foreach ($itemCombination as $property) {
                $code = $property['CODE'];

                if (empty($code) && !Type::isNumeric($code))
                    $code = $property['ID'];

                if (!ArrayHelper::isIn($code, $parameters))
                    continue;

                if ($property['IBLOCK_ID'] != $condition->iBlockId)
                    continue;

                $values[] = $property['VALUE']['TEXT'];
            }

            return $values;
        });

        /** Устанавливаем обработчик свойств торговых предложений мета-информации */
        OfferPropertyTag::setHandler(function ($parameters) use (&$condition, &$itemCombination, &$sku) {
            $values = [];

            if ($sku === false)
                return $values;

            foreach ($itemCombination as $property) {
                $code = $property['CODE'];

                if (empty($code) && !Type::isNumeric($code))
                    $code = $property['ID'];

                if (!ArrayHelper::isIn($code, $parameters))
                    continue;

                if ($property['IBLOCK_ID'] != $sku['IBLOCK_ID'])
                    continue;

                $values[] = $property['VALUE']['TEXT'];
            }

            return $values;
        });

        if (empty($condition->iBlockId))
            return $condition->id;

        $sites = $condition->getSites(true);

        if (!$sites->isEmpty()) {
            if ($catalogUse)
                $sku = CCatalogSku::GetInfoByProductIBlock($condition->iBlockId);

            $condition->generateUrl(null, $writer);
        }

        /** Убираем обработчик свойств мета-информации */
        PropertyTag::setHandler(null);
        /** Убираем обработчик свойств торговых предложений мета-информации */
        OfferPropertyTag::setHandler(null);

        return $condition->id;
    }
}