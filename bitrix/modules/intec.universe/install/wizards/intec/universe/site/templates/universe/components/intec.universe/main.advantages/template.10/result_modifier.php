<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PICTURE_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'SVG_USE' => 'N',
    'SVG_PROPERTY' => null,
    'COLUMNS' => 5
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'SVG' => [
        'USE' => $arParams['SVG_USE'] === 'Y' && !empty($arParams['SVG_PROPERTY']),
        'PROPERTY' => $arParams['SVG_PROPERTY']
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4, 5], $arParams['COLUMNS'])
];

$arFiles = Collection::from([]);

foreach ($arResult['ITEMS'] as $sKey => $arItem) {
    $arResult['ITEMS'][$sKey]['DATA'] = [
        'PICTURE' => $arItem['PREVIEW_PICTURE']['ID']
    ];
    $arResult['ITEMS'][$sKey]['DATA']['PICTURE'] = empty($arResult['ITEMS'][$sKey]['DATA']['PICTURE']) ? $arItem['DETAIL_PICTURE']['ID'] : $arResult['ITEMS'][$sKey]['DATA']['PICTURE'];

    if ($arVisual['SVG']['USE']) {
        $iSvg = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arVisual['SVG']['PROPERTY'],
            'VALUE'
        ]);

        if (!empty($iSvg))
            $arResult['ITEMS'][$sKey]['DATA']['PICTURE'] = $iSvg;
    }

    if (!empty($arResult['ITEMS'][$sKey]['DATA']['PICTURE']))
        if (!$arFiles->has($arResult['ITEMS'][$sKey]['DATA']['PICTURE']))
            $arFiles->add($arResult['ITEMS'][$sKey]['DATA']['PICTURE']);
}

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

foreach ($arResult['ITEMS'] as $sKey => $arItem) {
    if (!empty($arResult['ITEMS'][$sKey]['DATA']['PICTURE']))
        $arResult['ITEMS'][$sKey]['DATA']['PICTURE'] = $arFiles->get($arResult['ITEMS'][$sKey]['DATA']['PICTURE']);
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);