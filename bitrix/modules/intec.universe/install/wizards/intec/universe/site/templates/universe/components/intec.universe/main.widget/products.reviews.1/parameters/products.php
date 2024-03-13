<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 * @var \intec\core\collections\Arrays $arIBlocks
 */

$arTemplateParameters['PRODUCTS_IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PRODUCTS_IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
        if (!empty($arCurrentValues['PRODUCTS_IBLOCK_TYPE']) && $value['IBLOCK_TYPE_ID'] !== $arCurrentValues['PRODUCTS_IBLOCK_TYPE'])
            return ['skip' => true];

        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['PRODUCTS_IBLOCK_ID'])) {
    $arTemplateParameters['PRODUCTS_FILTER'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_FILTER'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'productsReviewsFilter'
    ];

    include(__DIR__.'/price.php');

    $arTemplateParameters['PRODUCTS_LIST_URL'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_LIST_URL'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
    $arTemplateParameters['PRODUCTS_SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
        'SECTION',
        'PRODUCTS_REVIEWS_PRODUCTS_SECTION_URL',
        Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_SECTION_URL'),
        '',
        'BASE'
    );
    $arTemplateParameters['PRODUCTS_DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
        'DETAIL',
        'PRODUCTS_REVIEWS_PRODUCTS_DETAIL_URL',
        Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_DETAIL_URL'),
        '',
        'BASE'
    );
}