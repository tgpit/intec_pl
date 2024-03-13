<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'NAME_SHOW' => 'N',
    'COLUMNS' => 4,
    'COLUMNS_MOBILE' => 2,
    'DESCRIPTION_SHOW' => 'N'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'NAME' => [
        'SHOW' => $arParams['NAME_SHOW'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'COLUMNS' => [
        'DESKTOP' => ArrayHelper::fromRange([4, 5, 6], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([2, 1], $arParams['COLUMNS_MOBILE'])
    ],
    'NAVIGATION' => [
        'SHOW' => [
            'TOP' => false,
            'BOTTOM' => false,
            'ALWAYS' => $arParams['PAGER_SHOW_ALWAYS']
        ],
        'COUNT' => Type::toInteger($arParams['NEWS_COUNT'])
    ],
    'MENU_POSITION' => $arParams['MENU_POSITION']
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arNavigation = [];

if (!empty($arResult['NAV_RESULT'])) {
    $arNavigation = [
        'PAGE' => [
            'COUNT' => $arResult['NAV_RESULT']->NavPageCount,
            'NUMBER' => $arResult['NAV_RESULT']->NavPageNomer,
        ],
        'NUMBER' => $arResult['NAV_RESULT']->NavNum
    ];

    if ($arVisual['NAVIGATION']['SHOW']['ALWAYS']) {
        $arVisual['NAVIGATION']['SHOW']['TOP'] = $arParams['DISPLAY_TOP_PAGER'];
        $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = $arParams['DISPLAY_BOTTOM_PAGER'];
    } else if ($arVisual['NAVIGATION']['COUNT'] > 0 && $arNavigation['PAGE']['COUNT'] > 1) {
        $arVisual['NAVIGATION']['SHOW']['TOP'] = $arParams['DISPLAY_TOP_PAGER'];
        $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = $arParams['DISPLAY_BOTTOM_PAGER'];
    }
} else {
    $arVisual['NAVIGATION']['SHOW']['TOP'] = false;
    $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = false;
}

foreach ($arResult['ITEMS'] as &$arItem) {
    $rsSection = CIBlockSection::GetList(
        [],
        [
            'ID' => $arItem['IBLOCK_SECTION_ID'],
            'CNT_ACTIVE' => 'Y','ELEMENT_SUBSECTIONS' => 'N'
        ],
        true,
        [
            'ID',
            'IBLOCK_ID',
            'CODE'
        ]
    );

    if ($arSection = $rsSection->GetNext()) {
        $arItem['IBLOCK_SECTION_CODE'] = $arSection['CODE'];
    }
}

$arResult['VISUAL'] = &$arVisual;
$arResult['NAVIGATION'] = &$arNavigation;

unset($rsSection, $arSection, $arVisual, $arNavigation);