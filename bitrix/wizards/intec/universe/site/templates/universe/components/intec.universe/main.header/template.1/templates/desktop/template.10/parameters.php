<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arReturn = [];
$arReturn['LOGOTYPE_WIDTH'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_DESKTOP_TEMP10_LOGOTYPE_WIDTH'),
    'TYPE' => 'STRING',
    'DEFAULT' => '130'
];

return $arReturn;