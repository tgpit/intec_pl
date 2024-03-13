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

if (!Loader::includeModule('intec.seo'))
    return;

Core::$app->web->css->addFile(Core::getAlias('@intec/seo/resources/css/icons.css'));

$bIsMenu = true;
include(__DIR__.'/url.php');

$arMenu = [
    'parent_menu' => 'global_intec',
    'text' => Loc::getMessage('intec.seo.menu'),
    'icon' => 'seo-menu-icon',
    'page_icon' => 'seo-menu-icon',
    'items_id' => 'intec_seo',
    'items' => [[
        'text' => Loc::getMessage('intec.seo.menu.autofill'),
        'icon' => 'seo-menu-icon-autofill-templates',
        'page_icon' => 'seo-menu-icon-autofill-templates',
        'url' => $arUrlTemplates['autofill.templates'],
        'more_url' => [
            'seo_autofill_templates_edit'
        ],
        'items_id' => 'intec_seo_autofill_patterns'
    ], [
        'text' => Loc::getMessage('intec.seo.menu.articles'),
        'icon' => 'seo-menu-icon-articles-templates',
        'page_icon' => 'seo-menu-icon-articles-templates',
        'url' => $arUrlTemplates['articles.templates'],
        'more_url' => [
            'seo_articles_templates_edit'
        ],
        'items_id' => 'intec_seo_articles_template'
    ], [
        'text' => Loc::getMessage('intec.seo.menu.texts'),
        'icon' => 'seo-menu-icon-texts',
        'page_icon' => 'seo-menu-icon-texts',
        'items_id' => 'intec_seo_texts',
        'items' => [[
            'text' => Loc::getMessage('intec.seo.menu.texts.patterns'),
            'icon' => 'seo-menu-icon-texts-patterns',
            'page_icon' => 'seo-menu-icon-texts-patterns',
            'url' => $arUrlTemplates['texts.patterns'],
            'more_url' => [
                'seo_texts_patterns_edit'
            ],
            'items_id' => 'intec_seo_texts_patterns'
        ], [
            'text' => Loc::getMessage('intec.seo.menu.texts.generator'),
            'icon' => 'seo-menu-icon-texts-generator',
            'page_icon' => 'seo-menu-icon-texts-generator',
            'url' => $arUrlTemplates['texts.generator'],
            'items_id' => 'intec_seo_texts_generator'
        ]]
    ], [
        'text' => Loc::getMessage('intec.seo.menu.filter'),
        'icon' => 'seo-menu-icon-filter',
        'page_icon' => 'seo-menu-icon-filter',
        'items_id' => 'intec_seo_filter',
        'items' => [[
            'text' => Loc::getMessage('intec.seo.menu.filter.conditions'),
            'icon' => 'seo-menu-icon-filter-conditions',
            'page_icon' => 'seo-menu-icon-filter-conditions',
            'url' => $arUrlTemplates['filter.conditions'],
            'more_url' => [
                'seo_filter_conditions_edit'
            ],
            'items_id' => 'intec_seo_filter_conditions',
            'items' => [[
                'text' => Loc::getMessage('intec.seo.menu.filter.conditions.generators'),
                'icon' => 'seo-menu-icon-filter-conditions-generators',
                'page_icon' => 'seo-menu-icon-filter-conditions-generators',
                'url' => $arUrlTemplates['filter.conditions.generators'],
                'more_url' => [
                    'seo_filter_conditions_generators_edit'
                ],
                'items_id' => 'intec_seo_filter_conditions_generators'
            ]]
        ], [
            'text' => Loc::getMessage('intec.seo.menu.filter.url'),
            'icon' => 'seo-menu-icon-filter-url',
            'page_icon' => 'seo-menu-icon-filter-url',
            'url' => $arUrlTemplates['filter.url'],
            'more_url' => [
                'seo_filter_url_edit'
            ],
            'items_id' => 'intec_seo_filter_url'
        ], [
            'text' => Loc::getMessage('intec.seo.menu.filter.visits'),
            'icon' => 'seo-menu-icon-filter-visits',
            'page_icon' => 'seo-menu-icon-filter-visits',
            'url' => $arUrlTemplates['filter.visits'],
            'items_id' => 'intec_seo_filter_visits'
        ], [
            'text' => Loc::getMessage('intec.seo.menu.filter.sitemap'),
            'icon' => 'seo-menu-icon-filter-sitemap',
            'page_icon' => 'seo-menu-icon-filter-sitemap',
            'url' => $arUrlTemplates['filter.sitemap'],
            'more_url' => [
                'seo_filter_sitemap_edit'
            ],
            'items_id' => 'intec_seo_filter_sitemap'
        ], [
            'text' => Loc::getMessage('intec.seo.menu.filter.debug'),
            'icon' => 'seo-menu-icon-filter-debug',
            'page_icon' => 'seo-menu-icon-filter-debug',
            'url' => $arUrlTemplates['filter.debug'],
            'items_id' => 'intec_seo_filter_debug'
        ]]
    ], [
        'text' => Loc::getMessage('intec.seo.menu.iblocks'),
        'icon' => 'seo-menu-icon-iblocks',
        'page_icon' => 'seo-menu-icon-iblocks',
        'items_id' => 'intec_seo_iblocks',
        'items' => [[
            'text' => Loc::getMessage('intec.seo.menu.iblocks.metadataTemplates'),
            'icon' => 'seo-menu-icon-iblocks-metadata-templates',
            'page_icon' => 'seo-menu-icon-iblocks-metadata-templates',
            'url' => $arUrlTemplates['iblocks.metadata.templates'],
            'more_url' => [
                'seo_iblocks_metadata_templates_edit'
            ],
            'items_id' => 'intec_seo_iblocks_metadata_templates'
        ], [
            'text' => Loc::getMessage('intec.seo.menu.iblocks.elementsNamesTemplates'),
            'icon' => 'seo-menu-icon-iblocks-elements-names-templates',
            'page_icon' => 'seo-menu-icon-iblocks-elements-names-templates',
            'url' => $arUrlTemplates['iblocks.elements.names.templates'],
            'more_url' => [
                'seo_iblocks_elements_names_templates_edit'
            ],
            'items_id' => 'intec_seo_iblocks_elements_names_templates'
        ]]
    ], [
        'text' => Loc::getMessage('intec.seo.menu.sites.settings'),
        'icon' => 'seo-menu-icon-sites-settings',
        'page_icon' => 'seo-menu-icon-sites-settings',
        'url' => $arUrlTemplates['sites.settings'],
        'items_id' => 'intec_seo_sites_settings'
    ]]
];

return $arMenu;