<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $sSite
 */

$arOfferIBlock = CCatalogSku::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);

if (empty($arOfferIBlock['IBLOCK_ID']))
    return;

$arOfferProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
    'SITE_ID' => $sSite,
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => $arOfferIBlock['IBLOCK_ID']
]));

$arTemplateParameters['QUICK_VIEW_OFFER_TREE_PROPS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFER_TREE_PROPS'),
    'TYPE' => 'LIST',
    'VALUES' => $arOfferProperties->asArray(function ($key, $property) {
        if (empty($property['CODE']))
            return ['skip' => true];

        return [
            'key' => $property['CODE'],
            'value' => '['.$property['CODE'].'] '.$property['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'MULTIPLE' => 'Y'
];
$arTemplateParameters['QUICK_VIEW_OFFERS_FIELD_CODE'] = CIBlockParameters::GetFieldCode(
    Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_FIELD_CODE'),
    'VISUAL'
);
$arTemplateParameters['QUICK_VIEW_OFFERS_PROPERTY_CODE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_PROPERTY_CODE'),
    'TYPE' => 'LIST',
    'VALUES' => $arOfferProperties->asArray(function ($key, $property) {
        if (empty($property['CODE']))
            return ['skip' => true];

        return [
            'key' => $property['CODE'],
            'value' => '['.$property['CODE'].'] '.$property['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'MULTIPLE' => 'Y'
];
$arTemplateParameters['QUICK_VIEW_OFFERS_SORT_FIELD'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'shows' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_SHOWS'),
        'sort' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_SORT'),
        'timestamp_x' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_TIMESTAMP'),
        'name' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_NAME'),
        'id' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_ID'),
        'active_from' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_ACTIVE_FROM'),
        'active_to' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_ACTIVE_TO'),
    ],
    'DEFAULT' => 'sort'
];
$arTemplateParameters['QUICK_VIEW_OFFERS_SORT_ORDER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_ORDER'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'asc' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_ORDER_ASC'),
        'desc' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_ORDER_DESC')
    ],
    'DEFAULT' => 'asc'
];
$arTemplateParameters['QUICK_VIEW_OFFERS_SORT_FIELD2'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_2'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'shows' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_SHOWS'),
        'sort' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_SORT'),
        'timestamp_x' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_TIMESTAMP'),
        'name' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_NAME'),
        'id' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_ID'),
        'active_from' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_ACTIVE_FROM'),
        'active_to' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_FIELD_ACTIVE_TO'),
    ],
    'DEFAULT' => 'sort'
];
$arTemplateParameters['QUICK_VIEW_OFFERS_SORT_ORDER2'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_ORDER_2'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'asc' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_ORDER_ASC'),
        'desc' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_OFFERS_SORT_ORDER_DESC')
    ],
    'DEFAULT' => 'asc'
];