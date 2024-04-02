<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'NAME_SHOW' => 'N',
    'NAME_ALIGN' => 'left',
    'TITLE_POSITION' => 'top',
    'PICTURE_SHOW' => 'N',
    'PICTURE_ALIGN' => 'left',
    'PICTURE_POSITION' => 'left',
    'PREVIEW_SHOW' => 'N',
    'PREVIEW_ALIGN' => 'left',
    'BACKGROUND_SHOW' => 'N',
    'BACKGROUND_COLOR' => null,
    'THEME' => 'light',
    'SVG_FILE_USE' => 'N',
    'PROPERTY_SVG_FILE' => null,
    'LINK_USE' => 'N',
    'LINK_PROPERTY_USE' => 'N',
    'LINK_PROPERTY' => null,
    'COLUMNS' => 4,
    'MOBILE_COLUMNS' => 2
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'NAME' => [
        'SHOW' => $arParams['NAME_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['NAME_ALIGN']),
    ],
    'TITLE' => [
        'POSITION' => ArrayHelper::fromRange(['left', 'top'], $arParams['TITLE_POSITION'])
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['PICTURE_ALIGN']),
        'POSITION' => ArrayHelper::fromRange(['left', 'top'], $arParams['PICTURE_POSITION'])
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['PREVIEW_ALIGN'])
    ],
    'THEME' => ArrayHelper::fromRange(['light', 'dark'], $arParams['THEME']),
    'COLUMNS' => ArrayHelper::fromRange([3, 4, 5], $arParams['COLUMNS']),
    'MOBILE' => [
        'COLUMNS' => ArrayHelper::fromRange([1, 2], $arParams['MOBILE_COLUMNS']),
    ],
    'BACKGROUND' => [
        'SHOW' => $arParams['BACKGROUND_SHOW'] === 'Y',
        'COLOR' => $arParams['BACKGROUND_COLOR']
    ]
];

if ($arVisual['TITLE']['POSITION'] == 'left')
    $arVisual['COLUMNS'] = 3;

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'LINK' => $arItem['DETAIL_PAGE_URL'],
        'IMAGE' => null
    ];

    if ($arParams['LINK_PROPERTY_USE'] === 'Y' && !empty($arParams['LINK_PROPERTY'])) {
        $sLink = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['LINK_PROPERTY'],
            'VALUE'
        ]);

        if (!empty($sLink))
            $arItem['DATA']['LINK'] = $sLink;
    }

    if ($arParams['LINK_USE'] !== 'Y')
        $arItem['DATA']['LINK'] = null;

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        $arItem['DATA']['IMAGE'] = $arItem['PREVIEW_PICTURE'];
    } else if (!empty($arItem['DETAIL_PICTURE'])) {
        $arItem['DATA']['IMAGE'] = $arItem['DETAIL_PICTURE'];
    }
}

unset($arItem, $sLink);

$arFiles = Collection::from([]);

foreach ($arResult['ITEMS'] as &$arItem) {
    if ($arParams['SVG_FILE_USE'] === 'Y' && !empty($arParams['PROPERTY_SVG_FILE'])) {
        $arPropertySvg = null;

        $arPropertySvg = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_SVG_FILE']
        ]);

        if (!empty($arPropertySvg['VALUE']))
            if (!$arFiles->has($arPropertySvg['VALUE']))
                $arFiles->add($arPropertySvg['VALUE']);
    }
}

unset($arItem);

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

foreach ($arResult['ITEMS'] as $sKey => &$arItem) {
    if ($arParams['SVG_FILE_USE'] === 'Y' && !empty($arParams['PROPERTY_SVG_FILE'])) {

        $arPropertySvg = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_SVG_FILE']
        ]);

        if (!empty($arPropertySvg['VALUE']))
            $arItem['DATA']['IMAGE'] = $arFiles->get($arPropertySvg['VALUE']);
    }
}

unset($arItem, $arPropertySvg, $arFiles);