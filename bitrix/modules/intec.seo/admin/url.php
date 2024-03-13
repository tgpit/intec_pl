<?php

if (!isset($bIsMenu))
    IntecSeo::Initialize();

$sUrlRoot = '/bitrix/admin';
$arUrlTemplates = [
    'autofill.templates' => $sUrlRoot.'/seo_autofill_templates.php?lang='.LANGUAGE_ID,
    'autofill.templates.add' => $sUrlRoot.'/seo_autofill_templates_edit.php?lang='.LANGUAGE_ID,
    'autofill.templates.edit' => $sUrlRoot.'/seo_autofill_templates_edit.php?template=#template#lang='.LANGUAGE_ID,
    'autofill.templates.copy' => $sUrlRoot.'/seo_autofill_templates_edit.php?template=#template#&action=copy&lang='.LANGUAGE_ID,
    'articles.templates' => $sUrlRoot.'/seo_articles_templates.php?lang='.LANGUAGE_ID,
    'articles.templates.add' => $sUrlRoot.'/seo_articles_templates_edit.php?lang='.LANGUAGE_ID,
    'articles.templates.edit' => $sUrlRoot.'/seo_articles_templates_edit.php?template=#template#lang='.LANGUAGE_ID,
    'articles.templates.copy' => $sUrlRoot.'/seo_articles_templates_edit.php?template=#template#&action=copy&lang='.LANGUAGE_ID,
    'texts.patterns' => $sUrlRoot.'/seo_texts_patterns.php?lang='.LANGUAGE_ID,
    'texts.patterns.add' => $sUrlRoot.'/seo_texts_patterns_edit.php?lang='.LANGUAGE_ID,
    'texts.patterns.edit' => $sUrlRoot.'/seo_texts_patterns_edit.php?textPattern=#textPattern#&lang='.LANGUAGE_ID,
    'texts.generator' => $sUrlRoot.'/seo_texts_generator.php?lang='.LANGUAGE_ID,
    'filter.conditions' => $sUrlRoot.'/seo_filter_conditions.php?lang='.LANGUAGE_ID,
    'filter.conditions.add' => $sUrlRoot.'/seo_filter_conditions_edit.php?lang='.LANGUAGE_ID,
    'filter.conditions.edit' => $sUrlRoot.'/seo_filter_conditions_edit.php?condition=#condition#&tab=#tab#&lang='.LANGUAGE_ID,
    'filter.conditions.copy' => $sUrlRoot.'/seo_filter_conditions_edit.php?condition=#condition#&action=copy&lang='.LANGUAGE_ID,
    'filter.conditions.generators' => $sUrlRoot.'/seo_filter_conditions_generators.php?lang='.LANGUAGE_ID,
    'filter.conditions.generators.add' => $sUrlRoot.'/seo_filter_conditions_generators_edit.php?lang='.LANGUAGE_ID,
    'filter.conditions.generators.edit' => $sUrlRoot.'/seo_filter_conditions_generators_edit.php?generator=#generator#&lang='.LANGUAGE_ID,
    'filter.url' => $sUrlRoot.'/seo_filter_url.php?lang='.LANGUAGE_ID,
    'filter.url.add' => $sUrlRoot.'/seo_filter_url_edit.php?lang='.LANGUAGE_ID,
    'filter.url.edit' => $sUrlRoot.'/seo_filter_url_edit.php?url=#url#&lang='.LANGUAGE_ID,
    'filter.visits' => $sUrlRoot.'/seo_filter_visits.php?lang='.LANGUAGE_ID,
    'filter.sitemap' => $sUrlRoot.'/seo_filter_sitemap.php?lang='.LANGUAGE_ID,
    'filter.sitemap.add' => $sUrlRoot.'/seo_filter_sitemap_edit.php?site=#site#&lang='.LANGUAGE_ID,
    'filter.sitemap.edit' => $sUrlRoot.'/seo_filter_sitemap_edit.php?sitemap=#sitemap#&lang='.LANGUAGE_ID,
    'filter.debug' => $sUrlRoot.'/seo_filter_debug.php?lang='.LANGUAGE_ID,
    'filter.debug.history' => $sUrlRoot.'/seo_filter_debug_history.php?url=#url#&lang='.LANGUAGE_ID,
    'iblocks.metadata.templates' => $sUrlRoot.'/seo_iblocks_metadata_templates.php?lang='.LANGUAGE_ID,
    'iblocks.metadata.templates.add' => $sUrlRoot.'/seo_iblocks_metadata_templates_edit.php?lang='.LANGUAGE_ID,
    'iblocks.metadata.templates.edit' => $sUrlRoot.'/seo_iblocks_metadata_templates_edit.php?template=#template#lang='.LANGUAGE_ID,
    'iblocks.elements.names.templates' => $sUrlRoot.'/seo_iblocks_elements_names_templates.php?lang='.LANGUAGE_ID,
    'iblocks.elements.names.templates.add' => $sUrlRoot.'/seo_iblocks_elements_names_templates_edit.php?lang='.LANGUAGE_ID,
    'iblocks.elements.names.templates.edit' => $sUrlRoot.'/seo_iblocks_elements_names_templates_edit.php?template=#template#lang='.LANGUAGE_ID,
    'sites.settings' => $sUrlRoot.'/seo_sites_settings.php?lang='.LANGUAGE_ID,
];

unset($sUrlRoot);