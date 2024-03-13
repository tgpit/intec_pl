<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$sComponent = 'bitrix:catalog.element';
$sTemplate = 'banner.product.1';
$sPrefix = 'PRODUCT_';

$arProductParametersUse = [
    'IBLOCK_TYPE',
    'IBLOCK_ID',
    'FORM_ID',
    'FORM_PROPERTY_PRODUCT',
    'FORM_TEMPLATE',
    'ACTION',
    'MARKS_SHOW',
    'MARKS_TEMPLATE',
    'PROPERTY_ORDER_USE',
    'PROPERTY_MARKS_RECOMMEND',
    'PROPERTY_MARKS_HIT',
    'PROPERTY_MARKS_NEW',
    'PROPERTY_MARKS_SHARE',
    'QUANTITY_SHOW',
    'QUANTITY_MODE',
    'QUANTITY_BOUNDS_FEW',
    'QUANTITY_BOUNDS_MANY',
    'PRICE_RANGE',
    'PRICE_DIFFERENCE',
    'TIMER_SHOW',
    'PURCHASE_ORDER_BUTTON_TEXT',
    'SHOW_PRICE_COUNT',
    'BASKET_URL',
    'ACTION_VARIABLE',
    'ID_VARIABLE',
    'DISPLAY_COMPARE',
    'USE_COMPARE',
    'COMPARE_PATH',
    'COMPARE_NAME',
    'DELAY_USE',
    'PRICE_DIFFERENCE',
    'TIMER_SHOW',
    'TIMER_TIME_ZERO_HIDE',
    'TIMER_MODE',
    'TIMER_ELEMENT_ID_INTRODUCE',
    'TIMER_TIMER_SECONDS_SHOW',
    'TIMER_TIMER_QUANTITY_SHOW',
    'TIMER_TIMER_QUANTITY_ENTER_VALUE',
    'TIMER_TIMER_PRODUCT_UNITS_USE',
    'TIMER_TIMER_QUANTITY_HEADER_SHOW',
    'TIMER_TIMER_QUANTITY_HEADER',
    'TIMER_TIMER_HEADER_SHOW',
    'TIMER_TIMER_HEADER',
    'TIMER_SETTINGS_USE',
    'TIMER_LAZYLOAD_USE',
    'TIMER_TIMER_QUANTITY_OVER',
    'TIMER_UNITS_USE',
    'TIMER_UNITS_VALUE',
    'SECTION_URL',
    'DETAIL_URL',
    'SECTION_ID_VARIABLE',
    'CHECK_SECTION_ID_VARIABLE',
    'PRICE_CODE',
    'CONVERT_CURRENCY',
    'PROPERTY_OLD_PRICE_BASE',
    'CURRENCY_ID',
    'VOTE_SHOW',
    'VOTE_MODE'
];

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
    $sComponent,
    $sTemplate,
    $siteTemplate,
    $arCurrentValues,
    $sPrefix,
    function ($sKey, &$arParameter) use (&$arProductParametersUse) {
        $arParameter['NAME'] = Loc::getMessage('C_MAIN_SLIDER_TEMPLATE_3_PRODUCT_TITLE').'. '.$arParameter['NAME'];

        if (ArrayHelper::isIn($sKey, $arProductParametersUse))
            return true;

        return false;
    },
    Component::PARAMETERS_MODE_BOTH
));

unset($sComponent, $sTemplate, $sPrefix, $arProductParametersUse);