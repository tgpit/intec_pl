<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;

/**
 * @var array $arOriginalParams
 */

$arQuickView = [];
$arQuickView['USE'] = $this->arParams['QUICK_VIEW_USE'] === 'Y';
$arQuickView['PREFIX'] = 'QUICK_VIEW_';
$arQuickView['TEMPLATE'] = $this->arParams['QUICK_VIEW_TEMPLATE'];
$arQuickView['PARAMETERS'] = [
    'IBLOCK_TYPE' => $this->arParams['QUICK_VIEW_IBLOCK_TYPE'],
    'IBLOCK_ID' => $this->arParams['QUICK_VIEW_IBLOCK_ID'],
    'SECTION_URL' => '',
    'DETAIL_URL' => '',
    'BASKET_URL' => '',
    'ACTION_VARIABLE' => null,
    'PRODUCT_ID_VARIABLE' => null,
    'SECTION_ID_VARIABLE' => null,
    'PRODUCT_QUANTITY_VARIABLE' => null,
    'PRODUCT_PROPS_VARIABLE' => null,
    'SET_TITLE' => 'N',
    'SET_CANONICAL_URL' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'MESSAGE_404' => null,
    'SET_STATUS_404' => 'N',
    'ADD_PROPERTIES_TO_BASKET' => 'N',
    'PARTIAL_PRODUCT_PROPERTIES' => 'N',
    'LINK_IBLOCK_TYPE' => null,
    'LINK_IBLOCK_ID' => null,
    'LINK_PROPERTY_SID' => null,
    'LINK_ELEMENTS_URL' => null,
    'USE_MAIN_ELEMENT_SECTION' => 'N',
    'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
    'SET_VIEWED_IN_COMPONENT' => 'Y',
    'PRODUCT_DISPLAY_MODE' => 'Y',
    'PRODUCT_PROPERTIES' => '',
    'OFFERS_CART_PROPERTIES' => '',
    'OFFERS_PROPERTY_PICTURE_DIRECTORY' => '',
    'ACTION' => 'detail',
    'OFFER_TREE_PROPS' => $this->arParams['QUICK_VIEW_OFFERS_PROPERTY_CODE']
];

foreach ($this->arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $arQuickView['PREFIX']))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($arQuickView['PREFIX']));
    $arQuickView['PARAMETERS'][$sKey] = $sValue;
}

if (!empty($arQuickView['TEMPLATE'])) {
    $arQuickView['TEMPLATE'] = 'quick.view.'.$arQuickView['TEMPLATE'];
} else {
    $arQuickView['USE'] = false;
}

$dData = function (&$arItem) use (&$arOriginalParams, &$arQuickView) {
    $fHandle = function (&$arItem) use (&$arOriginalParams, &$arQuickView) {
        $arData = [
            'quickView' => [
                'show' => false,
                'template' => null,
                'parameters' => []
            ]
        ];

        if ($arOriginalParams['QUICK_VIEW_USE'] && !empty($arQuickView['TEMPLATE'])) {
            $arData['quickView']['template'] = $arQuickView['TEMPLATE'];
            $arData['quickView']['show'] = true;
        }

        if ($arData['quickView']['show']) {
            $arParameters = $arQuickView['PARAMETERS'];
            $arParameters = ArrayHelper::merge($arParameters, [
                'ELEMENT_ID' => $arItem['ID'],
                'ELEMENT_CODE' => $arItem['CODE'],
                'SECTION_ID' => $arItem['IBLOCK_SECTION_ID'],
                'SECTION_CODE' => null
            ]);
            $arData['quickView']['parameters'] = $arParameters;
        }

        return $arData;
    };

    $arData = $fHandle($arItem);

    return $arData;
};
