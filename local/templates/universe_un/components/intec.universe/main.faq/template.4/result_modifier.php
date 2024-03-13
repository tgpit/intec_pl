<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\collections\Arrays;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'PROPERTY_EXPANDED' => null,
    'LIMITED_ITEMS_USE' => 'N',
    'LIMITED_ITEMS_COUNT' => 3,
    'SEE_ALL_SHOW' => 'N',
    'SEE_ALL_POSITION' => 'left',
    'SEE_ALL_TEXT' => null,
    'SEE_ALL_URL' => null
], $arParams);

$arVisual = [
    'LIMITED_ITEMS' => [
        'USE' => $arParams['LIMITED_ITEMS_USE'] === 'Y' && !empty($arParams['LIMITED_ITEMS_COUNT']) && count($arResult['ITEMS']) > $arParams['LIMITED_ITEMS_COUNT'],
        'COUNT' => $arParams['LIMITED_ITEMS_COUNT']
    ],
    'SEE_ALL' => [
        'SHOW' => $arParams['SEE_ALL_SHOW'] === 'Y',
        'POSITION' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['SEE_ALL_POSITION']),
        'TEXT' => !empty($arParams['SEE_ALL_TEXT']) ? $arParams['SEE_ALL_TEXT'] : Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_TEMPLATE_SEE_ALL_TEXT_DEFAULT'),
        'LINK' => $arParams['SEE_ALL_URL']
    ]
];

if ($arVisual['SEE_ALL']['SHOW'] && empty($arVisual['SEE_ALL']['LINK'])) {
    $arIblock = Arrays::fromDBResult(CIBlock::GetByID($arParams['IBLOCK_ID']))->getFirst();
    $arMacros = [
        'SITE_DIR' => SITE_DIR,
        'SERVER_NAME' => $_SERVER['SERVER_NAME'],
        'IBLOCK_TYPE_ID' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_CODE' => $arIblock['CODE'],
        'IBLOCK_EXTERNAL_ID' => !empty($arIblock['EXTERNAL_ID']) ? $arIblock['EXTERNAL_ID'] : $arIblock['XML_ID']
    ];
    $arItem = ArrayHelper::getFirstValue($arResult['ITEMS']);
    $arVisual['SEE_ALL']['LINK'] = StringHelper::replaceMacros($arItem['LIST_PAGE_URL'], $arMacros);

    unset($arIblock, $arMacros, $arItem);
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$hSetCheckboxProperty = function (&$arItem, $property) use (&$arParams) {
    $arReturn = false;

    if (!empty($property)) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $property,
            'VALUE'
        ]);

        if (!empty($arProperty))
            $arReturn = $arProperty === 'Y';
    }

    return $arReturn;
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'TEXT' => !empty($arItem['PREVIEW_TEXT']) ? $arItem['PREVIEW_TEXT'] : $arItem['DETAIL_TEXT'],
        'EXPANDED' => $hSetCheckboxProperty($arItem, $arParams['PROPERTY_EXPANDED'])
    ];
}

unset($hSetCheckboxProperty, $arItem);