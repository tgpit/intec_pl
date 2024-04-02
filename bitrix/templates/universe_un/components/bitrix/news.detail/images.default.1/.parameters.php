<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/** @var array $arCurrentValues */

if (!Loader::includeModule('intec.core') || !Loader::includeModule('iblock'))
    return;

$arIBlockType = [];
$arIBlocksList = [];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PROPERTY_SHOW'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_PROPERTY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProductsProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hPropertiesElements = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'E' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['PROPERTY_PRODUCTS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_PROPERTY_PRODUCTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arProductsProperties->asArray($hPropertiesElements),
        'ADDITIONAL_VALUES' => 'N',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['PROPERTY_PRODUCTS']))
    include(__DIR__.'/parameters/products.php');

include(__DIR__.'/parameters/shares.php');
