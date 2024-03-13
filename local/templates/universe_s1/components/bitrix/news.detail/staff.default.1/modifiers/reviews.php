<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arReviewsParameters = [
    'PREFIX' => 'REVIEWS_',
    'TEMPLATE' => 'template.' . $arParams['REVIEWS_TEMPLATE'],
    'PARAMETERS' => [
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'FILTER' => [
            'ID' => $arResult['DATA']['REVIEWS']['VALUES']
        ],
        'HEADER_TEXT' => $arParams['REVIEWS_HEADER_TEXT'],
        'HEADER_SHOW' => !empty($arParams['REVIEWS_HEADER_TEXT']) ? 'Y' : 'N'
    ]
];
$sLength = StringHelper::length($arReviewsParameters['PREFIX']);
$arExcluded = [
    'TEMPLATE'
];

foreach ($arParams as $key => $arValue) {
    if (!StringHelper::startsWith($key, $arReviewsParameters['PREFIX']))
        continue;

    $key = StringHelper::cut($key, $sLength);

    if (ArrayHelper::isIn($key, $arExcluded))
        continue;

    $arReviewsParameters['PARAMETERS'][$key] = $arValue;
}

$arResult['REVIEWS'] = $arReviewsParameters;

unset($arReviewsParameters, $sLength, $arExcluded, $key, $arValue);
