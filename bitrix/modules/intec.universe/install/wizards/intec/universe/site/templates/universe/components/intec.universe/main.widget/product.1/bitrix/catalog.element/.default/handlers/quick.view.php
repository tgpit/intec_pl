<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

return function (&$parameters) {
    $result = [];
    $prefix = 'QUICK_VIEW_';
    $length = StringHelper::length($prefix);
    $excluded = [
        'USE',
        'TEMPLATE',
        'PROPERTY_MARKS_HIT',
        'PROPERTY_MARKS_NEW',
        'PROPERTY_MARKS_RECOMMEND',
        'PROPERTY_MARKS_SHARE'
    ];

    foreach ($parameters as $key => $value) {
        if (StringHelper::startsWith($key, 'SECTION_TIMER_') && $key!== 'SECTION_TIMER_SHOW') {
            $key = StringHelper::cut(
                $key,
                StringHelper::length('SECTION_')
            );
            $result[$key] = $value;

            continue;
        }

        if (!StringHelper::startsWith($key, $prefix))
            continue;

        $key = StringHelper::cut($key, $length);

        if (!ArrayHelper::isIn($key, $excluded))
            $result[$key] = $value;
    }

    return [
        'component' => 'bitrix:catalog.element',
        'template' => 'quick.view.'.(!empty($parameters['QUICK_VIEW_TEMPLATE']) ? $parameters['QUICK_VIEW_TEMPLATE'] : '1'),
        'parameters' => ArrayHelper::merge($result, [
            'IBLOCK_TYPE' => $parameters['IBLOCK_TYPE'],
            'IBLOCK_ID' => $parameters['IBLOCK_ID'],
            'ELEMENT_ID' => $parameters['ELEMENT_ID'],
            'SECTION_ID' => $parameters['SECTION_ID'],
            'SETTINGS_USE' => $parameters['LAZYLOAD_USE'],
            'LAZYLOAD_USE' => $parameters['LAZYLOAD_USE'],
            'PRICE_CODE' => $parameters['PRICE_CODE'],
            'CONVERT_CURRENCY' => $parameters['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $parameters['CURRENCY_ID'],
            'PRICE_VAT_INCLUDE' => $parameters['PRICE_VAT_INCLUDE'],
            'BASKET_URL' => $parameters['BASKET_URL'],
            'COMPARE_USE' => $parameters['COMPARE_USE'],
            'COMPARE_CODE' => $parameters['COMPARE_CODE'],
            'USE_COMPARE' => $parameters['COMPARE_USE'],
            'COMPARE_NAME' => $parameters['COMPARE_CODE'],
            'DELAY_USE' => $parameters['DELAY_USE'],
            'PROPERTY_MARKS_HIT' => $parameters['PROPERTY_MARKS_HIT'],
            'PROPERTY_MARKS_NEW' => $parameters['PROPERTY_MARKS_NEW'],
            'PROPERTY_MARKS_RECOMMEND' => $parameters['PROPERTY_MARKS_RECOMMEND'],
            'PROPERTY_MARKS_SHARE' => $parameters['PROPERTY_MARKS_SHARE'],
            'PROPERTY_PICTURES' => $parameters['PROPERTY_PICTURES'],
            'PROPERTY_ARTICLE' => $parameters['PROPERTY_ARTICLE'],
            'MARKS_SHOW' => $parameters['MARKS_SHOW'],
            'ARTICLE_SHOW' => $parameters['ARTICLE_SHOW'],
            'COUNTER_SHOW' => $parameters['COUNTER_SHOW'],
            'QUANTITY_SHOW' => $parameters['QUANTITY_SHOW'],
            'QUANTITY_MODE' => $parameters['QUANTITY_MODE'],
            'QUANTITY_BOUNDS_FEW' => $parameters['QUANTITY_BOUNDS_FEW'],
            'QUANTITY_BOUNDS_MANY' => $parameters['QUANTITY_BOUNDS_MANY'],
            'PRICE_RANGE_SHOW' => $parameters['PRICE_RANGE_SHOW'],
            'PRICE_DISCOUNT_SHOW' => $parameters['PRICE_DISCOUNT_SHOW'],
            'PRICE_DISCOUNT_PERCENT' => $parameters['PRICE_DISCOUNT_PERCENT'],
            'PRICE_DISCOUNT_ECONOMY' => $parameters['PRICE_DISCOUNT_ECONOMY'],
            'VOTE_USE' => $parameters['VOTE_USE'],
            'VOTE_MODE' => $parameters['VOTE_MODE'],
            'SECTION_URL' => $parameters['SECTION_URL'],
            'DETAIL_URL' => $parameters['DETAIL_URL'],
            'SHOW_DEACTIVATED' => 'N',
            'USE_PRICE_COUNT' => 'N',
            'PRICE_VAT_SHOW_VALUE' => 'N',
            'SET_TITLE' => 'N',
            'SET_CANONICAL_URL' => 'N',
            'SET_BROWSER_TITLE' => 'N',
            'SET_META_KEYWORDS' => 'N',
            'SET_META_DESCRIPTION' => 'N',
            'SET_LAST_MODIFIED' => 'N',
            'USE_MAIN_ELEMENT_SECTION' => 'N',
            'STRICT_SECTION_CHECK' => 'N',
            'ADD_SECTIONS_CHAIN' => 'N',
            'ADD_ELEMENT_CHAIN' => 'N',
            'SHOW_SKU_DESCRIPTION' => 'N',
            'COMPATIBLE_MODE' => 'Y',
            'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
            'SET_VIEWED_IN_COMPONENT' => 'N',
            'TIMER_SHOW' => $parameters[$prefix.'TIMER_SHOW']
        ]),
        'settings' => [
            'parameters' => [
                'className' => 'popup-window-quick-view',
                'width' => null
            ]
        ]
    ];
};