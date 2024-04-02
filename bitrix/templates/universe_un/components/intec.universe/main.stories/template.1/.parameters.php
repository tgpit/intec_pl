<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock\Model\PropertyFeature;
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

$sSite = null;

if (!empty($_REQUEST['site'])) {
    $sSite = $_REQUEST['site'];
} else if (!empty($_REQUEST['src_site'])) {
    $sSite = $_REQUEST['src_site'];
}

$bEnabledProperties = PropertyFeature::isEnabledFeatures();

$arIBlocks = [
    'type' => CIBlockParameters::GetIBlockTypes(),
    'items' => []
];

if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $arIBlocks['items'] = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite,
        'TYPE' => $arCurrentValues['IBLOCK_TYPE']
    ]));
} else {
    $arIBlocks['items'] = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC']));
}

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hPropertyText = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && empty($value['USER_TYPE']) && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyCheckbox = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'L' && empty($value['USER_TYPE']) && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyText = $arProperties->asArray($hPropertyText);
    $arPropertyCheckbox = $arProperties->asArray($hPropertyCheckbox);
}

$arTemplateParameters['POPUP_TIME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_POPUP_TIME'),
    'TYPE' => 'STRING',
    'DEFAULT' => 5
];

$arTemplateParameters['PROPERTY_LINK'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_PROPERTY_LINK'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyText,
    'ADDITIONAL_VALUES' => 'Y',
];

$arTemplateParameters['PROPERTY_BUTTON_TEXT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_PROPERTY_BUTTON_TEXT'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyText,
    'ADDITIONAL_VALUES' => 'Y',
];

$arTemplateParameters['BUTTON_TEXT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_BUTTON_TEXT_DEFAULT')
];

$arTemplateParameters['LIST_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_LIST_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'rectangle' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_LIST_VIEW_RECTANGLE'),
        'round' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_LIST_VIEW_ROUND')
    ],
    'DEFAULT' => 'round'
];

$arTemplateParameters['NAVIGATION_BUTTON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_NAVIGATION_BUTTON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];
$arTemplateParameters['SHOW_MORE_BUTTON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_SHOW_MORE_BUTTON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SHOW_MORE_BUTTON_SHOW'] === 'Y') {
    $arTemplateParameters['SHOW_MORE_BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_SHOW_MORE_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_SHOW_MORE_BUTTON_TEXT_DEFAULT')
    ];
}

$arTemplateParameters['LIST_COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_LIST_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8
    ],
    'DEFAULT' => '8'
];