<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global $APPLICATION
 */

if (!defined('EDITOR')) {
    $arSettings = [
        'PROFILE' => $arParams['SETTINGS_PROFILE'],
        'LIST' => [
            'TEMPLATE' => null
        ]
    ];

    if (Properties::get('template-images-lazyload-use'))
        $arParams['LAZYLOAD_USE'] = 'Y';

    switch ($arParams['SETTINGS_PROFILE']) {
        case 'news': {
            $arSettings['LIST']['TEMPLATE'] = Properties::get('sections-news-template');
            break;
        }
        case 'articles': {
            $arSettings['LIST']['TEMPLATE'] = Properties::get('sections-articles-template');
            break;
        }
        case 'blog': {
            $arSettings['LIST']['TEMPLATE'] = Properties::get('sections-blog-template');
            break;
        }
    }

    switch ($arSettings['LIST']['TEMPLATE']) {
        case 'blocks.1': {
            $arParams['LIST_TEMPLATE'] = 'tile.2';

            if ($arSettings['PROFILE'] === 'blog')
                $arParams['LIST_TEMPLATE'] = 'tile.1';

            $arParams['LIST_COLUMNS'] = 3;
            $arParams['LIST_VIEW'] = 'big';
            $arParams['LIST_DATE_SHOW'] = 'Y';
            $arParams['LIST_PREVIEW_SHOW'] = 'Y';
            $arParams['LIST_TAGS_SHOW'] = 'Y';
            break;
        }
        case 'list.1': {
            $arParams['LIST_TEMPLATE'] = 'list.1';
            $arParams['LIST_DELIMITER_SHOW'] = 'Y';
            $arParams['LIST_IMAGE_SHOW'] = 'Y';
            $arParams['LIST_IMAGE_VIEW'] = 'default';
            $arParams['LIST_DATE_SHOW'] = 'Y';
            $arParams['LIST_PREVIEW_SHOW'] = 'Y';
            $arParams['LIST_TAGS_SHOW'] = 'Y';
            break;
        }
        case 'tiles.1': {
            $arParams['LIST_TEMPLATE'] = 'tile.3';
            $arParams['LIST_ROUNDED'] = 'N';
            $arParams['LIST_PREVIEW_SHOW'] = 'Y';
            $arParams['LIST_DATE_SHOW'] = 'Y';
            $arParams['LIST_TAGS_SHOW'] = 'Y';
            break;
        }
    }

    $arParams['DETAIL_ADDITIONAL_PRODUCTS_CATEGORIES_SHOW'] = Properties::get('sections-articles-products-categories-show') ? 'Y' : 'N';
    $arParams['DETAIL_ADDITIONAL_PRODUCTS_SHOW'] = Properties::get('sections-articles-products-show') ? 'Y' : 'N';
    $arParams['DETAIL_ADDITIONAL_PRODUCTS_DELAY_USE'] = (!Properties::get('basket-delay-use') || !Properties::get('basket-use')) ? 'N' : 'Y';

    if (Properties::get('basket-use')) {
        $arParams['DETAIL_ADDITIONAL_PRODUCTS_COMPARE_ACTION'] = 'buy';
        $arParams['DETAIL_ADDITIONAL_PRODUCTS_ACTION'] = 'buy';
        $arParams['DETAIL_ADDITIONAL_PRODUCTS_QUICK_VIEW_ACTION'] = 'buy';
    } else {
        $arParams['DETAIL_ADDITIONAL_PRODUCTS_COMPARE_ACTION'] = 'detail';
        $arParams['DETAIL_ADDITIONAL_PRODUCTS_ACTION'] = 'order';
        $arParams['DETAIL_ADDITIONAL_PRODUCTS_QUICK_VIEW_ACTION'] = 'detail';
    }

    unset($arSettings);
}