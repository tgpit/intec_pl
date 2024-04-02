<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var string $componentName
 * @var string $templateName
 * @var string $siteTemplate
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

if (Loader::includeModule('iblock')) {
    $arIBlocksTypes = CIBlockParameters::GetIBlockTypes();

    $arIBlocks = [];
    $rsIBlocks = CIBlock::GetList([], [
        'ACTIVE' => 'Y',
        'TYPE' => $arCurrentValues['REQUESTED_IBLOCK_ID_TYPE']
    ]);

    while ($arIBlock = $rsIBlocks->GetNext())
        $arIBlocks[$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
}

if ($arCurrentValues['REQUESTED_IBLOCK_ID']) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['REQUESTED_IBLOCK_ID'],
    ]))->indexBy('ID');

    $hPropertiesCheckbox = function ($sKey, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
            return ['skip' => true];

        return [
            'key' => $arProperty['CODE'],
            'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
        ];
    };
}

$arTemplateParameters['INPUT_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_INPUT_ID'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['TIPS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['TIPS_VIEW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'list.1' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW_LIST_1'),
        'list.2' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW_LIST_2'),
        'list.3' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW_LIST_3')
    ]
];

$arTemplateParameters['HIDE_REQUESTED'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_HIDE_REQUESTED'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['REQUESTED_IBLOCK_ID_TYPE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_REQUESTED_IBLOCK_ID_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arTemplateParameters['REQUESTED_IBLOCK_ID'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_REQUESTED_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks,
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['REQUESTED_IBLOCK_ID'])) {
    if ($arCurrentValues['HIDE_REQUESTED'] === 'Y') {
        $arTemplateParameters['PROPERTY_REQUEST'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_PROPERTY_REQUESTED'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

if (Loader::includeModule('catalog')) {
    include(__DIR__.'/parameters/catalog/base.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/parameters/catalog/lite.php');
}

include(__DIR__.'/parameters/products.php');