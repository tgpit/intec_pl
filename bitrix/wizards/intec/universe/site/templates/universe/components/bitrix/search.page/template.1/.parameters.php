<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlocksType = CIBlockParameters::GetIBlockTypes();
$rsIBlocks = CIBlock::GetList();
$test = [];

while ($arIBlock = $rsIBlocks->GetNext()) {
    $test[] = $arIBlock;
    $arIBlocks[$arIBlock['IBLOCK_TYPE_ID']][$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
    $arIBlocks['all'][$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
}

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_USE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_USE'] === 'Y') {
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocksType,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE'])) {
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks[$arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE']],
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'])) {
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_TITLE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_TITLE_DEFAULT')
    ];
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_COUNT_ELEMENTS'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_COUNT_ELEMENTS'),
        'TYPE' => 'STRING',
        'DEFAULT' => '6'
    ];
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_IS_CATALOG'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_IS_CATALOG'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'] === 'Y') {
        $arProperties = null;
        $rsProperties = CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID']]);

        while ($arProperty = $rsProperties->GetNext()) {
            if ($arProperty['PROPERTY_TYPE'] === 'L')
                $arProperties[$arProperty['CODE']] = '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME'];
        }

        $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_PROPERTY_FILTER'] = [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_1_BLOCK_ON_EMPTY_RESULTS_PROPERTY_FILTER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    include(__DIR__.'/parameters/elements.php');
}
