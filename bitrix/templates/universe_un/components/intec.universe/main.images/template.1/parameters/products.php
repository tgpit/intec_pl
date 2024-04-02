<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'SITE_ID' => $sSite,
    'ACTIVE' => 'Y'
]));

$arTemplateParameters['PRODUCTS_IBLOCK_TYPE'] = [
    'PARENT' => 'PRODUCTS',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PRODUCTS_IBLOCK_ID'] = [
    'PARENT' => 'PRODUCTS',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
        if (!empty($arCurrentValues['PRODUCTS_IBLOCK_TYPE']) && $value['IBLOCK_TYPE_ID'] !== $arCurrentValues['PRODUCTS_IBLOCK_TYPE'])
            return ['skip' => true];

        return [
            'key' => $value['ID'],
            'value' => '[' . $value['ID'] . '] ' . $value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['PRODUCTS_IBLOCK_ID'])) {
    $arTemplateParameters['PRODUCTS_ELEMENTS_COUNT'] = [
        'PARENT' => 'PRODUCTS',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_ELEMENTS_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
    $arTemplateParameters['PRODUCTS_FILTER'] = [
        'PARENT' => 'PRODUCTS',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_FILTER'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'collectionsFilter'
    ];

    include(__DIR__ . '/prices.php');

    $arTemplateParameters['PRODUCTS_SORT_BY'] = [
        'PARENT' => 'PRODUCTS',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_SORT_BY'),
        'TYPE' => 'LIST',
        'VALUES' => CIBlockParameters::GetElementSortFields(),
        'DEFAULT' => 'SORT'
    ];
    $arTemplateParameters['PRODUCTS_ORDER_BY'] = [
        'PARENT' => 'PRODUCTS',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_ORDER_BY'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'ASC' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_ORDER_BY_ASC'),
            'DESC' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_ORDER_BY_DESC')
        ],
        'DEFAULT' => 'ASC'
    ];
    $arTemplateParameters['PRODUCTS_LIST_URL'] = [
        'PARENT' => 'PRODUCTS',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_LIST_URL'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
    $arTemplateParameters['PRODUCTS_SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
        'SECTION',
        'IMAGES_PRODUCTS_SECTION_URL',
        Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_SECTION_URL'),
        '',
        'PRODUCTS'
    );
    $arTemplateParameters['PRODUCTS_DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
        'DETAIL',
        'IMAGES_PRODUCTS_DETAIL_URL',
        Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_DETAIL_URL'),
        '',
        'PRODUCTS'
    );

    $excluded = [
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'PRICE_CODE',
        'CONVERT_CURRENCY',
        'CURRENCY_ID'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        'bitrix:catalog.section',
        'images.list.1',
        $siteTemplate,
        $arCurrentValues,
        'PRODUCTS_',
        function ($key, &$parameter) use (&$excluded) {
            if (ArrayHelper::isIn($key, $excluded))
                return false;

            $parameter['PARENT'] = 'PRODUCTS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));
}