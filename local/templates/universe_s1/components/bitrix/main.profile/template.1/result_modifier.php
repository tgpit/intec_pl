<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'READ_ONLY' => 'N',
    'ALL_FIELDS_SHOW' => 'N'
], $arParams);

$arVisual = [
    'READ_ONLY' => $arParams['READ_ONLY'] === 'Y',
    'ALL_FIELDS_SHOW' => $arParams['ALL_FIELDS_SHOW'] === 'Y',
];

$arFields = [
    'NAME' => $arResult['arUser']['NAME'],
    'LAST_NAME' => $arResult['arUser']['LAST_NAME'],
    'LOGIN' => $arResult['arUser']['LOGIN'],
    'PERSONAL_PHONE' => $arResult['arUser']['PERSONAL_PHONE'],
    'EMAIL' => $arResult['arUser']['EMAIL']
];

$arResult['MAIN_FIELDS'] = $arFields;
$arResult['VISUAL'] = $arVisual;

unset($arVisual, $arFields);
