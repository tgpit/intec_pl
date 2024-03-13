<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\GroupLangTable;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arIBlocksType = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
]))->indexBy('ID');

$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksType,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arAccessoriesIBlocks = $arIBlocks->asArray(function ($key, $arIBlock) use (&$arCurrentValues) {
        if ($arIBlock['IBLOCK_TYPE_ID'] === $arCurrentValues['IBLOCK_TYPE'])
            return [
                'key' => $arIBlock['ID'],
                'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
            ];

        return ['skip' => true];
    });
else
    $arAccessoriesIBlocks = $arIBlocks->asArray(function ($key, $arIBlock) {
        return [
            'key' => $arIBlock['ID'],
            'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
        ];
    });

$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arAccessoriesIBlocks,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyTextSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyLinkedMultiple = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'E' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'Y')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyLinkedMultiple = $arProperties->asArray($hPropertyLinkedMultiple);

    $arTemplateParameters['ELEMENT_ID_ENTER'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_ELEMENT_ID_ENTER'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['ELEMENT_ID_ENTER'] === 'Y') {
        $arAllElements = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]))->indexBy('ID');

        $arAccessoriesElements = $arAllElements->asArray(function ($key, $arIBlock) use (&$arCurrentValues) {
            if ($arIBlock['IBLOCK_ID'] === $arCurrentValues['IBLOCK_ID'])
                return [
                    'key' => $key,
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];

            return ['skip' => true];
        });

        $arTemplateParameters['ELEMENT_ID'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_ELEMENT_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arAccessoriesElements,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    } else {
        $arTemplateParameters['REQUEST_NAME'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_REQUEST_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'PRODUCT_ID',
        ];
    }

    $arTemplateParameters['PROPERTY_PRODUCTS_ACCESSORIES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_PROPERTY_PRODUCTS_ACCESSORIES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    if (!empty($arCurrentValues['PROPERTY_PRODUCTS_ACCESSORIES'])) {
        $arTemplateParameters['SECTIONS_LIST_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_SECTIONS_LIST_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ];
        $arTemplateParameters['FILTER_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_FILTER_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['FILTER_SHOW'] === 'Y') {
            include(__DIR__ . '/parameters/filter.php');
        }

        include(__DIR__ . '/parameters/section.php');
    }

    $arTemplateParameters['ERROR_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_ERROR_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];

    $arTemplateParameters['LIST_SORT_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_LIST_SORT_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];
    if ($arCurrentValues['LIST_SORT_SHOW'] === 'Y') {
        if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
            $arPrices = Arrays::from(GroupTable::getList()->fetchAll());
            $arPricesLanguage = Arrays::from(GroupLangTable::getList([
                'filter' => [
                    'LANG' => LANGUAGE_ID
                ]
            ])->fetchAll())->indexBy('CATALOG_GROUP_ID');

            $arTemplateParameters['LIST_SORT_PRICE'] = [
                'PARENT' => 'LIST_SETTINGS',
                'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_LIST_SORT_PRICE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPrices->asArray(function($iIndex, $arPrice) use (&$arPricesLanguage) {
                    $sName = $arPricesLanguage->get($arPrice['ID']);

                    if (!empty($sName))
                        $sName = $sName['NAME'];

                    if (empty($sName))
                        $sName = $arPrice['NAME'];

                    return [
                        'key' => $arPrice['ID'],
                        'value' => '['.$arPrice['ID'].'] '.$sName
                    ];
                })
            ];

            unset($arPricesLanguage);
            unset($arPrices);
        } else if (Loader::includeModule('intec.startshop')) {
            $arPrices = Arrays::fromDBResult(CStartShopPrice::GetList([], [
                'ACTIVE' => 'Y'
            ]));

            $arTemplateParameters['LIST_SORT_PRICE'] = [
                'PARENT' => 'LIST_SETTINGS',
                'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_LIST_SORT_PRICE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPrices->asArray(function($iIndex, $arPrice) {
                    $sName = ArrayHelper::getValue($arPrice, ['LANG', LANGUAGE_ID, 'NAME']);

                    if (empty($sName))
                        $sName = $arPrice['CODE'];

                    return [
                        'key' => $arPrice['ID'],
                        'value' => '['.$arPrice['ID'].'] '.$sName
                    ];
                })
            ];

            unset($arPrices);
        }
    }
}