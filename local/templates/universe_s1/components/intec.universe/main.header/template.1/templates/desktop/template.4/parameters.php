<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\UnsetArrayValue;

$arReturn = [];
$arReturn['LOGOTYPE_WIDTH'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_DESKTOP_TEMP4_LOGOTYPE_WIDTH'),
    'TYPE' => 'STRING',
    'DEFAULT' => '130'
];
$arReturn['ADDRESS_SHOW'] = new UnsetArrayValue();
$arReturn['AUTHORIZATION_SHOW'] = new UnsetArrayValue();
$arReturn['BASKET_SHOW'] = new UnsetArrayValue();
$arReturn['DELAY_SHOW'] = new UnsetArrayValue();
$arReturn['COMPARE_SHOW'] = new UnsetArrayValue();
$arReturn['SEARCH_SHOW'] = new UnsetArrayValue();

return $arReturn;