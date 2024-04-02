<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'PROPERTY_RATING' => null,
    'RATING_USE' => 'N',
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => null,
    'FORM_SUBMIT_TEXT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'MODE' => ArrayHelper::fromRange(['disabled', 'active'], $arParams['FORM_ADD_MODE']),
    'RATING' => [
        'USE' => $arParams['RATING_USE'] === 'Y' && !empty($arParams['PROPERTY_RATING']),
        'CODE' => $arParams['PROPERTY_RATING']
    ],
    'CONSENT' => [
        'SHOW' => $arParams['CONSENT_SHOW'] === 'Y' && !empty($arParams['CONSENT_URL']),
        'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], [
            'SITE_DIR' => SITE_DIR
        ])
    ],
    'SUBMIT' => [
        'TEXT' => $arParams['FORM_SUBMIT_TEXT']
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);