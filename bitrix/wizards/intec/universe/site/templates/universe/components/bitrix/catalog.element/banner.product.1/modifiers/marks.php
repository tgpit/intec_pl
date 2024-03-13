<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 */

$arResult['MARKS'] = [
    'NEW' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_NEW'],
        'VALUE'
    ]),
    'HIT' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_HIT'],
        'VALUE'
    ]),
    'RECOMMEND' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_RECOMMEND'],
        'VALUE'
    ]),
    'SHARE' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_SHARE'],
        'VALUE'
    ])
];

$arResult['MARKS']['NEW'] = !empty($arResult['MARKS']['NEW']);
$arResult['MARKS']['HIT'] = !empty($arResult['MARKS']['HIT']);
$arResult['MARKS']['RECOMMEND'] = !empty($arResult['MARKS']['RECOMMEND']);
$arResult['MARKS']['SHARE'] = !empty($arResult['MARKS']['SHARE']);