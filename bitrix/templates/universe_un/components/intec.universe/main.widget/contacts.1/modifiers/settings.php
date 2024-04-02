<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\template\Properties;

if (defined('EDITOR'))
    return;

$arParams['LAZYLOAD_USE'] = Properties::get('template-images-lazyload-use') ? 'Y' : 'N';
$arParams['MAP_VENDOR'] = Properties::get('base-map-vendor');
$arParams['CONSENT_SHOW'] = Properties::get('base-consent') ? 'Y' : 'N';