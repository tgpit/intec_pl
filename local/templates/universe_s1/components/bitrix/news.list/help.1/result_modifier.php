<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\template\Properties;
use intec\core\helpers\ArrayHelper;

/**
 * @var $arParams
 * @var $arResult
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'IBLOCK_DESCRIPTION_SHOW' => 'N',
    'BANNER_SHOW' => 'N',
    'BANNER_THEME' => 'dark',
    'BANNER_TITLE_SHOW' => 'N',
    'BANNER_TITLE' => null,
    'BANNER_SUBTITLE_SHOW' => 'N',
    'BANNER_SUBTITLE' => null,
    'ELEMENT_LINK_USE' => 'N',
    'ELEMENT_LINK_PROPERTY' => null
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'IBLOCK_DESCRIPTION' => [
        'SHOW' => $arParams['IBLOCK_DESCRIPTION_SHOW'] === 'Y' && !empty($arResult['DESCRIPTION'])
    ],
    'LINK' => [
        'HIDE' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
    ],
    'BANNER' => [
        'SHOW' => $arParams['BANNER_SHOW'] === 'Y' && !empty($arResult['PICTURE']),
        'PICTURE' => null,
        'THEME' => $arParams['BANNER_THEME'],
        'TITLE' => [
            'SHOW' => $arParams['BANNER_TITLE_SHOW'] === 'Y',
            'VALUE' => $arParams['BANNER_TITLE']
        ],
        'SUBTITLE' => [
            'SHOW' => $arParams['BANNER_SUBTITLE_SHOW'] === 'Y',
            'VALUE' => $arParams['BANNER_SUBTITLE']
        ]
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER'] && !empty($arResult['NAV_STRING'])
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER'] && !empty($arResult['NAV_STRING'])
        ]
    ]
];

if (!empty($arResult['PICTURE'])) {
    $sBanner = CFile::ResizeImageGet($arResult['PICTURE'], [
        'width' => 1700,
        'height' => 400
    ], BX_RESIZE_IMAGE_PROPORTIONAL);
    $arVisual['BANNER']['PICTURE'] = $sBanner['src'];

    unset($sBanner);
}

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'LINK' => [
            'SHOW' => !($arVisual['LINK']['HIDE'] && empty($arItem['DETAIL_TEXT'])),
            'VALUE' => $arItem['DETAIL_PAGE_URL']
        ]
    ];

    $sLink = ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['ELEMENT_LINK_PROPERTY'], 'VALUE']);

    if ($arItem['DATA']['LINK']['SHOW']
        && $arParams['ELEMENT_LINK_USE'] === 'Y'
        && !empty($arParams['ELEMENT_LINK_PROPERTY'])
        && !empty($sLink)) {
        $arItem['DATA']['LINK']['VALUE'] = $sLink;
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual, $sLink);
