<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\template\Properties;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'BANNER_DARK_TEXT' => null,
    'BANNER_PROPERTY_TITLE' => null,
    'BANNER_PROPERTY_SUBTITLE' => null,
    'BANNER_PROPERTY_DURATION_END' => null,
    'BANNER_SALE' => null,
    'DESCRIPTION_PROPERTY_TITLE' => null,
    'DESCRIPTION_PROPERTY_SUBTITLE' => null,
    'CONDITIONS_HEADER' => null,
    'CONDITIONS_PROPERTY_ELEMENTS' => null,
    'CONDITIONS_IBLOCK_TYPE' => null,
    'CONDITIONS_IBLOCK_ID' => null,
    'CONDITIONS_COLUMNS' => 3,
    'SERVICES_HEADER' => null,
    'SERVICES_HEADER_POSITION' => 'left',
    'SERVICES_PROPERTY_ELEMENTS' => null,
    'SERVICES_IBLOCK_TYPE' => null,
    'SERVICES_IBLOCK_ID' => null,
    'PRODUCTS_HEADER' => null,
    'PRODUCTS_HEADER_POSITION' => 'left',
    'PRODUCTS_PROPERTY_ELEMENTS' => null,
    'PRODUCTS_IBLOCK_TYPE' => null,
    'PRODUCTS_IBLOCK_ID' => null
], $arParams);

$sBannerTheme = null;

if (!empty($arParams['BANNER_DARK_TEXT'])) {
    $sBannerTheme = CIBlockElement::GetProperty(
        $arResult['IBLOCK_ID'],
        $arResult['ID'],
        ['SORT' => 'ASC'],
        ['CODE' => $arParams['BANNER_DARK_TEXT']]
    )->GetNext();

    $sBannerTheme = $sBannerTheme['VALUE'];
}

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y',
        'PROPERTIES' => []
    ],
    'BANNER' => [
        'THEME' => !empty($sBannerTheme) ? 'dark' : 'light'
    ]
];

unset($sBannerTheme);

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['BLOCKS'] = [
    'BANNER' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/banner.php'
    ],
    'ICONS' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/icons.php'
    ],
    'DESCRIPTION' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/description.php'
    ],
    'CONDITIONS' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/conditions.php'
    ],
    'FORM' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/form.php'
    ],
    'PRODUCTS' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/products.php'
    ],
    'SERVICES' => [
        'ACTIVE' => false,
        'PATH' => __DIR__.'/parts/services.php'
    ]
];

/** Banner */

if (!empty($arResult['DETAIL_PICTURE'])) {
    $arResult['BLOCKS']['BANNER']['PICTURE'] = $arResult['DETAIL_PICTURE'];
} else if (!empty($arResult['PREVIEW_PICTURE'])) {
    $arResult['BLOCKS']['BANNER']['PICTURE'] = $arResult['PREVIEW_PICTURE'];
}

if (!empty($arResult['BLOCKS']['BANNER']['PICTURE'])) {
    $arResult['BLOCKS']['BANNER']['PICTURE'] = $arResult['BLOCKS']['BANNER']['PICTURE']['SRC'];
} else {
    $arResult['BLOCKS']['BANNER']['PICTURE'] = null;
}

if (!empty($arParams['BANNER_PROPERTY_TITLE']))
    $arResult['BLOCKS']['BANNER']['TITLE'] = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BANNER_PROPERTY_TITLE'], 'VALUE']);
if (!empty($arParams['BANNER_PROPERTY_SUBTITLE']))
    $arResult['BLOCKS']['BANNER']['SUBTITLE'] = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BANNER_PROPERTY_SUBTITLE'], 'VALUE']);

$arResult['BLOCKS']['BANNER']['BANNER_DATE'] = CIBlockFormatProperties::DateFormat('d F Y', MakeTimeStamp(ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BANNER_PROPERTY_DURATION_END'], 'VALUE']), CSite::GetDateFormat()));
$arResult['BLOCKS']['BANNER']['BANNER_DATE_TIMER'] = CIBlockFormatProperties::DateFormat('Y-m-d H:i:s', MakeTimeStamp(ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BANNER_PROPERTY_DURATION_END'], 'VALUE']), CSite::GetDateFormat()));

$arResult['BLOCKS']['BANNER']['BANNER_SALE'] = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BANNER_SALE'], 'VALUE']);

$arVisual['TIMER']['PROPERTIES'] = [
    'component' => 'intec.universe:product.timer',
    'template' => 'template.2',
    'parameters' => [
        'ELEMENT_ID' => $arResult['ID'],
        'IBLOCK_ID' => $arResult['IBLOCK_ID'],
        'IBLOCK_TYPE' => CIBlockType::GetByID($arResult['IBLOCK_ID']),
        'QUANTITY' => 1,
        'ITEM_NAME' => $arResult['NAME'],
        'TIMER_QUANTITY_OVER' => 'N',
        'TIME_ZERO_HIDE' => 'Y',
        'MODE' => 'set',
        'UNTIL_DATE' => $arResult['BLOCKS']['BANNER']['BANNER_DATE_TIMER'],
        'TIMER_SECONDS_SHOW' => $arParams['TIMER_SECONDS_SHOW'],
        'TIMER_QUANTITY_SHOW' => 'N',
        'TIMER_HEADER_SHOW' => 'N',
        'TIMER_HEADER' => '',
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'TIMER_TITLE_SHOW' => 'N',
        'COMPOSITE_FRAME_MODE' => 'A',
        'COMPOSITE_FRAME_TYPE' => 'AUTO',
        'SALE_SHOW' => $arParams['TIMER_SALE_SHOW'],
        'SALE_VALUE' => $arResult['BLOCKS']['BANNER']['BANNER_SALE']
    ]
];

if (!empty($arResult['BLOCKS']['BANNER']['PICTURE']) && $arParams['BANNER_SHOW'] === 'Y')
    $arResult['BLOCKS']['BANNER']['ACTIVE'] = true;

/** Icons */

$sPrefix = 'ICONS_';
$arResult['BLOCKS']['ICONS']['TEMPLATE'] = ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE');

if (!empty($arResult['BLOCKS']['ICONS']['TEMPLATE'])) {
    $arResult['BLOCKS']['ICONS']['TEMPLATE'] = 'template.'.$arResult['BLOCKS']['ICONS']['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (StringHelper::startsWith($sKey, $sPrefix)) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arResult['BLOCKS']['ICONS']['PARAMETERS'][$sKey] = $mValue;
        }
    }
}

if (!empty($arParams['ICONS_LINK'])) {
    $rsProp = CIBlockElement::GetProperty(
        $arResult['IBLOCK_ID'],
        $arResult['ID'],
        ['ID' => 'ASC'],
        ['CODE' => $arParams['ICONS_LINK']]
    );

    while ($arOb = $rsProp->GetNext()) {
        $arResult['BLOCKS']['ICONS']['ELEMENTS'][] = (int)$arOb['VALUE'];
    }

    unset($rsProp, $arOb);
}

$arResult['BLOCKS']['ICONS']['PARAMETERS'] = ArrayHelper::merge($arResult['BLOCKS']['ICONS']['PARAMETERS'], [
    'FILTER' => [
        'ID' => $arResult['BLOCKS']['ICONS']['ELEMENTS']
    ],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'SETTINGS_USE' => $arParams['SETTINGS_USE']
]);

if (!empty($arResult['BLOCKS']['ICONS']['PARAMETERS']) && $arParams['ICONS_SHOW'] === 'Y' && !empty($arResult['BLOCKS']['ICONS']['ELEMENTS']))
    $arResult['BLOCKS']['ICONS']['ACTIVE'] = true;

/** Description */

$arResult['BLOCKS']['DESCRIPTION']['TITLE'] = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['DESCRIPTION_PROPERTY_TITLE'], 'VALUE']);

if (Type::isArray($arResult['BLOCKS']['DESCRIPTION']['TITLE'])) {
    $arResult['BLOCKS']['DESCRIPTION']['TITLE'] = $arResult['BLOCKS']['DESCRIPTION']['TITLE']['TEXT'];
}

if (!empty($arResult['DETAIL_TEXT'])) {
    $arResult['BLOCKS']['DESCRIPTION']['TEXT'] = $arResult['DETAIL_TEXT'];
} else if (!empty($arResult['PREVIEW_TEXT'])) {
    $arResult['BLOCKS']['DESCRIPTION']['TEXT'] = $arResult['PREVIEW_TEXT'];
}

if (!empty($arResult['BLOCKS']['DESCRIPTION']['TEXT']) && $arParams['DESCRIPTION_SHOW'] === 'Y')
    $arResult['BLOCKS']['DESCRIPTION']['ACTIVE'] = true;

/** Conditions */

$sPrefix = 'CONDITIONS_';
$arResult['BLOCKS']['CONDITIONS']['TEMPLATE'] = ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE');

if (!empty($arResult['BLOCKS']['CONDITIONS']['TEMPLATE'])) {
    $arResult['BLOCKS']['CONDITIONS']['TEMPLATE'] = 'template.'.$arResult['BLOCKS']['CONDITIONS']['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (StringHelper::startsWith($sKey, $sPrefix)) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arResult['BLOCKS']['CONDITIONS']['PARAMETERS'][$sKey] = $mValue;
        }
    }
}

$arResult['BLOCKS']['CONDITIONS']['PARAMETERS'] = ArrayHelper::merge($arResult['BLOCKS']['CONDITIONS']['PARAMETERS'], [
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME']
]);

$arResult['BLOCKS']['CONDITIONS']['HEADER'] = [
    'VALUE' => $arParams['CONDITIONS_HEADER'],
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['CONDITIONS_HEADER_POSITION'])
];
$arResult['BLOCKS']['CONDITIONS']['COLUMNS'] = $arParams['CONDITIONS_COLUMNS'];
$arResult['BLOCKS']['CONDITIONS']['IBLOCK'] = [
    'TYPE' => $arParams['CONDITIONS_IBLOCK_TYPE'],
    'ID' => $arParams['CONDITIONS_IBLOCK_ID'],
    'ELEMENTS' => ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['CONDITIONS_PROPERTY_ELEMENTS'], 'VALUE']),
];

if (!empty($arResult['BLOCKS']['CONDITIONS']['IBLOCK']['ELEMENTS']) && $arParams['CONDITIONS_SHOW'] === 'Y') {
    $arResult['BLOCKS']['CONDITIONS']['ACTIVE'] = true;
    $arResult['BLOCKS']['CONDITIONS']['PARAMETERS']['FILTER'] = [
        'ID' => $arResult['BLOCKS']['CONDITIONS']['IBLOCK']['ELEMENTS']
    ];
}

/** Form */

$sPrefix = 'FORM_';

$arResult['BLOCKS']['FORM']['TEMPLATE'] = ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE');

if (!empty($arResult['BLOCKS']['FORM']['TEMPLATE'])) {
    $arResult['BLOCKS']['FORM']['TEMPLATE'] = 'form.'.$arResult['BLOCKS']['FORM']['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (StringHelper::startsWith($sKey, $sPrefix)) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arResult['BLOCKS']['FORM']['PARAMETERS'][$sKey] = $mValue;
        }
    }
}

$arResult['BLOCKS']['FORM']['PARAMETERS'] = ArrayHelper::merge($arResult['BLOCKS']['FORM']['PARAMETERS'], [
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME']
]);

if ((!empty($arParams['FORM_FORM_ID']) || !empty($arParams['FORM_FORM_TEMPLATE'])) && $arParams['FORM_SHOW'] === 'Y')
    $arResult['BLOCKS']['FORM']['ACTIVE'] = true;

/** Products */

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

$arParams['PRODUCTS_LIST_URL'] = StringHelper::replaceMacros($arParams['PRODUCTS_LIST_URL'], $arMacros);
$arParams['PRODUCTS_SECTION_URL'] = StringHelper::replaceMacros($arParams['PRODUCTS_SECTION_URL'], $arMacros);
$arParams['PRODUCTS_DETAIL_URL'] = StringHelper::replaceMacros($arParams['PRODUCTS_DETAIL_URL'], $arMacros);

$arResult['BLOCKS']['PRODUCTS']['HEADER'] = [
    'VALUE' => $arParams['PRODUCTS_HEADER'],
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['PRODUCTS_HEADER_POSITION'])
];

$arResult['BLOCKS']['PRODUCTS']['IBLOCK'] = [
    'TYPE' => $arParams['PRODUCTS_IBLOCK_TYPE'],
    'ID' => $arParams['PRODUCTS_IBLOCK_ID'],
    'ELEMENTS' => ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['PRODUCTS_PROPERTY_ELEMENTS'], 'VALUE'])
];

$arResult['BLOCKS']['PRODUCTS']['PRODUCTS_USE_LIST_URL'] = $arParams['PRODUCTS_USE_LIST_URL'] === 'Y' ? true : false;

if ($arResult['BLOCKS']['PRODUCTS']['PRODUCTS_USE_LIST_URL'] && !empty($arParams['PRODUCTS_LIST_URL'])) {
    $arResult['BLOCKS']['PRODUCTS']['PRODUCTS_LIST_URL'] = $arParams['PRODUCTS_LIST_URL'];
    $arResult['BLOCKS']['PRODUCTS']['PRODUCTS_LIST_URL_POSITION'] = $arParams['PRODUCTS_LIST_URL_POSITION'];
}

$arResult['BLOCKS']['PRODUCTS']['PRODUCTS_LIST_URL'] = $arParams['PRODUCTS_LIST_URL'];

$GLOBALS['arProductsFilter'] = [
    'ID' => $arResult['BLOCKS']['PRODUCTS']['IBLOCK']['ELEMENTS']
];

$sPrefix = 'PRODUCTS_';
$arResult['BLOCKS']['PRODUCTS']['TEMPLATE'] = ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE');

if (!empty($arResult['BLOCKS']['PRODUCTS']['TEMPLATE'])) {
    $arResult['BLOCKS']['PRODUCTS']['TEMPLATE'] = 'catalog.tile.'.$arResult['BLOCKS']['PRODUCTS']['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (StringHelper::startsWith($sKey, $sPrefix)) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arResult['BLOCKS']['PRODUCTS']['PARAMETERS'][$sKey] = $mValue;
        }
    }
}

$arResult['BLOCKS']['PRODUCTS']['PARAMETERS'] = ArrayHelper::merge($arResult['BLOCKS']['PRODUCTS']['PARAMETERS'], [
    'USE_FILTER' => 'Y',
    'FILTER_NAME' => 'arProductsFilter',
    'SEF_MODE' => $arParams['SEF_MODE'],
    'AJAX_MODE' => $arParams['AJAX_MODE'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'QUICK_VIEW_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'QUICK_VIEW_TIMER_SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'QUICK_VIEW_TIMER_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
    'OFFER_TREE_PROPS' => $arResult['BLOCKS']['PRODUCTS']['PARAMETERS']['OFFERS_PROPERTY_CODE'],
    'PRODUCT_DISPLAY_MODE' => 'Y',
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'BY_LINK' => 'Y'
]);

if (!empty($arResult['BLOCKS']['PRODUCTS']['IBLOCK']['ELEMENTS']) && $arParams['PRODUCTS_SHOW'] === 'Y')
    $arResult['BLOCKS']['PRODUCTS']['ACTIVE'] = true;

/** Services */

$arResult['BLOCKS']['SERVICES']['HEADER'] = [
    'VALUE' => $arParams['SERVICES_HEADER'],
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['SERVICES_HEADER_POSITION'])
];
$arResult['BLOCKS']['SERVICES']['IBLOCK'] = [
    'TYPE' => $arParams['SERVICES_IBLOCK_TYPE'],
    'ID' => $arParams['SERVICES_IBLOCK_ID'],
    'ELEMENTS' => ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['SERVICES_PROPERTY_ELEMENTS'], 'VALUE']),
];

$sPrefix = 'SERVICES_';
$arResult['BLOCKS']['SERVICES']['TEMPLATE'] = ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE');

if (!empty($arResult['BLOCKS']['SERVICES']['TEMPLATE'])) {
    $arResult['BLOCKS']['SERVICES']['TEMPLATE'] = 'services.tile.'.$arResult['BLOCKS']['SERVICES']['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (StringHelper::startsWith($sKey, $sPrefix)) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arResult['BLOCKS']['SERVICES']['PARAMETERS'][$sKey] = $mValue;
        }
    }
}

$GLOBALS['arServicesFilter'] = [
    'ID' => $arResult['BLOCKS']['SERVICES']['IBLOCK']['ELEMENTS']
];

$arResult['BLOCKS']['SERVICES']['PARAMETERS'] = ArrayHelper::merge($arResult['BLOCKS']['SERVICES']['PARAMETERS'], [
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'USE_FILTER' => 'Y',
    'FILTER_NAME' => 'arServicesFilter',
    'SEF_MODE' => $arParams['SEF_MODE'],
    'AJAX_MODE' => $arParams['AJAX_MODE'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'BY_LINK' => 'Y'
]);

if (!empty($arResult['BLOCKS']['SERVICES']['IBLOCK']['ELEMENTS']) && $arParams['SERVICES_SHOW'] === 'Y')
    $arResult['BLOCKS']['SERVICES']['ACTIVE'] = true;

$arResult['VISUAL'] = $arVisual;

unset($sPrefix, $arVisual);

$this->__component->SetResultCacheKeys(['PREVIEW_PICTURE', 'DETAIL_PICTURE']);