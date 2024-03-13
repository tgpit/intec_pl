<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'));


$arTemplateParameters = [];

/*$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_32_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];*/

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_32_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3',
        4 => '4',
    ],
    'DEFAULT' => 3
];

$arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
]))->indexBy('ID');

$hPropertyNumber = function ($sKey, $arProperty) {
    if ($arProperty['PROPERTY_TYPE'] == 'N' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
        return [
            'key' => $arProperty['CODE'],
            'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
        ];

    return ['skip' => true];
};

$arPropertyNumber = $arProperties->asArray($hPropertyNumber);

$arTemplateParameters['PROPERTY_NUMBER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_32_PROPERTY_NUMBER'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyNumber,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arTemplateParameters['PROPERTY_MAX_NUMBER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_32_PROPERTY_MAX_NUMBER'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyNumber,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];