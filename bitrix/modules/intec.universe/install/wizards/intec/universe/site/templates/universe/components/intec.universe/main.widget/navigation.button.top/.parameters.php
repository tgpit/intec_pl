<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters['RADIUS'] = array(
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_BUTTON_TOP_BORDER_RADIUS'),
    'TYPE' => 'STRING',
);