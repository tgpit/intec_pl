<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arProjectsParameters = [
    'PREFIX' => 'PROJECTS_',
    'TEMPLATE' => 'template.' . $arParams['PROJECTS_TEMPLATE'],
    'PARAMETERS' => [
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'FILTER' => [
            'ID' => $arResult['DATA']['PROJECTS']['VALUES']
        ],
        'HEADER_TEXT' => $arParams['PROJECTS_HEADER_TEXT'],
        'HEADER_SHOW' => !empty($arParams['PROJECTS_HEADER_TEXT']) ? 'Y' : 'N'
    ]
];
$sLength = StringHelper::length($arProjectsParameters['PREFIX']);
$arExcluded = [
    'TEMPLATE'
];

foreach ($arParams as $key => $arValue) {
    if (!StringHelper::startsWith($key, $arProjectsParameters['PREFIX']))
        continue;

    $key = StringHelper::cut($key, $sLength);

    if (ArrayHelper::isIn($key, $arExcluded))
        continue;

    $arProjectsParameters['PARAMETERS'][$key] = $arValue;
}

$arResult['PROJECTS'] = $arProjectsParameters;

unset($arProjectsParameters, $sLength, $arExcluded, $key, $arValue);
