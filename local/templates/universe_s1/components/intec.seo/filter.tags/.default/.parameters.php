<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters['MOBILE_HIDE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('FILTER_TAGS_DEFAULT_MOBILE_HIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['SLIDER_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('FILTER_TAGS_DEFAULT_SLIDER_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['SLIDER_ARROW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('FILTER_TAGS_DEFAULT_SLIDER_ARROW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['COUNT_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('FILTER_TAGS_DEFAULT_COUNT_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['SHOW_ALL_COUNT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('FILTER_TAGS_DEFAULT_SHOW_ALL_COUNT'),
    'TYPE' => 'STRING',
    'DEFAULT' => ''
];
