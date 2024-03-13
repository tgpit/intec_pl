<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\GroupLangTable;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $ARTemplateParameters
 * @var array $arCurrentValues
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

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $arPrices = Arrays::from(GroupTable::getList()->fetchAll());
    $arPricesLanguage = Arrays::from(GroupLangTable::getList([
        'filter' => [
            'LANG' => LANGUAGE_ID
        ]
    ])->fetchAll())->indexBy('CATALOG_GROUP_ID');

    $arTemplateParameters['LIST_SORT_PRICE'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_SORT_PRICE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPrices->asArray(function($iIndex, $arPrice) use (&$arPricesLanguage) {
            $sName = $arPricesLanguage->get($arPrice['ID']);

            if (!empty($sName))
                $sName = $sName['NAME'];

            if (empty($sName))
                $sName = $arPrice['NAME'];

            return [
                'key' => $arPrice['ID'],
                'value' => '['.$arPrice['ID'].'] '.$sName
            ];
        })
    ];

    unset($arPricesLanguage);
    unset($arPrices);
} else if (Loader::includeModule('intec.startshop')) {
    $arPrices = Arrays::fromDBResult(CStartShopPrice::GetList([], [
        'ACTIVE' => 'Y'
    ]));

    $arTemplateParameters['LIST_SORT_PRICE'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_SORT_PRICE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPrices->asArray(function($iIndex, $arPrice) {
            $sName = ArrayHelper::getValue($arPrice, ['LANG', LANGUAGE_ID, 'NAME']);

            if (empty($sName))
                $sName = $arPrice['CODE'];

            return [
                'key' => $arPrice['ID'],
                'value' => '['.$arPrice['ID'].'] '.$sName
            ];
        })
    ];

    unset($arPrices);
}

$sPrefix = 'PRODUCTS_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'catalog.'.$sTemplate;

$arTemplateParameters['LIST_VIEW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'text' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_VIEW_TEXT'),
        'list' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_VIEW_LIST'),
        'tile' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_VIEW_TILE')
    ]
];

$sPrefix = 'LIST_GENERAL_';

$arUsedParameters = [
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
    'HIDE_NOT_AVAILABLE'
];

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
    $sComponent,
    $sTemplate,
    $siteTemplate,
    $arCurrentValues,
    $sPrefix,
    function ($sKey, &$arParameter) use (&$sView, &$arUsedParameters) {
        if (ArrayHelper::isIn($sKey, $arUsedParameters)) {
            $arParameter['PARENT'] = 'LIST_SETTINGS';
            $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS').'. '.$arParameter['NAME'];

            return true;
        }

        return false;
    },
    Component::PARAMETERS_MODE_COMPONENT
));

foreach (['TEXT', 'LIST', 'TILE'] as $sView) {
    $sPrefix = 'LIST_'.$sView.'_';
    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
    $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

    $arCurrentValuesProducts = $arCurrentValues;
    $arCurrentValuesProducts['IBLOCK_ID'] = $arCurrentValues['PRODUCTS_IBLOCK_ID'];
    $arCurrentValuesProducts['IBLOCK_TYPE'] = $arCurrentValues['PRODUCTS_IBLOCK_TYPE'];

    if (!empty($sTemplate))
        $sTemplate = 'catalog.'.$sTemplate;

    $arTemplateParameters[$sPrefix.'TEMPLATE'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_'.$sPrefix.'TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($sTemplate)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValuesProducts,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$sView) {
                if (StringHelper::startsWith($sKey, 'QUICK_VIEW_') && $sKey !== 'QUICK_VIEW_USE' && $sKey !== 'QUICK_VIEW_DETAIL')
                    return false;

                $arParameter['PARENT'] = 'LIST_SETTINGS';
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LIST_'.$sView).'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }
}

include(__DIR__.'/quick.view.php');