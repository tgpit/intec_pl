<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\template\Properties;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_FILE_MP4' => null,
    'PROPERTY_FILE_WEBM' => null,
    'PROPERTY_FILE_OGV' => null,
    'POSITION' => 'left',
    'BUTTON_USE' => 'N',
    'BUTTON_TEXT' => null,
    'MODE' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'SHOW' => false,
    'POSITION' => ArrayHelper::fromRange(['left', 'right'], $arParams['POSITION']),
    'BUTTON' => [
        'USE' => $arParams['BUTTON_USE'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['link', 'form', 'product'], $arParams['MODE']),
        'TEXT' => !empty($arParams['BUTTON_TEXT']) ? $arParams['BUTTON_TEXT'] : Loc::getMessage('C_MAIN_VIDEO_FIXED_1_TEMPLATE_BUTTON_TEXT_DEFAULT'),
        'LINK' => $arParams['BUTTON_LINK'],
        'DELAY' => ArrayHelper::fromRange([0, 1, 2, 3, 4, 5], $arParams['BUTTON_DELAY_SHOW'])
    ]
];

$arResult['ITEM']['DATA'] = [
    'VIDEO' => [
        'FILES' => []
    ],
    'FORM' => [
        'ID' => $arParams['FORM_ID'],
        'TEMPLATE' => $arParams['FORM_TEMPLATE'],
        'TITLE' => !empty($arParams['FORM_TITLE']) ? $arParams['FORM_TITLE'] : Loc::getMessage('C_MAIN_VIDEO_FIXED_1_TEMPLATE_FORM_TITLE_DEFAULT'),
        'CONSENT' => StringHelper::replaceMacros($arParams['FORM_CONSENT'], [
            'SITE_DIR' => SITE_DIR
        ])
    ],
    'QUICK_VIEW' => [
        'SHOW' => $arVisual['BUTTON']['MODE'] === 'product' && !empty($arParams['QUICK_VIEW_TEMPLATE']),
        'TEMPLATE' => 'quick.view.'.$arParams['QUICK_VIEW_TEMPLATE'],
        'PARAMETERS' => []
    ]
];

if (($arVisual['BUTTON']['MODE'] === 'link' && empty($arVisual['BUTTON']['LINK'])) ||
    ($arVisual['BUTTON']['MODE'] === 'form' && empty($arParams['FORM_ID']) && $arParams['FORM_TEMPLATE']) ||
    ($arVisual['BUTTON']['MODE'] === 'product' && empty($arParams['QUICK_VIEW_TEMPLATE']) && (empty($arParams['QUICK_VIEW_ELEMENT_ID']) || empty($arParams['QUICK_VIEW_ELEMENT_CODE'])))
    )
    $arVisual['BUTTON']['USE'] = false;

if ($arVisual['BUTTON']['MODE'] === 'product') {
    foreach ($arParams as $sKey => $sValue) {
        if (!StringHelper::startsWith($sKey, 'QUICK_VIEW_'))
            continue;

        $sKey = StringHelper::cut($sKey, StringHelper::length('QUICK_VIEW_'));
        $arResult['ITEM']['DATA']['QUICK_VIEW']['PARAMETERS'][$sKey] = $sValue;
    }
}

if (!empty($arParams['PROPERTY_FILE_MP4'])) {
    $arProperty = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_FILE_MP4'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (Type::isArray($arProperty))
            $arProperty = ArrayHelper::getFirstValue($arProperty);

        if (!empty($arProperty))
            $arResult['ITEM']['DATA']['VIDEO']['FILES']['MP4'] = $arProperty;
    }
}

if (!empty($arParams['PROPERTY_FILE_WEBM'])) {
    $arProperty = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_FILE_WEBM'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (Type::isArray($arProperty))
            $arProperty = ArrayHelper::getFirstValue($arProperty);

        if (!empty($arProperty))
            $arResult['ITEM']['DATA']['VIDEO']['FILES']['WEBM'] = $arProperty;
    }
}

if (!empty($arParams['PROPERTY_FILE_OGV'])) {
    $arProperty = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_FILE_OGV'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (Type::isArray($arProperty))
            $arProperty = ArrayHelper::getFirstValue($arProperty);

        if (!empty($arProperty))
            $arResult['ITEM']['DATA']['VIDEO']['FILES']['OGV'] = $arProperty;
    }
}

$arFiles = Collection::from([]);

foreach ($arResult['ITEM']['DATA']['VIDEO']['FILES'] as $sFile)
    if (!empty($sFile) && !$arFiles->has($sFile))
        $arFiles->add($sFile);

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = Arrays::from([]);
}

if (!$arFiles->isEmpty()) {
    $arItemFiles = $arResult['ITEM']['DATA']['VIDEO']['FILES'];

    foreach ($arItemFiles as $sType => $sItemFile)
        if ($arFiles->exists($sItemFile)) {
            $arResult['ITEM']['DATA']['VIDEO']['FILES'][$sType] = $arFiles->get($sItemFile);
        } else {
            unset($arResult['ITEM']['DATA']['VIDEO']['FILES'][$sType]);
        }

    unset($sType, $sItemFile, $arItemFiles);
}

if (!empty($arResult['ITEM']['DATA']['VIDEO']['FILES']))
    $arVisual['SHOW'] = true;

if (empty($arVisual['BUTTON']['MODE']))
    $arVisual['BUTTON']['USE'] = false;

$arResult['VISUAL'] = $arVisual;

unset($arVisual, $arFiles);