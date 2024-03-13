<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;

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
    'PRODUCTS_PROPERTY' => null,
    'DETAIL_TEXT_HEADER_PROPERTY' => null,
    'BANNER_TEXT_PROPERTY' => null,
    'BANNER_THEME_PROPERTY' => null,
    'SHARES_PROPERTY' => null,
    'PRODUCTS_FILTER_NAME' => 'arrCollectionProductsFilter',
    'DETAIL_TEXT_SHOW' => 'Y',
    'SHARES_SHOW' => 'N',
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'BANNER' => [
        'PICTURE' => !empty($arResult['DETAIL_PICTURE']) ? $arResult['DETAIL_PICTURE']['SRC'] : $arResult['PREVIEW_PICTURE']['SRC'],
        'TEXT' => null,
        'THEME' => 'dark'
    ],
    'PRODUCTS' => [
        'SHOW' => $arParams['PRODUCTS_SHOW'] === 'Y',
        'HEADER' => [
            'SHOW' => $arParams['PRODUCTS_HEADER_SHOW'] === 'Y',
            'VALUE' => !empty($arParams['PRODUCTS_HEADER']) ? $arParams['PRODUCTS_HEADER'] : $arResult['NAME']
        ]
    ],
    'SHARES' => [
        'SHOW' => $arParams['SHARES_SHOW'] === 'Y'
    ],
    'DETAIL' => [
        'SHOW' => $arParams['DETAIL_TEXT_SHOW'] === 'Y' && !empty($arResult['DETAIL_TEXT']),
        'HEADER' => null,
        'TEXT' => $arResult['DETAIL_TEXT']
    ],
    'LINK' => [
        'BACK' => [
            'SHOW' => $arParams['LINK_SHOW'] === 'Y',
            'VALUE' => ArrayHelper::getValue($arParams, 'LINK_VALUE')
        ]
    ],
    'VIEWS' => [
        'TEXT' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_VIEWS_TEXT'),
            'VALUE' => 'text',
            'ICON' => 'glyph-icon-view_text',
            'ACTIVE' => false
        ],
        'LIST' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_VIEWS_LIST'),
            'VALUE' => 'list',
            'ICON' => 'glyph-icon-view_list',
            'ACTIVE' => false
        ],
        'TILE' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_VIEWS_TILE'),
            'VALUE' => 'tile',
            'ICON' => 'glyph-icon-view_tile',
            'ACTIVE' => false
        ]
    ]
];

if (!empty($arParams['BANNER_TEXT_PROPERTY'])) {
    $arBannerText = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BANNER_TEXT_PROPERTY']]);

    if (Type::isArray($arBannerText['VALUE'])) {
        $arBannerText = CIBlockFormatProperties::GetDisplayValue($arResult['PROPERTIES'], $arBannerText, null);
        $arVisual['BANNER']['TEXT'] = $arBannerText['DISPLAY_VALUE'];
    } else {
        $arVisual['BANNER']['TEXT'] = $arBannerText['VALUE'];
    }

    unset($arBannerText);
}

if (!empty($arParams['BANNER_THEME_PROPERTY'])) {
    $arProperty = ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['BANNER_THEME_PROPERTY'],
        'VALUE'
    ]);

    if (!empty($arProperty))
        $arVisual['BANNER']['THEME'] = 'light';
}

if (!empty($arParams['DETAIL_TEXT_HEADER_PROPERTY']))
    $arVisual['DETAIL']['HEADER'] = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['DETAIL_TEXT_HEADER_PROPERTY'], 'VALUE']);

if ($arVisual['PRODUCTS']['SHOW'] && !empty($arParams['PRODUCTS_PROPERTY'])) {
    $arProductIds = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['PRODUCTS_PROPERTY'], 'VALUE']);

    if (!empty($arProductIds) && !empty($arParams['PRODUCTS_FILTER_NAME'])) {
        $GLOBALS[$arParams['PRODUCTS_FILTER_NAME']] = ['ID' => $arProductIds];
    } else {
        $arVisual['PRODUCTS']['SHOW'] = false;
    }
} else {
    $arVisual['PRODUCTS']['SHOW'] = false;
}

$sView = Core::$app->request->get('view');
$arCurrentView = !empty($sView) ? $sView : Core::$app->session->get('BITRIX_COLLECTION_VIEW');

unset($sView);

if (empty($arCurrentView))
    $arCurrentView = ArrayHelper::getValue($arParams, 'LIST_VIEW');

Core::$app->session->set('BITRIX_COLLECTION_VIEW', $arCurrentView);

foreach ($arVisual['VIEWS'] as $sView => &$arView) {
    if ($arCurrentView === $arView['VALUE'])
        $arView['ACTIVE'] = true;

    unset($arView);
}

$arResult['VISUAL'] = $arVisual;

unset($sView, $arCurrentView, $arVisual);