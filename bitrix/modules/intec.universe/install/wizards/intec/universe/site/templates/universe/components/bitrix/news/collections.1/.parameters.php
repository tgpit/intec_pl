<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arCurrentValues
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
	return;

Loc::loadMessages(__FILE__);

$arTemplateParameters = [];

include(__DIR__.'/parameters/hidden.php');

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_COLLECTIONS_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_COLLECTIONS_1_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

include(__DIR__.'/parameters/regionality.php');
include(__DIR__.'/parameters/list.php');
include(__DIR__.'/parameters/detail.php');

if (!Loader::includeModule('catalog') && Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');