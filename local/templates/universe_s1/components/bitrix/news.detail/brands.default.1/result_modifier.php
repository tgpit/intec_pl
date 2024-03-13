<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;
use intec\core\collections\Arrays;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!CModule::IncludeModule('iblock'))
    return;

if (!CModule::IncludeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'PRODUCTS_SHOW' => 'N',
    'SECTIONS_SHOW' => 'N',
    'SECTIONS_QUANTITY' => 0,
    'PRODUCTS_IBLOCK_ID' => null,
    'PRODUCTS_PROPERTY_BRAND' => null,
    'CACHE_TYPE_ORIGINAL' => 'Y'
], $arParams);

$arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE_ORIGINAL'];

$arResult['LAZYLOAD'] = [
    'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
    'STUB' => null
];

$arVisual = [
    'PRODUCTS' => [
        'SHOW' => $arParams['PRODUCTS_SHOW'] === 'Y',
        'HEADER' => [
            'SHOW' => $arParams['PRODUCTS_HEADER_SHOW'] === 'Y',
            'VALUE' => ArrayHelper::getValue($arParams, 'PRODUCTS_HEADER')
        ]
    ],
    'SECTIONS' => [
        'SHOW' => $arParams['SECTIONS_SHOW'] === 'Y',
        'HEADER' => [
            'SHOW' => $arParams['SECTIONS_HEADER_SHOW'] === 'Y',
            'VALUE' => ArrayHelper::getValue($arParams, 'SECTIONS_HEADER')
        ],
        'QUANTITY' => Type::isNumeric($arParams['SECTIONS_QUANTITY']) ? $arParams['SECTIONS_QUANTITY'] : 0
    ],
    'LINK' => [
        'SHOW' => $arParams['LINK_SHOW'] === 'Y',
        'VALUE' => ArrayHelper::getValue($arParams, 'LINK_VALUE')
    ]
];

if (empty($arVisual['PRODUCTS']['HEADER']['VALUE']))
    $arVisual['PRODUCTS']['HEADER']['SHOW'] = false;

if ($arVisual['PRODUCTS']['HEADER']['SHOW'])
    $arVisual['PRODUCTS']['HEADER']['VALUE'] = StringHelper::replaceMacros(
        $arVisual['PRODUCTS']['HEADER']['VALUE'], ['BRAND' => $arResult['NAME']]
    );

if (empty($arVisual['SECTIONS']['HEADER']['VALUE']))
    $arVisual['SECTIONS']['HEADER']['SHOW'] = false;

if ($arVisual['SECTIONS']['HEADER']['SHOW'])
    $arVisual['SECTIONS']['HEADER']['VALUE'] = StringHelper::replaceMacros(
        $arVisual['SECTIONS']['HEADER']['VALUE'], ['BRAND' => $arResult['NAME']]
    );

if ($arVisual['SECTIONS']['QUANTITY'] < 0) {
    $arVisual['SECTIONS']['QUANTITY'] = 0;
}

if (!empty($arParams['PRODUCTS_IBLOCK_ID']) || !empty($arParams['PRODUCTS_PROPERTY_BRAND'])) {
    $arFilter = [
        'IBLOCK_ID' => $arParams['PRODUCTS_IBLOCK_ID'],
        'ACTIVE' => 'Y',
        'PROPERTY_'.$arParams['PRODUCTS_PROPERTY_BRAND'] => $arResult['ID']
    ];

    $arElements = Arrays::fromDBResult(CIBlockElement::GetList([], $arFilter, ['IBLOCK_SECTION_ID']))->asArray();

    if (!empty($arElements)) {
        if ($arVisual['SECTIONS']['SHOW']) {
            unset($arFilter);
            $arFilter = [
                'ID' => []
            ];
            $iCounter = 0;

            foreach ($arElements as $arElement) {
                if ($iCounter == $arVisual['SECTIONS']['QUANTITY'] && $arVisual['SECTIONS']['QUANTITY'] > 0) {
                    break;
                }

                if (!empty($arElement['IBLOCK_SECTION_ID'])) {
                    $arFilter['ID'][] = $arElement['IBLOCK_SECTION_ID'];
                    $iCounter ++;
                }
            }
            
            if (!empty($arParams['SECTIONS_FILTER_NAME']) && !empty($arFilter['ID'])) {
                $GLOBALS[$arParams['SECTIONS_FILTER_NAME']] = $arFilter;
            } else {
                $arVisual['SECTIONS']['SHOW'] = false;
            }
        }
    } else {
        $arVisual['PRODUCTS']['SHOW'] = false;
        $arVisual['SECTIONS']['SHOW'] = false;
    }

    unset($arFilter, $arElements, $arSections, $arSectionsQuantity, $iCounter);

} else {
    $arVisual['PRODUCTS']['SHOW'] = false;
    $arVisual['SECTIONS']['SHOW'] = false;
}

if (defined('EDITOR'))
    $arResult['LAZYLOAD']['USE'] = false;

if ($arResult['LAZYLOAD']['USE'])
    $arResult['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL'] = $arVisual;

unset($arVisual);