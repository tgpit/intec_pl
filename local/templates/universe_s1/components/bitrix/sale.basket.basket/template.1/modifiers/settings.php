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
    $arParams['TOTAL_BLOCK_FIXED_MODE'] = Properties::get('basket-total-fixed') ? 'Y' : 'N';
    $arParams['QUICK_VIEW_USE'] = Properties::get('basket-quick-view-use') ? 'Y' : 'N';
    $arParams['CONFIRM_REMOVE_PRODUCT_USE'] = Properties::get('basket-confirm-remove-product-use') ? 'Y' : 'N';
}