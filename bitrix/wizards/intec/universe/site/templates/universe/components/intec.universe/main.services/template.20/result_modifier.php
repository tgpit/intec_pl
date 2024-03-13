<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3,
    'DESCRIPTION_USE' => 'N',
    'LINK_USE' => 'N',
    'HEADER_POSITION' => 'left',
    'HEADER_BUTTON_POSITION' => 'center',
    'HEADER_BUTTON_SHOW' => 'N',
    'HEADER_BUTTON_TEXT' => null,

    'SVG_FILE_USE' => 'N',
    'PROPERTY_SVG_FILE' => null,
    'SVG_FILE_COLOR' => 'original',
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'COLUMNS' => ArrayHelper::fromRange([4, 3, 2], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'NAME' => [
        'POSITION' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['NAME_POSITION'])
    ],
    'BORDERS' => [
        'USE' => $arParams['BORDERS_USE'] === 'Y'
    ],
    'SVG' => [
        'USE' => $arParams['SVG_FILE_USE'] === 'Y',
        'COLOR' => ArrayHelper::fromRange(['original', 'theme'], $arParams['SVG_FILE_COLOR'])
    ],
    'HEADER' => [
        'POSITION' => ArrayHelper::fromRange(['top', 'left'], $arParams['HEADER_POSITION'])
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');


if (empty($arParams['PROPERTY_SVG_FILE']))
    $arVisual['SVG']['USE'] = false;

$arFiles = Collection::from([]);

foreach ($arResult['ITEMS'] as &$arItem) {
    if ($arVisual['SVG']['USE']) {
        $mValue = ArrayHelper::getValue($arItem['PROPERTIES'][$arParams['PROPERTY_SVG_FILE']], 'VALUE');

        if (!empty($mValue))
            if (!$arFiles->has($mValue))
                $arFiles->add($mValue);
    }
}

unset($arItem, $mValue);

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

foreach($arResult['ITEMS'] as &$arItem) {
    if ($arVisual['SVG']['USE']) {
        $mValue = ArrayHelper::getValue($arItem['PROPERTIES'][$arParams['PROPERTY_SVG_FILE']], 'VALUE');

        if (!empty($mValue))
            $mValue = $arFiles->get($mValue);

        if (!empty($mValue))
            $arItem['PREVIEW_PICTURE'] = $mValue;
    }

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        if (empty($arItem['PREVIEW_PICTURE']['TITLE']))
            $arItem['PREVIEW_PICTURE']['TITLE'] = $arItem['NAME'];

        if (empty($arItem['PREVIEW_PICTURE']['ALT']))
            $arItem['PREVIEW_PICTURE']['ALT'] = $arItem['NAME'];
    }
}

unset($arItem);

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);

$arResult['BLOCKS']['BUTTON'] = [
    'SHOW' => $arParams['HEADER_BUTTON_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['HEADER_BUTTON_POSITION']),
    'TEXT' => $arParams['HEADER_BUTTON_TEXT'],
    'LINK' => null
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arResult['BLOCKS']['BUTTON']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

if (empty($arResult['BLOCKS']['BUTTON']['TEXT']) || empty($arResult['BLOCKS']['BUTTON']['LINK']))
    $arResult['BLOCKS']['BUTTON']['SHOW'] = false;

unset($arMacros);