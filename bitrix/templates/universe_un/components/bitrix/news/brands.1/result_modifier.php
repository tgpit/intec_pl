<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'DETAIL_LAZYLOAD_USE' => 'N',
    'REGIONALITY_USE' => 'N',
    'REGIONALITY_FILTER_USE' => 'N',
    'REGIONALITY_FILTER_PROPERTY' => null,
    'REGIONALITY_FILTER_STRICT' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (empty($arParams['LIST_FIELD_CODE']) || !Type::isArray($arParams['LIST_FIELD_CODE']))
    $arParams['LIST_FIELD_CODE'] = [];

if (empty($arParams['DETAIL_FIELD_CODE']) || !Type::isArray($arParams['DETAIL_FIELD_CODE']))
    $arParams['DETAIL_FIELD_CODE'] = [];

$arFields = [
    'PREVIEW_PICTURE',
    'DETAIL_PICTURE',
    'DATE_ACTIVE_FROM',
    'ACTIVE_FROM',
    'DATE_ACTIVE_TO',
    'ACTIVE_TO',
    'DATE_CREATE',
    'TIMESTAMP_X'
];

foreach ($arFields as $sField) {
    if (!ArrayHelper::isIn($sField, $arParams['LIST_FIELD_CODE']))
        $arParams['LIST_FIELD_CODE'][] = $sField;

    if (!ArrayHelper::isIn($sField, $arParams['DETAIL_FIELD_CODE']))
        $arParams['DETAIL_FIELD_CODE'][] = $sField;
}

unset($arFields, $sField);

if (empty($arParams['FILTER_NAME']))
    $arParams['FILTER_NAME'] = 'arrBrandsFilter';

$rsIBlock = CIBlock::GetByID($arParams['IBLOCK_ID']);
$arIBlock = $rsIBlock->Fetch();

$arResult['IBLOCK'] = $arIBlock;

$arResult['REGIONALITY'] = [
    'USE' => $arParams['REGIONALITY_USE'] === 'Y',
    'FILTER' => [
        'USE' => $arParams['REGIONALITY_FILTER_USE'] === 'Y',
        'PROPERTY' => $arParams['REGIONALITY_FILTER_PROPERTY'],
        'STRICT' => $arParams['REGIONALITY_FILTER_STRICT'] === 'Y'
    ]
];

if (empty($arParams['IBLOCK_ID']) || !Loader::includeModule('intec.regionality'))
    $arResult['REGIONALITY']['USE'] = false;

if (empty($arResult['REGIONALITY']['FILTER']['PROPERTY']))
    $arResult['REGIONALITY']['FILTER']['USE'] = false;

if ($arResult['REGIONALITY']['USE']) {
    $oRegion = Region::getCurrent();

    if (!empty($oRegion)) {
        if ($arResult['REGIONALITY']['FILTER']['USE']) {
            if (!isset($GLOBALS[$arParams['FILTER_NAME']]) || !Type::isArray($GLOBALS[$arParams['FILTER_NAME']]))
                $GLOBALS[$arParams['FILTER_NAME']] = [];

            $arConditions = [
                'LOGIC' => 'OR',
                ['PROPERTY_'.$arResult['REGIONALITY']['FILTER']['PROPERTY'] => $oRegion->id]
            ];

            if (!$arResult['REGIONALITY']['FILTER']['STRICT'])
                $arConditions[] = ['PROPERTY_'.$arResult['REGIONALITY']['FILTER']['PROPERTY'] => false];

            $GLOBALS[$arParams['FILTER_NAME']][] = $arConditions;
        }
    }
}