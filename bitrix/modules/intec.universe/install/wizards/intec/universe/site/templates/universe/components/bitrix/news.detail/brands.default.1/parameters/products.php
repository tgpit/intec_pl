<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
    <?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\GroupLangTable;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$sComponent = 'bitrix:catalog.section';
$sTemplate = 'catalog.';

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

$sPrefix = 'PRODUCTS_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'catalog.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arUsedParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'PRICE_CODE',
        'BASKET_URL',
        'HIDE_NOT_AVAILABLE_OFFERS',
        'OFFERS_CART_PROPERTIES',
        'OFFERS_PROPERTY_CODE',
        'OFFERS_SORT_FIELD',
        'OFFERS_SORT_ORDER',
        'OFFERS_LIMIT',
        'PAGE_ELEMENT_COUNT',
        'DISPLAY_TOP_PAGER',
        'DISPLAY_BOTTOM_PAGER',
        'PAGER_TITLE',
        'PAGER_SHOW_ALWAYS',
        'PAGER_TEMPLATE',
        'PAGER_SHOW_ALL',
        'CURRENCY_ID',
        'CONVERT_CURRENCY',
        'HIDE_NOT_AVAILABLE',
        'PROPERTY_CODE',
        'QUANTITY_MODE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$arUsedParameters) {
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS').'. '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, $arUsedParameters))
                    return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ),
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS').'. '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'LAZYLOAD_USE',
                    'PROPERTY_STORES_SHOW',
                    'OFFERS_PROPERTY_STORES_SHOW'
                ])) return false;

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        )
    );

    unset($arUsedParameters);
}

if (Loader::includeModule('form')) {
    include(__DIR__.'/base/forms.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/lite/forms.php');
}