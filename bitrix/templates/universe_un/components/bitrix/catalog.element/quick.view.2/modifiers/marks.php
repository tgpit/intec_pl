<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCodes
 * @var array $arResult
 */

$arResult['MARKS'] = [
    'NEW' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arCodes['MARKS']['NEW'],
        'VALUE'
    ]),
    'HIT' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arCodes['MARKS']['HIT'],
        'VALUE'
    ]),
    'RECOMMEND' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arCodes['MARKS']['RECOMMEND'],
        'VALUE'
    ]),
    'SHARE' => ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arCodes['MARKS']['SHARE'],
        'VALUE'
    ]),
    'ORIENTATION' => $arCodes['MARKS']['ORIENTATION'],
];


$arResult['MARKS']['NEW'] = !empty($arResult['MARKS']['NEW']);
$arResult['MARKS']['HIT'] = !empty($arResult['MARKS']['HIT']);
$arResult['MARKS']['RECOMMEND'] = !empty($arResult['MARKS']['RECOMMEND']);
$arResult['MARKS']['SHARE'] = !empty($arResult['MARKS']['SHARE']);