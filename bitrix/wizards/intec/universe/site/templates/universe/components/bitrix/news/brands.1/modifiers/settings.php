<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (!defined('EDITOR')) {
    $arSettings = [
        'LIST' => [
            'TEMPLATE' => Properties::get('sections-brands-template')
        ]
    ];

    switch ($arSettings['LIST']['TEMPLATE']) {
        case 'tiles.1': {
            $arParams['LIST_VIEW'] = 'tiles.1';
            break;
        }
        case 'tiles.2': {
            $arParams['LIST_VIEW'] = 'tiles.2';
            break;
        }
        case 'list.1': {
            $arParams['LIST_VIEW'] = 'list.1';
            break;
        }
    }

    if (Properties::get('template-images-lazyload-use')) {
        $arParams['LIST_LAZYLOAD_USE'] = 'Y';
        $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
    }

    $arParams['DETAIL_FILTER_USE'] = Properties::get('sections-brands-filter-use') ? 'Y' : 'N';
    $arParams['DETAIL_SECTIONS_SHOW'] = Properties::get('sections-brands-sections-show') ? 'Y' : 'N';
    $arParams['DETAIL_PRODUCTS_SHOW'] = Properties::get('sections-brands-products-show') ? 'Y' : 'N';
    $arParams['DETAIL_PRODUCTS_IMAGE_ASPECT_RATIO'] = Properties::get('catalog-elements-tile-image-aspect-ratio');
    $arParams['DETAIL_PRODUCTS_COMPARE_USE'] = (!Properties::get('basket-compare-use')) ? 'N' : 'Y';
    $arParams['DETAIL_PRODUCTS_DELAY_USE'] = (!Properties::get('basket-delay-use') || !Properties::get('basket-use')) ? 'N' : 'Y';
    $arParams['DETAIL_PRODUCTS_QUICK_VIEW_USE'] = Properties::get('catalog-quick-view-use') ? 'Y' : 'N';
    $arParams['DETAIL_PRODUCTS_QUICK_VIEW_DETAIL'] = Properties::get('catalog-quick-view-detail') ? 'Y' : 'N';

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