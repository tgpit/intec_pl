<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/** @var array $arCurrentValues */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
	return;

Loc::loadMessages(__FILE__);

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProductsProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hPropertiesElements = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'E' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertiesElements = $arProductsProperties->asArray($hPropertiesElements);
}

$arTemplateParameters = [];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_IMAGES_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];
$arTemplateParameters['MENU_FILTER_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_IMAGES_1_MENU_FILTER_USE'),
    'TYPE' => 'STRING',
    'DEFAULT' => 'arrFilterMenu'
];
$arTemplateParameters['MENU_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_IMAGES_1_MENU_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_NEWS_IMAGES_1_MENU_POSITION_LEFT'),
        'top' => Loc::getMessage('C_NEWS_IMAGES_1_MENU_POSITION_TOP'),
    ]
];
$arTemplateParameters['MENU_EMPTY_HIDE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_IMAGES_1_MENU_EMPTY_HIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];
$arTemplateParameters['PROPERTY_PRODUCTS'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_IMAGES_1_PROPERTY_PRODUCTS'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertiesElements,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

include(__DIR__.'/parameters/regionality.php');
include(__DIR__.'/parameters/list.php');
include(__DIR__.'/parameters/detail.php');

if (!Loader::includeModule('catalog') && Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');

$arTemplateParameters['USE_RSS'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['USE_RATING'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['USE_REVIEW'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['USE_CATEGORIES'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DISPLAY_TOP_PAGER'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DETAIL_DISPLAY_TOP_PAGER'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DETAIL_DISPLAY_BOTTOM_PAGER'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DETAIL_PRODUCTS_DISPLAY_TOP_PAGER'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DETAIL_PRODUCTS_DISPLAY_BOTTOM_PAGER'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['USE_FILTER'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DETAIL_PAGER_SHOW_ALL'] = [
    'HIDDEN' => 'Y'
];
$arTemplateParameters['PAGER_SHOW_ALL'] = [
    'HIDDEN' => 'Y'
];