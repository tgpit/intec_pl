<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'LINE_COUNT' => 4,
    'VIEW' => 'number',
    'LINK_PROPERTY_USE' => 'N',
    'LINK_PROPERTY' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4, 5], $arParams['LINE_COUNT']),
    'VIEW' => ArrayHelper::fromRange([
        'number',
        'icon'
    ], $arParams['VIEW'])
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'LINK' => $arItem['DETAIL_PAGE_URL']
    ];

    if ($arParams['LINK_PROPERTY_USE'] === 'Y' && !empty($arParams['LINK_PROPERTY'])) {
        $sLink = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['LINK_PROPERTY'],
            'VALUE'
        ]);

        if (!empty($sLink))
            $arItem['DATA']['LINK'] = $sLink;
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);
