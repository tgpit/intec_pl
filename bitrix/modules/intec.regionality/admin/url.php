<?php

if (!isset($bIsMenu))
    IntecRegionality::Initialize();

$sUrlRoot = '/bitrix/admin';
$arUrlTemplates = [
    'regions' => $sUrlRoot.'/regionality_regions.php?lang='.LANGUAGE_ID,
    'regions.add' => $sUrlRoot.'/regionality_regions_edit.php?lang='.LANGUAGE_ID,
    'regions.edit' => $sUrlRoot.'/regionality_regions_edit.php?region=#region#&lang='.LANGUAGE_ID,
    'regions.domains' => $sUrlRoot.'/regionality_regions_domains.php?region=#region#&lang='.LANGUAGE_ID,
    'regions.domains.add' => $sUrlRoot.'/regionality_regions_domains_edit.php?region=#region#&lang='.LANGUAGE_ID,
    'regions.domains.edit' => $sUrlRoot.'/regionality_regions_domains_edit.php?region=#region#&domain=#domain#&lang='.LANGUAGE_ID,
    'resolve' => $sUrlRoot.'/regionality_resolve.php?lang='.LANGUAGE_ID,
    'sites.settings' => $sUrlRoot.'/regionality_sites_settings.php?lang='.LANGUAGE_ID,
    'sites.settings.robots' => $sUrlRoot.'/regionality_sites_settings_robots.php?lang='.LANGUAGE_ID.'&site=#site#',
    'sites.settings.sitemap' => $sUrlRoot.'/regionality_sites_settings_sitemap.php?lang='.LANGUAGE_ID.'&site=#site#',
    'variables' => $sUrlRoot.'/regionality_variables.php?lang='.LANGUAGE_ID
];

unset($sUrlRoot);