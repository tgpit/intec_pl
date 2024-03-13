<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'VIDEO_IBLOCK_TYPE' => null,
    'VIDEO_IBLOCK_ID' => null,
    'STAFF_IBLOCK_TYPE' => null,
    'STAFF_IBLOCK_ID' => null,
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'SEND_USE' => 'N',
    'SEND_TEMPLATE' => null,
    'PROPERTY_INFORMATION' => null,
    'PROPERTY_RATING' => null,
    'PROPERTY_VIDEO' => null,
    'VIDEO_PROPERTY_URL' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_FILES' => null,
    'PROPERTY_STAFF' => null,
    'STAFF_PROPERTY_POSITION' => null,
    'LIST_TEMPLATE' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'SEND' => [
        'USE' => $arParams['SEND_USE'] === 'Y',
        'TEMPLATE' => !empty($arParams['SEND_TEMPLATE']) ? $arParams['SEND_TEMPLATE'] : '.default'
    ],
    'LIST' => [
        'TEMPLATE' => !empty($arParams['LIST_TEMPLATE']) ? 'reviews.'.$arParams['LIST_TEMPLATE'] : '.default'
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);