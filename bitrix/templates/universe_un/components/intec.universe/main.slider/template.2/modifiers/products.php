<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$sPrefix = 'PRODUCT_';
$arProductParameters = [
    'PARAMETERS' => [
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'SET_TITLE' => 'N',
        'SET_BROWSER_TITLE' => 'N',
        'SET_META_KEYWORDS' => 'N',
        'SET_META_DESCRIPTION' => 'N'
    ]
];

foreach ($arParams as $sKey => $sValue) {
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut($sKey, StringHelper::length($sPrefix));

        if ($sKey !== 'ELEMENT_ID') {
            $arProductParameters['PARAMETERS'][$sKey] = $sValue;
        }
    }
}

$arResult['PRODUCT'] = $arProductParameters;

unset($arProductParameters, $sPrefix);