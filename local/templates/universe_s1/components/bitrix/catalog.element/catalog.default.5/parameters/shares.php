<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

if (empty($arCurrentValues['IBLOCK_ID']))
    return;

$arTemplateParameters['SHARES_IBLOCK_TYPE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['SHARES_IBLOCK_ID'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC']))->indexBy('ID')->asArray(function ($key, $value) use (&$arCurrentValues) {
        if (empty($arCurrentValues['SHARES_IBLOCK_TYPE']) || $value['IBLOCK_TYPE_ID'] === $arCurrentValues['SHARES_IBLOCK_TYPE'])
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (empty($arCurrentValues['SHARES_IBLOCK_ID']))
    return;

$arTemplateParameters['SHARES_ACTIVE_DATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_ACTIVE_DATE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['SHARES_MODE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_MODE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'default' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_MODE_DEFAULT'),
        'auto' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_MODE_AUTO')
    ],
    'DEFAULT' => 'default',
    'REFRESH' => 'Y'
];

$arPropertiesShares = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
    'IBLOCK_ID' => $arCurrentValues['SHARES_IBLOCK_ID'],
    'ACTIVE' => 'Y'
]))->indexBy('CODE');

$hPropertySharesLink = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'E' && $value['LIST_TYPE'] === 'L')
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};
$hPropertySharesText = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['LIST_TYPE'] === 'L' && empty($value['USER_TYPE']))
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};

$arPropertySharesLink = $arPropertiesShares->asArray($hPropertySharesLink);
$arPropertySharesText = $arPropertiesShares->asArray($hPropertySharesText);

if ($arCurrentValues['SHARES_MODE'] === 'default') {
    $arTemplateParameters['SHARES_PROPERTY_PRODUCTS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_PROPERTY_PRODUCTS'),
        'TYPE' => 'LIST',
        'VALUES' => Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]))->indexBy('CODE')->asArray($hPropertySharesLink),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
} else if ($arCurrentValues['SHARES_MODE'] === 'auto') {
    $arTemplateParameters['SHARES_IBLOCK_PROPERTY_PRODUCTS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_IBLOCK_PROPERTY_PRODUCTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertySharesLink,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['SHARES_IBLOCK_PROPERTY_DISCOUNT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_IBLOCK_PROPERTY_DISCOUNT'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertySharesText,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['SHARES_IBLOCK_PROPERTY_DATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_IBLOCK_PROPERTY_DATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertySharesText,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (empty($arCurrentValues['SHARES_PROPERTY']) || empty($arCurrentValues['SHARES_IBLOCK_PROPERTY_PRODUCTS']))
    return;

$arTemplateParameters['SHARES_HEADER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_HEADER'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES_HEADER_DEFAULT')
];

unset(
    $arPropertiesShares,
    $hPropertySharesLink,
    $hPropertySharesText,
    $arPropertySharesLink,
    $arPropertySharesText
);

$sComponent = 'intec.universe:main.widget';
$sTemplate = 'catalog.shares.1';
$sPrefix = 'SHARES_';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arTemplate) {
    return [
        'key' => $key,
        'value' => $arTemplate['NAME']
    ];
});

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arExcluded = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'ELEMENT_ID_ENTER',
        'ELEMENT_ID',
        'PROPERTY_DISCOUNT',
        'PROPERTY_DATE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$value) use (&$arExcluded) {
            if (ArrayHelper::isIn($key, $arExcluded))
                return false;

            $value['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SHARES').' '.$value['NAME'];
            $value['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset($arExcluded);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);