<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (!defined('EDITOR')) {
    $arSettings = [
        'LIST' => [
            'TEMPLATE' => Properties::get('sections-collections-template')
        ]
    ];

    switch ($arSettings['LIST']['TEMPLATE']) {
        case 'tile.1': {
            $arParams['LIST_TEMPLATE'] = 'tile.1';
            break;
        }
        case 'tile.2': {
            $arParams['LIST_TEMPLATE'] = 'tile.2';
            break;
        }
        case 'list.1': {
            $arParams['LIST_TEMPLATE'] = 'list.1';
            break;
        }
    }

    if (Properties::get('template-images-lazyload-use')) {
        $arParams['LIST_LAZYLOAD_USE'] = 'Y';
        $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
    } else {
        $arParams['LIST_LAZYLOAD_USE'] = 'N';
        $arParams['DETAIL_LAZYLOAD_USE'] = 'N';
    }

    $arParams['DETAIL_PRODUCTS_SHOW'] = Properties::get('sections-collections-products-show') ? 'Y' : 'N';
    $arParams['DETAIL_PRODUCTS_IMAGE_ASPECT_RATIO'] = Properties::get('catalog-elements-tile-image-aspect-ratio');
    $arParams['DETAIL_QUICK_VIEW_USE'] = Properties::get('catalog-quick-view-use') ? 'Y' : 'N';
    $arParams['DETAIL_QUICK_VIEW_DETAIL'] = Properties::get('catalog-quick-view-detail') ? 'Y' : 'N';

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