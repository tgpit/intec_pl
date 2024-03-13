<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['TOP_LINE_ELEMENT_COUNT']['HIDDEN'] = 'Y';
$arTemplateParameters['TOP_FIELD_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['TOP_PROPERTY_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['SECTION_LINE_ELEMENT_COUNT']['HIDDEN'] = 'Y';
$arTemplateParameters['LIST_FIELD_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['LIST_PROPERTY_CODE']['HIDDEN'] = 'Y';

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PHOTO_GALLERY_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_PHOTO_GALLERY_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

include(__DIR__.'/parameters/top.php');
include(__DIR__.'/parameters/list.php');