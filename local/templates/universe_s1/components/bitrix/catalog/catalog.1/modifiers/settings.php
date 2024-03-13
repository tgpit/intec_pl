<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

$arParams['DETAIL_GALLERY_ZOOM'] = ArrayHelper::isIn(
    'zoom',
    Properties::get('catalog-detail-gallery-modes')
) ? 'Y' : 'N';

$arParams['DETAIL_GALLERY_POPUP'] = ArrayHelper::isIn(
    'popup',
    Properties::get('catalog-detail-gallery-modes')
) ? 'Y' : 'N';

$arParams['DETAIL_GALLERY_ACTION'] = ArrayHelper::isIn(
    'popup',
    Properties::get('catalog-detail-gallery-modes')
) ? 'popup' : 'none';

$arParams['DETAIL_SKU_VIEW'] = Properties::get('catalog-detail-sku-view');
$arParams['LIST_VIEW'] = Properties::get('catalog-products-view-mode');
$arParams['MENU_VIEW'] = Properties::get('catalog-menu-view');
$arParams['MENU_MAIN_VIEW'] = Properties::get('catalog-menu-root-view');
$arParams['DETAIL_PANEL_SHOW'] = Properties::get('catalog-detail-panel-show') ? 'Y' : 'N';
$arParams['DETAIL_PANEL_MOBILE_SHOW'] = Properties::get('mobile-catalog-detail-panel-show') ? 'Y' : 'N';
$arParams['DETAIL_INFORMATION_PAYMENT_SHOW'] = Properties::get('catalog-detail-information-payment-show') ? 'Y' : 'N';
$arParams['DETAIL_INFORMATION_SHIPMENT_SHOW'] = Properties::get('catalog-detail-information-shipment-show') ? 'Y' : 'N';
$arParams['DETAIL_SIZES_SHOW'] = Properties::get('catalog-detail-sizes-show') ? 'Y' : 'N';
$arParams['DETAIL_STOREMAP_MAP_TYPE'] = Properties::get('base-map-vendor');
$arParams['SECTIONS_ROOT_MENU_SHOW'] = Properties::get('catalog-root-menu-show') ? 'Y' : 'N';
$arParams['SECTIONS_CHILDREN_MENU_SHOW'] = Properties::get('catalog-sections-menu-show') ? 'Y' : 'N';
$arParams['DETAIL_MENU_SHOW'] = Properties::get('catalog-detail-menu-show') ? 'Y' : 'N';
$arParams['SECTIONS_ROOT_TEMPLATE'] = Properties::get('catalog-root-template');
$arParams['SECTION_TOP_DEPTH'] = 2;
$arParams['ROOT_LAYOUT'] = Properties::get('catalog-root-layout');
$arParams['SECTIONS_LAYOUT'] = Properties::get('catalog-sections-layout');

if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.1') {
    $arParams['SECTIONS_ROOT_BORDERS'] = 'Y';
    $arParams['SECTIONS_ROOT_COLUMNS'] = 3;
    $arParams['SECTIONS_ROOT_CHILDREN_SHOW'] = 'Y';
    $arParams['SECTIONS_ROOT_CHILDREN_COUNT'] = 0;
    $arParams['SECTIONS_ROOT_PICTURE_SHOW'] = 'Y';
    $arParams['SECTIONS_ROOT_PICTURE_SIZE'] = 'large';
    $arParams['SECTIONS_ROOT_DESCRIPTION_SHOW'] = 'Y';
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'list.1') {
    $arParams['SECTIONS_ROOT_TEMPLATE'] = 'list.1';
    $arParams['SECTIONS_ROOT_BORDERS'] = 'Y';
    $arParams['SECTIONS_ROOT_COLUMNS'] = 1;
    $arParams['SECTIONS_ROOT_CHILDREN_SHOW'] = 'Y';
    $arParams['SECTIONS_ROOT_CHILDREN_COUNT'] = 0;
    $arParams['SECTIONS_ROOT_PICTURE_SHOW'] = 'Y';
    $arParams['SECTIONS_ROOT_PICTURE_SIZE'] = 'small';
    $arParams['SECTIONS_ROOT_DESCRIPTION_SHOW'] = 'Y';
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.2') {
    $arParams['SECTIONS_ROOT_BORDERS'] = 'Y';
    $arParams['SECTIONS_ROOT_COLUMNS'] = 5;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.3') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 4;
    $arParams['SECTIONS_ROOT_CHILDREN_SHOW'] = 'Y';
    $arParams['SECTIONS_ROOT_CHILDREN_COUNT'] = 0;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.4') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 4;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.5') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 3;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'slider.1') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 6;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.6') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 5;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.7') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 2;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.8') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 4;
} else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.9') {
    $arParams['SECTIONS_ROOT_COLUMNS'] = 3;
}

$arParams['SECTIONS_CHILDREN_TEMPLATE'] = Properties::get('catalog-sections-template');

if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.1') {
    $arParams['SECTIONS_CHILDREN_BORDERS'] = 'Y';
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 2;
    $arParams['SECTIONS_CHILDREN_CHILDREN_SHOW'] = 'Y';
    $arParams['SECTIONS_CHILDREN_CHILDREN_COUNT'] = 0;
    $arParams['SECTIONS_CHILDREN_PICTURE_SHOW'] = 'Y';
    $arParams['SECTIONS_CHILDREN_PICTURE_SIZE'] = 'small';
    $arParams['SECTIONS_CHILDREN_DESCRIPTION_SHOW'] = 'Y';
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'list.1') {
    $arParams['SECTIONS_CHILDREN_TEMPLATE'] = 'list.1';
    $arParams['SECTIONS_CHILDREN_BORDERS'] = 'Y';
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 1;
    $arParams['SECTIONS_CHILDREN_CHILDREN_SHOW'] = 'Y';
    $arParams['SECTIONS_CHILDREN_CHILDREN_COUNT'] = 0;
    $arParams['SECTIONS_CHILDREN_PICTURE_SHOW'] = 'Y';
    $arParams['SECTIONS_CHILDREN_PICTURE_SIZE'] = 'small';
    $arParams['SECTIONS_CHILDREN_DESCRIPTION_SHOW'] = 'Y';
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.2') {
    $arParams['SECTIONS_CHILDREN_BORDERS'] = 'Y';
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 4;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.3') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 3;
    $arParams['SECTIONS_CHILDREN_CHILDREN_SHOW'] = 'N';
    $arParams['SECTIONS_CHILDREN_CHILDREN_COUNT'] = 0;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.4') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 3;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.5') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 2;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'slider.1') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 6;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.6') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 5;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.7') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 2;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.8') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 4;
} else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.9') {
    $arParams['SECTIONS_CHILDREN_COLUMNS'] = 3;
}

$arParams['FILTER_AJAX'] = Properties::get('catalog-filter-ajax') ? 'Y' : 'N';
$arParams['FILTER_TEMPLATE'] = Properties::get('catalog-filter-template');

switch ($arParams['FILTER_TEMPLATE']) {
    case 'vertical.1':
        $arParams['FILTER_TYPE'] = 'vertical';
        $arParams['FILTER_TEMPLATE'] = 1;
        break;
    case 'vertical.2':
        $arParams['FILTER_TYPE'] = 'vertical';
        $arParams['FILTER_TEMPLATE'] = 2;
        break;
    case 'horizontal.1':
        $arParams['FILTER_TYPE'] = 'horizontal';
        $arParams['FILTER_TEMPLATE'] = 1;
        break;
    case 'horizontal.2':
        $arParams['FILTER_TYPE'] = 'horizontal';
        $arParams['FILTER_TEMPLATE'] = 2;
}

$arParams['LIST_TEXT_TEMPLATE'] = Properties::get('catalog-elements-text-template');
$arParams['LIST_LIST_TEMPLATE'] = Properties::get('catalog-elements-list-template');
$arParams['LIST_TILE_TEMPLATE'] = Properties::get('catalog-elements-tile-template');
$arParams['LIST_TILE_IMAGE_ASPECT_RATIO'] = Properties::get('catalog-elements-tile-image-aspect-ratio');
$arParams['LIST_TILE_COLUMNS'] = 3;
$arParams['LIST_TILE_COLUMNS_MOBILE'] = Properties::get('mobile-catalog-elements-tile-columns');

switch ($arParams['LIST_TILE_TEMPLATE']) {
    case 'tile.1': break;
    case 'tile.1.columns.4':
        $arParams['LIST_TILE_TEMPLATE'] = 'tile.1';
        $arParams['LIST_TILE_COLUMNS'] = 4;
        break;
    case 'tile.2': break;
    case 'tile.2.columns.4':
        $arParams['LIST_TILE_TEMPLATE'] = 'tile.2';
        $arParams['LIST_TILE_COLUMNS'] = 4;
        break;
    case 'tile.3': break;
    case 'tile.4': break;
    case 'tile.4.columns.4':
        $arParams['LIST_TILE_TEMPLATE'] = 'tile.4';
        $arParams['LIST_TILE_COLUMNS'] = 4;
        break;
}

$arParams['DETAIL_TEMPLATE'] = Properties::get('catalog-detail-template');

switch ($arParams['DETAIL_TEMPLATE']) {
    case 'default.1.wide':
        $arParams['DETAIL_TEMPLATE'] = 'default.1';
        $arParams['DETAIL_VIEW'] = 'wide';
        break;
    case 'default.1.tabs.top':
        $arParams['DETAIL_TEMPLATE'] = 'default.1';
        $arParams['DETAIL_VIEW'] = 'tabs';
        $arParams['DETAIL_VIEW_TABS_POSITION'] = 'top';
        break;
    case 'default.1.tabs.right':
        $arParams['DETAIL_TEMPLATE'] = 'default.1';
        $arParams['DETAIL_VIEW'] = 'tabs';
        $arParams['DETAIL_VIEW_TABS_POSITION'] = 'right';
        break;
    case 'default.2.wide':
        $arParams['DETAIL_TEMPLATE'] = 'default.2';
        $arParams['DETAIL_VIEW'] = 'wide';
        break;
    case 'default.2.narrow':
        $arParams['DETAIL_TEMPLATE'] = 'default.2';
        $arParams['DETAIL_VIEW'] = 'narrow';
        break;
    case 'default.2.tabs.top':
        $arParams['DETAIL_TEMPLATE'] = 'default.2';
        $arParams['DETAIL_VIEW'] = 'tabs';
        $arParams['DETAIL_VIEW_TABS_POSITION'] = 'top';
        break;
    case 'default.3.wide':
        $arParams['DETAIL_TEMPLATE'] = 'default.3';
        break;
    case 'default.4.wide':
        $arParams['DETAIL_TEMPLATE'] = 'default.5';
        $arParams['DETAIL_SECTIONS'] = 'N';
        break;
    case 'default.4.tabs.top':
        $arParams['DETAIL_TEMPLATE'] = 'default.5';
        $arParams['DETAIL_SECTIONS'] = 'Y';
        break;
}

switch ($arParams['DETAIL_TEMPLATE']) {
    case 'default.1':
    case 'default.2': {
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_TEMPLATE'] = '1';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_BORDERS'] = 'Y';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_COLUMNS'] = '4';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_POSITION'] = 'left';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_SIZE'] = 'small';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_SLIDER_USE'] = 'N';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_TEMPLATE'] = '1';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_BORDERS'] = 'Y';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_COLUMNS'] = '4';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_POSITION'] = 'left';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_SIZE'] = 'small';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_SLIDER_USE'] = 'N';
        break;
    }
    case 'default.3': {
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_TEMPLATE'] = '2';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_BORDERS'] = 'Y';
        $arParams['DETAIL_PRODUCTS_ASSOCIATED_NAME_ALIGN'] = 'left';
		$arParams['DETAIL_PRODUCTS_ASSOCIATED_PRICE_ALIGN'] = 'left';
		$arParams['DETAIL_PRODUCTS_ASSOCIATED_ACTION'] = 'buy';
		$arParams['DETAIL_PRODUCTS_ASSOCIATED_COUNTER_SHOW'] = 'Y';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_TEMPLATE'] = '2';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_BORDERS'] = 'Y';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_NAME_ALIGN'] = 'left';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_PRICE_ALIGN'] = 'left';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_ACTION'] = 'buy';
        $arParams['DETAIL_PRODUCTS_RECOMMENDED_COUNTER_SHOW'] = 'Y';
        break;
    }
}

$arParams['QUICK_VIEW_USE'] = Properties::get('catalog-quick-view-use') ? 'Y' : 'N';
$arParams['QUICK_VIEW_DETAIL'] = Properties::get('catalog-quick-view-detail') ? 'Y' : 'N';
$arParams['QUICK_VIEW_TEMPLATE'] = Properties::get('catalog-quick-view-template');

if (Properties::get('basket-use')) {
    $arParams['COMPARE_ACTION'] = 'buy';
    $arParams['LIST_TEXT_ACTION'] = 'buy';
    $arParams['LIST_LIST_ACTION'] = 'buy';
    $arParams['LIST_TILE_ACTION'] = 'buy';
    $arParams['DETAIL_ACTION'] = 'buy';
    $arParams['QUICK_VIEW_ACTION'] = 'buy';
} else {
    $arParams['COMPARE_ACTION'] = 'detail';
    $arParams['LIST_TEXT_ACTION'] = 'order';
    $arParams['LIST_LIST_ACTION'] = 'order';
    $arParams['LIST_TILE_ACTION'] = 'order';
    $arParams['DETAIL_ACTION'] = 'order';
    $arParams['QUICK_VIEW_ACTION'] = 'detail';
}

$arParams['DELAY_USE'] = (!Properties::get('basket-delay-use') || !Properties::get('basket-use')) ? 'N' : 'Y';

$arParams['USE_COMPARE'] = (!Properties::get('basket-compare-use')) ? 'N' : 'Y';

if (Properties::get('template-images-lazyload-use')) {
    $arParams['ORDER_FAST_LAZYLOAD_USE'] = 'Y';
    $arParams['COMPARE_LAZYLOAD_USE'] = 'Y';
    $arParams['SECTIONS_ROOT_LAZYLOAD_USE'] = 'Y';
    $arParams['SECTIONS_CHILDREN_LAZYLOAD_USE'] = 'Y';
    $arParams['LIST_TEXT_LAZYLOAD_USE'] = 'Y';
    $arParams['LIST_LIST_LAZYLOAD_USE'] = 'Y';
    $arParams['LIST_TILE_LAZYLOAD_USE'] = 'Y';
    $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
    $arParams['QUICK_VIEW_LAZYLOAD_USE'] = 'Y';
}

if ($arParams['ROOT_LAYOUT'] === '2') {
    if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.1') {
        $arParams['SECTIONS_ROOT_COLUMNS'] = 3;
    } else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'list.1') {
        $arParams['SECTIONS_ROOT_COLUMNS'] = 3;
    } else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.2') {
        $arParams['SECTIONS_ROOT_COLUMNS'] = 5;
    } else if ($arParams['SECTIONS_ROOT_TEMPLATE'] === 'tile.3') {
        $arParams['SECTIONS_ROOT_COLUMNS'] = 4;
    }
}

if ($arParams['SECTIONS_LAYOUT'] === '2') {
    if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.1') {
        $arParams['SECTIONS_CHILDREN_COLUMNS'] = 4;
    } else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'list.1') {
        $arParams['SECTIONS_CHILDREN_COLUMNS'] = 3;
    } else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.2') {
        $arParams['SECTIONS_CHILDREN_COLUMNS'] = 5;
    } else if ($arParams['SECTIONS_CHILDREN_TEMPLATE'] === 'tile.3') {
        $arParams['SECTIONS_CHILDREN_COLUMNS'] = 4;
    }
}

if (Properties::get('catalog-recalculation-price-use')) {
    $arParams['LIST_TEXT_RECALCULATION_PRICES_USE'] = 'Y';
    $arParams['LIST_LIST_RECALCULATION_PRICES_USE'] = 'Y';
    $arParams['LIST_TILE_RECALCULATION_PRICES_USE'] = 'Y';
    $arParams['DETAIL_RECALCULATION_PRICES_USE'] = 'Y';
} else {
    $arParams['LIST_TEXT_RECALCULATION_PRICES_USE'] = 'N';
    $arParams['LIST_LIST_RECALCULATION_PRICES_USE'] = 'N';
    $arParams['LIST_TILE_RECALCULATION_PRICES_USE'] = 'N';
    $arParams['DETAIL_RECALCULATION_PRICES_USE'] = 'N';
}

$arParams['DETAIL_DELIVERY_CALCULATION_USE'] = Properties::get('catalog-detail-delivery-calculation-use') ? 'Y' : 'N';
$arParams['DETAIL_FORM_CHEAPER_SHOW'] = Properties::get('catalog-detail-form-cheaper-show') ? 'Y' : 'N';

$arParams['ADDITIONAL_ARTICLES_SHOW'] = Properties::get('catalog-section-articles-show') ? 'Y' : 'N';
$arParams['DETAIL_ARTICLES_SHOW'] = Properties::get('catalog-detail-articles-show') ? 'Y' : 'N';

$arParams['USE_GIFTS_DETAIL'] = Properties::get('catalog-detail-gift-show') ? 'Y' : 'N';

switch (Properties::get('catalog-detail-gift-template')) {
    case 'tile':
        $arParams['DETAIL_GIFTS_VIEW'] = '2';
        break;
    case 'block':
        $arParams['DETAIL_GIFTS_VIEW'] = '1';
        break;
    case 'list':
        $arParams['DETAIL_GIFTS_VIEW'] = '3';
        break;
}

$arParams['USE_GIFTS_SECTION'] = Properties::get('catalog-list-gift-show') ? 'Y' : 'N';

if (Properties::get('catalog-list-timer-show')) {
    $arParams['LIST_TEXT_SECTION_TIMER_SHOW'] = 'Y';
    $arParams['LIST_LIST_SECTION_TIMER_SHOW'] = 'Y';
    $arParams['LIST_TILE_SECTION_TIMER_SHOW'] = 'Y';
} else {
    $arParams['LIST_TEXT_SECTION_TIMER_SHOW'] = 'N';
    $arParams['LIST_LIST_SECTION_TIMER_SHOW'] = 'N';
    $arParams['LIST_TILE_SECTION_TIMER_SHOW'] = 'N';
}

$arParams['QUICK_VIEW_TIMER_SHOW'] = Properties::get('catalog-quick-view-timer-show') ? 'Y' : 'N';

$arParams['DETAIL_TIMER_SHOW'] = Properties::get('catalog-detail-timer-show') ? 'Y' : 'N';

switch (Properties::get('catalog-menu-submenu-template')) {
    case 'type.1':
        $arParams['MENU_TEMPLATE'] = '1';
        break;
    case 'type.2':
        $arParams['MENU_TEMPLATE'] = '3';
        break;
}

$arParams['INTEREST_PRODUCTS_SHOW'] = Properties::get('catalog-list-interest-products-show') ? 'Y' : 'N';
$arParams['DETAIL_PRICE_CREDIT_SHOW'] = Properties::get('catalog-detail-price-credit-show') ? 'Y' : 'N';
$arParams['DETAIL_MAIN_VIEW'] = Properties::get('catalog-detail-main-template');
$arParams['QUICK_VIEW_SLIDE_USE'] = Properties::get('catalog-quick-view-slider-use') ? 'Y' : 'N';
