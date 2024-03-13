<?php
namespace intec\importexport\models\excel;

use Bitrix\Main\Loader;
use Bitrix\Catalog\VatTable;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\InheritedProperty\SectionValues;

class IBlockHelper extends IBlockSelections
{
    /**
     * Возвращает только не пустые типы инфоблоков если установлен параметр ($notEmpty = true).
     * Возвращает все типы инфоблоков если не установлен параметр ($notEmpty = false).
     * @param bool $notEmpty - возвращать только не пустые типы
     * @return array|null
     */
    public static function getIBlockTypes ($notEmpty = true)
    {
        $arIBlocksTypes = \CIBlockParameters::GetIBlockTypes();
        $result = $arIBlocksTypes;

        if (empty($result))
            return null;

        if ($notEmpty) {
            $notEmptyTypes = self::getIBlockTypesList();
            $result = [];

            foreach ($arIBlocksTypes as $key => $value) {
                if (self::hasValue($key, $notEmptyTypes)) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }


    /**
     * Возвращает типы инфоблоков у которых есть инфоблоки.
     * @return array - результирующий массив с типами инфоблоков
     */
    public static function getIBlockTypesList ()
    {
        $result = Arrays::fromDBResult(\CIBlock::GetList([
            'SORT' => 'ASC'
        ], []))->indexBy('IBLOCK_TYPE_ID')->asArray(
            function ($index, $iBlock) {
                return [
                    'value' => $iBlock['IBLOCK_TYPE_ID']
                ];
            });

        $result = ArrayHelper::unique($result);

        return $result;
    }

    /**
     * @param $key
     * @return bool|string
     */
    public static function getMainName ($key)
    {
        if (empty($key))
            return false;

        $message = StringHelper::toLowerCase($key);
        $message = str_replace('_', '.', $message);

        $result = Loc::getMessage('importexport.export.table.helper.main.properties.label.' . $message);

        return $result;
    }

    /**
     * Определяет наличие значения в массиве.
     * @param $value - значение
     * @param $array - массив
     * @param $recursive - проверять вложенные массивы
     * @return bool
     */
    public static function hasValue ($value, $array, $recursive = false)
    {
        if (empty($value) || empty($array))
            return false;

        $result = false;

        if (!Type::isArray($array))
            return $array === $value;

        foreach ($array as $item) {

            if (Type::isArray($item)) {
                if ($recursive) {
                    $result = self::hasValue($value, $item, true);

                    if ($result)
                        break;
                } else {
                    continue;
                }
            }

            if ($value === $item) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public static function getHighloadBlock ($indexBy = 'TABLE_NAME')
    {
        if (!Loader::includeModule('highloadblock'))
            return false;

        $hlblocks = Arrays::fromDBResult(HighloadBlockTable::getList())->indexBy('TABLE_NAME')->asArray();

        foreach ($hlblocks as &$hlblock) {
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $res = $entity_data_class::getList();
            $hlblock['PROPERTIES'] = Arrays::fromDBResult($res)->indexBy('UF_XML_ID')->asArray();
        }

        return $hlblocks;
    }

    /**
     * Переводит массив параметров к виду ‘CODE’ => ‘VALUE’.
     * @param $elements - Массив для изменения.
     * @param $delimiter - Разделитель множественных свойств.
     * @return bool/array - Вернет массив с свойствами. В случае ошибке вернет false.
     */
    public static function prepareElement ($elements, $delimiter = ';', $prefix = '')
    {
        if (empty($elements))
            return false;

        $result = [];

        $hiLoadBlock = self::getHighloadBlock();

        if (empty($delimiter))
            $delimiter = ';';

        foreach ($elements as &$element) {
            if (!empty($element['PROPERTIES'])) {
                foreach ($element['PROPERTIES'] as &$property) {
                    if (!empty($property['DESCRIPTION'])) {
                        if (Type::isArray($property['DESCRIPTION'])) {
                            $hasDiscription = false;

                            foreach ($property['DESCRIPTION'] as $description) {
                                if (!empty($description)) {
                                    $hasDiscription = true;
                                    break;
                                }
                            }

                            if ($hasDiscription)
                                $result[$prefix . 'PROPERTY_' . $property['CODE'] . '_DESCRIPTION'] = implode($delimiter , $property['DESCRIPTION']);
                        } else {
                            $result[$prefix . 'PROPERTY_' . $property['CODE'] . '_DESCRIPTION'] = $property['DESCRIPTION'];
                        }
                    }

                    if ($property['PROPERTY_TYPE'] === 'S' && $property['USER_TYPE'] === 'HTML') {
                        if (empty($property['VALUE'])) {
                            continue;
                        } elseif ($property['MULTIPLE'] === 'Y') {
                            $subResult = [];

                            foreach ($property['VALUE'] as $value) {
                                $subResult[] = $value['TEXT'];
                            }

                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter, $subResult);
                        } else {
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $property['VALUE']['TEXT'];
                        }
                    } elseif ($property['PROPERTY_TYPE'] === 'L') {
                        if (!$property['VALUE'])
                            continue;
                        elseif (Type::isArray($property['VALUE']))
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter, $property['VALUE_ENUM']);
                        else
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $property['VALUE_ENUM'];

                    } elseif ($property['PROPERTY_TYPE'] === 'F') {
                        if (!$property['VALUE'])
                            continue;

                        if (Type::isArray($property['VALUE'])) {
                            foreach ($property['VALUE'] as &$value) {
                                $value = \CFile::GetFileArray($value);
                                $value = $value['SRC'];
                            }

                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter, $property['VALUE']);
                        } else {
                            $property['VALUE'] = \CFile::GetFileArray($property['VALUE']);
                            $property['VALUE'] = $property['VALUE']['SRC'];
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $property['VALUE'];
                        }
                    } elseif ($property['PROPERTY_TYPE'] === 'S' && $property['USER_TYPE'] === 'directory') {
                        if (Type::isArray($property['VALUE']) && !empty($property['VALUE'])) {
                            $names = [];

                            foreach ($property['VALUE'] as $value) {
                                $names[] = $hiLoadBlock[$property['USER_TYPE_SETTINGS']['TABLE_NAME']]['PROPERTIES'][$value]['UF_NAME'];
                            }

                            if ($names) {
                                $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter , $names);
                            }
                        } else {
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $hiLoadBlock[$property['USER_TYPE_SETTINGS']['TABLE_NAME']]['PROPERTIES'][$property['VALUE']]['UF_NAME'];
                        }

                    } elseif ($property['PROPERTY_TYPE'] === 'S' && $property['USER_TYPE'] === 'video') {

                        if (empty($property['VALUE'])) {
                            continue;
                        } elseif ($property['MULTIPLE'] === 'Y') {
                            $subResult = [];

                            foreach ($property['VALUE'] as $value) {
                                $subResult[] = $value['path'];
                            }

                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter, $subResult);
                        } else {
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $property['VALUE']['path'];
                        }
                    } elseif ($property['PROPERTY_TYPE'] === 'E' && empty($property['USER_TYPE'])) {

                        if (!$property['VALUE'])
                            continue;
                        elseif (Type::isArray($property['VALUE']))
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter , $property['VALUE']);
                        else
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $property['VALUE'];

                    } else {
                        if (!$property['VALUE'])
                            continue;
                        elseif (Type::isArray($property['VALUE']))
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = implode($delimiter , $property['VALUE']);
                        else
                            $result[$prefix . 'PROPERTY_' . $property['CODE']] = $property['VALUE'];
                    }
                }

                unset($element['PROPERTIES']);
                if (!empty($result))
                    $element = array_merge($element, $result);

                unset($result);
            }
        }

        return $elements;
    }

    /**
     * Свойства SEO.
     */
    public static function getSeoPropertyElements ($elements, $prefix = '')
    {
        if (empty($elements))
            return false;

        $prefixSeo = $prefix . 'SEO_PROPERTY_';
        $result = [];

        foreach ($elements as &$element) {
            $seoProperties= new \Bitrix\Iblock\InheritedProperty\ElementValues($element[$prefix . "IBLOCK_ID"], $element[$prefix . "ID"]);
            $seoProperties = $seoProperties->getValues();

            foreach ($seoProperties as $key => $seoProperty) {
                $result[$prefixSeo . $key] = $seoProperty;
            }

            $seoProperties = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($element[$prefix . "IBLOCK_ID"], $element[$prefix . "ID"]);
            $seoProperties = $seoProperties->findTemplates();

            foreach ($seoProperties as $key => $seoProperty) {
                if ($seoProperty['INHERITED'] === 'N') {
                    $result[$prefixSeo . 'CHANGE_' . $key] = 'Y';
                    $result[$prefix . 'TEMPLATE_' . $key] = $seoProperty['TEMPLATE'];
                }
            }

            $element = array_merge($element, $result);
        }

        return $elements;
    }

    /**
     * Свойства каталога.
     */
    public static function getCatalogPropertyElements ($elements, $prefix = '')
    {
        if (empty($elements))
            return false;

        if (!Loader::includeModule('catalog'))
            return $elements;

        $measureList = Arrays::fromDBResult(\CCatalogMeasure::getList())->indexBy('ID')->asArray();
        $price = new Price();

        foreach ($elements as &$element) {
            if (!empty($prefix)) {
                $result = [];

                foreach ($element as $key => &$property) {
                    $result[$prefix . $key] = $property;
                }

                $element = $result;
                unset($result);
            }

            $result = self::getCatalogPropertyElement($element[$prefix . 'ID'], $measureList, $prefix);
            $element = array_merge($element, $result);
            $result = $price->getPrices($element[$prefix . 'ID'], $element[$prefix . 'LID'], $prefix);
            if (!empty($result))
                $element = array_merge($element, $result);
        }

        return $elements;
    }

    /**
     * Свойства каталога.
     */
    public static function getCatalogPropertyElement ($elementId, $measureList = [], $prefix = '')
    {
        if (empty($elementId))
            return false;

        $result = [];

        if (empty($measureList))
            $measureList = Arrays::fromDBResult(\CCatalogMeasure::getList())->indexBy('ID')->asArray();

        $inform = \CCatalogProduct::GetByID($elementId);

        $inform['PRODUCT_TYPE'] = Loc::getMessage('property.catalog.product.type.' . $inform['TYPE']);
        $inform['MEASURE_SYMBOL'] = $measureList[$inform['MEASURE']]['SYMBOL'];
        $measureRatio = Arrays::fromDBResult(\CCatalogMeasureRatio::getList([],['PRODUCT_ID' => $elementId]))->asArray();
        $price = new Price();

        if (!empty($measureRatio ))
            $measureRatio = ArrayHelper::getFirstValue($measureRatio);

        $inform['MEASURE_RATIO'] = $measureRatio['RATIO'];

        if (!empty($inform['VAT_ID'])) {
            $vat = Arrays::fromDBResult(VatTable::GetByID($inform['VAT_ID']))->asArray();

            if (!empty($vat))
                $vat = ArrayHelper::getFirstValue($vat);

            $vat = $vat['RATE'];
            $inform['VAT'] = $vat;
        }

        $result = [
            $prefix . 'CATALOG_PROPERTY_PURCHASING_PRICE' => $inform['PURCHASING_PRICE'],
            $prefix . 'CATALOG_PROPERTY_PURCHASING_CURRENCY' => $inform['PURCHASING_CURRENCY'],
            $prefix . 'CATALOG_PROPERTY_QUANTITY' => $inform['QUANTITY'],
            $prefix . 'CATALOG_PROPERTY_QUANTITY_RESERVED' => $inform['QUANTITY_RESERVED'],
            $prefix . 'CATALOG_PROPERTY_WEIGHT' => $inform['WEIGHT'],
            $prefix . 'CATALOG_PROPERTY_LENGTH' => $inform['LENGTH'],
            $prefix . 'CATALOG_PROPERTY_WIDTH' => $inform['WIDTH'],
            $prefix . 'CATALOG_PROPERTY_HEIGHT' => $inform['HEIGHT'],
            $prefix . 'CATALOG_PROPERTY_MEASURE' => $inform['MEASURE_SYMBOL'],
            $prefix . 'CATALOG_PROPERTY_MEASURE_RATIO' => $inform['MEASURE_RATIO'],
            $prefix . 'CATALOG_PROPERTY_VAT_INCLUDED' => $inform['VAT_INCLUDED'],
            $prefix . 'CATALOG_PROPERTY_VAT' => $inform['VAT'],
            $prefix . 'CATALOG_PROPERTY_PRODUCT_TYPE' => $inform['PRODUCT_TYPE'],
            $prefix . 'CATALOG_PROPERTY_QUANTITY_TRACE' => $inform['CAN_BUY_ZERO'],
            $prefix . 'CATALOG_PROPERTY_CAN_BUY_ZERO' => $inform['CAN_BUY_ZERO'],
            $prefix . 'CATALOG_PROPERTY_SUBSCRIBE' => $inform['SUBSCRIBE'],
        ];

        $priceRange = $price->getRangePrice($elementId, $prefix . 'CATALOG_PROPERTY_', '_PRICE_EXT');

        if (!empty($priceRange))
            $result = ArrayHelper::merge($result, $priceRange);

        return $result;
    }


    /**
     * Свойства раздела.
     */
    public static function getSectionsProperties ($elements, $iblockId, $levels = 0)
    {
        if (empty($elements))
            return false;

        $sectionPropertiesList = Arrays::fromDBResult(\CIBlockSection::GetTreeList(['IBLOCK_ID' => $iblockId]), true)->indexBy('ID')->asArray();

        foreach ($elements as &$element) {
            $result = self::getSectionsProperty($element, $sectionPropertiesList, $levels);
            $element = array_merge($element, $result);
        }

        return $elements;
    }

    /**
     * Свойства раздела.
     */
    public static function getSectionsProperty ($element, $sectionPropertiesList, $levels = 0)
    {
        if (empty($element))
            return false;

        $prefix = 'SECTION_PROPERTY_';
        $levelPrefix = 'LEVEL_';
        $levels = Type::toInteger($levels);
        $result = [];

        if (!Type::isInteger($levels))
            $levels = 0;

        $sectionList = self::getSection($sectionPropertiesList, $element['IBLOCK_ID'], $element['IBLOCK_SECTION_ID']);
        $userFields = [];

        Arrays::fromDBResult(\CIBlockSection::GetList(
            [],
            ['IBLOCK_ID' => $element['IBLOCK_ID'], 'ID' => $element['IBLOCK_SECTION_ID']],
            false,
            ['UF_*'])
        )->asArray(function ($index, $section) use (&$userFields) {
            foreach ($section as $key => $property) {
                if (StringHelper::startsWith($key, 'UF_'))
                    $userFields[$key] = $property;
            }
        });

        $code = $prefix . $levelPrefix . '0_';

        $sectionValues = new SectionValues($element['IBLOCK_ID'], $element['IBLOCK_SECTION_ID']);
        $seo = $sectionValues->getValues();

        $result[$code . 'NAME'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['NAME'];
        $result[$code . 'CODE'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['CODE'];
        $result[$code . 'XML_ID'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['XML_ID'];
        $result[$code . 'ID'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['ID'];
        $result[$code . 'ACTIVE'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['ACTIVE'];
        $result[$code . 'SORT'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['SORT'];
        $result[$code . 'PICTURE'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['PICTURE'];
        $result[$code . 'DETAIL_PICTURE'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['DETAIL_PICTURE'];
        $result[$code . 'DESCRIPTION'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['DESCRIPTION'];
        $result[$code . 'SECTION_PAGE_URL'] = $sectionPropertiesList[$element['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL'];
        $result[$code . 'META_TITLE'] = $seo['SECTION_META_TITLE'];
        $result[$code . 'META_KEYWORDS'] = $seo['SECTION_META_KEYWORDS'];
        $result[$code . 'META_DESCRIPTION'] = $seo['SECTION_META_DESCRIPTION'];
        $result[$code . 'PAGE_TITLE'] = $seo['SECTION_PAGE_TITLE'];

        if (!empty($sectionList)) {
            $pathNames = '';
            foreach ($sectionList as $sectionListItem) {
                if (empty($pathNames))
                    $pathNames = $sectionListItem['NAME'];
                else
                    $pathNames = $pathNames . ' / ' . $sectionListItem['NAME'];
            }

            $result[$code . 'SECTION_DEPTH_LEVEL'] = count($sectionList);
            $result[$code . 'SECTION_PATH_NAMES'] = $pathNames;
            $result[$code . 'SECTION_IBLOCK_SECTION_ID'] = $sectionList[count($sectionList) - 1]['ID'];

            unset($pathNames);
        }

        $seoProperties = new \Bitrix\Iblock\InheritedProperty\SectionTemplates($element["IBLOCK_ID"], $element["IBLOCK_SECTION_ID"]);
        $seoProperties = $seoProperties->findTemplates();

        foreach ($seoProperties as $key => $seoProperty) {
            $result[$code . 'TEMPLATE_' . $key] = $seoProperty['TEMPLATE'];
        }

        unset($seoProperties);

        foreach ($userFields as $key => $userField) {
            $result[$code . $key] = $userField;
        }

        if ($levels > 0) {
            for ($i = 1; $i <= $levels; $i++) {

                if (empty($sectionList[$i]))
                    continue;

                $code = $prefix . $levelPrefix . $i . '_';
                $result[$code . 'NAME'] = $sectionList[$i]['NAME'];
                $result[$code . 'CODE'] = $sectionList[$i]['CODE'];
                $result[$code . 'XML_ID'] = $sectionList[$i]['XML_ID'];
                $result[$code . 'ID'] = $sectionList[$i]['ID'];
                $result[$code . 'ACTIVE'] = $sectionList[$i]['ACTIVE'];
                $result[$code . 'SORT'] = $sectionList[$i]['SORT'];
                $result[$code . 'PICTURE'] = $sectionList[$i]['PICTURE'];
                $result[$code . 'DETAIL_PICTURE'] = $sectionList[$i]['DETAIL_PICTURE'];
                $result[$code . 'DESCRIPTION'] = $sectionList[$i]['DESCRIPTION'];
                $result[$code . 'SECTION_PAGE_URL'] = $sectionList[$i]['SECTION_PAGE_URL'];
                $result[$code . 'META_TITLE'] = $sectionList[$i]['SEO']['SECTION_META_TITLE'];
                $result[$code . 'META_KEYWORDS'] = $sectionList[$i]['SEO']['SECTION_META_KEYWORDS'];
                $result[$code . 'META_DESCRIPTION'] = $sectionList[$i]['SEO']['SECTION_META_DESCRIPTION'];
                $result[$code . 'PAGE_TITLE'] = $sectionList[$i]['SEO']['SECTION_PAGE_TITLE'];

                if (!empty($sectionList[$i]['UF'])) {
                    foreach ($sectionList[$i]['UF'] as $key => $field) {
                        $result[$code . $key] = $field;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * раздел.
     */
    public static function getSection ($sectionPropertiesList, $iblockId, $sectionId, $indexBy = 'DEPTH_LEVEL')
    {
        if (empty($sectionPropertiesList) || empty($iblockId) || empty($sectionId))
            return false;

        if (empty($indexBy))
            $indexBy = 'ID';

        $index = $sectionPropertiesList[$sectionId][$indexBy];
        $result[$index] = $sectionPropertiesList[$sectionId];

        $sectionValues = new SectionValues($iblockId, $sectionId);
        $seo = $sectionValues->getValues();
        $result[$index]['SEO'] = $seo;

        $userFields  = [];

        Arrays::fromDBResult(\CIBlockSection::GetList(
            [],
            ['IBLOCK_ID' => $iblockId, 'ID' => $sectionId],
            false,
            ['UF_*'])
        )->asArray(function ($index, $section) use (&$userFields) {
            foreach ($section as $key => $property) {
                if (StringHelper::startsWith($key, 'UF_'))
                    $userFields[$key] = $property;
            }
        });

        $result[$index]['UF'] = $userFields;


        if (!empty($sectionPropertiesList[$sectionId]['IBLOCK_SECTION_ID'])) {
            $subSection = self::getSection($sectionPropertiesList, $iblockId, $sectionPropertiesList[$sectionId]['IBLOCK_SECTION_ID']);

            return ArrayHelper::merge($subSection, $result);
        } else {
            return $result;
        }
    }


    public static function getOffers ($elements, $iblockId, $offersFormat = 'line', $priceType = null, $delimiter = ';', $useNonPrice = true)
    {
        if (empty($elements) || empty($iblockId))
            return false;

        if (!Loader::includeModule('catalog'))
            return $elements;

        $delimiter = '|';

        $offersFormat = ArrayHelper::fromRange(['line', 'minimal', 'under', 'default'], $offersFormat);
        $elementsId = [];

        foreach ($elements as $element) {
            $elementsId[] = $element['ID'];
        }

        $fields = [
            'ID',
            'IBLOCK_ID',
            'NAME',
            'CODE',
            'XML_ID',
            'PREVIEW_PICTURE',
            'PREVIEW_PICTURE_DESCRIPTION',
            'PREVIEW_TEXT',
            'PREVIEW_TEXT_TYPE',
            'DETAIL_PICTURE',
            'DETAIL_PICTURE_DESCRIPTION',
            'DETAIL_TEXT',
            'DETAIL_TEXT_TYPE',
            'ACTIVE',
            'ACTIVE_FROM',
            'ACTIVE_TO',
            'SORT',
            'TAGS',
            'DATE_CREATE',
            'CREATED_BY',
            'TIMESTAMP_X',
            'MODIFIED_BY',
            'SHOW_COUNTER',
            'DETAIL_PAGE_URL',
            'SECTION_PATH',
            'PRICE'
        ];


        $offers = \CCatalogSku::getOffersList($elementsId, $iblockId, [], $fields,[]);


        foreach ($elements as &$element) {
            if (empty($offers[$element['ID']]))
                continue;

            if ($offersFormat != 'default' && $offersFormat != 'under') {
                $element = self::getOffersFormat($element, $offers[$element['ID']], $offersFormat, $priceType, $useNonPrice, $delimiter);
            } else {
                $element['OFFERS'] = $offers[$element['ID']];
                continue;
            }
        }

        return $elements;
    }

    /*
     * $format - [line, minimal, under, default]
     * */
    public static function getOffersFormat ($element, $offers, $format = 'line', $priceType = null, $useNonPrice = true, $delimiter = ';')
    {
        if (empty($element) || empty($offers))
            return false;

        $format = ArrayHelper::fromRange(['line', 'minimal', 'under', 'default'], $format);
        $prefix = 'OFFERS_';

        if ($format === 'minimal' && empty($priceType))
            return false;

        if ($format === 'line') {
            $properties = [];

            $offers = self::getOffersProperties($offers);
            $offers = self::prepareElement($offers);
            $offers = self::getSeoPropertyElements($offers);
            $offers = self::getCatalogPropertyElements($offers);

            foreach ($offers as $offer) {
                foreach ($offer as $key => $offerProperty) {
                    if ($key === 'PREVIEW_PICTURE' || $key === 'DETAIL_PICTURE') {
                        $offerProperty = \CFile::GetFileArray($offerProperty);
                        $offerProperty = $offerProperty['SRC'];
                    }

                    if (empty($properties[$prefix . $key]))
                        $properties[$prefix . $key] = $offerProperty;
                    else
                        $properties[$prefix . $key] = $properties[$prefix . $key] . $delimiter . $offerProperty;
                }
            }

            $element = array_merge($element, $properties);
        } elseif ($format === 'minimal') {

            $minPrice = null;
            $minId = null;
            $pricePrefix = $prefix . 'CATALOG_PROPERTY_';

            if ($priceType !== 'PURCHASING')
                $pricePrefix = $pricePrefix . 'PRICE';

            $pricePrefix = StringHelper::toUpperCase($pricePrefix). $priceType . '_';

            $offers = self::getCatalogPropertyElements($offers, $prefix);

            foreach ($offers as $offer) {
                $localMinPrice = false;

                if (!empty(Type::toFloat($offer[$pricePrefix . 'PRICE'])) && Type::toFloat($offer[$pricePrefix . 'PRICE']) !== 0) {
                    $localMinPrice = Type::toFloat($offer[$pricePrefix . 'PRICE']);

                    if (!empty($offer[$pricePrefix . 'PRICE_DISCOUNT']) && Type::toFloat($offer[$pricePrefix . 'PRICE_DISCOUNT']) !== 0)
                        if ($localMinPrice < Type::toFloat($offer[$pricePrefix . 'PRICE_DISCOUNT']))
                            $localMinPrice = Type::toFloat($offer[$pricePrefix . 'PRICE_DISCOUNT']);
                }

                if ($localMinPrice !== false) {
                    if (empty($minPrice) && $minPrice !== 0)
                        $minPrice = $localMinPrice;

                    if ($minPrice >= $localMinPrice) {
                        $minPrice = $localMinPrice;
                        $minId = $offer[$prefix . 'ID'];
                    }
                } else {
                    if ($useNonPrice) {
                        $minPrice = 0;
                        $minId = $offer[$prefix . 'ID'];
                    }
                }

                unset($priceExt);
            }

            if (!empty($minId)) {
                $offers[$minId]['PROPERTIES'] = self::getOfferProperties($offers[$minId], $prefix);
                $offers[$minId] = self::prepareElement([$offers[$minId]], $delimiter, $prefix);
                $offers[$minId] = self::getSeoPropertyElements($offers[$minId], $prefix);
                $offers[$minId] = ArrayHelper::getFirstValue($offers[$minId]);

                $element = array_merge($element, $offers[$minId]);
            }
        }

        return $element;
    }

    public static function getOffersProperties ($offers)
    {
        if (empty($offers))
            return false;

        foreach ($offers as &$offer) {
            $offer['PROPERTIES'] = self::getOfferProperties($offer);
        }

        return $offers;
    }

    public static function getOfferProperties ($offer, $prefix = '')
    {
        if (empty($offer)) return false;

        $result = [];
        $properties = Arrays::fromDBResult(\CIBlockElement::GetProperty($offer[$prefix . 'IBLOCK_ID'], $offer[$prefix . 'ID']))->asArray();

        foreach ($properties as $property) {
            if (empty($result[$property['CODE']])) {
                $result[$property['CODE']] = $property;
            } else {

                if (!empty($result[$property['CODE']]['PROPERTY_VALUE_ID'])) {
                    if (!Type::isArray($result[$property['CODE']]['PROPERTY_VALUE_ID'])) {
                        $buffer = $result[$property['CODE']]['PROPERTY_VALUE_ID'];
                        $result[$property['CODE']]['PROPERTY_VALUE_ID'] = [];
                        $result[$property['CODE']]['PROPERTY_VALUE_ID'][] = $buffer;
                    }

                    $result[$property['CODE']]['PROPERTY_VALUE_ID'][] = $property['PROPERTY_VALUE_ID'];
                } else {
                    $result[$property['CODE']]['PROPERTY_VALUE_ID'] = $property['PROPERTY_VALUE_ID'];
                }

                if (!empty($result[$property['CODE']]['VALUE'])) {
                    if (!Type::isArray($result[$property['CODE']]['VALUE'])) {
                        $buffer = $result[$property['CODE']]['VALUE'];
                        $result[$property['CODE']]['VALUE'] = [];
                        $result[$property['CODE']]['VALUE'][] = $buffer;
                    }

                    $result[$property['CODE']]['VALUE'][] = $property['VALUE'];
                } else {
                    $result[$property['CODE']]['VALUE'] = $property['VALUE'];
                }

                if (!empty($result[$property['CODE']]['DESCRIPTION'])) {
                    if (!Type::isArray($result[$property['CODE']]['DESCRIPTION'])) {
                        $buffer = $result[$property['CODE']]['DESCRIPTION'];
                        $result[$property['CODE']]['DESCRIPTION'] = [];
                        $result[$property['CODE']]['DESCRIPTION'][] = $buffer;
                    }

                    $result[$property['CODE']]['DESCRIPTION'][] = $property['DESCRIPTION'];
                } else {
                    $result[$property['CODE']]['DESCRIPTION'] = $property['DESCRIPTION'];
                }

                if (!empty($result[$property['CODE']]['VALUE_ENUM'])) {
                    if (!Type::isArray($result[$property['CODE']]['VALUE_ENUM'])) {
                        $buffer = $result[$property['CODE']]['VALUE_ENUM'];
                        $result[$property['CODE']]['VALUE_ENUM'] = [];
                        $result[$property['CODE']]['VALUE_ENUM'][] = $buffer;
                    }

                    $result[$property['CODE']]['VALUE_ENUM'][] = $property['VALUE_ENUM'];
                } else {
                    $result[$property['CODE']]['VALUE_ENUM'] = $property['VALUE_ENUM'];
                }

                if (!empty($result[$property['CODE']]['VALUE_XML_ID'])) {
                    if (!Type::isArray($result[$property['CODE']]['VALUE_XML_ID'])) {
                        $buffer = $result[$property['CODE']]['VALUE_XML_ID'];
                        $result[$property['CODE']]['VALUE_XML_ID'] = [];
                        $result[$property['CODE']]['VALUE_XML_ID'][] = $buffer;
                    }

                    $result[$property['CODE']]['VALUE_XML_ID'][] = $result['VALUE_XML_ID'];
                } else {
                    $result[$property['CODE']]['VALUE_XML_ID'] = $property['VALUE_XML_ID'];
                }

                if (!empty($result[$property['CODE']]['VALUE_SORT'])) {
                    if (!Type::isArray($result[$property['CODE']]['VALUE_SORT'])) {
                        $buffer = $result[$property['CODE']]['VALUE_SORT'];
                        $result[$property['CODE']]['VALUE_SORT'] = [];
                        $result[$property['CODE']]['VALUE_SORT'][] = $buffer;
                    }

                    $result[$property['CODE']]['VALUE_SORT'][] = $result['VALUE_SORT'];
                } else {
                    $result[$property['CODE']]['VALUE_SORT'] = $property['VALUE_SORT'];
                }
            }
        }

        return $result;
    }


    public static function getPropertiesNames ($iBlockId = null, $properties = [], $level)
    {
        if (empty($iBlockId) || empty($properties))
            return null;

        $result = [];
        $allProperties = [];
        $propertiesUse = [
            'property' => false,
            'catalog' => false,
            'seo' => false,
            'section' => false
        ];

        foreach ($properties as $property) {
            if (!Type::isString($property) || empty($property))
                continue;

            if (StringHelper::startsWith($property, 'PROPERTY_'))
                $propertiesUse['property'] = true;
            elseif (StringHelper::startsWith($property, 'CATALOG_PROPERTY_'))
                $propertiesUse['catalog'] = true;
            elseif (StringHelper::startsWith($property, 'SEO_PROPERTY_'))
                $propertiesUse['seo'] = true;
            elseif (StringHelper::startsWith($property, 'SECTION_PROPERTY_'))
                $propertiesUse['section'] = true;
            else
                $allProperties[$property]['name'] = IBlockHelper::getMainName($property);
        }

        if ($propertiesUse['property']) {
            $allProperties = ArrayHelper::merge(
                $allProperties,
                IBlockSelections::getBaseProperties($iBlockId, '', false, true)
            );
        }

        if ($propertiesUse['catalog']) {
            $allProperties = ArrayHelper::merge(
                $allProperties,
                IBlockSelections::getCatalogProperties($iBlockId, false, true)
            );
        }

        if ($propertiesUse['seo']) {
            $allProperties = ArrayHelper::merge(
                $allProperties,
                IBlockSelections::getSeoProperties(false, true)
            );
        }

        if ($propertiesUse['section']) {
            $allProperties = ArrayHelper::merge(
                $allProperties,
                IBlockSelections::getSectionProperties($iBlockId, $level, false, true)
            );
        }

        foreach ($properties as $property) {
            if (Type::isString($property))
                $result[$property] = $allProperties[$property]['name'];
        }

        return $result;
    }

    public static function getPropertiesName ($iBlockId = null, $properties = [])
    {
        if (empty($iBlockId) || empty($properties))
            return null;

        $allProperties = IBlockSelections::getBaseProperties($iBlockId, '', false, true);
        $result = [];

        if (empty($allProperties))
            return null;

        foreach ($properties as &$property) {
            $result[$allProperties[$property]['code']] = $allProperties[$property]['name'];
        }

        return $result;
    }

    public static function getSelectedProperties ($elements, $properties, $hiLoadBlock = false, $delimiter = ';')
    {
        if (empty($elements) || empty($properties))
            return null;

        if (empty($delimiter))
            $delimiter = ';';

        if ($hiLoadBlock === true)
            $hiLoadBlock = IBlockHelper::getHighloadBlock();

        if (empty($hiLoadBlock))
            $hiLoadBlock = false;

        $propertiesList = [];
        $result = [];

        foreach ($elements as $element) {
            foreach ($properties as $property) {
                if (StringHelper::startsWith($property, 'PROPERTY_')) {
                    $unPrefixProperty = StringHelper::cut($property, 9);
                    $currentProperty = ArrayHelper::getValue($element['PROPERTIES'], $unPrefixProperty);

                    if (!empty($currentProperty['USER_TYPE_SETTINGS']) && !empty($hiLoadBlock)) {
                        if (Type::isArray($currentProperty['VALUE']) && !empty($currentProperty['VALUE'])) {
                            $names = [];

                            foreach ($currentProperty['VALUE'] as $value) {
                                $names[] = $hiLoadBlock[$currentProperty['USER_TYPE_SETTINGS']['TABLE_NAME']]['PROPERTIES'][$value]['UF_NAME'];
                            }

                            if ($names) {
                                $propertiesList[] = implode($delimiter , $names);
                            }
                        }
                    } else {
                        if (!$currentProperty['VALUE'])
                            $propertiesList[] = null;
                        elseif (Type::isArray($currentProperty['VALUE']))
                            $propertiesList[] = implode($delimiter , $currentProperty['VALUE']);
                        else
                            $propertiesList[] = $currentProperty['VALUE'];
                    }

                } elseif (StringHelper::startsWith($property, 'CATALOG_PROPERTY_')) {
                    $propertiesList[] = ArrayHelper::getValue($element, $property);
                } else {
                    $propertiesList[] = ArrayHelper::getValue($element, $property);
                }
            }

            $result[] = $propertiesList;
            $propertiesList = [];
        }

        return $result;
    }

    public static function getSelectedCatalogProperties ($elements, $properties, $delimiter)
    {
        if (empty($elements) || empty($properties))
            return null;

        if (empty($delimiter))
            $delimiter = ';';

        $result = [];
        foreach ($elements as $element) {
            $result[] = \CCatalogProduct::GetByIDEx($element['ID']);
        }

        return $result;
    }

    public static function getSelected ($elements, $selected, $settings = null, $delimiter = ';')
    {
        if (empty($elements) || empty($selected))
            return $elements;

        $hasSettings = false;

        foreach ($settings as $setting) {
            if (!empty($setting)) {
                $hasSettings = true;
                break;
            }
        }

        $result = [];

        foreach ($elements as $element) {
            $subResult = [];

            foreach ($selected as $key => $code) {
                if ($hasSettings) {
                    $subResult[] = ConditionHelper::getComputedValue($element, $code, $settings[$key], $delimiter);
                } else {
                    $subResult[] = $element[$code];
                }
            }

            $result[] = $subResult;
        }

        return $result;
    }

    public static function getExceptionIds ($iblockId = null, $exceptionId = [])
    {
        if (empty($exceptionId) || empty($iblockId))
            return [];

        $filter = ['IBLOCK_ID' => $iblockId, '!ID' => $exceptionId];
        $elements = Arrays::fromDBResult(\CIBlockElement::GetList([], $filter, false, false,['ID']))
            ->asArray(function ($index, $element){
                return [
                    'value' => $element['ID']
                ];
            });

        return $elements;
    }

    public static function deactivateElements ($iblockId = null, $elementsId = [], $exceptionId = [], $fields = [])
    {
        if (!empty($fields)) {
            $fields = ArrayHelper::merge(['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'N'], $fields);
        } else {
            $fields = ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'N'];
        }

        $result = [];

        if (!empty($elementsId)) {
            foreach ($elementsId as $id) {
                $newElement = new \CIBlockElement;
                $result[$id] = $newElement->Update($id, $fields);
            }
        } elseif (!empty($exceptionId)) {
            $elements = self::getExceptionIds($iblockId, $exceptionId);

            foreach ($elements as $id) {
                $newElement = new \CIBlockElement;
                $result[$id] = $newElement->Update($id, $fields);
            }
        }

        return $result;
    }

    public static function getSectionExceptionIds ($iblockId = null, $exceptionId = [])
    {
        if (empty($iblockId))
            return [];


        $filter = ['IBLOCK_ID' => $iblockId, '!ID' => $exceptionId];

        $sections = Arrays::fromDBResult(\CIBlockSection::GetList([], $filter, ['ELEMENT_SUBSECTIONS' => 'Y'], []))
            ->asArray(function ($index, $element){
                return [
                    'value' => $element['ID']
                ];
            });

        return $sections;
    }

    public static function deactivateSections ($iblockId = null, $elementsId = [], $exceptionId = [], $fields = [])
    {
        if (!empty($fields)) {
            $fields = ArrayHelper::merge(['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'N'], $fields);
        } else {
            $fields = ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'N'];
        }

        $result = [];

        if (!empty($elementsId)) {
            foreach ($elementsId as $id) {
                $newElement = new \CIBlockSection;
                $result[$id] = $newElement->Update($id, $fields, true);
            }
        } elseif (!empty($exceptionId)) {
            $elements = self::getSectionExceptionIds($iblockId, $exceptionId);

            foreach ($elements as $id) {
                $newElement = new \CIBlockSection;
                $result[$id] = $newElement->Update($id, $fields);
            }
        }

        return $result;
    }

    public static function getCurrency ()
    {
        return \CCurrency::GetList();
    }
}