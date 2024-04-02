<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'BLOCKS' => [],
    'BLOCKS_ORDER' => '',
    'PROPERTY_PRICE' => null,
    'PROPERTY_PRICE_OLD' => null,
    'PRICE_OLD_SHOW' => 'N',
    'PROPERTY_CURRENCY' => null,
    'PRICE_FORMAT' => '#VALUE# #CURRENCY#',

    'BLOCKS_BANNER_WIDE' => 'Y',
    'BLOCKS_BANNER_TITLE_H1' => 'Y',
    'BLOCKS_BANNER_SPLIT' => 'N',
    'BLOCKS_BANNER_ADDITIONAL_SHOW' => 'N',
    'BLOCKS_BANNER_ADDITIONAL' => null,
    'BLOCKS_BANNER_OVERHEAD_SHOW' => 'N',
    'BLOCKS_BANNER_OVERHEAD' => null,
    'BLOCKS_BANNER_TEXT_SHOW' => 'Y',
    'BLOCKS_BANNER_TEXT_HEADER_SHOW' => 'N',
    'BLOCKS_BANNER_TEXT_POSITION' => 'inside',
    'BLOCKS_BANNER_ORDER_BUTTON_SHOW' => null,
    'BLOCKS_BANNER_ORDER_BUTTON_TEXT' => null,
    'BLOCKS_BANNER_ORDER_FORM_ID' => null,
    'BLOCKS_BANNER_ORDER_FORM_TEMPLATE' => null,
    'BLOCKS_BANNER_ORDER_FORM_SERVICE' => null,
    'BLOCKS_BANNER_ORDER_FORM_CONSENT' => null,
    'BLOCKS_BANNER_COLOR_OVERHEAD' => null,
    'BLOCKS_BANNER_COLOR_HEADER' => null,
    'BLOCKS_BANNER_COLOR_DESCRIPTION' => null,
    'BLOCKS_BANNER_COLOR_ADDITIONAL' => null,
    'BLOCKS_BANNER_COLOR_PRICE' => null,

    'BLOCKS_ICONS_1_HEADER' => null,
    'BLOCKS_ICONS_1_PROPERTY_HEADER' => null,
    'BLOCKS_ICONS_1_HEADER_POSITION' => null,
    'BLOCKS_ICONS_1_IBLOCK_TYPE' => null,
    'BLOCKS_ICONS_1_IBLOCK_ID' => null,
    'BLOCKS_ICONS_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_DESCRIPTION_1_HEADER' => null,
    'BLOCKS_DESCRIPTION_1_PROPERTY_HEADER' => null,
    'BLOCKS_DESCRIPTION_1_HEADER_POSITION' => null,

    'BLOCKS_STAGES_1_HEADER' => null,
    'BLOCKS_STAGES_1_IBLOCK_TYPE' => null,
    'BLOCKS_STAGES_1_IBLOCK_ID' => null,
    'BLOCKS_STAGES_1_PROPERTY_HEADER' => null,
    'BLOCKS_STAGES_1_HEADER_POSITION' => null,
    'BLOCKS_STAGES_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_PROPERTIES_1_HEADER' => null,
    'BLOCKS_PROPERTIES_1_PROPERTY_HEADER' => null,
    'BLOCKS_PROPERTIES_1_HEADER_POSITION' => null,
    'BLOCKS_PROPERTIES_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_RATES_1_HEADER' => null,
    'BLOCKS_RATES_1_IBLOCK_TYPE' => null,
    'BLOCKS_RATES_1_IBLOCK_ID' => null,
    'BLOCKS_RATES_1_PROPERTIES' => null,
    'BLOCKS_RATES_1_PROPERTY_HEADER' => null,
    'BLOCKS_RATES_1_HEADER_POSITION' => null,
    'BLOCKS_RATES_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_SERVICES_1_HEADER' => null,
    'BLOCKS_SERVICES_1_PROPERTY_HEADER' => null,
    'BLOCKS_SERVICES_1_HEADER_POSITION' => null,
    'BLOCKS_SERVICES_1_IBLOCK_TYPE' => null,
    'BLOCKS_SERVICES_1_IBLOCK_ID' => null,
    'BLOCKS_SERVICES_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_STAGES_2_HEADER' => null,
    'BLOCKS_STAGES_2_IBLOCK_TYPE' => null,
    'BLOCKS_STAGES_2_IBLOCK_ID' => null,
    'BLOCKS_STAGES_2_PROPERTY_HEADER' => null,
    'BLOCKS_STAGES_2_HEADER_POSITION' => null,
    'BLOCKS_STAGES_2_PROPERTY_ELEMENTS' => null,

    'BLOCKS_DOCUMENTS_1_HEADER' => null,
    'BLOCKS_DOCUMENTS_1_PROPERTY_HEADER' => null,
    'BLOCKS_DOCUMENTS_1_HEADER_POSITION' => null,
    'BLOCKS_DOCUMENTS_1_PROPERTY_ELEMENTS' => null,

    /*'BLOCKS_FORM' => null,*/

    'BLOCKS_CERTIFICATES_1_HEADER' => null,
    'BLOCKS_CERTIFICATES_1_PROPERTY_HEADER' => null,
    'BLOCKS_CERTIFICATES_1_HEADER_POSITION' => null,
    'BLOCKS_CERTIFICATES_1_IBLOCK_TYPE' => null,
    'BLOCKS_CERTIFICATES_1_IBLOCK_ID' => null,
    'BLOCKS_CERTIFICATES_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_STAFF_1_HEADER' => null,
    'BLOCKS_STAFF_1_DESCRIPTION' => null,
    'BLOCKS_STAFF_1_IBLOCK_TYPE' => null,
    'BLOCKS_STAFF_1_IBLOCK_ID' => null,
    'BLOCKS_STAFF_1_PROPERTY_HEADER' => null,
    'BLOCKS_STAFF_1_HEADER_POSITION' => null,
    'BLOCKS_STAFF_1_PROPERTY_DESCRIPTION' => null,
    'BLOCKS_STAFF_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_FAQ_1_HEADER' => null,
    'BLOCKS_FAQ_1_IBLOCK_TYPE' => null,
    'BLOCKS_FAQ_1_IBLOCK_ID' => null,
    'BLOCKS_FAQ_1_PROPERTY_HEADER' => null,
    'BLOCKS_FAQ_1_HEADER_POSITION' => null,
    'BLOCKS_FAQ_1_PROPERTY_ELEMENTS' => null,

    /*advantages.1*/

    'BLOCKS_VIDEOS_1_HEADER' => null,
    'BLOCKS_VIDEOS_1_PROPERTY_HEADER' => null,
    'BLOCKS_VIDEOS_1_HEADER_POSITION' => null,
    'BLOCKS_VIDEOS_1_IBLOCK_TYPE' => null,
    'BLOCKS_VIDEOS_1_IBLOCK_ID' => null,
    'BLOCKS_VIDEOS_1_PROPERTY_ELEMENTS' => null,
    'BLOCKS_VIDEOS_1_PROPERTY_LINK' => null,

    'BLOCKS_GALLERY_1_HEADER' => null,
    'BLOCKS_GALLERY_1_PROPERTY_HEADER' => null,
    'BLOCKS_GALLERY_1_HEADER_POSITION' => null,
    'BLOCKS_GALLERY_1_IBLOCK_TYPE' => null,
    'BLOCKS_GALLERY_1_IBLOCK_ID' => null,
    'BLOCKS_GALLERY_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_PROJECTS_1_HEADER' => null,
    'BLOCKS_PROJECTS_1_PROPERTY_HEADER' => null,
    'BLOCKS_PROJECTS_1_HEADER_POSITION' => null,
    'BLOCKS_PROJECTS_1_IBLOCK_TYPE' => null,
    'BLOCKS_PROJECTS_1_IBLOCK_ID' => null,
    'BLOCKS_PROJECTS_1_PROPERTY_ELEMENTS' => null,

    'BLOCKS_REVIEWS_1_HEADER' => null,
    'BLOCKS_REVIEWS_1_PROPERTY_HEADER' => null,
    'BLOCKS_REVIEWS_1_HEADER_POSITION' => null,
    'BLOCKS_REVIEWS_1_IBLOCK_TYPE' => null,
    'BLOCKS_REVIEWS_1_IBLOCK_ID' => null,
    'BLOCKS_REVIEWS_1_PROPERTY_ELEMENTS' => null,
    'BLOCKS_REVIEWS_1_PROPERTY_POSITION' => null,

    'BLOCKS_PRODUCTS_1_HEADER' => null,
    'BLOCKS_PRODUCTS_1_PROPERTY_HEADER' => null,
    'BLOCKS_PRODUCTS_1_HEADER_POSITION' => null,
    'BLOCKS_PRODUCTS_1_IBLOCK_TYPE' => null,
    'BLOCKS_PRODUCTS_1_IBLOCK_ID' => null,
    'BLOCKS_PRODUCTS_1_PROPERTY_ELEMENTS' => null,
    'BLOCKS_PRODUCTS_1_PRICE_CODE' => null,

    'BLOCKS_SERVICES_2_HEADER' => null,
    'BLOCKS_SERVICES_2_PROPERTY_HEADER' => null,
    'BLOCKS_SERVICES_2_HEADER_POSITION' => null,
    'BLOCKS_SERVICES_2_IBLOCK_TYPE' => null,
    'BLOCKS_SERVICES_2_IBLOCK_ID' => null,
    'BLOCKS_SERVICES_2_PROPERTY_ELEMENTS' => null,

    'BLOCKS_NEWS_1_HEADER' => null,
    'BLOCKS_NEWS_1_DESCRIPTION' => null,
    'BLOCKS_NEWS_1_IBLOCK_TYPE' => null,
    'BLOCKS_NEWS_1_IBLOCK_ID' => null,
    'BLOCKS_NEWS_1_PROPERTY_HEADER' => null,
    'BLOCKS_NEWS_1_HEADER_POSITION' => null,
    'BLOCKS_NEWS_1_PROPERTY_DESCRIPTION' => null,
    'BLOCKS_NEWS_1_PROPERTY_ELEMENTS' => null,
    'BLOCKS_NEWS_1_VIEW' => 'template.9',

    'BLOCKS_BRANDS_1_HEADER' => null,
    'BLOCKS_BRANDS_1_PROPERTY_HEADER' => null,
    'BLOCKS_BRANDS_1_HEADER_POSITION' => null,
    'BLOCKS_BRANDS_1_IBLOCK_TYPE' => null,
    'BLOCKS_BRANDS_1_IBLOCK_ID' => null,
    'BLOCKS_BRANDS_1_PROPERTY_ELEMENTS' => null,
    'BLOCKS_BRANDS_1_PROPERTY_POSITION' => null,

], $arParams);

$arResult['LAZYLOAD'] = [
    'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
    'STUB' => null
];

if (defined('EDITOR'))
    $arResult['LAZYLOAD']['USE'] = false;

if ($arResult['LAZYLOAD']['USE'])
    $arResult['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

include(__DIR__.'/modifiers/blocks.php');
include(__DIR__.'/modifiers/properties.php');

$fGetPropertyValue = function ($sName, $bRaw = false) use (&$arResult) {
    $mValue = null;

    if (empty($arResult['PROPERTIES'][$sName]))
        return $mValue;

    $arProperty = $arResult['PROPERTIES'][$sName];

    if (!empty($arProperty['USER_TYPE']) && !$bRaw) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue($arResult, $arProperty, 'services_out');

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            $mValue = $arProperty['DISPLAY_VALUE'];
        } else {
            $mValue = $arProperty['VALUE'];
        }
    } else {
        $mValue = $bRaw ? $arProperty['~VALUE'] : $arProperty['VALUE'];
    }

    return $mValue;
};

$arResult['PRICE'] = [
    'BASE' => [
        'VALUE' => $fGetPropertyValue($arParams['PROPERTY_PRICE']),
    ],
    'OLD' => [
        'VALUE' => $fGetPropertyValue($arParams['PROPERTY_PRICE_OLD']),
    ],
    'CURRENCY' => $fGetPropertyValue($arParams['PROPERTY_CURRENCY']),
    'FORMAT' => $fGetPropertyValue($arParams['PROPERTY_PRICE_FORMAT'])
];

if (!Type::isNumeric($arResult['PRICE']['BASE']['VALUE']))
    $arResult['PRICE']['BASE']['VALUE'] = null;

if (empty($arResult['PRICE']['CURRENCY']))
    $arResult['PRICE']['CURRENCY'] = $arParams['CURRENCY'];

if (empty($arResult['PRICE']['FORMAT']))
    $arResult['PRICE']['FORMAT'] = $arParams['PRICE_FORMAT'];

if (!empty($arResult['PRICE']['BASE']['VALUE'])) {
    $arResult['PRICE']['BASE']['VALUE'] = number_format($arResult['PRICE']['BASE']['VALUE'], 0, '', ' ');
    $arResult['PRICE']['BASE']['VALUE'] = StringHelper::replaceMacros($arResult['PRICE']['FORMAT'], [
        'VALUE' => $arResult['PRICE']['BASE']['VALUE'],
        'CURRENCY' => $arResult['PRICE']['CURRENCY']
    ]);
}
if (!empty($arResult['PRICE']['OLD']['VALUE'])) {
    $arResult['PRICE']['OLD']['VALUE'] = number_format($arResult['PRICE']['OLD']['VALUE'], 0, '', ' ');
    $arResult['PRICE']['OLD']['VALUE'] = StringHelper::replaceMacros($arResult['PRICE']['FORMAT'], [
        'VALUE' => $arResult['PRICE']['OLD']['VALUE'],
        'CURRENCY' => $arResult['PRICE']['CURRENCY']
    ]);
}

/** Блок banner */
$arBlock = &$arResult['BLOCKS']['banner'];

if ($arBlock['ACTIVE']) {
    $arBlock['WIDE'] = $arParams['BLOCKS_BANNER_WIDE'] === 'Y';
    $arBlock['TITLE_TAG'] = $arParams['BLOCKS_BANNER_TITLE_H1'] === 'Y' ? 'h1' : 'div';
    $arBlock['HEIGHT'] = $arParams['BLOCKS_BANNER_HEIGHT'];
    $arBlock['SPLIT'] = $arParams['BLOCKS_BANNER_SPLIT'] === 'Y';
    $arBlock['NAME'] = $arResult['NAME'];
    $arBlock['OVERHEAD'] = [
        'SHOW' => $arParams['BLOCKS_BANNER_OVERHEAD_SHOW'] == 'Y',
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_BANNER_OVERHEAD'])
    ];

    $arBlock['TEXT'] = [
        'SHOW' => $arParams['BLOCKS_BANNER_TEXT_SHOW'] === 'Y',
        'VALUE' => $arResult['PREVIEW_TEXT']
    ];

    if (empty($arBlock['OVERHEAD']['VALUE']))
        $arBlock['OVERHEAD']['SHOW'] = false;

    if (empty($arBlock['TEXT']['VALUE']))
        $arBlock['TEXT']['SHOW'] = false;

    $arBlock['PRICE'] = [
        'BASE' => [
            'SHOW' => !empty($arResult['PRICE']['BASE']['VALUE']),
            'VALUE' => $arResult['PRICE']['BASE']['VALUE']
        ],
        'OLD' => [
            'SHOW' => !empty($arResult['PRICE']['OLD']['VALUE']),
            'VALUE' => $arResult['PRICE']['OLD']['VALUE']
        ]
    ];

    $arBlock['BUTTON']['SHOW'] = $fGetPropertyValue($arParams['BLOCKS_BANNER_PROPERTY_ORDER_BUTTON_SHOW']);
    $arBlock['BUTTON']['SHOW'] = !empty($arBlock['BUTTON']['SHOW']);

    if ($arBlock['BUTTON']['SHOW']) {
        $arBlock['BUTTON']['TEXT'] = $arParams['BLOCKS_BANNER_ORDER_BUTTON_TEXT'];

        if (!empty($arBlock['BUTTON']['TEXT']) && !empty($arParams['BLOCKS_BANNER_ORDER_FORM_ID'])) {
            $arBlock['FORM'] = [
                'ID' => $arParams['BLOCKS_BANNER_ORDER_FORM_ID'],
                'TEMPLATE' => $arParams['BLOCKS_BANNER_ORDER_FORM_TEMPLATE'],
                'FIELDS' => [
                    'SERVICE' => $arParams['BLOCKS_BANNER_ORDER_FORM_SERVICE']
                ],
                'CONSENT' => $arParams['BLOCKS_BANNER_ORDER_FORM_CONSENT']
            ];
        } else {
            $arBlock['BUTTON']['SHOW'] = false;
        }
    }

    if (!empty($arResult['DETAIL_PICTURE'])) {
        $arBlock['PICTURE'] = $arResult['DETAIL_PICTURE'];
    } else if (!empty($arResult['PREVIEW_PICTURE'])) {
        $arBlock['PICTURE'] = $arResult['PREVIEW_PICTURE'];
    }

    if (!empty($arBlock['PICTURE'])) {
        $arBlock['PICTURE'] = $arBlock['PICTURE']['SRC'];
    } else {
        $arBlock['PICTURE'] = null;
    }

    $arBlock['ADDITIONAL'] = [
        'SHOW' => $arParams['BLOCKS_BANNER_ADDITIONAL_SHOW'] == 'Y',
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_BANNER_ADDITIONAL'])
    ];
}

/** Блок icons.1 */
$arBlock = &$arResult['BLOCKS']['icons.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_ICONS_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_ICONS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_ICONS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_ICONS_1_PROPERTY_ELEMENTS'], true),
        'SVG_USE' => $arParams['BLOCKS_ICONS_1_SVG_USE'],
        'SVG_PROPERTY' => $arParams['BLOCKS_ICONS_1_SVG_PROPERTY']
    ];

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_ICONS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_ICONS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_ICONS_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок description.1 */
$arBlock = &$arResult['BLOCKS']['description.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['HEADER'] = [
        'SHOW' => !$arResult['BLOCKS']['banner']['TEXT']['HEADER']['SHOW'] || !$arResult['BLOCKS']['banner']['SPLIT'],
        'VALUE' => $fGetPropertyValue($arParams['DESCRIPTION_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_DESCRIPTION_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['DESCRIPTION_HEADER'];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['SHOW'] = false;

    $arBlock['TEXT'] = $arResult['DETAIL_TEXT'];

    if (empty($arBlock['TEXT']))
        $arBlock['ACTIVE'] = false;

    $arBlock['THEME'] = ArrayHelper::fromRange([
        'light',
        'dark',
    ], ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['BLOCKS_DESCRIPTION_1_THEME'], 'VALUE_XML_ID']));

    if (empty($arBlock['THEME']))
        $arBlock['THEME'] = 'light';
}


/** Блок description.items.1 */
$arBlock = &$arResult['BLOCKS']['description.items.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_DESCRIPTION_ITEMS_1_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => [
            'LINK' => $arParams['BLOCKS_GALLERY_1_PROPERTY_LINK']
        ]
    ];

    $arBlock['TEMPLATE'] = ArrayHelper::fromRange([ 'template.37', 'template.38'], $arParams['BLOCKS_DESCRIPTION_ITEMS_1_TEMPLATE']);

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_DESCRIPTION_ITEMS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_DESCRIPTION_ITEMS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_DESCRIPTION_ITEMS_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок stages.1 */
$arBlock = &$arResult['BLOCKS']['stages.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_STAGES_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_STAGES_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_STAGES_1_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_STAGES_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_STAGES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_STAGES_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок properties.1 */
$arBlock = &$arResult['BLOCKS']['properties.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_PROPERTIES_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_PROPERTIES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_PROPERTIES_1_HEADER'];

    if (empty($arResult['DISPLAY_PROPERTIES']))
        $arBlock['ACTIVE'] = false;
}

/** Блок rates.1 */
$arBlock = &$arResult['BLOCKS']['rates.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_RATES_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_RATES_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_RATES_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_RATES_1_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => $arParams['BLOCKS_RATES_1_PROPERTIES']
    ];

    $arBlock['TEMPLATE'] = ArrayHelper::fromRange([
        'template.5',
        'template.6'
    ], $arParams['BLOCKS_RATES_1_TEMPLATE']);

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_RATES_1_HEADER_POSITION']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_RATES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_RATES_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок services.1 */
$arBlock = &$arResult['BLOCKS']['services.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_SERVICES_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['IBLOCK_TYPE'],
        'ID' => $arParams['IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_SERVICES_1_PROPERTY_ELEMENTS'], true),
    ];

    $arBlock['TEMPLATE'] = ArrayHelper::fromRange([
        'template.22',
        'template.23'
    ], $arParams['BLOCKS_SERVICES_1_TEMPLATE']);

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_SERVICES_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_SERVICES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_SERVICES_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок stages.2 */
$arBlock = &$arResult['BLOCKS']['stages.2'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_STAGES_2_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_STAGES_2_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_STAGES_2_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_STAGES_2_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_STAGES_2_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_STAGES_2_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;

    $arBlock['VIEW'] = ArrayHelper::fromRange(['1', '2'], $arParams['BLOCKS_STAGES_2_VIEW']);

    $arBlock['BACKGROUND_USE'] = $arParams['BLOCKS_STAGES_2_BACKGROUND_USE'] === 'Y';
}

/** Блок documents.1 */
$arBlock = &$arResult['BLOCKS']['documents.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_DOCUMENTS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_DOCUMENTS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_DOCUMENTS_1_HEADER'];

    $arProperty = ArrayHelper::getValue($arResult['PROPERTIES'], $arParams['BLOCKS_DOCUMENTS_1_PROPERTY_FILES']);

    if (!empty($arProperty['VALUE']))
        foreach ($arProperty['VALUE'] as $iKey => $arFile) {
            $arBlock['DOCUMENTS'][$iKey] = $arFile;
        }

    if (empty($arBlock['DOCUMENTS']))
        $arBlock['ACTIVE'] = false;

    unset($arProperty);
}

/** Блок form.1 */
$arBlock = &$arResult['BLOCKS']['form.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_FORM_1_';

    $arBlock['PARAMETERS'] = [
        'SETTINGS_USE' => 'N',
        'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N'
    ];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    if (empty($arParams['BLOCKS_FORM_1_FORM_ID'])) {
        $arBlock['ACTIVE'] = false;
    }
}

/** Блок certificates.1**/
$arBlock = &$arResult['BLOCKS']['certificates.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_CERTIFICATES_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_CERTIFICATES_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_CERTIFICATES_1_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_CERTIFICATES_1_HEADER_POSITION']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_CERTIFICATES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_CERTIFICATES_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок staff.1 */
$arBlock = &$arResult['BLOCKS']['staff.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_STAFF_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_STAFF_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_STAFF_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_STAFF_1_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_STAFF_1_HEADER_POSITION']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_STAFF_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_STAFF_1_HEADER'];

    $arBlock['DESCRIPTION'] = $fGetPropertyValue($arParams['BLOCKS_STAFF_1_PROPERTY_DESCRIPTION']);

    if (empty($arBlock['DESCRIPTION']))
        $arBlock['DESCRIPTION'] = $arParams['BLOCKS_STAFF_1_DESCRIPTION'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;

    $arBlock['VIEW'] = ArrayHelper::fromRange(['1', '2', '3'], $arParams['BLOCKS_STAFF_1_VIEW']);

    if ($arBlock['VIEW'] == '1') {
        $arBlock['TEMPLATE'] = 'template.5';
    } elseif ($arBlock['VIEW'] == '2') {
        $arBlock['TEMPLATE'] = 'template.6';
        $arBlock['PARAMETERS']['PROPERTY_POSITION'] = $arParams['BLOCKS_STAFF_1_PROPERTY_POSITION'];
        $arBlock['PARAMETERS']['COLUMNS'] = '3';
        $arBlock['PARAMETERS']['PICTURE_SIZE'] = 'middle';
        $arBlock['PARAMETERS']['PREVIEW_SHOW'] = 'N';
    } else {
        $arBlock['TEMPLATE'] = 'template.6';
        $arBlock['PARAMETERS']['PROPERTY_POSITION'] = $arParams['BLOCKS_STAFF_1_PROPERTY_POSITION'];
        $arBlock['PARAMETERS']['COLUMNS'] = '2';
        $arBlock['PARAMETERS']['PICTURE_SIZE'] = 'big';
        $arBlock['PARAMETERS']['PREVIEW_SHOW'] = 'Y';
    }
}

/** Блок faq.1 */
$arBlock = &$arResult['BLOCKS']['faq.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_FAQ_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_FAQ_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_FAQ_1_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_FAQ_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_FAQ_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_FAQ_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок advantages.1 */
$arBlock = &$arResult['BLOCKS']['advantages.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_ADVANTAGES_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_ADVANTAGES_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_ADVANTAGES_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_ADVANTAGES_1_PROPERTY_ELEMENTS'], true),
    ];

    $arBlock['PRICE_CODE'] = $arParams['BLOCKS_ADVANTAGES_1_PRICE_CODE'];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_ADVANTAGES_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_ADVANTAGES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_ADVANTAGES_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок videos.1 */
$arBlock = &$arResult['BLOCKS']['videos.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_VIDEOS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_VIDEOS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_VIDEOS_1_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => [
            'LINK' => $arParams['BLOCKS_VIDEOS_1_PROPERTY_LINK']
        ]
    ];

    $arBlock['TEMPLATE'] = ArrayHelper::fromRange(['template.3', 'template.4', 'template.5'], $arParams['BLOCKS_VIDEOS_1_TEMPLATE']);

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_VIDEOS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_VIDEOS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_VIDEOS_1_HEADER'];

    $arBlock['DESCRIPTION'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_VIDEOS_1_PROPERTY_DESCRIPTION']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_VIDEOS_1_DESCRIPTION_POSITION'])
    ];

    if (empty($arBlock['DESCRIPTION']['VALUE']))
        $arBlock['DESCRIPTION']['VALUE'] = $arParams['BLOCKS_VIDEOS_1_DESCRIPTION'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок gallery.1 */
$arBlock = &$arResult['BLOCKS']['gallery.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_GALLERY_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_GALLERY_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_GALLERY_1_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => [
            'LINK' => $arParams['BLOCKS_GALLERY_1_PROPERTY_LINK']
        ]
    ];

    $arBlock['TEMPLATE'] = ArrayHelper::fromRange([ 'template.5', 'template.6', 'template.7'], $arParams['BLOCKS_GALLERY_1_TEMPLATE']);

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_GALLERY_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_GALLERY_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_GALLERY_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок projects.1 */
$arBlock = &$arResult['BLOCKS']['projects.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_PROJECTS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_PROJECTS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_PROJECTS_1_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_PROJECTS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_PROJECTS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_PROJECTS_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок reviews.1 */
$arBlock = &$arResult['BLOCKS']['reviews.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_REVIEWS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_REVIEWS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_REVIEWS_1_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => [
            'RATING' => $arParams['BLOCKS_REVIEWS_1_PROPERTY_RATING']
        ]
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_REVIEWS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_REVIEWS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_REVIEWS_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок products.1 */
$arBlock = &$arResult['BLOCKS']['products.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_PRODUCTS_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_PRODUCTS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_PRODUCTS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_PRODUCTS_1_PROPERTY_ELEMENTS'], true),
    ];

    $arBlock['PRICE_CODE'] = $arParams['BLOCKS_PRODUCTS_1_PRICE_CODE'];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_PRODUCTS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_PRODUCTS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_PRODUCTS_1_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок services.2 */
$arBlock = &$arResult['BLOCKS']['services.2'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_SERVICES_2_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['IBLOCK_TYPE'],
        'ID' => $arParams['IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_SERVICES_2_PROPERTY_ELEMENTS'], true),
    ];

    $arBlock['PROPERTIES'] = [
        'PRICE' => [
            'BASE' => [
                'VALUE' => $arParams['PROPERTY_PRICE']
            ],
            'OLD' => [
                'SHOW' => $arParams['BLOCKS_SERVICES_2_PRICE_OLD_SHOW'],
                'VALUE' => $arParams['BLOCKS_SERVICES_2_PRICE_OLD'],
            ],
            'CURRENCY' => $arParams['PROPERTY_CURRENCY'],
            'PRICE_FORMAT' => $arParams['PROPERTY_PRICE_FORMAT']
        ]
    ];

    $arBlock['PRICE'] = [
        'CURRENCY' => $arParams['CURRENCY'],
        'FORMAT' => $arParams['PRICE_FORMAT']
    ];
    $arBlock['PRICE_OLD'] = [
        'CURRENCY' => $arParams['CURRENCY'],
        'FORMAT' => $arParams['PRICE_FORMAT']
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_SERVICES_2_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_SERVICES_2_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_SERVICES_2_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок news.1 */
$arBlock = &$arResult['BLOCKS']['news.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_NEWS_1_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_NEWS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_NEWS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_NEWS_1_PROPERTY_ELEMENTS'], true)
    ];

    $arBlock['TEMPLATE'] = $arParams['BLOCKS_NEWS_1_VIEW'];

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_NEWS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_NEWS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_NEWS_1_HEADER'];

    $arBlock['DESCRIPTION'] = $fGetPropertyValue($arParams['BLOCKS_NEWS_1_PROPERTY_DESCRIPTION']);

    if (empty($arBlock['DESCRIPTION']))
        $arBlock['DESCRIPTION'] = $arParams['BLOCKS_NEWS_1_DESCRIPTION'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;


}

/** Блок brands.1 */
$arBlock = &$arResult['BLOCKS']['brands.1'];

if ($arBlock['ACTIVE']) {
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_BRANDS_1_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_BRANDS_1_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_BRANDS_1_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => [
            'POSITION' => $arParams['BLOCKS_BRANDS_1_PROPERTY_POSITION']
        ]
    ];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_BRANDS_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_BRANDS_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_BRANDS_1_HEADER'];

    $arBlock['DESCRIPTION'] = $fGetPropertyValue($arParams['BLOCKS_BRANDS_1_PROPERTY_DESCRIPTION']);

    if (empty($arBlock['DESCRIPTION']))
        $arBlock['DESCRIPTION'] = $arParams['BLOCKS_BRANDS_1_DESCRIPTION'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок projects.2 */
$arBlock = &$arResult['BLOCKS']['projects.2'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_PROJECTS_2_';
    $arBlock['IBLOCK'] = [
        'TYPE' => $arParams['BLOCKS_PROJECTS_2_IBLOCK_TYPE'],
        'ID' => $arParams['BLOCKS_PROJECTS_2_IBLOCK_ID'],
        'ELEMENTS' => $fGetPropertyValue($arParams['BLOCKS_PROJECTS_2_PROPERTY_ELEMENTS'], true),
        'PROPERTIES' => $arParams['BLOCKS_PROJECTS_2_PROPERTIES']
    ];

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_PROJECTS_2_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_PROJECTS_2_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_PROJECTS_2_HEADER'];

    if (!empty($arBlock['IBLOCK']['ELEMENTS']) && !Type::isArray($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['IBLOCK']['ELEMENTS'] = [$arBlock['IBLOCK']['ELEMENTS']];

    if (empty($arBlock['IBLOCK']['ID']) || empty($arBlock['IBLOCK']['ELEMENTS']))
        $arBlock['ACTIVE'] = false;

    $arBlock['SLIDER'] = [
        'USE' => $arParams['BLOCKS_PROJECTS_2_SLIDER_USE'] === 'Y',
        'NAV' => $arParams['BLOCKS_PROJECTS_2_SLIDER_NAV'] === 'Y'
    ];
}

/** Блок shares.1 */
$arBlock = &$arResult['BLOCKS']['shares.1'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_SHARES_1_';

    $arBlock['ELEMENTS'] = $fGetPropertyValue($arParams['BLOCKS_SHARES_1_PROPERTY_ELEMENTS'], true);

    if (!empty($arParams['BLOCKS_SHARES_1_TEMPLATE']))
        $arBlock['TEMPLATE'] = 'template.' . $arParams['BLOCKS_SHARES_1_TEMPLATE'];
    else
        $arBlock['TEMPLATE'] = '.default';

    $arBlock['PRICE_CODE'] = $arParams['BLOCKS_SHARES_1_PRICE_CODE'];

    $arBlock['HEADER'] = [
        'VALUE' => $fGetPropertyValue($arParams['BLOCKS_SHARES_1_PROPERTY_HEADER']),
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['BLOCKS_SHARES_1_HEADER_POSITION'])
    ];

    if (empty($arBlock['HEADER']['VALUE']))
        $arBlock['HEADER']['VALUE'] = $arParams['BLOCKS_SHARES_1_HEADER'];

    if (!empty($arBlock['ELEMENTS']) && !Type::isArray($arBlock['ELEMENTS']))
        $arBlock['ELEMENTS'] = [$arBlock['ELEMENTS']];

    $arBlock['PARAMETERS'] = [];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    if (empty($arBlock['PARAMETERS']['IBLOCK_ID']) || empty($arBlock['ELEMENTS']))
        $arBlock['ACTIVE'] = false;
}

/** Блок form.2 */
$arBlock = &$arResult['BLOCKS']['form.2'];

if ($arBlock['ACTIVE']) {
    $sPrefix = 'BLOCKS_FORM_2_';

    $arBlock['PARAMETERS'] = [
        'SETTINGS_USE' => 'N',
        'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N'
    ];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arBlock['PARAMETERS'][$sKey] = $mValue;
    }

    if (empty($arParams['BLOCKS_FORM_2_FORM_ID'])) {
        $arBlock['ACTIVE'] = false;
    }
}

unset($arBlock);

$this->__component->SetResultCacheKeys(['PREVIEW_PICTURE', 'DETAIL_PICTURE']);