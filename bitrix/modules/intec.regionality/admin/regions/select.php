<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\collections\Arrays;
use intec\regionality\models\Region;
use intec\regionality\models\SiteSettings;

if (empty($_GET['site']))
    return;

define('SITE_ID', $_GET['site']);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (
    !Loader::includeModule('intec.core') &&
    !Loader::includeModule('intec.regionality')
) return;

$request = Core::$app->request;
$path = $request->get('path');
$region = $request->get('region');
$site = $request->get('site');
$current = $request->get('current');
$current = $current === 'Y';

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'))->indexBy('ID');
$site = $sites->get($site);

if (empty($site) || empty($region))
    return;

if (empty($path))
    $path = '/';

$siteSetting = SiteSettings::get();
$region = Region::findOne($region);

if (empty($region))
    return;

if (!$region->active || !$region->isForSites($site['ID']))
    return;

if ($current) {
    Region::setCurrent($region);
} else {
    Region::setSessional($region);
}

Region::remember(
    $region,
    $siteSetting->domainsUse,
    $siteSetting->regionRememberTime
);

LocalRedirect($request->getHostInfo().$path, true);