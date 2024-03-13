<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\PersonTypeTable;
use intec\core\collections\Arrays;

if (!Loader::includeModule('intec.core') || !Loader::includeModule('sale'))
    return;

if ($_REQUEST['src_site'] && is_string($_REQUEST['src_site'])) {
    $sSiteId = $_REQUEST['src_site'];
} else {
    $sSiteId = \CSite::GetDefSite();
}

$arParameters = [];

$arPersons = Arrays::from(PersonTypeTable::getList(['filter' => ['LID' => $sSiteId]])->fetchAll());

$arPersons = $arPersons->asArray(function ($iIndex, $arPerson) {
    return [
        'key' => $arPerson['ID'],
        'value' => '['.$arPerson['ID'].'] '.$arPerson['NAME']
    ];
});

$arParameters['PERSONS_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PROFILE_ADD_PARAMETER_PERSONS_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arPersons,
    'MULTIPLE' => 'Y'
];

$arParameters['PATH_TO_LIST'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_PROFILE_ADD_PARAMETER_PATH_TO_LIST'),
    'TYPE' => 'STRING'
];

$arParameters['USE_AJAX_LOCATIONS'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_PROFILE_ADD_PARAMETER_USE_AJAX_LOCATIONS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arParameters['SET_TITLE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_PROFILE_ADD_PARAMETER_SET_TITLE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SET_TITLE'] === 'Y') {
    $arParameters['TITLE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_PROFILE_ADD_PARAMETER_TITLE'),
        'TYPE' => 'STRING'
    ];
}

$arComponentParameters = [
    'PARAMETERS' => $arParameters
];
