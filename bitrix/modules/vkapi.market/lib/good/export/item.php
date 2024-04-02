<?php

namespace VKapi\Market\Good\Export;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для подготовки данных о товаре для выгрузки в вк
 * Class Item
 * 
 * @package VKapi\Market\Good\Export;
 */
class Item
{
    const PROPERTY_TYPE_L = 'L';
    // L - список
    const PROPERTY_TYPE_S = 'S';
    // S - строка
    const PROPERTY_TYPE_N = 'N';
    // N - число
    const PROPERTY_TYPE_F = 'F';
    // F - файл
    const PROPERTY_TYPE_G = 'G';
    // G - привязка к разделу
    const PROPERTY_TYPE_E = 'E';
    // E - привязка к элементу
    protected $productId = 0;
    protected $arOffers = [];
    /**
     * @var \VKapi\Market\Export\Item
     */
    protected $oExportItem = null;
    /**
     * @var \VKapi\Market\Good\Export\Description
     */
    protected $oGoodExportDescription = null;
    /**
     * @var \CIBLockElement
     */
    protected $oIblockElementOld = null;
    /**
     * @var \VKapi\Market\Export\Photo
     */
    protected $oPhoto = null;
    /**
     * @var \VKapi\Market\Property\VariantTable
     */
    protected $oPropertyVariantTable = null;
    /**
     * Временный кэш с данными
     * @var array
     */
    protected $arCache = [];
    public function __construct($productId, $arOffersId, \VKapi\Market\Export\Item $exportItem)
    {
        if (!$this->manager()->isInstalledIblockModule()) {
            // подключаем по умолчанию
        }
        $this->productId = intval($productId);
        $arOffersId = (array) $arOffersId;
        $arOffersId = array_map('intval', $arOffersId);
        $arOffersId = array_values(array_unique($arOffersId));
        if (empty($arOffersId)) {
            $arOffersId[] = 0;
        }
        $this->arOffers = $arOffersId;
        $this->oExportItem = $exportItem;
    }
    /**
     * @return \VKapi\Market\Export\Item
     */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
     * @return \VKapi\Market\Good\Export\Description
     */
    public function description()
    {
        if (is_null($this->oGoodExportDescription)) {
            $this->oGoodExportDescription = new \VKapi\Market\Good\Export\Description($this->exportItem());
        }
        return $this->oGoodExportDescription;
    }
    /**
     * @return \CIBlockElement
     */
    public function iblockElementOld()
    {
        if (is_null($this->oIblockElementOld)) {
            $this->oIblockElementOld = new \CIBlockElement();
        }
        return $this->oIblockElementOld;
    }
    /**
     * @return \VKapi\Market\Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * @return \VKapi\Market\Good\Reference\Album
     */
    public function goodReferenceAlbum()
    {
        return \VKapi\Market\Good\Reference\Album::getInstance();
    }
    /**
     * Вернет объект для работы с выгружаемыми картинками
     * 
     * @return \VKapi\Market\Export\Photo
     */
    public function photo()
    {
        if (is_null($this->oPhoto)) {
            $this->oPhoto = new \VKapi\Market\Export\Photo();
            $this->oPhoto->setExportItem($this->exportItem());
        }
        return $this->oPhoto;
    }
    /**
     * Вернет объект для работы с таблицей выгруженных вариантов свойств
     * 
     * @return \VKapi\Market\Property\VariantTable
     */
    public function propertyVariantTable()
    {
        if (is_null($this->oPropertyVariantTable)) {
            $this->oPropertyVariantTable = new \VKapi\Market\Property\VariantTable();
        }
        return $this->oPropertyVariantTable;
    }
    /**
     * вернет языкозависимое сообщение
     * @param $name
     * @param array $arReplace
     * @return string|null
     */
    public function getMessage($name, $arReplace = [])
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.GOOD.EXPORT.ITEM.' . $name, $arReplace);
    }
    /**
     * Является ли торговым предложением, либо объединенными торговыми предложениями в 1
     * @return bool
     */
    public function isOffer()
    {
        return max($this->arOffers) > 0;
    }
    /**
     * Вернет id товара
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }
    /**
     * Вернет xml_id товара
     * @return string
     */
    public function getProductXmlId()
    {
        $ar = $this->getProductFields();
        if (isset($ar['PRODUCT_XML_ID'])) {
            return $ar['PRODUCT_XML_ID'];
        }
        return null;
    }
    /**
     * Вернет ID инфоблока товара
     * @return null|int
     */
    public function getProductIblockId()
    {
        $ar = $this->getProductFields();
        if (isset($ar['PRODUCT_IBLOCK_ID'])) {
            return (int) $ar['PRODUCT_IBLOCK_ID'];
        }
        return null;
    }
    /**
     * Вернет массив id торговых предложений,
     * для обычного товара - [0],
     * для товара с ТП при расширенном режиме [1,2,3]
     * @return int[]
     */
    public function getOfferIds()
    {
        return $this->arOffers;
    }
    /**
     * Вернет первый id из спиcка оферов, всегда возвращает значение >= 0
     * @return int
     */
    public function getOfferId()
    {
        return $this->arOffers[0] ?: 0;
    }
    /**
     * Вернет xml_id торгового предложения
     * @return string
     */
    public function getOfferXmlId()
    {
        $ar = $this->getOfferFields($this->getOfferId());
        if (isset($ar['OFFER_XML_ID'])) {
            return $ar['OFFER_XML_ID'];
        }
        return null;
    }
    /**
     * Вернет ID инфоблока торгового предложения
     * @return null|int
     */
    public function getOfferIblockId()
    {
        $ar = $this->getOfferFields($this->getOfferId());
        if (isset($ar['OFFER_IBLOCK_ID'])) {
            return (int) $ar['OFFER_IBLOCK_ID'];
        }
        return null;
    }
    /**
     * Вернет базовые поля офера как элемента инфоблока
     * 
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getProductFields()
    {
        $arReturn = [];
        // времненый кэш
        if (isset($this->arCache['getProductFields'])) {
            return $this->arCache['getProductFields'];
        }
        $dbr = $this->iblockElementOld()->getList(['ID' => 'ASC'], ['ID' => $this->getProductId()], false, false, ['ID', 'XML_ID', 'EXTERNAL_ID', 'CODE', 'IBLOCK_ID', 'ACTIVE', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'PREVIEW_PICTURE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']);
        if ($obElement = $dbr->GetNextElement(true, false)) {
            $arElement = $obElement->getFields();
            $arProperties = $obElement->GetProperties();
            $arReturn['PRODUCT_SEO_TITLE'] = '';
            $arReturn['PRODUCT_SEO_META_TITLE'] = '';
            foreach ($arElement as $key => $value) {
                $arReturn['PRODUCT_' . $key] = $value;
            }
            foreach ($arProperties as $key => $value) {
                $arReturn['PROPERTY_' . $value['ID']] = $this->getPreparedPropertyValue($value);
                switch ($value['PROPERTY_TYPE']) {
                    case self::PROPERTY_TYPE_F:
                        $arReturn['PROPERTY_' . $value['ID'] . '_FID'] = $value['VALUE'];
                        break;
                    case self::PROPERTY_TYPE_L:
                        $arReturn['PROPERTY_' . $value['ID'] . '_ENUM_ID'] = $value['VALUE_ENUM_ID'];
                        break;
                    case self::PROPERTY_TYPE_S:
                        switch ($value['USER_TYPE']) {
                            case 'directory':
                                $arReturn['PROPERTY_' . $value['ID'] . '_ENUM_ID'] = $this->getHighloadEnumIdByPropertyValue($value);
                                break;
                        }
                        $arReturn['PROPERTY_' . $value['ID'] . '_FID'] = $value['VALUE'];
                        break;
                }
            }
            $arReturn['PRODUCT_PREVIEW_TEXT'] = $this->htmlToText($arReturn['PRODUCT_PREVIEW_TEXT'], $this->getHtmlToTextDeleteRules());
            $arReturn['PRODUCT_DETAIL_TEXT'] = $this->htmlToText($arReturn['PRODUCT_DETAIL_TEXT'], $this->getHtmlToTextDeleteRules());
            $arReturn['PRODUCT_NAME'] = $this->htmlToText($arReturn['PRODUCT_NAME']);
            // ссылка на страницу товара -------------
            $arReturn['PRODUCT_DETAIL_PAGE_URL'] = $arReturn['PRODUCT_DETAIL_PAGE_URL'];
            // SEO вкладка --
            if (class_exists('Bitrix\\Iblock\\InheritedProperty\\ElementValues')) {
                $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arElement['IBLOCK_ID'], $arElement['ID']);
                $values = $ipropValues->getValues();
                if (isset($values['ELEMENT_PAGE_TITLE'])) {
                    $arReturn['PRODUCT_SEO_TITLE'] = $this->htmlToText($values['ELEMENT_PAGE_TITLE']);
                }
                if (isset($values['ELEMENT_META_TITLE'])) {
                    $arReturn['PRODUCT_SEO_META_TITLE'] = $this->htmlToText($values['ELEMENT_META_TITLE']);
                }
                unset($ipropValues);
            }
        }
        $this->arCache['getProductFields'] = $arReturn;
        return $arReturn;
    }
    /**
     * Вернет массив из масовов описывающих товар, для формирвоания данных для вк
     * 
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getProductData()
    {
        // времненый кэш
        if (isset($this->arCache['getProductData'])) {
            return $this->arCache['getProductData'];
        }
        $arProduct = $this->getProductFields();
        // цены количество
        if (!$this->isOffer()) {
            $this->fillCatalogStoreDimensions($arProduct, $this->getProductId());
            $this->fillCatalogPrice($arProduct, $this->getProductId());
            if (preg_match('/^PROPERTY_(\\d+)$/', $this->exportItem()->getProductWeight(), $match)) {
                $arProduct['CATALOG_WEIGHT'] = $arProduct['PROPERTY_' . $match[1]];
            }
            if (preg_match('/^PROPERTY_(\\d+)$/', $this->exportItem()->getProductLength(), $match)) {
                $arProduct['CATALOG_LENGTH'] = $arProduct['PROPERTY_' . $match[1]];
            }
            if (preg_match('/^PROPERTY_(\\d+)$/', $this->exportItem()->getProductHeight(), $match)) {
                $arProduct['CATALOG_HEIGHT'] = $arProduct['PROPERTY_' . $match[1]];
            }
            if (preg_match('/^PROPERTY_(\\d+)$/', $this->exportItem()->getProductWidth(), $match)) {
                $arProduct['CATALOG_WIDTH'] = $arProduct['PROPERTY_' . $match[1]];
            }
            // цена ------
            $field = $this->exportItem()->getProductPrice();
            if (preg_match('/^PRICE_(\\d+)$/', $field, $match)) {
                $arProduct = array_replace($arProduct, $this->getPriceGroupFields($match[1], $arProduct));
            } elseif (array_key_exists($field, $arProduct)) {
                $arProduct['PRICE'] = $this->preparePrice($arProduct[$field]);
                $arProduct['PRICE_FORMAT'] = $this->getFormatedPrice($arProduct['PRICE']);
            }
            // старая цена
            $field = $this->exportItem()->getProductPriceOld();
            if (preg_match('/^PROPERTY_/', $field, $match)) {
                if (array_key_exists($field, $arProduct)) {
                    $arProduct['PRICE_OLD'] = $this->preparePrice($arProduct[$field]);
                    $arProduct['PRICE_OLD_FORMAT'] = $this->getFormatedPrice($arProduct['PRICE_OLD']);
                }
            }
            $this->calcPriceDiscount($arProduct);
        }
        [$arProductNew] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_AFTER_PREPARE_PRODUCT_DATA, ['arProduct' => $arProduct, 'arExportData' => $this->exportItem()->getData(), 'goodExportItem' => $this], true);
        if (!empty($arProductNew)) {
            $arProduct = $arProductNew;
        }
        $this->arCache['getProductData'] = $arProduct;
        return $arProduct;
    }
    /**
     * Вернет базовые поля офера ка кэлемента инфоблока
     * @param $offerId
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOfferFields($offerId)
    {
        // времненый кэш
        if (isset($this->arCache['getOfferFields'][$offerId])) {
            return $this->arCache['getOfferFields'][$offerId];
        }
        $arReturn = [];
        $dbr = $this->iblockElementOld()->getList(['ID' => 'ASC'], ['ID' => $offerId], false, false, ['ID', 'XML_ID', 'EXTERNAL_ID', 'CODE', 'IBLOCK_ID', 'ACTIVE', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'PREVIEW_PICTURE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']);
        if ($obElement = $dbr->GetNextElement(true, false)) {
            $arElement = $obElement->getFields();
            $arProperties = $obElement->GetProperties();
            $arReturn['OFFER_SEO_TITLE'] = '';
            $arReturn['OFFER_SEO_META_TITLE'] = '';
            foreach ($arElement as $key => $value) {
                $arReturn['OFFER_' . $key] = $value;
            }
            foreach ($arProperties as $key => $value) {
                $arReturn['PROPERTY_' . $value['ID']] = $this->getPreparedPropertyValue($value);
                switch ($value['PROPERTY_TYPE']) {
                    case self::PROPERTY_TYPE_F:
                        $arReturn['PROPERTY_' . $value['ID'] . '_FID'] = $value['VALUE'];
                        break;
                    case self::PROPERTY_TYPE_L:
                        $arReturn['PROPERTY_' . $value['ID'] . '_ENUM_ID'] = $value['VALUE_ENUM_ID'];
                        break;
                    case self::PROPERTY_TYPE_S:
                        switch ($value['USER_TYPE']) {
                            case 'directory':
                                $arReturn['PROPERTY_' . $value['ID'] . '_ENUM_ID'] = $this->getHighloadEnumIdByPropertyValue($value);
                                break;
                        }
                        $arReturn['PROPERTY_' . $value['ID'] . '_FID'] = $value['VALUE'];
                        break;
                }
            }
            $arReturn['OFFER_PREVIEW_TEXT'] = $this->htmlToText($arReturn['OFFER_PREVIEW_TEXT'], $this->getHtmlToTextDeleteRules());
            $arReturn['OFFER_DETAIL_TEXT'] = $this->htmlToText($arReturn['OFFER_DETAIL_TEXT'], $this->getHtmlToTextDeleteRules());
            $arReturn['OFFER_NAME'] = $this->htmlToText($arReturn['OFFER_NAME']);
            // SEO вкладка --
            if (class_exists('Bitrix\\Iblock\\InheritedProperty\\ElementValues')) {
                $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arElement['IBLOCK_ID'], $arElement['ID']);
                $values = $ipropValues->getValues();
                if (isset($values['ELEMENT_PAGE_TITLE'])) {
                    $arReturn['OFFER_SEO_TITLE'] = $this->htmlToText($values['ELEMENT_PAGE_TITLE']);
                }
                if (isset($values['ELEMENT_META_TITLE'])) {
                    $arReturn['OFFER_SEO_META_TITLE'] = $this->htmlToText($values['ELEMENT_META_TITLE']);
                }
                unset($ipropValues);
            }
        }
        unset($obElement, $oElement, $arPrices);
        $this->arCache['getOfferFields'][$offerId] = $arReturn;
        return $arReturn;
    }
    /**
     * Вернет массив описывающий тп,
     * для формирвоания данных для вк,
     * включая цены, остатки и тп
     * 
     * @param $offerId
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOfferData($offerId)
    {
        // времненый кэш
        if (isset($this->arCache['getOfferData'][$offerId])) {
            return $this->arCache['getOfferData'][$offerId];
        }
        $arOffer = $this->getOfferFields($offerId);
        $arProduct = $this->getProductFields();
        // свойства для вк
        $this->fillVariants($arOffer);
        // цены количество
        $this->fillCatalogStoreDimensions($arOffer, $offerId);
        $this->fillCatalogPrice($arOffer, $offerId, true);
        $field = $this->exportItem()->getOfferWeight();
        if (preg_match('/^PROPERTY_(\\d+)$/', $field, $match)) {
            if (array_key_exists($field, $arOffer)) {
                $arOffer['CATALOG_WEIGHT'] = $arOffer[$field];
            } elseif (array_key_exists($field, $arProduct)) {
                $arOffer['CATALOG_WEIGHT'] = $arProduct[$field];
            }
        }
        $field = $this->exportItem()->getOfferLength();
        if (preg_match('/^PROPERTY_(\\d+)$/', $field, $match)) {
            if (array_key_exists($field, $arOffer)) {
                $arOffer['CATALOG_LENGTH'] = $arOffer[$field];
            } elseif (array_key_exists($field, $arProduct)) {
                $arOffer['CATALOG_LENGTH'] = $arProduct[$field];
            }
        }
        $field = $this->exportItem()->getOfferHeight();
        if (preg_match('/^PROPERTY_(\\d+)$/', $field, $match)) {
            if (array_key_exists($field, $arOffer)) {
                $arOffer['CATALOG_HEIGHT'] = $arOffer[$field];
            } elseif (array_key_exists($field, $arProduct)) {
                $arOffer['CATALOG_HEIGHT'] = $arProduct[$field];
            }
        }
        $field = $this->exportItem()->getOfferWidth();
        if (preg_match('/^PROPERTY_(\\d+)$/', $field, $match)) {
            if (array_key_exists($field, $arOffer)) {
                $arOffer['CATALOG_WIDTH'] = $arOffer[$field];
            } elseif (array_key_exists($field, $arProduct)) {
                $arOffer['CATALOG_WIDTH'] = $arProduct[$field];
            }
        }
        // цена ------
        $field = $this->exportItem()->getOfferPrice();
        if (preg_match('/^PRICE_(\\d+)$/', $field, $match)) {
            $arOffer = array_replace($arOffer, $this->getPriceGroupFields($match[1], $arOffer));
        } elseif (array_key_exists($field, $arOffer)) {
            $arOffer['PRICE'] = $this->preparePrice($arOffer[$field]);
            $arOffer['PRICE_FORMAT'] = $this->getFormatedPrice($arOffer['PRICE']);
        } elseif (array_key_exists($field, $arProduct)) {
            $arOffer['PRICE'] = $this->preparePrice($arProduct[$field]);
            $arOffer['PRICE_FORMAT'] = $this->getFormatedPrice($arOffer['PRICE']);
        }
        // старая цена
        $field = $this->exportItem()->getOfferPriceOld();
        if (preg_match('/^PROPERTY_/', $field, $match)) {
            if (array_key_exists($field, $arOffer)) {
                $arOffer['PRICE_OLD'] = $this->preparePrice($arOffer[$field]);
                $arOffer['PRICE_OLD_FORMAT'] = $this->getFormatedPrice($arOffer['PRICE_OLD']);
            } elseif (array_key_exists($field, $arProduct)) {
                $arOffer['PRICE_OLD'] = $this->preparePrice($arProduct[$field]);
                $arOffer['PRICE_OLD_FORMAT'] = $this->getFormatedPrice($arOffer['PRICE_OLD']);
            }
        }
        $this->calcPriceDiscount($arOffer);
        [$arOfferNew] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_AFTER_PREPARE_OFFER_DATA, ['arOffer' => $arOffer, 'arExportData' => $this->exportItem()->getData(), 'arProduct' => $arProduct, 'goodExportItem' => $this], true);
        if (!empty($arOfferNew)) {
            $arOffer = $arOfferNew;
        }
        $this->arCache['getOfferData'][$offerId] = $arOffer;
        return $arOffer;
    }
    /**
     * Добавит utm метки в url
     * @param $productUrl
     * @return string
     */
    public function prepareProductUrl($productUrl)
    {
        $uri = new \Bitrix\Main\Web\Uri($this->getSiteUrl() . $productUrl);
        $utm = $this->manager()->getUrlUtm();
        if (strlen(trim($utm)) > 0) {
            $arReplace = ['{group_id}' => $this->exportItem()->getGroupId(), '{export_id}' => $this->exportItem()->getId(), '{sku}' => $this->getFieldSku()];
            $utm = str_replace(array_keys($arReplace), array_values($arReplace), $utm);
            $arUtm = [];
            parse_str($utm, $arUtm);
            $uri->addParams($arUtm);
        }
        return $uri->getLocator();
    }
    /**
     * Верент идентификатор валюты
     * 
     * @return string
     */
    public function getCurrencyId()
    {
        static $defaultCurrency;
        // если есть валюты, то проверяе мналичие валюты RUB
        if (!isset($defaultCurrency)) {
            $defaultCurrency = 'RUB';
            if (\VKapi\Market\Manager::getInstance()->isInstalledCurrencyModule()) {
                if ($base = \Bitrix\Currency\CurrencyManager::getBaseCurrency()) {
                    $defaultCurrency = $base;
                } elseif (!empty($arList = \Bitrix\Currency\CurrencyManager::getCurrencyList())) {
                    $arListKeys = array_keys($arList);
                    $defaultCurrency = reset($arListKeys);
                }
                unset($base, $arList);
            }
        }
        return $this->exportItem()->getCurrencyId();
    }
    /**
     * Вернет сконвертированную цену в текущую валюту
     * 
     * @param $price - исходная цена
     * @param $currency - исходная валюта
     * @return float|int
     */
    public function getCurrencyConvertPrice($price, $currency)
    {
        if ($this->manager()->isInstalledCurrencyModule() && $currency != $this->getCurrencyId()) {
            // раз валюты отличаются, то конвертируем в нужную
            return \CCurrencyRates::ConvertCurrency($price, $currency, $this->getCurrencyId());
        }
        return $price;
    }
    /**
     * Возвращает подготовленое значение свойства, например если испольузется привязка к элементу
     * для формирования масива описывающего товар
     * 
     * @param $prop
     * @return string
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getPreparedPropertyValue($prop)
    {
        $val = $prop['~VALUE'] ?? $prop['VALUE'];
        if (is_array($val)) {
            if (empty($val)) {
                return '';
            }
        } elseif (trim($val) == '') {
            return trim($val);
        }
        switch ($prop['PROPERTY_TYPE']) {
            case self::PROPERTY_TYPE_S:
                switch ($prop['USER_TYPE']) {
                    // справочник
                    case 'directory':
                        return $this->getPreparedPropertyValueHighload($prop);
                        break;
                    case 'ElementXmlID':
                        if (is_array($val)) {
                            $arValues = [];
                            $arFindId = [];
                            foreach ($val as $vid) {
                                $vid = trim($vid);
                                if (!isset($this->arPrepiredPropValue[$prop['ID']][$vid])) {
                                    $arFindId[] = $vid;
                                } else {
                                    $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$vid];
                                }
                            }
                            if (count($arFindId)) {
                                $dbrElement = $this->manager()->iblockElementOld()->getList(['SORT' => 'ASC'], ['XML_ID' => $arFindId], false, false, ['ID', 'NAME', 'XML_ID']);
                                while ($arElement = $dbrElement->Fetch()) {
                                    $this->arPrepiredPropValue[$prop['ID']][trim($arElement['XML_ID'])] = $this->htmlToText($arElement['NAME']);
                                    $arValues[] = $this->arPrepiredPropValue[$prop['ID']][trim($arElement['XML_ID'])];
                                }
                            }
                            return implode(',', $arValues);
                        } else {
                            $val = trim($val);
                            if (!isset($this->arPrepiredPropValue[$prop['ID']][$val])) {
                                $this->arPrepiredPropValue[$prop['ID']][$val] = '';
                                $dbrElement = $this->manager()->iblockElementOld()->getList(['SORT' => 'ASC'], ['XML_ID' => $val], false, false, ['ID', 'NAME', 'XML_ID']);
                                if ($arElement = $dbrElement->Fetch()) {
                                    $this->arPrepiredPropValue[$prop['ID']][trim($arElement['XML_ID'])] = $this->htmlToText($arElement['NAME']);
                                }
                            }
                            return $this->arPrepiredPropValue[$prop['ID']][$val];
                        }
                        break;
                    case 'HTML':
                        if ($val['TYPE'] === 'HTML') {
                            return trim($this->htmlToText($val['TEXT'], $this->getHtmlToTextDeleteRules()));
                        } else {
                            return trim($val['TEXT']);
                        }
                        break;
                    default:
                        if (is_array($val)) {
                            return implode(',', array_diff($val, ['']));
                        } else {
                            return $val;
                        }
                        break;
                }
                break;
            // ссылки на файлы
            case self::PROPERTY_TYPE_F:
                if (is_array($val)) {
                    $arValues = [];
                    $arFindId = [];
                    foreach ($val as $fid) {
                        $fid = intval($fid);
                        if (!isset($this->arPrepiredPropValue[$prop['ID']][$fid])) {
                            $arFindId[] = $fid;
                        } else {
                            $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$fid];
                        }
                    }
                    if (count($arFindId)) {
                        $dbrFiles = $this->manager()->file()->GetList([], ['@ID' => $arFindId]);
                        while ($arFile = $dbrFiles->Fetch()) {
                            $this->arPrepiredPropValue[$prop['ID']][$arFile['ID']] = $this->getSiteUrl() . $this->manager()->file()->GetFileSRC($arFile);
                            $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$arFile['ID']];
                        }
                    }
                    return implode(',', $arValues);
                } else {
                    $val = intval($val);
                    if (!isset($this->arPrepiredPropValue[$prop['ID']][$val])) {
                        $this->arPrepiredPropValue[$prop['ID']][$val] = false;
                        if ($arFile = $this->manager()->file()->GetFileArray($val)) {
                            $this->arPrepiredPropValue[$prop['ID']][$val] = $this->getSiteUrl() . $this->manager()->file()->GetFileSRC($arFile);
                        }
                    }
                    return $this->arPrepiredPropValue[$prop['ID']][$val];
                }
                break;
            // названия элементов
            case self::PROPERTY_TYPE_E:
                if (is_array($val)) {
                    $arValues = [];
                    $arFindId = [];
                    foreach ($val as $vid) {
                        $vid = intval($vid);
                        if (!isset($this->arPrepiredPropValue[$prop['ID']][$vid])) {
                            $arFindId[] = $vid;
                        } else {
                            $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$vid];
                        }
                    }
                    if (count($arFindId)) {
                        $dbrElement = $this->manager()->iblockElementOld()->getList(['SORT' => 'ASC'], ['ID' => $arFindId], false, false, ['ID', 'NAME']);
                        while ($arElement = $dbrElement->Fetch()) {
                            $this->arPrepiredPropValue[$prop['ID']][$arElement['ID']] = $this->htmlToText($arElement['NAME']);
                            $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$arElement['ID']];
                        }
                    }
                    return implode(',', $arValues);
                } else {
                    $val = intval($val);
                    if (!isset($this->arPrepiredPropValue[$prop['ID']][$val])) {
                        $this->arPrepiredPropValue[$prop['ID']][$val] = '';
                        $dbrElement = $this->manager()->iblockElementOld()->getList([], ['ID' => $val], false, false, ['ID', 'NAME']);
                        if ($arElement = $dbrElement->Fetch()) {
                            $this->arPrepiredPropValue[$prop['ID']][$val] = $this->htmlToText($arElement['NAME']);
                        }
                    }
                    return $this->arPrepiredPropValue[$prop['ID']][$val];
                }
                break;
            // названия разделов
            case self::PROPERTY_TYPE_G:
                if (is_array($val)) {
                    $arValues = [];
                    $arFindId = [];
                    foreach ($val as $vid) {
                        $vid = intval($vid);
                        // то что ищем
                        if (!isset($this->arPrepiredPropValue[$prop['ID']][$vid])) {
                            $arFindId[] = $vid;
                        } else {
                            $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$vid];
                        }
                    }
                    if (count($arFindId)) {
                        $dbrSection = $this->manager()->iblockSectionOld()->GetList(['SORT' => 'ASC'], ['ID' => $arFindId], false, ['ID', 'NAME']);
                        while ($arSection = $dbrSection->Fetch()) {
                            $this->arPrepiredPropValue[$prop['ID']][$arSection['ID']] = $this->htmlToText($arSection['NAME']);
                            $arValues[] = $this->arPrepiredPropValue[$prop['ID']][$arSection['ID']];
                        }
                    }
                    return implode(',', $arValues);
                } else {
                    $val = intval($val);
                    if (!isset($this->arPrepiredPropValue[$prop['ID']][$val])) {
                        $this->arPrepiredPropValue[$prop['ID']][$val] = '';
                        $dbrSection = $this->manager()->iblockSectionOld()->GetList([], ['ID' => $val], false, ['ID', 'NAME']);
                        if ($arSection = $dbrSection->Fetch()) {
                            $this->arPrepiredPropValue[$prop['ID']][$val] = $this->htmlToText($arSection['NAME']);
                        }
                    }
                    return $this->arPrepiredPropValue[$prop['ID']][$val];
                }
                break;
            default:
                // \Bitrix\Iblock\PropertyTable::TYPE_LIST
                // \Bitrix\Iblock\PropertyTable::TYPE_NUMBER
                if (is_array($val)) {
                    return implode(', ', array_diff($val, ['']));
                } else {
                    return $val;
                }
        }
    }
    /**
     * Возвращает подготовленое значение свойства типа справочник
     * для формирвоания масива описывающего товар
     * 
     * @param $propertyValue - значение свойства элемента инфоблок
     * @return string
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getPreparedPropertyValueHighload($propertyValue)
    {
        static $arHighloadClasses;
        $val = $propertyValue['VALUE'];
        do {
            if (!$this->manager()->isInstalledHighloadBlockModule()) {
                break;
            }
            if ($propertyValue['PROPERTY_TYPE'] != self::PROPERTY_TYPE_S) {
                break;
            }
            if ($propertyValue['USER_TYPE'] != 'directory') {
                break;
            }
            $tableName = null;
            if (isset($propertyValue['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'])) {
                $tableName = $propertyValue['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'];
            } elseif (isset($propertyValue['USER_TYPE_SETTINGS']['TABLE_NAME'])) {
                $tableName = $propertyValue['USER_TYPE_SETTINGS']['TABLE_NAME'];
            }
            if (is_null($tableName)) {
                break;
            }
            // получаем класс для работы
            if (!isset($arHighloadClasses[$tableName])) {
                // сначала выбрать информацию о ней из базы данных
                if (!($arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(['select' => ['*'], 'order' => ['NAME' => 'ASC'], 'filter' => ['TABLE_NAME' => $tableName]])->fetch())) {
                    break;
                }
                // затем инициализировать класс сущности
                $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
                $strEntityDataClass = $obEntity->getDataClass();
                $arHighloadClasses[$tableName] = new $strEntityDataClass();
            }
            if (is_array($val)) {
                $arValues = [];
                $arFindId = [];
                foreach ($val as $vid) {
                    $vid = trim($vid);
                    if (!isset($this->arPrepiredPropValue[$propertyValue['ID']][$vid])) {
                        $arFindId[] = $vid;
                    } else {
                        $arValues[] = $this->arPrepiredPropValue[$propertyValue['ID']][$vid];
                    }
                }
                if (count($arFindId)) {
                    $entity = $arHighloadClasses[$tableName]::getEntity();
                    $filter = ['ID' => $arFindId];
                    if ($entity->hasField('UF_XML_ID')) {
                        $filter = ['UF_XML_ID' => $arFindId];
                    }
                    $dbrRows = $arHighloadClasses[$tableName]->getList(['filter' => $filter]);
                    while ($arRow = $dbrRows->fetch()) {
                        $k = $arRow['UF_XML_ID'] ?? $arRow['ID'];
                        $this->arPrepiredPropValue[$propertyValue['ID']][$k] = $arRow['UF_NAME'] ?? '';
                        $arValues[] = $this->arPrepiredPropValue[$propertyValue['ID']][$k];
                    }
                }
                return implode(',', $arValues);
            } else {
                $val = trim($val);
                if (!isset($this->arPrepiredPropValue[$propertyValue['ID']][$val])) {
                    $this->arPrepiredPropValue[$propertyValue['ID']][$val] = false;
                    $entity = $arHighloadClasses[$tableName]::getEntity();
                    $filter = ['ID' => $val];
                    if ($entity->hasField('UF_XML_ID')) {
                        $filter = ['UF_XML_ID' => $val];
                    }
                    $dbrRows = $arHighloadClasses[$tableName]->getList(['filter' => $filter]);
                    if ($arRow = $dbrRows->fetch()) {
                        $k = $arRow['UF_XML_ID'] ?? $arRow['ID'];
                        $this->arPrepiredPropValue[$propertyValue['ID']][$k] = $arRow['UF_NAME'] ?? '';
                        $arValues[] = $this->arPrepiredPropValue[$propertyValue['ID']][$k];
                    }
                }
                return isset($this->arPrepiredPropValue[$propertyValue['ID']][$val]) ? $this->arPrepiredPropValue[$propertyValue['ID']][$val] : '';
            }
        } while (false);
        // решение по умолчанию
        if (is_array($val)) {
            return implode(',', array_diff($val, ['']));
        } else {
            return $val;
        }
    }
    /**
     * Возвращает ID значения свойства типа справочник
     * 
     * @param $propertyValue - значение свойства элемента инфоблок
     * @return string
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getHighloadEnumIdByPropertyValue($propertyValue)
    {
        static $arHighloadClasses;
        $val = $propertyValue['VALUE'];
        do {
            if (!$this->manager()->isInstalledHighloadBlockModule()) {
                break;
            }
            if ($propertyValue['PROPERTY_TYPE'] != self::PROPERTY_TYPE_S) {
                break;
            }
            if ($propertyValue['USER_TYPE'] != 'directory') {
                break;
            }
            $tableName = null;
            if (isset($propertyValue['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'])) {
                $tableName = $propertyValue['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'];
            } elseif (isset($propertyValue['USER_TYPE_SETTINGS']['TABLE_NAME'])) {
                $tableName = $propertyValue['USER_TYPE_SETTINGS']['TABLE_NAME'];
            }
            if (is_null($tableName)) {
                break;
            }
            // получаем класс для работы
            if (!isset($arHighloadClasses[$tableName])) {
                // сначала выбрать информацию о ней из базы данных
                if (!($arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(['select' => ['*'], 'order' => ['NAME' => 'ASC'], 'filter' => ['TABLE_NAME' => $tableName]])->fetch())) {
                    break;
                }
                // затем инициализировать класс сущности
                $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
                $strEntityDataClass = $obEntity->getDataClass();
                $arHighloadClasses[$tableName] = new $strEntityDataClass();
            }
            if (is_array($val)) {
                $arValues = [];
                $arFindId = [];
                foreach ($val as $vid) {
                    $vid = trim($vid);
                    if (!isset($this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$vid])) {
                        $arFindId[] = $vid;
                    } else {
                        $arValues[] = $this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$vid];
                    }
                }
                if (count($arFindId)) {
                    $entity = $arHighloadClasses[$tableName]::getEntity();
                    $filter = ['ID' => $arFindId];
                    if ($entity->hasField('UF_XML_ID')) {
                        $filter = ['UF_XML_ID' => $arFindId];
                    }
                    $dbrRows = $arHighloadClasses[$tableName]->getList(['filter' => $filter]);
                    while ($arRow = $dbrRows->fetch()) {
                        $k = $arRow['UF_XML_ID'] ?? $arRow['ID'];
                        $this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$k] = $arRow['ID'];
                        $arValues[] = $this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$k];
                    }
                }
                return implode(',', $arValues);
            } else {
                $val = trim($val);
                if (!isset($this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$val])) {
                    $this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$val] = 0;
                    $entity = $arHighloadClasses[$tableName]::getEntity();
                    $filter = ['ID' => $val];
                    if ($entity->hasField('UF_XML_ID')) {
                        $filter = ['UF_XML_ID' => $val];
                    }
                    $dbrRows = $arHighloadClasses[$tableName]->getList(['filter' => $filter]);
                    if ($arRow = $dbrRows->fetch()) {
                        $k = $arRow['UF_XML_ID'] ?? $arRow['ID'];
                        $this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$k] = $arRow['ID'];
                    }
                }
                return isset($this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$val]) ? $this->arPrepiredPropHighloadXmlIdToId[$propertyValue['ID']][$val] : '';
            }
        } while (false);
        // решение по умолчанию
        if (is_array($val)) {
            return implode(',', array_diff($val, ['']));
        } else {
            return $val;
        }
    }
    /**
     * Вернет адрес сайта
     * 
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->manager()->getSiteSchema($this->exportItem()->getSiteId()) . $this->manager()->getSiteHost($this->exportItem()->getSiteId());
    }
    /**
     * Вернет текс без тегов
     * 
     * @param $text
     * @return false|int|string|string[]
     */
    public function htmlToText($text, $arDeleteRules = [])
    {
        // перевод html в text
        $text = htmlspecialcharsBack(\HTMLToTxt($text, "", $arDeleteRules, false));
        // замена &nbsp;
        $text = preg_replace('/(&[a-z]+;)/', ' ', $text);
        return $text;
    }
    /**
     * Вернет набор правил, для функции перевода html в текст, согласно условия необходимости вырезания ссылок, картинок, таблиц
     * @return array
     */
    public function getHtmlToTextDeleteRules()
    {
        $arReturn = [];
        $arSet = $this->exportItem()->getDescriptionDeleteRules();
        foreach ($arSet as $set) {
            switch ($set) {
                case 'IMG':
                    $arReturn[] = "/<img[^>]*?>/is";
                    break;
                case 'LINK':
                    $arReturn[] = "/<a[^>]*?>.*?<\\/a>/is";
                    break;
                case 'TABLE':
                    $arReturn[] = "/<table[^>]*?>(.*?)<\\/table>/is";
                    break;
            }
        }
        return $arReturn;
    }
    /**
     * Вернет отформатированую цену вместе с валютой (текущей), 1020.30 руб
     * 
     * @param $price
     * @return mixed
     */
    public function getFormatedPrice($price)
    {
        if (function_exists('CurrencyFormat')) {
            return \CurrencyFormat($price, $this->getCurrencyId());
        }
        return $this->preparePrice($price) . ' ' . $this->getMessage('PRICE_CURRENCY_SHORT_FORMAT');
    }
    /**
     * Заполнит массив с данными о ценах
     * 
     * @param $arReturn - ссылка на массив описывающий товар
     * @param $productOrOfferId - идентификтаор товара или торгового предложения
     * @param $forOffer - заполнение производится дл яторгового предложения
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function fillCatalogPrice(&$arReturn, $productOrOfferId, $forOffer = false)
    {
        $arReturn['CURRENCY'] = $this->getCurrencyId();
        $arReturn['PRICE'] = 0;
        $arReturn['PRICE_FORMAT'] = '';
        $arReturn['PRICE_OLD'] = '';
        $arReturn['PRICE_OLD_FORMAT'] = '';
        $arReturn['DISCOUNT_PRICE'] = '';
        $arReturn['DISCOUNT_PRICE_FORMAT'] = '';
        $arReturn['DISCOUNT_CURRENCY'] = '';
        $arReturn['DISCOUNT_CURRENCY_FORMAT'] = '';
        // сначала заполним всеми вараинтами типов цен
        if (!$this->manager()->isInstalledCatalogModule()) {
            return $arReturn;
        }
        // заполнение значениями по типам цен
        $dbrPrice = $this->manager()->catalogPrice()->getList(['filter' => ["PRODUCT_ID" => $productOrOfferId], 'select' => ['ID', 'PRODUCT_ID', 'CURRENCY', 'PRICE', 'CATALOG_GROUP_ID']]);
        while ($arPrice = $dbrPrice->fetch()) {
            $arReturn += $this->addPriceGroupPrefix($arPrice['CATALOG_GROUP_ID'], $this->preparePriceGroup($arPrice, $forOffer));
        }
    }
    /**
     * Добавит префикс к полям расчитанному типу цены
     * @param $groupId - идентификатор тпиа цены
     * @param $arPrice - масив полей описывающих тип цены
     * @return array
     */
    public function addPriceGroupPrefix($groupId, $arPrice)
    {
        $arReturn = [];
        $prefix = 'CATALOG_GROUP_' . $groupId . '_';
        foreach ($arPrice as $k => $v) {
            $arReturn[$prefix . $k] = $v;
        }
        return $arReturn;
    }
    /**
     * Произведет расчеты для цены
     * @param $arPrice - масив полей описывающих тип цены
     * @param bool $forOffer - для торгового предложения
     */
    public function preparePriceGroup($arPrice, $forOffer = false)
    {
        $arReturn = [
            'CURRENCY' => $this->getCurrencyId(),
            //валюта
            'PRICE' => $arPrice['PRICE'],
            //цена
            'PRICE_OLD' => '',
            //старая цена
            'DISCOUNT_PRICE' => '',
            // размер скидки в рублях
            'DISCOUNT_PERCENT' => '',
            //размер скидки в процентах
            'PRICE_FORMAT' => '',
            'PRICE_OLD_FORMAT' => '',
            'DISCOUNT_PRICE_FORMAT' => '',
            'DISCOUNT_PERCENT_FORMAT' => '',
        ];
        // конвертируем цену -----
        $arReturn['PRICE'] = $this->getCurrencyConvertPrice($arPrice['PRICE'], $arPrice['CURRENCY']);
        // рассчитываем скидки -----
        $arDiscounts = $this->manager()->catalogDiscount()->GetDiscountByPrice($arPrice["ID"], $forOffer ? $this->exportItem()->getOfferPriceUserGroupIds() : $this->exportItem()->getProductPriceUserGroupIds(), "N", $this->exportItem()->getSiteId());
        $discountPrice = $this->manager()->catalogProduct()->CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);
        $discountPrice = $this->getCurrencyConvertPrice($discountPrice, $arPrice['CURRENCY']);
        // округляем
        $discountPrice = \Bitrix\Catalog\Product\Price::roundPrice($arPrice["CATALOG_GROUP_ID"], $discountPrice, $arPrice['CURRENCY']);
        if ((int) $discountPrice && $discountPrice < $arReturn['PRICE']) {
            $arReturn['PRICE_OLD'] = $arReturn['PRICE'];
            $arReturn['DISCOUNT_PERCENT'] = round(($arReturn['PRICE_OLD'] - $discountPrice) / $arReturn['PRICE_OLD'] * 100);
            $arReturn['DISCOUNT_PRICE'] = round($arReturn['PRICE_OLD'] - $discountPrice);
        }
        $arReturn['PRICE'] = $discountPrice;
        $arReturn['PRICE_FORMAT'] = $arReturn['PRICE'];
        if (!!$arReturn['PRICE_OLD']) {
            $arReturn['PRICE_OLD_FORMAT'] = $arReturn['PRICE_OLD'];
        }
        if (!!$arReturn['DISCOUNT_PRICE']) {
            $arReturn['DISCOUNT_PRICE_FORMAT'] = $arReturn['DISCOUNT_PRICE'];
        }
        if (!!$arReturn['DISCOUNT_PERCENT']) {
            $arReturn['DISCOUNT_PERCENT_FORMAT'] = $arReturn['DISCOUNT_PERCENT'] . '%';
        }
        // форматирование цен ----------
        $arReturn['PRICE_FORMAT'] = $this->getFormatedPrice($arReturn["PRICE"]);
        if (!!$arReturn['PRICE_OLD']) {
            $arReturn['PRICE_OLD_FORMAT'] = $this->getFormatedPrice($arReturn["PRICE_OLD"]);
        }
        if (!!$arReturn['DISCOUNT_PRICE']) {
            $arReturn['DISCOUNT_PRICE_FORMAT'] = $this->getFormatedPrice($arReturn["DISCOUNT_PRICE"]);
        }
        return $arReturn;
    }
    /**
     * Вернет первоначальные поля с расчетом цен,
     * те убирает из ключа префикс, например CATALOG_GROUP_1_
     * @param $priceGroupId
     * @param $arFields
     * @return array
     */
    public function getPriceGroupFields($priceGroupId, $arFields)
    {
        $arReturn = [];
        $prefix = 'CATALOG_GROUP_' . $priceGroupId . '_';
        $arKeys = ['CURRENCY', 'PRICE', 'PRICE_OLD', 'DISCOUNT_PRICE', 'DISCOUNT_PERCENT', 'PRICE_FORMAT', 'PRICE_OLD_FORMAT', 'DISCOUNT_PRICE_FORMAT', 'DISCOUNT_PERCENT_FORMAT'];
        foreach ($arKeys as $key) {
            if (isset($arFields[$prefix . $key])) {
                $arReturn[$key] = $arFields[$prefix . $key];
            }
        }
        return $arReturn;
    }
    /**
     * Рассчитает размер скидки от старой цены, если она установлена
     * @param $arProduct
     */
    public function calcPriceDiscount(&$arProduct)
    {
        $arProduct['PRICE'] = (float) $arProduct['PRICE'];
        $arProduct['PRICE_OLD'] = (float) $arProduct['PRICE_OLD'];
        if ($arProduct['PRICE'] > 0 && $arProduct['PRICE_OLD'] > 0) {
            $arProduct['DISCOUNT_PERCENT'] = round(floatval($arProduct['PRICE_OLD'] - $arProduct['PRICE']) / $arProduct['PRICE_OLD'] * 100);
            $arProduct['DISCOUNT_PRICE'] = round($arProduct['PRICE_OLD'] - $arProduct['PRICE']);
            $arProduct['DISCOUNT_PRICE_FORMAT'] = $this->getFormatedPrice($arProduct['DISCOUNT_PRICE']);
            $arProduct['DISCOUNT_PERCENT_FORMAT'] = $arProduct['DISCOUNT_PERCENT'] . '%';
        } else {
            $arProduct['PRICE_OLD'] = '';
            $arProduct['DISCOUNT_PERCENT'] = '';
            $arProduct['DISCOUNT_PRICE'] = '';
            $arProduct['PRICE_OLD_FORMAT'] = '';
            $arProduct['DISCOUNT_PRICE_FORMAT'] = '';
            $arProduct['DISCOUNT_PERCENT_FORMAT'] = '';
        }
    }
    /**
     * Заполнит массив с данными о товаре - данными о количестве, ценах и тп
     * 
     * @param $arReturn - ссылка на массив описывающий товар
     * @param $productOrOfferId - идентификтаор товара или торгового предложения
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function fillCatalogStoreDimensions(&$arReturn, $productOrOfferId)
    {
        $arReturn['CATALOG_WEIGHT'] = 0;
        $arReturn['CATALOG_WIDTH'] = 0;
        $arReturn['CATALOG_HEIGHT'] = 0;
        $arReturn['CATALOG_LENGTH'] = 0;
        $arReturn['CATALOG_MEASURE'] = 0;
        $arReturn['CATALOG_MEASURE_NAME'] = '';
        $arReturn['CATALOG_QUANTITY'] = 0;
        $arReturn['CATALOG_AVAILABLE'] = $this->getMessage('NO');
        if ($this->manager()->isInstalledCatalogModule()) {
            $dbrProductQuantity = \Bitrix\Catalog\Model\Product::getList(['filter' => ['ID' => $productOrOfferId], 'select' => ['ID', 'QUANTITY', 'AVAILABLE', 'WEIGHT', 'WIDTH', 'HEIGHT', 'LENGTH', 'MEASURE']]);
            while ($arProduct = $dbrProductQuantity->fetch()) {
                $arReturn['CATALOG_WEIGHT'] = (int) $arProduct['WEIGHT'];
                $arReturn['CATALOG_WIDTH'] = (int) $arProduct['WIDTH'];
                $arReturn['CATALOG_HEIGHT'] = (int) $arProduct['HEIGHT'];
                $arReturn['CATALOG_LENGTH'] = (int) $arProduct['LENGTH'];
                $arReturn['CATALOG_MEASURE'] = (int) $arProduct['MEASURE'];
                $arReturn['CATALOG_MEASURE_NAME'] = $this->manager()->getMeasureName((int) $arProduct['MEASURE']);
                $arReturn['CATALOG_QUANTITY'] = (int) $arProduct['QUANTITY'];
                $arReturn['CATALOG_AVAILABLE'] = $arProduct['AVAILABLE'] == 'N' ? $this->getMessage('NO') : $this->getMessage('YES');
            }
            // на складах ---------
            if (class_exists('\\CCatalogStoreProduct')) {
                $dbrStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(['filter' => ['=PRODUCT_ID' => $productOrOfferId], 'select' => ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']]);
                while ($arStoreProduct = $dbrStoreProduct->fetch()) {
                    $arReturn['CATALOG_STORE_' . $arStoreProduct['STORE_ID']] = intval($arStoreProduct['AMOUNT']);
                }
            }
        }
    }
    /**
     * Заполняет в
     * @param $arOffer
     */
    public function fillVariants(&$arOffer)
    {
        $arOffer['VARIANTS'] = [];
        $arProps = $this->exportItem()->getPropertyIds();
        // нужно получить значения свойств отмеченных для выгрузки
        foreach ($arProps as $propId) {
            if (!empty($arOffer['PROPERTY_' . $propId . '_ENUM_ID'])) {
                $enums = explode(',', $arOffer['PROPERTY_' . $propId . '_ENUM_ID']);
                $enumId = reset($enums);
                // запрашиваем значения
                $arVariant = $this->propertyVariantTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PROPERTY_ID' => $propId, 'ENUM_ID' => $enumId], 'select' => ['PROPERTY_ID', 'ENUM_ID', 'VK_VARIANT_ID'], 'limit' => 1])->fetch();
                if ($arVariant) {
                    $arOffer['VARIANTS'][] = $arVariant;
                }
            }
        }
    }
    /**
     * Возврашает массив всех разделов к которым привязан товар
     * [sectionId, ....]
     * 
     * @return array
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getElementSections()
    {
        $arReturn = [];
        $dbr = $this->manager()->iblockElementSection()->getList(['filter' => ['IBLOCK_ELEMENT_ID' => $this->productId]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar['IBLOCK_SECTION_ID'];
        }
        return $arReturn;
    }
    /**
     * @return array [[ID:int, ALBUM_ID:int, VK_ID:int, CATEGORY_ID:int], ...]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getAlbums()
    {
        $arReturn = [];
        $query = $this->goodReferenceAlbum()->getTable()::query();
        $query->setSelect(['*', 'VK_ID' => 'ALBUM_EXPORT.VK_ID', 'ALBUM_PARAMS' => 'ALBUM.PARAMS']);
        $query->setFilter(['PRODUCT_ID' => $this->getProductId(), 'OFFER_ID' => $this->getOfferIds(), '!VK_ID' => null]);
        $query->registerRuntimeField('ALBUM_EXPORT', new \Bitrix\Main\Entity\ReferenceField('ALBUM_EXPORT', '\\VKapi\\Market\\Album\\ExportTable', ['=this.ALBUM_ID' => 'ref.ALBUM_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT']));
        $dbrAlbum = $query->exec();
        while ($arAlbum = $dbrAlbum->fetch()) {
            $arReturn[$arAlbum['ID']] = ['ID' => (int) $arAlbum['ID'], 'ALBUM_ID' => (int) $arAlbum['ALBUM_ID'], 'VK_ID' => (int) $arAlbum['VK_ID'], 'CATEGORY_ID' => (int) $arAlbum['ALBUM_PARAMS']['CATEGORY_ID']];
        }
        return $arReturn;
    }
    /**
     * Вернет идентфикиаторы подборок в вк, к которым долежн быть привязан товар
     * @return array - [VK_ID:int, ...]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getAlbumsVkIds()
    {
        return array_column($this->getAlbums(), 'VK_ID');
    }
    /**
     * Вренет описание полей для выгрузки в вк
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFields()
    {
        $arFields = ['owner_id' => '-' . $this->exportItem()->getGroupId(), 'price' => $this->getFieldPrice(), 'price_format' => '', 'old_price' => $this->getFieldOldPrice(), 'old_price_format' => 0, 'name' => $this->getFieldName(), 'category_id' => $this->getFieldCategoryId(), 'deleted' => $this->getFieldDeleted(), 'description' => $this->getFieldDescription(), 'main_photo_id' => $this->getFieldMainPhotoId(), 'photo_ids' => $this->getFieldPhotoIds(), 'dimension_width' => $this->getFieldDimensionWidth(), 'dimension_height' => $this->getFieldDimensionHeight(), 'dimension_length' => $this->getFieldDimensionLength(), 'weight' => $this->getFieldDimensionWeight(), 'sku' => $this->getFieldSku(), 'stock_amount' => $this->getFieldStockAmount(), 'url' => $this->getFieldUrl()];
        if ($this->exportItem()->isEnabledExtendedGoods()) {
            $arFields['variant_ids'] = $this->getVariantIds();
        }
        $arFields['price_format'] = $this->getFormatedPrice($arFields['price']);
        if ($arFields['old_price'] <= 0) {
            unset($arFields['old_price']);
            unset($arFields['old_price_format']);
        }
        if ($this->isOffer()) {
            $arOffersData = [];
            foreach ($this->getOfferIds() as $offerId) {
                $arOffersData = $this->getOfferData($offerId);
            }
            [$arFieldsResolve] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_AFTER_PREPARE_FIELDS_VK_FROM_OFFER, ['arFields' => $arFields, 'arExportData' => $this->exportItem()->getData(), 'arProduct' => $this->getProductData(), 'arOffers' => $arOffersData, 'goodExportItem' => $this], true);
        } else {
            [$arFieldsResolve] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_AFTER_PREPARE_FIELDS_VK_FROM_PRODUCT, ['arFields' => $arFields, 'arExportData' => $this->exportItem()->getData(), 'arProduct' => $this->getProductData(), 'goodExportItem' => $this], true);
        }
        if (is_array($arFields) && isset($arFieldsResolve['owner_id'])) {
            $arFields = $arFieldsResolve;
        }
        return $arFields;
    }
    /**
     * вернет значение для поля цена
     * @return float - 0.01
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldPrice()
    {
        $price = 0;
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $priceField = $this->exportItem()->getOfferPrice();
            $arOffer = $this->getOfferData($this->getOfferId());
            if (preg_match('/^PRICE_/', $priceField)) {
                $price = $arOffer['PRICE'];
            } elseif (isset($arOffer[$priceField])) {
                $price = $arOffer[$priceField];
            } elseif (isset($arProduct[$priceField])) {
                $price = $arProduct[$priceField];
            }
        } else {
            $priceField = $this->exportItem()->getProductPrice();
            if (preg_match('/^PRICE_/', $priceField)) {
                $price = $arProduct['PRICE'];
            } elseif (isset($arProduct[$priceField])) {
                $price = $arProduct[$priceField];
            }
        }
        return $this->preparePrice($price);
    }
    /**
     * вернет значение для поля старая цена
     * @return float -
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldOldPrice()
    {
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $price = $arOffer['PRICE_OLD'];
        } else {
            $arProduct = $this->getProductData();
            $price = $arProduct['PRICE_OLD'];
        }
        return $this->preparePrice($price);
    }
    /**
     * Верент подготовленую цену для вк, без валюты, например 1020.30
     * 
     * @param $price
     * @return mixed|string
     */
    public function preparePrice($price)
    {
        $price = str_replace([' '], [''], $price);
        $price = number_format(floatval($price), 2, '.', '');
        return (float) $price;
    }
    /**
     * Вернет название для товара
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldName()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            // наименование ----------------------------------
            if (isset($arOffer[$this->exportItem()->getOfferName()])) {
                $name = $arOffer[$this->exportItem()->getOfferName()];
            } elseif (isset($arProduct[$this->exportItem()->getOfferName()])) {
                $name = $arProduct[$this->exportItem()->getOfferName()];
            } else {
                $name = $arOffer['OFFER_NAME'];
            }
        } else {
            $name = trim($arProduct[$this->exportItem()->getProductName()]) ?: $arProduct['PRODUCT_NAME'];
        }
        // string 4-100 cp1251
        $name = $this->manager()->truncateTextVK((string) $name, 120);
        return $name;
    }
    /**
     * Вернет id категории вконтакте
     * @return int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldCategoryId()
    {
        $categoryId = $this->exportItem()->getCategoryId();
        $arAlbums = $this->getAlbums();
        $arCategories = array_column($arAlbums, 'CATEGORY_ID');
        $arCategories = array_diff($arCategories, [0]);
        if (count($arCategories)) {
            $categoryId = reset($arCategories);
        }
        return (int) $categoryId;
    }
    /**
     * Вернет статус товара (1 — товар удален, 0 — товар не удален)
     * @return int
     */
    public function getFieldDeleted()
    {
        return (int) 0;
    }
    /**
     * Вернет описание товара
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldDescription()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffers = [];
            if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                foreach ($this->getOfferIds() as $offerId) {
                    $arOffers[] = $this->getOfferData($offerId);
                }
            } else {
                $arOffers[] = $this->getOfferData($this->getOfferId());
            }
            $text = $this->description()->getOffersText($arProduct, $arOffers);
            if (strlen($text) < 10) {
                $text = $this->exportItem()->getOfferDefaultText();
            }
        } else {
            $text = $this->description()->getProductText($arProduct);
            if (strlen($text) < 10) {
                $text = $this->exportItem()->getProductDefaultText();
            }
        }
        $text = $this->manager()->truncateText($text, $this->manager()->getDescriptionLengthLimit());
        return (string) $text;
    }
    /**
     * Вернет url товара, от 0 до 320 символов
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldUrl()
    {
        $arProduct = $this->getProductData();
        $arProduct['PRODUCT_DETAIL_PAGE_URL'] = $this->prepareProductUrl($arProduct['PRODUCT_DETAIL_PAGE_URL']);
        return (string) $arProduct['PRODUCT_DETAIL_PAGE_URL'];
    }
    /**
     * Вернет главную картинку товара или массив описывающйи ее для предпросомтра
     * @return string|mixed
     */
    public function getFieldMainPhotoId()
    {
        $arMainPhoto = [];
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $photoField = $this->exportItem()->getOfferPhoto();
            foreach ($this->getOfferIds() as $offerId) {
                $arOffer = $this->getOfferData($offerId);
                if (array_key_exists($photoField . '_FID', $arProduct)) {
                    $arMainPhoto = array_merge($arMainPhoto, (array) $arProduct[$photoField . '_FID']);
                } elseif (array_key_exists($photoField, $arProduct)) {
                    $arMainPhoto = array_merge($arMainPhoto, (array) $arProduct[$photoField]);
                } elseif (array_key_exists($photoField . '_FID', $arOffer)) {
                    $arMainPhoto = array_merge($arMainPhoto, (array) $arOffer[$photoField . '_FID']);
                } elseif (array_key_exists($photoField, $arOffer)) {
                    $arMainPhoto = array_merge($arMainPhoto, (array) $arOffer[$photoField]);
                }
            }
        } else {
            // картинки главная ---------------
            $photoField = $this->exportItem()->getProductPhoto();
            if (isset($arProduct[$photoField . '_FID'])) {
                $arMainPhoto = (array) $arProduct[$photoField . '_FID'];
            } elseif (isset($arProduct[$photoField])) {
                $arMainPhoto = (array) $arProduct[$photoField];
            }
        }
        $arMainPhoto = array_map('intval', $arMainPhoto);
        $arMainPhoto = array_diff($arMainPhoto, [0]);
        $arMainPhoto = array_slice(array_unique($arMainPhoto), 0, 1);
        if ($this->exportItem()->isPreviewMode()) {
            $resultPictures = $this->photo()->prepareProductFiles($arMainPhoto);
            $photoItems = $resultPictures->getData('items');
            if (count($photoItems)) {
                $fileResult = reset($photoItems);
                if ($fileResult->isSuccess()) {
                    return $fileResult->getData();
                }
            }
            return [];
        } else {
            // берем первый офер
            // для простого товара = 0
            // для офера в расширеном режиме ~12
            // для офера в расширеном режиме с объединением ~12
            // для офера в базовом режиме ~12
            // для офера в базовом режиме c объединением 0
            $offerId = $this->getOfferId();
            if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                $offerId = 0;
            }
            $resultPictures = $this->photo()->exportProductPictures($arMainPhoto, true, $this->getProductId(), $offerId);
            $photoItems = $resultPictures->getData('items');
            if (count($photoItems)) {
                $fileResult = reset($photoItems);
                if ($fileResult->isSuccess()) {
                    return $fileResult->getData('PHOTO_ID');
                }
            }
            return 0;
        }
    }
    /**
     * Вернет дополнительные кратинки товара в виде строки для вк
     * или массива с описанием файлов дял предпросмотра
     * @return string|mixed
     */
    public function getFieldPhotoIds()
    {
        $arPhotoMore = [];
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $photoField = $this->exportItem()->getOfferMorePhoto();
            foreach ($this->getOfferIds() as $offerId) {
                $arOffer = $this->getOfferData($offerId);
                if (array_key_exists($photoField . '_FID', $arProduct)) {
                    $arPhotoMore = array_merge($arPhotoMore, (array) $arProduct[$photoField . '_FID']);
                } elseif (array_key_exists($photoField, $arProduct)) {
                    $arPhotoMore = array_merge($arPhotoMore, (array) $arProduct[$photoField]);
                } elseif (array_key_exists($photoField . '_FID', $arOffer)) {
                    $arPhotoMore = array_merge($arPhotoMore, (array) $arOffer[$photoField . '_FID']);
                } elseif (array_key_exists($photoField, $arOffer)) {
                    $arPhotoMore = array_merge($arPhotoMore, (array) $arOffer[$photoField]);
                }
            }
        } else {
            // картинки главная ---------------
            $photoField = $this->exportItem()->getProductMorePhoto();
            if (isset($arProduct[$photoField . '_FID'])) {
                $arPhotoMore = (array) $arProduct[$photoField . '_FID'];
            } elseif (isset($arProduct[$photoField])) {
                $arPhotoMore = (array) $arProduct[$photoField];
            }
        }
        $arPhotoMore = array_map('intval', $arPhotoMore);
        $arPhotoMore = array_diff($arPhotoMore, [0]);
        $arPhotoMore = array_slice(array_unique($arPhotoMore), 0, 4);
        if ($this->exportItem()->isPreviewMode()) {
            $resultPictures = $this->photo()->prepareProductFiles($arPhotoMore);
            $photoItems = $resultPictures->getData('items');
            if (count($photoItems)) {
                $arReturn = [];
                foreach ($photoItems as $fileResult) {
                    if ($fileResult->isSuccess()) {
                        $arReturn[] = $fileResult->getData();
                    }
                }
                return $arReturn;
            }
            return [];
        } else {
            // берем первый офер
            // для простого товара = 0
            // для офера в расширеном режиме ~12
            // для офера в расширеном режиме с объединением ~12
            // для офера в базовом режиме ~12
            // для офера в базовом режиме c объединением 0
            $offerId = $this->getOfferId();
            if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                $offerId = 0;
            }
            $resultPictures = $this->photo()->exportProductPictures($arPhotoMore, false, $this->getProductId(), $offerId);
            $photoItems = $resultPictures->getData('items');
            if (count($photoItems)) {
                $arReturn = [];
                foreach ($photoItems as $fileResult) {
                    if ($fileResult->isSuccess()) {
                        $arReturn[] = $fileResult->getData('PHOTO_ID');
                    }
                }
                return implode(',', $arReturn);
            }
            return '';
        }
    }
    /**
     * Вернет ширину товара
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldDimensionWidth()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $field = $this->exportItem()->getOfferWidth();
            if (isset($arOffer[$field])) {
                return (int) $arOffer[$field];
            } elseif (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        } else {
            $field = $this->exportItem()->getProductWidth();
            if (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        }
        return 0;
    }
    /**
     * Вернет высоту товара
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldDimensionHeight()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $field = $this->exportItem()->getOfferHeight();
            if (isset($arOffer[$field])) {
                return (int) $arOffer[$field];
            } elseif (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        } else {
            $field = $this->exportItem()->getProductHeight();
            if (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        }
        return 0;
    }
    /**
     * Вернет длину товара
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldDimensionLength()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $field = $this->exportItem()->getOfferLength();
            if (isset($arOffer[$field])) {
                return (int) $arOffer[$field];
            } elseif (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        } else {
            $field = $this->exportItem()->getProductLength();
            if (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        }
        return 0;
    }
    /**
     * Вернет вес товара
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldDimensionWeight()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $field = $this->exportItem()->getOfferWeight();
            if (isset($arOffer[$field])) {
                return (int) $arOffer[$field];
            } elseif (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        } else {
            $field = $this->exportItem()->getProductWeight();
            if (isset($arProduct[$field])) {
                return (int) $arProduct[$field];
            }
        }
        return 0;
    }
    /**
     * Вернет артикул товара
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldSku()
    {
        $sku = '';
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $field = $this->exportItem()->getOfferSku();
            if (isset($arOffer[$field])) {
                $sku = $arOffer[$field];
            } elseif (isset($arProduct[$field])) {
                $sku = $arProduct[$field];
            }
        } else {
            $field = $this->exportItem()->getProductSku();
            if (isset($arProduct[$field])) {
                $sku = $arProduct[$field];
            }
        }
        return trim($sku);
    }
    /**
     * Вернет идентификтаоры вариантов свойств для привязки к товару
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getVariantIds()
    {
        $variants = [];
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $variants = array_column($arOffer['VARIANTS'], 'VK_VARIANT_ID');
        }
        return implode(',', $variants);
    }
    /**
     * Вернет количество товара
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldStockAmount()
    {
        $arProduct = $this->getProductData();
        if ($this->isOffer()) {
            $arOffer = $this->getOfferData($this->getOfferId());
            $field = $this->exportItem()->getOfferQuantity();
            if (isset($arOffer[$field])) {
                return max(0, (int) $arOffer[$field]);
            } elseif (isset($arProduct[$field])) {
                return max(0, (int) $arProduct[$field]);
            }
        } else {
            $field = $this->exportItem()->getProductQuantity();
            if (isset($arProduct[$field])) {
                return max(0, (int) $arProduct[$field]);
            }
        }
        // по умолчанию все доступны
        return -1;
    }
}
?>