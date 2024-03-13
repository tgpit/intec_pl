<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParameters = [];

$arParameters['TIME_ZERO_HIDE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_TIMER_COMPONENT_TIME_ZERO_HIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arParameters['DATE_END'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_TIMER_COMPONENT_DATE_END'),
    'TYPE' => 'STRING',
    'DEFAULT' => ''
];

$arComponentParameters = [
    'PARAMETERS' => $arParameters
];