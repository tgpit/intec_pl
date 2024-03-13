<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

unset($arParams['DETAIL_PRODUCTS_DISPLAY_TOP_PAGER']);
unset($arParams['DETAIL_PRODUCTS_DISPLAY_BOTTOM_PAGER']);
unset($arParams['~DETAIL_PRODUCTS_DISPLAY_TOP_PAGER']);
unset($arParams['~DETAIL_PRODUCTS_DISPLAY_BOTTOM_PAGER']);

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'DETAIL_LAZYLOAD_USE' => 'N',
    'REGIONALITY_USE' => 'N',
    'REGIONALITY_FILTER_USE' => 'N',
    'REGIONALITY_FILTER_PROPERTY' => null,
    'REGIONALITY_FILTER_STRICT' => 'N',
    'MENU_EMPTY_HIDE' => 'Y',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (!empty($arParams['PROPERTY_PRODUCTS'])) {
    if (!ArrayHelper::isIn($arParams['PROPERTY_PRODUCTS'], $arParams['DETAIL_PROPERTY_CODE'])) {
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_PRODUCTS'];
    }
}

if (empty($arParams['LIST_FIELD_CODE']) || !Type::isArray($arParams['LIST_FIELD_CODE']))
    $arParams['LIST_FIELD_CODE'] = [];

if (empty($arParams['DETAIL_FIELD_CODE']) || !Type::isArray($arParams['DETAIL_FIELD_CODE']))
    $arParams['DETAIL_FIELD_CODE'] = [];

$arVisual = [
    'MENU' => [
        'POSITION' => $arParams['MENU_POSITION'],
        'HIDE' => $arParams['MENU_EMPTY_HIDE'] === 'Y'
    ]
];

$arResult['VISUAL'] = &$arVisual;

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
    $arParams['FILTER_NAME'] = 'arrImagesFilter';

$rsIBlock = CIBlock::GetByID($arParams['IBLOCK_ID']);
$arIBlock = $rsIBlock->Fetch();

$arResult['IBLOCK'] = $arIBlock;

$arResult['IBLOCK_DESCRIPTION'] = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'DESCRIPTION');

$arResult['SECTIONS'] = Arrays::fromDBResult(CIBlockSection::GetList(
    ['SORT' => 'ASC'],
    [
        'IBLOCK_ID' => $arResult['IBLOCK']['ID'],
        'ACTIVE' => 'Y',
        'CNT_ACTIVE' => 'Y',
        'ELEMENT_SUBSECTIONS' => 'N'
    ],
    true,
    [
        'ID',
        'IBLOCK_ID',
        'NAME',
        'CODE',
        'ELEMENT_CNT'
    ]
))->asArray();

if ($arVisual['MENU']['HIDE']) {
    foreach ($arResult['SECTIONS']  as $sKey => $arSection){
        if($arSection['ELEMENT_CNT'] <= 0)
        {
            unset($arResult['SECTIONS'][$sKey]);
        }
    }
}

unset($arSection, $arVisual);