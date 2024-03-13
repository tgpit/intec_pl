<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (defined('EDITOR'))
    return;

$arParams['LAZYLOAD_USE'] = Properties::get('template-images-lazyload-use') ? 'Y' : 'N';
$arParams['LIST_TEMPLATE'] = Properties::get('sections-photo-template');
