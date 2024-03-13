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
    if (Properties::get('mobile-panel-hidden'))
        $arParams['PANEL_FIXED'] = 'N';
    else
        $arParams['PANEL_FIXED'] = 'Y';
}