<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

if ($arCurrentValues['TIPS_USE'] !== 'Y' || (
    $arCurrentValues['TIPS_VIEW'] !== 'list.2' &&
    $arCurrentValues['TIPS_VIEW'] !== 'list.3')
) return;

$sComponent = 'bitrix:catalog.section';
$sTemplate = $arCurrentValues['TIPS_VIEW'] === 'list.2' ? 'products.small.9' : 'products.small.10';

if (empty($sTemplate))
    return;

$sPrefix = 'PRODUCTS_';

$arTemplateParameters['PRODUCTS_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_PRODUCTS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRODUCTS_SHOW'] === 'Y') {
    $arParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'ELEMENT_SORT_FIELD',
        'ELEMENT_SORT_ORDER',
        'ELEMENT_SORT_FIELD2',
        'ELEMENT_SORT_ORDER2',
        'PRICE_CODE',
        'BASKET_URL',
        'CONVERT_CURRENCY',
        'CURRENCY_ID',
        'DISPLAY_COMPARE',
        'COMPARE_PATH'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) use (&$arParameters) {
            if (!ArrayHelper::isIn($sKey, $arParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_SEARCH_TITLE_POPUP_1_PRODUCTS') . ' ' . $arParameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_COMPONENT
    ));

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) {
            if ($sKey === 'COLUMNS')
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_SEARCH_TITLE_POPUP_1_PRODUCTS') . ' ' . $arParameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));
}

unset($sTemplate, $sComponent, $sPrefix);