<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

/** Параметры отображения */

$arParams = ArrayHelper::merge([
    'PROPERTY_TEXT_SOURCE' => 'preview',
    'VIEW' => 1,
    'COLUMNS' => 1
], $arParams);

$arVisual = [
    'VIEW' => ArrayHelper::fromRange(['1', '2'], $arParams['VIEW']),
    'TEXT' => [
        'SOURCE' => strtoupper($arParams['PROPERTY_TEXT_SOURCE']).'_TEXT'
    ],
    'NAME' => [
        'SIZE' => ArrayHelper::fromRange(['big', 'normal'], $arParams['ELEMENT_NAME_SIZE'])
    ],
    'COLUMNS' => ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['COLUMNS'])
];

if ($arVisual['VIEW'] === '2')
    $arVisual['COLUMNS'] = 1;

$arResult['VISUAL'] = $arVisual;
