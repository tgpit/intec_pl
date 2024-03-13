<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (!defined('EDITOR')) {
    $arSettings = [
        'LIST' => [
            'TEMPLATE' => Properties::get('sections-images-template')
        ]
    ];

    if (Properties::get('template-images-lazyload-use')) {
        $arParams['LIST_LAZYLOAD_USE'] = 'Y';
        $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
    }

    if (Properties::get('basket-use')) {
        $arParams['DETAIL_PRODUCTS_COMPARE_ACTION'] = 'buy';
        $arParams['DETAIL_PRODUCTS_ACTION'] = 'buy';
        $arParams['DETAIL_PRODUCTS_QUICK_VIEW_ACTION'] = 'buy';
    } else {
        $arParams['DETAIL_PRODUCTS_COMPARE_ACTION'] = 'detail';
        $arParams['DETAIL_PRODUCTS_ACTION'] = 'order';
        $arParams['DETAIL_PRODUCTS_QUICK_VIEW_ACTION'] = 'detail';
    }
}