<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

CModule::IncludeModule("iblock");

$sPrefix = 'QUICK_VIEW_';
$arTemplateParameters[$sPrefix.'USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('SB_DEFAULT_QUICK_VIEW_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N'
];

if ($arCurrentValues[$sPrefix.'USE'] === 'Y') {

    $arTemplateParameters['CACHE_TIME'] = [
        'PARENT' => 'CACHE_SETTINGS',
        'NAME' => Loc::getMessage('SB_DEFAULT_CACHE_TIME'),
        'TYPE' => 'STRING',
        'DEFAULT' => 36000000
    ];

    $arTemplateParameters['CACHE_GROUPS'] = [
        'PARENT' => 'CACHE_SETTINGS',
        'NAME' => Loc::getMessage('SB_DEFAULT_CACHE_GROUPS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ];

    $arTemplateParameters['SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
        'SECTION',
        'SECTION_URL',
        Loc::getMessage('SB_DEFAULT_SECTION_URL'),
        '',
        'URL_TEMPLATES'
    );

    $arTemplateParameters['DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
        'DETAIL',
        'DETAIL_URL',
        Loc::getMessage('SB_DEFAULT_DETAIL_URL'),
        '',
        'URL_TEMPLATES'
    );

    $arTemplateParameters['BASKET_URL'] = [
        'PARENT' => 'BASKET',
        'NAME' => Loc::getMessage('SB_DEFAULT_BASKET_URL'),
        'TYPE' => 'STRING',
        'DEFAULT' => '/personal/basket.php',
    ];

    $arTemplateParameters[$sPrefix.'IBLOCK_TYPE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('SB_DEFAULT_QUICK_VIEW_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'N',
        'VALUES' => CIBlockParameters::GetIBlockTypes(),
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues[$sPrefix.'IBLOCK_TYPE'] !== '') {

        $arIBlock = [];

        $iblockFilter = (
        !empty($arCurrentValues[$sPrefix.'IBLOCK_TYPE'])
            ? ['TYPE' => $arCurrentValues[$sPrefix.'IBLOCK_TYPE'], 'ACTIVE' => 'Y']
            : ['ACTIVE' => 'Y']
        );

        $rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);

        while ($arr = $rsIBlock->Fetch())
        {
            $id = (int)$arr['ID'];
            $arIBlock[$id] = '['.$id.'] '.$arr['NAME'];
        }

        unset($id, $arr, $rsIBlock, $iblockFilter, $offersIblock);

        $arTemplateParameters[$sPrefix.'IBLOCK_ID'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('SB_DEFAULT_QUICK_VIEW_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'N',
            'VALUES' => $arIBlock,
            'REFRESH' => 'Y'
        ];
    }

    $arPriceCodes = Arrays::fromDBResult(CStartShopPrice::GetList())->indexBy('CODE');

    $hPriceCodes = function ($sKey, $arProperty) {
        if (!empty($arProperty['CODE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['PRICE_CODE'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('SB_DEFAULT_PRICE_CODE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPriceCodes->asArray($hPriceCodes),
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y'
    ];
    if (!empty($arCurrentValues['PRICE_CODE'])) {
        $arPrices = $arPriceCodes->asArray(function ($sKey, $arProperty) {
            if (!empty($arProperty['CODE']))
                return [
                    'key' => $arProperty['CODE'],
                    'value' => $arProperty['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        });

        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues[$sPrefix.'IBLOCK_ID']
        ]))->indexBy('ID');

        foreach ($arCurrentValues['PRICE_CODE'] as $sPrice) {
            if (!empty($sPrice))
                $arTemplateParameters['PROPERTY_OLD_PRICE_' . $sPrice] = [
                    'PARENT' => 'PRICES',
                    'NAME' => Loc::getMessage('SB_DEFAULT_PROPERTY_OLD_PRICE', ['#PRICE_CODE#' => $arPrices[$sPrice]]),
                    'TYPE' => 'LIST',
                    'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
                        if ($arProperty['PROPERTY_TYPE'] === 'N' && $arProperty['LIST_TYPE'] === 'L' && $arProperty['MULTIPLE'] === 'N') {
                            return [
                                'key' => $arProperty['CODE'],
                                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                            ];
                        }

                        return ['skip' => true];
                    }),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
        }

        unset($arPrices, $arProperties);
    }
    $arTemplateParameters['CONVERT_CURRENCY'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('SB_DEFAULT_CONVERT_CURRENCY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
        $arCurrencies = Arrays::fromDBResult(CStartShopCurrency::GetList())->indexBy('CODE');

        $hCurrencies = function ($sKey, $arProperty) {
            if (!empty($arProperty['CODE']))
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        };

        $arTemplateParameters['CURRENCY_ID'] = [
            'PARENT' => 'PRICES',
            'NAME' => Loc::getMessage('SB_DEFAULT_CURRENCY_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arCurrencies->asArray($hCurrencies),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['USE_PRICE_COUNT'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('SB_DEFAULT_USE_PRICE_COUNT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];

    $arTemplateParameters['SHOW_PRICE_COUNT'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('SB_DEFAULT_SHOW_PRICE_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '1',
    ];

    $arTemplateParameters['PRICE_VAT_INCLUDE'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('SB_DEFAULT_PRICE_VAT_INCLUDE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];

    $arTemplateParameters['ADD_PROPERTIES_TO_BASKET'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('SB_DEFAULT_ADD_PROPERTIES_TO_BASKET'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PARTIAL_PRODUCT_PROPERTIES'] = [
        'PARENT' => 'BASKET',
        'NAME' => Loc::getMessage('SB_DEFAULT_PARTIAL_PRODUCT_PROPERTIES'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'HIDDEN' => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] === 'N' ? 'Y' : 'N')
    ];

    $arTemplateParameters['LINK_IBLOCK_TYPE'] = [
        'PARENT' => 'LINK',
        'NAME' => Loc::getMessage('SB_DEFAULT_LINK_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => CIBlockParameters::GetIBlockTypes(),
        'ADDITIONAL_VALUES' => 'Y',
    ];

    $arIBlock_LINK = [];

    $iblockFilter = (
    !empty($arCurrentValues['LINK_IBLOCK_TYPE']) ? ['TYPE' => $arCurrentValues['LINK_IBLOCK_TYPE'], 'ACTIVE' => 'Y'] : ['ACTIVE' => 'Y']
    );

    $rsIblock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);

    while ($arr = $rsIblock->Fetch())
        $arIBlock_LINK[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];

    unset($iblockFilter);

    $arProperty_LINK = [];
    if (!empty($arCurrentValues['LINK_IBLOCK_ID']) && (int)$arCurrentValues['LINK_IBLOCK_ID'] > 0) {
        $propertyIterator = Iblock\PropertyTable::getList([
            'select' => ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE', 'SORT'],
            'filter' => ['=IBLOCK_ID' => $arCurrentValues['LINK_IBLOCK_ID'], '=PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_ELEMENT, '=ACTIVE' => 'Y'],
            'order' => ['SORT' => 'ASC', 'NAME' => 'ASC']
        ]);
        while ($property = $propertyIterator->fetch())
        {
            $propertyCode = (string)$property['CODE'];
            if ($propertyCode == '')
                $propertyCode = $property['ID'];
            $arProperty_LINK[$propertyCode] = '['.$propertyCode.'] '.$property['NAME'];
        }
        unset($propertyCode, $property, $propertyIterator);
    }

    $arTemplateParameters['LINK_IBLOCK_ID'] = [
        'PARENT' => 'LINK',
        'NAME' => Loc::getMessage('SB_DEFAULT_LINK_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => $arIBlock_LINK
    ];

    $arTemplateParameters['LINK_PROPERTY_SID'] = [
        'PARENT' => "LINK",
        'NAME' => Loc::getMessage('SB_DEFAULT_LINK_PROPERTY_SID'),
        'TYPE' => "LIST",
        'ADDITIONAL_VALUES' => "Y",
        'VALUES' => $arProperty_LINK,
    ];

    $arTemplateParameters['LINK_ELEMENTS_URL'] = [
        'PARENT' => "LINK",
        'NAME' => Loc::getMessage('SB_DEFAULT_LINK_ELEMENTS_URL'),
        'TYPE' => "STRING",
        'DEFAULT' => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
    ];

    $arTemplateParameters['USE_MAIN_ELEMENT_SECTION'] = [
        'PARENT' => "ADDITIONAL_SETTINGS",
        'NAME' => Loc::getMessage('SB_DEFAULT_USE_MAIN_ELEMENT_SECTION'),
        'TYPE' => "CHECKBOX",
        'DEFAULT' => "N"
    ];

    $arTemplateParameters['COMPATIBLE_MODE'] = [
        'PARENT' => 'EXTENDED_SETTINGS',
        'NAME' => Loc::getMessage('SB_DEFAULT_COMPATIBLE_MODE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['DISABLE_INIT_JS_IN_COMPONENT'] = [
        'PARENT' => 'EXTENDED_SETTINGS',
        'NAME' => Loc::getMessage('SB_DEFAULT_DISABLE_INIT_JS_IN_COMPONENT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'HIDDEN' => (isset($arCurrentValues['COMPATIBLE_MODE']) && $arCurrentValues['COMPATIBLE_MODE'] === 'N' ? 'Y' : 'N')
    ];

    $offers = false;
    $usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();

    $arProperty_Offers = [];
    $arProperty_OffersWithoutFile = [];

    $iblockExists = (!empty($arCurrentValues['QUICK_VIEW_IBLOCK_ID']) && (int)$arCurrentValues['QUICK_VIEW_IBLOCK_ID'] > 0);

    $arSort = CIBlockParameters::GetElementSortFields(
        ['SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'],
        ['KEY_LOWERCASE' => 'Y']
    );
    $arAscDesc = [
        "asc" => Loc::getMessage('SB_DEFAULT_IBLOCK_SORT_ASC'),
        "desc" => Loc::getMessage('SB_DEFAULT_IBLOCK_SORT_DESC'),
    ];

    $arTemplateParameters['OFFERS_PROPERTY_CODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('SB_DEFAULT_OFFERS_PROPERTY_CODE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arProperty_Offers,
        'SIZE' => (count($arProperty_Offers) > 5 ? 8 : 3),
        'REFRESH' => isset($templateProperties['MAIN_BLOCK_OFFERS_PROPERTY_CODE']) ? 'Y' : 'N',
        'ADDITIONAL_VALUES' => 'Y',
    ];

    if (!empty($offers) && !$usePropertyFeatures) {

        $arTemplateParameters['OFFERS_CART_PROPERTIES'] = [
            'PARENT' => 'BASKET',
            'NAME' => Loc::getMessage('SB_DEFAULT_OFFERS_CART_PROPERTIES'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arProperty_OffersWithoutFile,
            'SIZE' => (count($arProperty_OffersWithoutFile) > 5 ? 8 : 3),
            'HIDDEN' => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
        ];
    }

    $arTemplateParameters['OFFERS_FIELD_CODE'] = CIBlockParameters::GetFieldCode(Loc::getMessage('SB_DEFAULT_OFFERS_FIELD_CODE'), "VISUAL");

    $arTemplateParameters['OFFERS_SORT_FIELD'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('SB_DEFAULT_OFFERS_SORT_FIELD'),
        'TYPE' => 'LIST',
        'VALUES' => $arSort,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'sort',
    ];

    $arTemplateParameters['OFFERS_SORT_ORDER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('SB_DEFAULT_OFFERS_SORT_ORDER'),
        'TYPE' => 'LIST',
        'VALUES' => $arAscDesc,
        'DEFAULT' => 'asc',
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['OFFERS_SORT_FIELD2'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('SB_DEFAULT_OFFERS_SORT_FIELD2'),
        'TYPE' => 'LIST',
        'VALUES' => $arSort,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'id',
    ];

    $arTemplateParameters['OFFERS_SORT_ORDER2'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('SB_DEFAULT_OFFERS_SORT_ORDER2'),
        'TYPE' => 'LIST',
        'VALUES' => $arAscDesc,
        'DEFAULT' => 'desc',
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['USE_COMPARE'] = [
        'PARENT' => 'COMPARE_SETTINGS',
        'NAME' => Loc::getMessage('SB_DEFAULT_USE_COMPARE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y',
    ];

    if ($arCurrentValues["USE_COMPARE"] == 'Y') {
        $arTemplateParameters['COMPARE_PATH'] = [
            'PARENT' => 'COMPARE',
            'NAME' => Loc::getMessage('SB_DEFAULT_COMPARE_PATH'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];
        $arTemplateParameters["COMPARE_NAME"] = [
            "PARENT" => "COMPARE_SETTINGS",
            "NAME" => Loc::getMessage('SB_DEFAULT_COMPARE_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "CATALOG_COMPARE_LIST"
        ];
    }

    $arTemplateParameters[$sPrefix.'SLIDE_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('SB_DEFAULT_QUICK_VIEW_SLIDE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $sComponent = 'bitrix:catalog.element';
    $sTemplate = 'quick.view.';

    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        $sComponent,
        $siteTemplate
    ))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
        if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
            return ['skip' => true];

        $sName = StringHelper::cut(
            $arTemplate['NAME'],
            StringHelper::length($sTemplate)
        );

        return [
            'key' => $sName,
            'value' => $sName
        ];
    });

    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
    $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

    if (!empty($sTemplate))
        $sTemplate = 'quick.view.'.$sTemplate;

    $arTemplateParameters[$sPrefix.'TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('SB_DEFAULT_QUICK_VIEW_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues[$sPrefix.'IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['sort' => 'asc'], [
            'IBLOCK_ID' => $arCurrentValues[$sPrefix.'IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]));

        $res = $arProperties->asArray(function ($iIndex, $arProperty) {
            $sCode = $arProperty['CODE'];

            if (empty($sCode))
                $sCode = $arProperty['ID'];

            return [
                'key' => $sCode,
                'value' => '['.$sCode.'] '.$arProperty['NAME']
            ];
        });

        $arTemplateParameters[$sPrefix . 'PROPERTY_CODE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SB_DEFAULT_QUICK_VIEW_PROPERTY_CODE'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $arProperties->asArray(function ($iIndex, $arProperty) {
                $sCode = $arProperty['CODE'];

                if (empty($sCode))
                    $sCode = $arProperty['ID'];

                return [
                    'key' => $sCode,
                    'value' => '['.$sCode.'] '.$arProperty['NAME']
                ];
            })
        ];
    }

    if (!empty($sTemplate)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) {
                if ($arParameter)
                    $arParameter['PARENT'] = 'LIST_SETTINGS';
                $arParameter['NAME'] = Loc::getMessage('SB_DEFAULT_QUICK_VIEW').'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }
}
