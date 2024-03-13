<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\PersonTypeTable;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

if (!Loader::includeModule('intec.core') || !Loader::includeModule('sale'))
    return;


$arParameters = [];

$arParameters['USE_BASKET'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_DELIVERY_PARAMETER_USE_BASKET'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'N',
    'DEFAULT' => 'Y'
];

$arPersons = Arrays::from(PersonTypeTable::getList()->fetchAll());
$arPersons = $arPersons->asArray(function ($iIndex, $arPerson) {
    return [
        'key' => $arPerson['ID'],
        'value' => '['.$arPerson['ID'].'] '.$arPerson['NAME']
    ];
});

$arParameters['PERSON_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_DELIVERY_PARAMETER_PERSON_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arPersons,
    'REFRESH' => 'N'
];

$arComponentParameters = [
    'PARAMETERS' => $arParameters
];