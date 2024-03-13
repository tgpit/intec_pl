<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'SUMMARY_FORM_SHOW' => 'N',
    'SUMMARY_FORM_ID' => null,
    'SUMMARY_FORM_TEMPLATE' => null,
    'SUMMARY_FORM_TITLE' => null,
    'SUMMARY_FORM_VACANCY' => null,
    'PROPERTY_CITY' => null,
    'PROPERTY_SKILL' => null,
    'PROPERTY_TYPE_EMPLOYMENT' => null,
    'PROPERTY_SALARY' => null,
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$arData = [];

$arData = [
    'CITY' => $arParams['PROPERTY_CITY'],
    'SKILL' => $arParams['PROPERTY_SKILL'],
    'TYPE_EMPLOYMENT' => $arParams['PROPERTY_TYPE_EMPLOYMENT'],
    'SALARY' => $arParams['PROPERTY_SALARY']
];

$arResult['DATA'] = $arData;

unset($arData);

$arResult['FORM'] = [
    'SUMMARY' => [
        'SHOW' => $arParams['SUMMARY_FORM_SHOW'],
        'ID' => $arParams['SUMMARY_FORM_ID'],
        'TEMPLATE' => $arParams['SUMMARY_FORM_TEMPLATE'],
        'TITLE' => $arParams['SUMMARY_FORM_TITLE'],
        'PROPERTIES' => [
            'VACANCY' => $arParams['SUMMARY_FORM_VACANCY']
        ]
    ]
];

if (empty($arResult['FORM']['SUMMARY']['ID']))
    $arResult['FORM']['SUMMARY']['SHOW'] = false;