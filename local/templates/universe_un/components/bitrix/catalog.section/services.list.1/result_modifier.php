<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'ROUNDED' => 'Y',
    'WIDE' => 'Y',
    'LAZY_LOAD' => 'N',
    'LOAD_ON_SCROLL' => 'N'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER']
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER']
        ],
        'LAZY' => [
            'BUTTON' => $arParams['LAZY_LOAD'] === 'Y',
            'SCROLL' => $arParams['LOAD_ON_SCROLL'] === 'Y'
        ]
    ],
    'ROUNDED' => $arParams['ROUNDED'] === 'Y',
    'WIDE' => $arParams['WIDE'] === 'Y'
];

$arResult['VISUAL'] = $arVisual;