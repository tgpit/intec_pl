<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$sTemplate = null;

$sTemplate = 'catalog.';

if (empty($sTemplate))
    return;

$sComponent = 'bitrix:catalog.section';
$sPrefix = 'ACCESSORIES_SECTION_';
$iLength = StringHelper::length($sTemplate);

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

$arTemplateParameters['SECTION_TEMPLATE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_SECTION_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['SECTION_TEMPLATE']))
    $sTemplate = $arCurrentValues['SECTION_TEMPLATE'];

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $sTemplate = 'catalog.' . $sTemplate;
    $arSectionCommonParameters = [

    ];

    $arSectionAllowParameters = [
        'PAGE_ELEMENT_COUNT',
        'SECTION_PROPERTY_CODE',
        'OFFERS_FIELD_CODE',
        'OFFERS_PROPERTY_CODE',
        'OFFERS_SORT_FIELD',
        'OFFERS_SORT_ORDER',
        'OFFERS_LIMIT',
        'PRICE_CODE',
        'USE_PRICE_COUNT',
        'SHOW_PRICE_COUNT',
        'PRICE_VAT_INCLUDE',
        'BASKET_URL',
        'ACTION_VARIABLE',
        'PRODUCT_ID_VARIABLE',
        'USE_PRODUCT_QUANTITY',
        'PRODUCT_QUANTITY_VARIABLE',
        'ADD_PROPERTIES_TO_BASKET',
        'PRODUCT_PROPS_VARIABLE',
        'PARTIAL_PRODUCT_PROPERTIES',
        'PRODUCT_PROPERTIES',
        'PAGER_TEMPLATE',
        'HIDE_NOT_AVAILABLE',
        'HIDE_NOT_AVAILABLE_OFFERS',
        'CONVERT_CURRENCY',
        'CURRENCY_ID',
        'OFFERS_CART_PROPERTIES'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arSectionAllowParameters) {
            if (ArrayHelper::isIn($key, $arSectionAllowParameters)) {
                $arParameter['NAME'] = Loc::getMessage('C_WIDGET_ACCESSORIES_SECTION').' '.$arParameter['NAME'];
                $arParameter['PARENT'] = 'LIST_SETTINGS';

                return true;
            }
        },
        Component::PARAMETERS_MODE_COMPONENT
    ));

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arSectionCommonParameters) {
            if (ArrayHelper::isIn($key, $arSectionCommonParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_WIDGET_ACCESSORIES_SECTION').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'LIST_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    $arTemplateParameters['SECTION_COMPARE_USE'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_SECTION_COMPARE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SECTION_COMPARE_USE'] === 'Y') {
        $arTemplateParameters['SECTION_COMPARE_PATH'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_SECTION_COMPARE_PATH'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'catalog/compare.php?action=#ACTION_CODE#'
        ];

        $arTemplateParameters['SECTION_COMPARE_NAME'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_SECTION_COMPARE_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'compare'
        ];
    }

    unset($arSectionCommonParameters);
}

unset($sTemplate, $sComponent, $sPrefix, $iLength, $arTemplates);