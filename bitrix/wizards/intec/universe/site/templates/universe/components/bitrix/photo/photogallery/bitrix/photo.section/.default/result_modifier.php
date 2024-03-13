<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'IMAGE_ASPECT_RATIO' => '10:7',
], $arParams);


$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'IMAGE' => [
        'ASPECT_RATIO' => 70,
    ],
]);

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arRatio = explode(':', $arParams['IMAGE_ASPECT_RATIO']);

if (count($arRatio) >= 2) {
    $arRatio[0] = Type::toInteger($arRatio[0]);
    $arRatio[1] = Type::toInteger($arRatio[1]);

    if ($arRatio[0] <= 0)
        $arRatio[0] = 1;

    if ($arRatio[1] <= 0)
        $arRatio[1] = 1;

    $arVisual['IMAGE']['ASPECT_RATIO'] = floor(100 * $arRatio[1] / $arRatio[0]);
}

unset($arRatio);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$iItemLineCount = 2;
$sItemLineCount = ArrayHelper::getValue($arParams, 'LINE_ELEMENT_COUNT');
if (Type::isNumber($sItemLineCount)){
    $iItemLineCount = $sItemLineCount;
    if ($iItemLineCount > 5) $iItemLineCount = 5;
    if ($iItemLineCount < 2) $iItemLineCount = 2;
}

$arResult['VIEW_PARAMETERS'] = [
    'LINE_ELEMENT_COUNT' => $iItemLineCount
];
