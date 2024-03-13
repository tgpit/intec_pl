<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 4], $arParams['COLUMNS'])
];

$pictures = [];

foreach ($arResult['SECTIONS'] as &$section) {
    $section['GALLERY'] = [];

    if (!empty($section['PICTURE']))
        $pictures[] = $section['PICTURE'];
}

unset($section);

if (!empty($pictures)) {
    $pictures = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $pictures)
    ]))->each(function ($key, &$value) {
        $value['SRC'] = CFile::GetFileSRC($value);
    })->indexBy('ID');

    foreach ($arResult['SECTIONS'] as &$section) {
        if (!empty($section['PICTURE']) && $pictures->exists($section['PICTURE']))
            $section['GALLERY'][] = $pictures->get($section['PICTURE']);

        if (!empty($section['ITEMS'])) {
            foreach ($section['ITEMS'] as &$item) {
                if (!empty($item['PREVIEW_PICTURE']))
                    $section['GALLERY'][] = $item['PREVIEW_PICTURE'];
                else if (!empty($item['DETAIL_PICTURE']))
                    $section['GALLERY'][] = $item['DETAIL_PICTURE'];
            }
        }
    }

    unset($section);
}

unset($pictures);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);