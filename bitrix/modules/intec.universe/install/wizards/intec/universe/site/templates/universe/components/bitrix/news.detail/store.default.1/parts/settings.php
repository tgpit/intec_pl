<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\template\Properties;

if (!defined('EDITOR')) {
    $arParams['MAP_VENDOR'] = Properties::get('base-map-vendor');
}