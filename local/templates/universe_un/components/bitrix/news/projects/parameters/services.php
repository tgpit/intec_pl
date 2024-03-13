<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arCurrentValues
 */

$sComponent = 'bitrix:catalog.section';
$sTemplate = 'services.tile.5';
$sPrefix = 'SERVICES_';

$arUsedParameters = [
    'IBLOCK_TYPE',
    'IBLOCK_ID',
    'ELEMENT_SORT_FIELD',
    'ELEMENT_SORT_ORDER',
    'ELEMENT_SORT_FIELD2',
    'ELEMENT_SORT_ORDER2',
    'INCLUDE_SUBSECTIONS',
    'SHOW_ALL_WO_SECTION',
    'SECTION_URL',
    'DETAIL_URL',
    'SECTION_ID_VARIABLE',
    'CONVERT_CURRENCY',
    'CURRENCY_ID',
    'PRICE_CODE',
    'USE_PRICE_COUNT',
    'SHOW_PRICE_COUNT',
    'PRICE_VAT_INCLUDE',
    'CONVERT_CURRENCY',
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
    'BASKET_URL',
    'DISPLAY_COMPARE',
    'COMPARE_PATH',
    'ACTION',
    'BORDERS',
    'COLUMNS',
    'COLUMNS_MOBILE',
    'IMAGE_ASPECT_RATIO',
    'RECALCULATION_PRICES_USE',
    'COUNTER_SHOW',
    'CONSENT_URL',
    'PROPERTY_ORDER_USE',
    'PROPERTY_MARKS_RECOMMEND',
    'PROPERTY_MARKS_NEW',
    'PROPERTY_MARKS_HIT',
    'PROPERTY_MARKS_SHARE',
    'DELAY_USE',
    'VOTE_SHOW',
    'QUANTITY_SHOW',
    'QUANTITY_MODE',
    'FORM_ID',
    'FORM_PROPERTY_PRODUCT',
    'FORM_TEMPLATE',
    'SECTION_ID',
    'SECTION_CODE',
    'SECTION_USER_FIELDS'
];

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
    Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        'SERVICES_',
        function ($sKey, &$arParameter) use (&$arUsedParameters) {
            $arParameter['NAME'] = Loc::getMessage('N_PROJECTS_PARAMETERS_SERVICES').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            if (ArrayHelper::isIn($sKey, $arUsedParameters))
                return true;

            return false;
        },
        Component::PARAMETERS_MODE_BOTH
    )
);

unset ($arUsedParameters);

unset($sComponent, $sTemplate, $sPrefix);

$arTemplateParameters['SERVICES_ALLOW_LINK'] = [
    'PARENT' => 'BASE',
    'TYPE' => 'CHECKBOX',
    'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_SERVICES_ALLOW_LINK')
];

if ($arCurrentValues['SERVICES_ALLOW_LINK'] === 'Y') {
    $arTemplateParameters['SERVICES_DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
        'DETAIL',
        'DETAIL_URL',
        Loc::getMessage('N_PROJECTS_PARAMETERS_SERVICES_DETAIL_URL'),
        '',
        'DETAIL_SETTINGS'
    );
}
