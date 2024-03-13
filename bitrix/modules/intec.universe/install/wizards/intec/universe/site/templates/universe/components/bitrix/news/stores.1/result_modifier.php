<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

if (!Loader::includeModule('intec.core'))
    return;

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