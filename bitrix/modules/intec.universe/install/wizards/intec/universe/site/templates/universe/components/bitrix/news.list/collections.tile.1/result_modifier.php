<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 4,
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'HIDE' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4], $arParams['COLUMNS']),
    'NAVIGATION' => [
        'SHOW' => [
            'TOP' => !empty($arResult['NAV_STRING']) && $arParams['DISPLAY_TOP_PAGER'],
            'BOTTOM' => !empty($arResult['NAV_STRING']) && $arParams['DISPLAY_BOTTOM_PAGER'],
            'ALWAYS' => $arParams['PAGER_SHOW_ALWAYS']
        ],
        'COUNT' => Type::toInteger($arParams['NEWS_COUNT'])
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'LINK' => [
            'SHOW' => !($arVisual['LINK']['HIDE'] && empty($arItem['DETAIL_TEXT'])),
            'VALUE' => $arItem['DETAIL_PAGE_URL']
        ]
    ];
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);