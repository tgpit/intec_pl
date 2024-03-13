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

    $arParams['ACCESSORIES_SECTION_QUICK_VIEW_USE'] = Properties::get('catalog-quick-view-use') ? 'Y' : 'N';
    $arParams['ACCESSORIES_SECTION_QUICK_VIEW_DETAIL'] = Properties::get('catalog-quick-view-detail') ? 'Y' : 'N';
}