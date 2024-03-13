<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'TOP_TEMPLATE' => null,
    'LIST_TEMPLATE' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'TOP' => [
        'TEMPLATE' => !empty($arParams['TOP_TEMPLATE']) ? 'gallery.'.$arParams['TOP_TEMPLATE'] : '.default'
    ],
    'LIST' => [
        'TEMPLATE' => !empty($arParams['LIST_TEMPLATE']) ? 'gallery.'.$arParams['LIST_TEMPLATE'] : '.default'
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);