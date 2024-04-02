<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;

$arTemplateParameters['SERVICES_PROPERTY_ELEMENTS'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES_PROPERTY_ELEMENTS'),
    'TYPE' => 'LIST',
    'VALUES' => $arProperties->asArray($hPropertiesElements),
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['SERVICES_HEADER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES_HEADER'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES_HEADER_DEFAULT')
];

$arTemplateParameters['SERVICES_HEADER_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES_HEADER_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_LEFT'),
        'center' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_CENTER'),
        'right' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_RIGHT')
    ],
    'DEFAULT' => 'left'
];

$arTemplateParameters['SERVICES_WIDE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES_WIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$sComponent = 'bitrix:catalog.section';
$sTemplate = 'services.tile.';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
    if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
        return ['skip' => true];

    $sName = StringHelper::cut(
        $arTemplate['NAME'],
        StringHelper::length($sTemplate)
    );

    return [
        'key' => $sName,
        'value' => $sName
    ];
});

$sPrefix = 'SERVICES_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'services.tile.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y',
    'DEFAULT' => 5
];

if (!empty($sTemplate)) {
    $arUsedParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'SECTION_ID',
        'SECTION_CODE',
        'SECTION_USER_FIELDS',
        'ELEMENT_SORT_FIELD',
        'ELEMENT_SORT_ORDER',
        'ELEMENT_SORT_FIELD2',
        'ELEMENT_SORT_ORDER2',
        'INCLUDE_SUBSECTIONS',
        'SHOW_ALL_WO_SECTION',
        'SECTION_URL',
        'DETAIL_URL',
        'SECTION_ID_VARIABLE',
        'PRICE_CODE',
        'USE_PRICE_COUNT',
        'SHOW_PRICE_COUNT',
        'PRICE_VAT_INCLUDE',
        'BASKET_URL',
        'ACTION_VARIABLE',
        'PRODUCT_ID_VARIABLE',
        'USE_PRODUCT_QUANTITY',
        'PRODUCT_QUANTITY_VARIABLE',
        'PRODUCT_PROPS_VARIABLE',
        'PAGER_TEMPLATE',
        'DISPLAY_TOP_PAGER',
        'DISPLAY_BOTTOM_PAGER',
        'PAGER_TITLE',
        'PAGER_SHOW_ALWAYS',
        'PAGER_DESC_NUMBERING',
        'HIDE_NOT_AVAILABLE',
        'CONVERT_CURRENCY',
        'DISPLAY_COMPARE',
        'COLUMNS'
    ];

    $arUnusedParams = [];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            'SERVICES_',
            function ($sKey, &$arParameter) use (&$arUsedParameters) {
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, $arUsedParameters))
                    return true;

                return false;
            },
            Component::PARAMETERS_MODE_BOTH
        )
    );

    unset ($arUsedParameters);
}