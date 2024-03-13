<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;

/**
 * @var $arUrlTemplates
 * @global CUser $USER
 */

global $USER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.regionality'))
    return;

Core::$app->web->css->addFile(Core::getAlias('@intec/regionality/resources/css/icons.css'));

$bIsMenu = true;
include(__DIR__.'/url.php');

$arMenu = [
    'parent_menu' => 'global_intec',
    'text' => Loc::getMessage('intec.regionality.menu'),
    'icon' => 'regionality-menu-icon',
    'page_icon' => 'regionality-menu-icon',
    'items_id' => 'intec_regionality',
    'items' => [[
        'text' => Loc::getMessage('intec.regionality.menu.regions'),
        'icon' => 'regionality-menu-icon-regions',
        'page_icon' => 'regionality-menu-icon-regions',
        'url' => $arUrlTemplates['regions'],
        'more_url' => [
            'regionality_regions_add',
            'regionality_regions_edit',
            'regionality_regions_domains',
            'regionality_regions_domains_add',
            'regionality_regions_domains_edit',
        ],
        'items_id' => 'intec_regionality_regions'
    ], [
        'text' => Loc::getMessage('intec.regionality.menu.sites.settings'),
        'icon' => 'regionality-menu-icon-sites-settings',
        'page_icon' => 'regionality-menu-icon-sites-settings',
        'url' => $arUrlTemplates['sites.settings'],
        'more_url' => [
            'regionality_sites_settings_robots',
            'regionality_sites_settings_sitemap'
        ],
        'items_id' => 'intec_regionality_sites_settings'
    ], [
        'text' => Loc::getMessage('intec.regionality.menu.variables'),
        'icon' => 'regionality-menu-icon-variables',
        'page_icon' => 'regionality-menu-icon-variables',
        'url' => $arUrlTemplates['variables'],
        'items_id' => 'intec_regionality_variables'
    ], [
        'text' => Loc::getMessage('intec.regionality.menu.resolve'),
        'icon' => 'regionality-menu-icon-resolve',
        'page_icon' => 'regionality-menu-icon-resolve',
        'url' => $arUrlTemplates['resolve'],
        'items_id' => 'intec_regionality_resolve'
    ]]
];

return $arMenu;