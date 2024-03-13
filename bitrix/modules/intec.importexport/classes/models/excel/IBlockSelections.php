<?php
namespace intec\importexport\models\excel;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

class IBlockSelections
{
    /**
     *  Создает список основных свойств элемента инфоблока.
     * @param bool $addHeader
     * @param array $custom
     * @return array
     */
    public static function getMainProperties ($addHeader = false, $custom = [])
    {
        if (!empty($custom))
            $propertiesList = $custom;
        else
            $propertiesList = [
                'ID',
                'NAME',
                'CODE',
                'XML_ID',
                'PREVIEW_PICTURE',
                'PREVIEW_PICTURE_DESCRIPTION',
                'PREVIEW_TEXT',
                'PREVIEW_TEXT_TYPE',
                'PREVIEW_TEXT_TYPE_TEXT',
                'PREVIEW_TEXT_TYPE_HTML',
                'DETAIL_PICTURE',
                'DETAIL_PICTURE_DESCRIPTION',
                'DETAIL_TEXT',
                'DETAIL_TEXT_TYPE',
                'DETAIL_TEXT_TYPE_TEXT',
                'DETAIL_TEXT_TYPE_HTML',
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
                'SECTION_PATH'
            ];

        $properties = [];

        if ($addHeader)
            $properties[] = [
                'id' => 0,
                'name' => Loc::getMessage('element'),
                'code' => 'GROUP_BASE',
                'disable' => true,
                'sortable' => false,
                'groupItemsId' => [
                    'count' => count($propertiesList)
                ]
            ];

        $idCounter = 0;

        foreach ($propertiesList as $value) {
            $idCounter++;

            $properties[] = [
                'id' => $idCounter,
                'name' => IBlockHelper::getMainName($value),
                'code' => $value,
                'sortable' => IBlockHelper::getSortable($value),
            ];
        }

        return $properties;
    }

    public static function getSortable ($value, $prefix = '')
    {
        if (empty($value))
            return false;

        $unSortableProperties = [
            'PREVIEW_PICTURE',
            'PREVIEW_PICTURE_DESCRIPTION',
            'PREVIEW_TEXT',
            'PREVIEW_TEXT_TYPE',
            'DETAIL_PICTURE',
            'DETAIL_PICTURE_DESCRIPTION',
            'DETAIL_TEXT',
            'DETAIL_TEXT_TYPE',
            'CREATED_BY',
            'MODIFIED_BY',
            'DETAIL_PAGE_URL',
            'SECTION_PATH'
        ];

        if (!empty($prefix)) {
            foreach ($unSortableProperties as &$unSortableProperty) {
                $unSortableProperty = $prefix . $unSortableProperty;
            }
        }

        return !IBlockHelper::hasValue($value, $unSortableProperties);
    }

    public static function getBaseProperties ($iBlockId, $namePrefix = '', $addHeader = false, $indexByCode = false)
    {
        if (empty($iBlockId))
            return [];

        if (!Loader::includeModule('iblock'))
            return [];

        $codePrefix = 'PROPERTY_';
        $hasDescription = false;

        $properties = [];

        Arrays::fromDBResult(\CIBlockProperty::GetList([], ['IBLOCK_ID' => $iBlockId]))->asArray(function ($index, $iBlock) use ($codePrefix, $namePrefix, &$properties) {
            $id = Type::toInteger($iBlock['ID']);
            $name = !empty($namePrefix) ? $namePrefix . ' ' . $iBlock['NAME'] . ' [' . $iBlock['CODE'] . ']' : $iBlock['NAME'] . ' [' . $iBlock['CODE'] . ']';
            $code = $codePrefix . $iBlock['CODE'];

            if (empty($iBlock['CODE']))
                $code = 'EMPTY_PROPERTY_' . $iBlock['ID'];

            $properties[] = [
                'id' => $id,
                'name' => $name,
                'code' => $code
            ];

            if ($iBlock['WITH_DESCRIPTION'] === 'Y') {
                $properties[] = [
                    'id' => $id,
                    'name' => $name . ' ' . Loc::getMessage('property.description'),
                    'code' => $code . '_DESCRIPTION',
                ];
            }
        });

        if ($indexByCode)
            $properties = TableHelper::getIndexByCode($properties);

        if (!empty($namePrefix)) {
            foreach ($properties as &$property) {
                $property['name'] = $namePrefix . ' ' . $property['name'];
            }
        }

        if ($addHeader) {
            ArrayHelper::unshift($properties, [
                'id' => 0,
                'name' => Loc::getMessage('properties'),
                'code' => 'GROUP_PROPERTIES',
                'disable' => true,
                'sortable' => false,
                'groupItemsId' => [
                    'count' => count($properties)
                ]

            ]);
        }

        return $properties;
    }

    public static function getSeoProperties ($addHeader = false, $indexByCode = false)
    {

        $properties = [];
        $codePrefix = 'SEO_PROPERTY_';
        $idCounter = 0;

        $propertyItems = [
            'ELEMENT_META_TITLE',
            'ELEMENT_META_KEYWORDS',
            'ELEMENT_META_DESCRIPTION',
            'ELEMENT_PAGE_TITLE',
            'ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'ELEMENT_DETAIL_PICTURE_FILE_NAME',
            'TEMPLATE_ELEMENT_META_TITLE',
            'TEMPLATE_ELEMENT_META_KEYWORDS',
            'TEMPLATE_ELEMENT_META_DESCRIPTION',
            'TEMPLATE_ELEMENT_PAGE_TITLE',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_NAME',
            'CHANGE_ELEMENT_META_TITLE',
            'CHANGE_ELEMENT_META_KEYWORDS',
            'CHANGE_ELEMENT_META_DESCRIPTION',
            'CHANGE_ELEMENT_PAGE_TITLE',
            'CHANGE_ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'CHANGE_ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'CHANGE_ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'CHANGE_ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'CHANGE_ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'CHANGE_ELEMENT_DETAIL_PICTURE_FILE_NAME',
        ];

        foreach ($propertyItems as $propertyItem) {
            $name = StringHelper::toLowerCase($propertyItem);
            $name = StringHelper::replace($name, ['_' => '.']);
            $name = 'seo.' . $name;

            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage($name),
                'code' => $codePrefix . $propertyItem
            ];
        }


        if ($addHeader) {
            ArrayHelper::unshift($properties, [
                'id' => 0,
                'name' => Loc::getMessage('seo'),
                'code' => 'GROUP_SEO',
                'disable' => true,
                'sortable' => false,
                'groupItemsId' => [
                    'count' => count($properties)
                ]
            ]);
        }

        if ($indexByCode)
            $properties = TableHelper::getIndexByCode($properties);

        return $properties;
    }

    public static function getCatalogProperties ($iBlockId, $addHeader = false, $indexByCode = false, $prefix = '')
    {
        if (empty($iBlockId))
            return [];

        if (!Loader::includeModule('catalog'))
            return [];

        $properties = [];
        $codePrefix = $prefix . 'CATALOG_PROPERTY_';

        $catalog = Arrays::fromDBResult(\CCatalog::GetList(['ID'=>'ASC'], ['IBLOCK_ID'=>$iBlockId]))->asArray();

        if (empty($catalog))
            return [];

        $idCounter = 0;

        if ($addHeader) {
            $langPrefix = '';

            if (!empty($prefix)) {
                $langPrefix = StringHelper::toLowerCase($prefix);
                $langPrefix = StringHelper::replace($langPrefix, ['_' => '.']);
            }

            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage($langPrefix . 'catalog'),
                'code' => $prefix . 'GROUP_CATALOG',
                'disable' => true,
                'sortable' => false
            ];
        }

        $properties[] = [
            'id' => $idCounter++,
            'name' => Loc::getMessage('catalog.purchasing.price'),
            'code' => $codePrefix . 'PURCHASING_PRICE',
            'sortable' => false
        ];

        $properties[] = [
            'id' => $idCounter++,
            'name' => Loc::getMessage('catalog.price.currency').' "'.Loc::getMessage('catalog.purchasing.price').'"',
            'code' => $codePrefix . 'PURCHASING_CURRENCY',
            'sortable' => false
        ];


        /**/
        $priceTypes = Arrays::fromDBResult(\CCatalogGroup::GetList(['SORT' => 'ASC']))->asArray();
        foreach ($priceTypes as $priceType) {

            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage('catalog.price.name').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'"',
                'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_PRICE',
                'sortable' => false
            ];
            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage('catalog.price.name').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'" '.Loc::getMessage('catalog.price.with.discount'),
                'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_PRICE_DISCOUNT',
                'sortable' => false
            ];
            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage('catalog.price.currency').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'"',
                'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_CURRENCY',
                'sortable' => false
            ];
            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage('catalog.price.name').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'" - '.Loc::getMessage('catalog.price.ext.mode'),
                'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_PRICE_EXT',
                'sortable' => true
            ];

            if ($priceType['BASE']!='Y') {
                $properties[] = [
                    'id' => $idCounter++,
                    'name' => Loc::getMessage('catalog.price.extra').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'" ('.Loc::getMessage('catalog.price.extra.measure').')',
                    'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_EXTRA',
                    'sortable' => true
                ];
                $properties[] = [
                    'id' => $idCounter++,
                    'name' => Loc::getMessage('catalog.price.extra.name').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'"',
                    'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_EXTRA_NAME',
                    'sortable' => true
                ];
                $properties[] = [
                    'id' => $idCounter++,
                    'name' => Loc::getMessage('catalog.price.extra.id').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'"',
                    'code' => $codePrefix . 'PRICE'.$priceType['ID'].'_EXTRA_ID',
                    'sortable' => true
                ];
            }
        }

        /**/
        $properties[] = [
            'id' => $idCounter++,
            'name' => Loc::getMessage('catalog.quantity'),
            'code' => $codePrefix . 'QUANTITY',
            'sortable' => true
        ];
        $properties[] = [
            'id' => $idCounter++,
            'name' => Loc::getMessage('catalog.quantity.reserved'),
            'code' => $codePrefix . 'QUANTITY_RESERVED',
            'sortable' => false
        ];


        /**/
        $stores = Arrays::fromDBResult(\CCatalogStore::GetList(
            ['SORT'=>'ID'],
            [],
            false,
            false,
            ['ID', 'TITLE', 'ADDRESS']
        ))->asArray();

        foreach ($stores as $store) {
            if(strlen($store['TITLE']) == 0 && $store['ADDRESS'])
                $store['TITLE'] = $store['ADDRESS'];

            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage('catalog.quantity.store').' "'.$store['TITLE'].'"',
                'code' => $codePrefix . 'STORE_AMOUNT_' . $store['ID'],
                'sortable' => false
            ];
        }

        $propertyItems = [
            'WEIGHT',
            'LENGTH',
            'WIDTH',
            'HEIGHT',
            'MEASURE',
            'MEASURE_RATIO',
            'VAT_INCLUDED',
            'VAT',
            'PRODUCT_TYPE',
            'QUANTITY_TRACE',
            'CAN_BUY_ZERO',
            'SUBSCRIBE'
        ];

        foreach ($propertyItems as $propertyItem) {
            $name = StringHelper::toLowerCase($propertyItem);
            $name = StringHelper::replace($name, ['_' => '.']);
            $name = 'catalog.' . $name;

            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage($name),
                'code' => $codePrefix . $propertyItem
            ];
        }

        if($addHeader)
            $properties[0]['groupItemsId']['count'] = count($properties) - 1;

        if ($indexByCode)
            $properties = TableHelper::getIndexByCode($properties);

        return $properties;
    }

    public static function getSectionProperties ($iBlockId, $levels = 0, $addHeader = false, $indexByCode = false)
    {
        if (empty($iBlockId))
            return [];

        $levels = Type::toInteger($levels);

        if (!Type::isInteger($levels))
            $levels = 0;

        if ($levels < 0)
            $levels = 0;

        $properties = [];
        $codePrefix = 'SECTION_PROPERTY_';
        $levelPrefix = 'LEVEL_';
        $idCounter = 0;

        $propertyItems = [
            'NAME',
            'CODE',
            'XML_ID',
            'ID',
            'ACTIVE',
            'SORT',
            'PICTURE',
            'DETAIL_PICTURE',
            'DESCRIPTION',
            'SECTION_PAGE_URL',
            'META_TITLE',
            'META_KEYWORDS',
            'META_DESCRIPTION',
            'PAGE_TITLE',
        ];
        $propertyExtraItems = [
            'TEMPLATE_SECTION_META_TITLE',
            'TEMPLATE_SECTION_META_KEYWORDS',
            'TEMPLATE_SECTION_META_DESCRIPTION',
            'TEMPLATE_SECTION_PAGE_TITLE',
            'TEMPLATE_SECTION_PICTURE_FILE_ALT',
            'TEMPLATE_SECTION_PICTURE_FILE_TITLE',
            'TEMPLATE_SECTION_PICTURE_FILE_NAME',
            'TEMPLATE_SECTION_DETAIL_PICTURE_FILE_ALT',
            'TEMPLATE_SECTION_DETAIL_PICTURE_FILE_TITLE',
            'TEMPLATE_SECTION_DETAIL_PICTURE_FILE_NAME',
            'TEMPLATE_ELEMENT_META_TITLE',
            'TEMPLATE_ELEMENT_META_KEYWORDS',
            'TEMPLATE_ELEMENT_META_DESCRIPTION',
            'TEMPLATE_ELEMENT_PAGE_TITLE',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_NAME',
            'SECTION_IBLOCK_SECTION_ID',
            'SECTION_DEPTH_LEVEL',
            'SECTION_PATH_NAMES',
            'SECTION_PROPERTIES',
        ];
        $propertyUFItems = [];

        $userFields = Arrays::fromDBResult(\CUserTypeEntity::GetList([], ['ENTITY_ID' => 'IBLOCK_' . $iBlockId . '_SECTION', 'LANG' => LANGUAGE_ID]))->asArray();

        foreach ($userFields as $userField) {
            $propertyUFItems[$userField['FIELD_NAME']] = $userField['EDIT_FORM_LABEL'] ? $userField['EDIT_FORM_LABEL'].' ('.$userField['FIELD_NAME'].')' : $userField['FIELD_NAME'];
        }

        $count = 0;

        for ($i = 0; $i <= $levels; $i++) {
            if ($addHeader) {
                $groupName = Loc::getMessage('section');

                if ($i > 0)
                    $groupName = "$groupName $i " . Loc::getMessage('section.level');

                $properties[] = [
                    'id' => 0,
                    'name' => $groupName,
                    'code' => 'GROUP_SECTION_PROPERTY_LEVEL_' . $i,
                    'disable' => true,
                    'sortable' => false
                ];

                unset($groupName );
            }

            foreach ($propertyItems as $propertyItem) {
                $name = StringHelper::toLowerCase($propertyItem);
                $name = StringHelper::replace($name, ['_' => '.']);
                $name = 'section.' . $name;

                if ($i > 0)
                    $name = Loc::getMessage($name) . ' [' . $i . ' ' . Loc::getMessage('section.level') . ']';
                else
                    $name = Loc::getMessage($name);

                $properties[] = [
                    'id' => $idCounter++,
                    'name' => $name,
                    'code' => $codePrefix . $levelPrefix . $i . '_' . $propertyItem
                ];
            }

            if ($i === 0) {
                foreach ($propertyExtraItems as $propertyExtraItem) {
                    $name = StringHelper::toLowerCase($propertyExtraItem);
                    $name = StringHelper::replace($name, ['_' => '.']);
                    $name = 'section.' . $name;

                    $properties[] = [
                        'id' => $idCounter++,
                        'name' => Loc::getMessage($name),
                        'code' => $codePrefix . $levelPrefix . $i . '_' . $propertyExtraItem
                    ];
                }
            }

            foreach ($propertyUFItems as $UFName => $propertyUFItem) {
                if ($i > 0)
                    $propertyUFItem = $propertyUFItem . ' [' . $i . ' ' . Loc::getMessage('section.level') . ']';

                $properties[] = [
                    'id' => $idCounter++,
                    'name' => $propertyUFItem,
                    'code' => $codePrefix . $levelPrefix . $i . '_' . $UFName
                ];
            }

            if($addHeader) {
                if ($i === 0) {
                    $count = count($properties);
                    $properties[0]['groupItemsId']['count'] = $count - 1;
                } else {
                    $properties[$count]['groupItemsId']['count'] = count($properties) - $count - 1;
                    $count = count($properties);
                }
            }
        }

        if ($indexByCode)
            $properties = TableHelper::getIndexByCode($properties);

        return $properties;
    }

    public static function getSaleProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getSetProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getKitProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getOrderPositionProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getOffersMainProperties ($addHeader = false, $custom = [])
    {
        if (!Loader::includeModule('catalog'))
            return [];

        $prefix = 'OFFERS_';

        if (!empty($custom))
            $propertiesList = $custom;
        else
            $propertiesList = [
                'ID',
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
                'SECTION_PATH'
            ];


        $properties = [];

        if ($addHeader)
            $properties[] = [
                'id' => 0,
                'name' => Loc::getMessage('offers.element'),
                'code' => 'GROUP_' . $prefix . '_BASE',
                'disable' => true,
                'sortable' => false,
                'groupItemsId' => [
                    'count' => count($propertiesList)
                ]
            ];

        $idCounter = 0;

        foreach ($propertiesList as $value) {
            $idCounter++;

            $properties[] = [
                'id' => $idCounter,
                'name' => IBlockHelper::getMainName($value),
                'code' => $prefix . $value,
                'sortable' => IBlockHelper::getSortable($prefix . $value),
            ];
        }

        return $properties;
    }

    public static function getOffersBaseProperties ($iBlockId, $namePrefix = '', $addHeader = false, $indexByCode = false)
    {
        if (empty($iBlockId))
            return [];

        if (!Loader::includeModule('catalog'))
            return [];

        $iBlockIdOffers = \CCatalog::GetByID($iBlockId);

        if (!$iBlockIdOffers) {
            $iBlockId = \CCatalogSKU::GetInfoByProductIBlock($iBlockId);
            $iBlockId = $iBlockId['IBLOCK_ID'];
        } else {
            $iBlockId = $iBlockIdOffers['OFFERS_IBLOCK_ID'];
        }

        unset($iBlockIdOffers);

        $codePrefix = 'OFFERS_PROPERTY_';
        $hasDescription = false;

        $properties = [];

        Arrays::fromDBResult(\CIBlockProperty::GetList([], ['IBLOCK_ID' => $iBlockId]))->asArray(function ($index, $iBlock) use ($codePrefix, $namePrefix, &$properties) {
            $id = Type::toInteger($iBlock['ID']);
            $name = !empty($namePrefix) ? $namePrefix . ' ' . $iBlock['NAME'] . ' [' . $iBlock['CODE'] . ']' : $iBlock['NAME'] . ' [' . $iBlock['CODE'] . ']';
            $code = $codePrefix . $iBlock['CODE'];

            if (empty($iBlock['CODE']))
                $code = 'EMPTY_OFFERS_PROPERTY_' . $iBlock['ID'];

            $properties[] = [
                'id' => $id,
                'name' => $name,
                'code' => $code
            ];

            if ($iBlock['WITH_DESCRIPTION'] === 'Y') {
                $properties[] = [
                    'id' => $id,
                    'name' => $name . ' ' . Loc::getMessage('property.description'),
                    'code' => $code . '_DESCRIPTION',
                ];
            }
        });


        if ($indexByCode)
            $properties = TableHelper::getIndexByCode($properties);

        if (!empty($namePrefix)) {
            foreach ($properties as &$property) {
                $property['name'] = $namePrefix . ' ' . $property['name'];
            }
        }

        if ($addHeader) {
            ArrayHelper::unshift($properties, [
                'id' => 0,
                'name' => Loc::getMessage('properties'),
                'code' => 'GROUP_OFFERS_PROPERTIES',
                'disable' => true,
                'sortable' => false,
                'groupItemsId' => [
                    'count' => count($properties)
                ]

            ]);
        }

        return $properties;
    }

    public static function getOffersSeoProperties ($addHeader = false, $indexByCode = false)
    {

        $properties = [];
        $codePrefix = 'OFFERS_SEO_PROPERTY_';
        $idCounter = 0;

        $propertyItems = [
            'ELEMENT_META_TITLE',
            'ELEMENT_META_KEYWORDS',
            'ELEMENT_META_DESCRIPTION',
            'ELEMENT_PAGE_TITLE',
            'ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'ELEMENT_DETAIL_PICTURE_FILE_NAME',
            'TEMPLATE_ELEMENT_META_TITLE',
            'TEMPLATE_ELEMENT_META_KEYWORDS',
            'TEMPLATE_ELEMENT_META_DESCRIPTION',
            'TEMPLATE_ELEMENT_PAGE_TITLE',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'TEMPLATE_ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'TEMPLATE_ELEMENT_DETAIL_PICTURE_FILE_NAME',
            'CHANGE_ELEMENT_META_TITLE',
            'CHANGE_ELEMENT_META_KEYWORDS',
            'CHANGE_ELEMENT_META_DESCRIPTION',
            'CHANGE_ELEMENT_PAGE_TITLE',
            'CHANGE_ELEMENT_PREVIEW_PICTURE_FILE_ALT',
            'CHANGE_ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
            'CHANGE_ELEMENT_PREVIEW_PICTURE_FILE_NAME',
            'CHANGE_ELEMENT_DETAIL_PICTURE_FILE_ALT',
            'CHANGE_ELEMENT_DETAIL_PICTURE_FILE_TITLE',
            'CHANGE_ELEMENT_DETAIL_PICTURE_FILE_NAME',
        ];

        foreach ($propertyItems as $propertyItem) {
            $name = StringHelper::toLowerCase($propertyItem);
            $name = StringHelper::replace($name, ['_' => '.']);
            $name = 'seo.' . $name;

            $properties[] = [
                'id' => $idCounter++,
                'name' => Loc::getMessage($name),
                'code' => $codePrefix . $propertyItem
            ];
        }

        if ($addHeader) {
            ArrayHelper::unshift($properties, [
                'id' => 0,
                'name' => Loc::getMessage('offers.seo'),
                'code' => 'GROUP_OFFERS_SEO',
                'disable' => true,
                'sortable' => false,
                'groupItemsId' => [
                    'count' => count($properties)
                ]
            ]);
        }

        if ($indexByCode)
            $properties = TableHelper::getIndexByCode($properties);

        return $properties;
    }

    public static function getOffersCatalogProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getOffersSaleProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getOffersOrderPositionProperties ($iBlockId)
    {
        if (empty($iBlockId))
            return null;

    }

    public static function getAllProperties ($iBlockId, $addHeaders = false, $level = null)
    {
        if (empty($iBlockId))
            return null;

        $properties = self::getMainProperties($addHeaders);
        $properties = array_merge($properties, self::getBaseProperties($iBlockId, null, $addHeaders));
        $properties = array_merge($properties, self::getCatalogProperties($iBlockId, $addHeaders));
        $properties = array_merge($properties, self::getSeoProperties($addHeaders));
        $properties = array_merge($properties, self::getSectionProperties($iBlockId, $level, $addHeaders));
        $properties = array_merge($properties, self::getOffersMainProperties($addHeaders));
        $properties = array_merge($properties, self::getOffersBaseProperties($iBlockId, null, $addHeaders));
        $properties = array_merge($properties, self::getCatalogProperties($iBlockId, $addHeaders, false, 'OFFERS_'));
        $properties = array_merge($properties, self::getOffersSeoProperties($addHeaders));
        $properties = TableHelper::changeValueOnKey($properties, 'id');

        if ($addHeaders)
            $properties = self::setGroupsCount($properties);

        return $properties;
    }

    public static function getElements ($iBlockId, $order = ['SORT'=>'ASC'], $filter = [], $select = ['UF_BROWSER_TITLE'],  $limit = 0, $offset = 0, $withProperties = false, $optimization = false)
    {
        if (empty($iBlockId))
            return null;

        /* if the select is greater than 50, a Mysql error occurs */
        $maxElements = 35;

        $elements = new ElementsQueryCustom;
        $elements->setIBlockId($iBlockId);
        $elements->setSort($order);
        $elements->setFilter($filter);

        /* if the select is greater than 50, a Mysql error occurs */
        if (count($select) < $maxElements)
            $elements->setSelect($select);

        $elements->setLimit($limit);
        $elements->setOffset($offset);

        /*  custom thing */
        /* if the select is greater than 50, a Mysql error occurs */
        if (count($select) < $maxElements) {
            $elements->setSelectProperties($select, true);
            $elements->setIsOptimization($optimization);
        }
        /* /custom thing */

        $elements->setWithProperties($withProperties);

        $elements = $elements->execute()->asArray();

        /*add section path and picture path*/
        foreach ($elements as &$element) {

            if (!empty($element['PREVIEW_PICTURE'])) {
                $element['PREVIEW_PICTURE'] = \CFile::GetFileArray($element['PREVIEW_PICTURE']);
                $element['PREVIEW_PICTURE'] = $element['PREVIEW_PICTURE']['SRC'];
            }

            if (!empty($element['DETAIL_PICTURE'])) {
                $element['DETAIL_PICTURE'] = \CFile::GetFileArray($element['DETAIL_PICTURE']);
                $element['DETAIL_PICTURE'] = $element['DETAIL_PICTURE']['SRC'];
            }

            $sectionPath = Arrays::fromDBResult(\CIBlockSection::GetNavChain($elements['IBLOCK_ID'], $element['IBLOCK_SECTION_ID']));
            $element['SECTION_PATH'] = '';

            if (!empty($sectionPath)) {
                foreach ($sectionPath as $value) {
                    if (empty($element['SECTION_PATH']))
                        $element['SECTION_PATH'] = $value['NAME'];
                    else
                        $element['SECTION_PATH'] = $element['SECTION_PATH'] . ' / ' . $value['NAME'];
                }
            }
        }

        return $elements;
    }

    public static function getElementsCount ($iBlockId, $order = ['ID'=>'ASC'], $filter = ['ID'])
    {
        $elements = new ElementsQueryCustom();
        $elements->setIBlockId($iBlockId);
        $elements->setSort($order);
        $elements->setFilter($filter);
        $elements->setSelect(['ID','NAME','IBLOCK_ID']);
        $elements->setWithProperties(false);

        $elements = $elements->execute()->asArray();

        return count($elements);
    }

    private static function setGroupsCount ($properties)
    {
        if (empty($properties))
            return $properties;

        foreach ($properties as &$property) {
            if (empty($property['groupItemsId']))
                continue;

            $property['groupItemsId']['from'] = $property['id'] + 1;
            $property['groupItemsId']['to'] = $property['id'] + $property['groupItemsId']['count'];
        }

        return $properties;
    }
}