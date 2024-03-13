<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\template\Properties;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['PICTURE'] = [
        'ORIGINAL_SRC' => null,
        'RESIZE_SRC' => null,
        'SIZE' => null
    ];
    $sPicture = null;

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        $sPicture = $arItem['PREVIEW_PICTURE'];
    } else if (!empty($arItem['DETAIL_PICTURE'])) {
        $sPicture = $arItem['DETAIL_PICTURE'];
    }

    if (!empty($sPicture)) {
        $arItem['PICTURE']['ORIGINAL_SRC'] = $sPicture['SRC'];
        $arItem['PICTURE']['SIZE'] = CFile::FormatSize($sPicture['FILE_SIZE']);
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 440,
            'height' => 640
        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
        $arItem['PICTURE']['RESIZE_SRC'] = $sPicture['src'];
    }
}

unset($arItem);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);
