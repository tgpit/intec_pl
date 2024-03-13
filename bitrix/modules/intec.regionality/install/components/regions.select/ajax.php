<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\Json;
use intec\core\net\Url;
use intec\regionality\models\Region;
use intec\regionality\models\SiteSettings;
use intec\regionality\tools\Domain as DomainTools;

if (empty($_POST['site']))
    return;

define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('SITE_ID', $_POST['site']);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

/**
 * @global CMain $APPLICATION
 */

if (
    !Loader::includeModule('intec.core') &&
    !Loader::includeModule('intec.regionality')
) die();

$request = Core::$app->request;

if (!$request->getIsPost() && !$request->getIsAjax())
    die();

$region = $request->post('region');
$site = $request->post('site');
$url = $request->post('url');

if (empty($url))
    $url = $request->getHostInfo();

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'))->indexBy('ID');
$site = $sites->get($site);
$response = [
    'status' => 'error'
];

if (!empty($site) && !empty($region)) {
    $siteSettings = SiteSettings::getCurrent();
    $region = Region::findOne($region);

    if (!empty($region)) {
        $regionCurrent = Region::getCurrent();

        if ($region->id !== $regionCurrent->id) {
            if ($region->active && $region->isForSites($site['ID'])) {
                $url = new Url($url);

                /** Если опция использования доменов активна */
                if ($siteSettings->domainsUse) {
                    $domain = $region->resolveDomain(IntecRegionality::CrossSiteUse() ? false : $site['ID'], true);
                    $domainRoot = DomainTools::getRoot($domain);
                    $domainCurrent = $request->getHostName();
                    $domainCurrent = DomainTools::getRoot($domainCurrent);

                    /** Если корневые домены совпадают или домен пустой, генерируем перенаправление на местный домен */
                    if (empty($domain) || $domainRoot === $domainCurrent) {
                        if (!empty($domain))
                            $url->setHost($domain);

                        Region::setCurrent($region);
                        Region::remember(
                            $region,
                            $siteSettings->domainsUse,
                            $siteSettings->regionRememberTime
                        );
                    } else {
                        $path = $url;
                        $path->setHost(null);
                        $path = $path->build();

                        $url = new Url($request->getHostInfo());
                        $url->setHost($domain);
                        $url->setPathString('/bitrix/admin/regionality_regions_select.php');
                        $url->getQuery()->setRange([
                            'path' => $path,
                            'region' => $region->id,
                            'site' => $site['ID'],
                            'current' => 'Y',
                            'lang' => LANGUAGE_ID
                        ]);
                    }
                } else {
                    Region::setCurrent($region);
                    Region::remember(
                        $region,
                        $siteSettings->domainsUse,
                        $siteSettings->regionRememberTime
                    );
                }

                $response = [
                    'status' => 'success',
                    'domain' => $domain,
                    'region' => $region->toArray(),
                    'action' => 'redirect',
                    'url' => $url->build()
                ];
            } else {
                $response['message'] = 'Region inactive or is not for this site.';
            }
        } else {
            Region::setCurrent($region);
            Region::remember(
                $region,
                $siteSettings->domainsUse,
                $siteSettings->regionRememberTime
            );

            $response['status'] = 'success';
        }
    } else {
        $response['message'] = 'Unknown region.';
    }
} else {
    $response['message'] = 'Unknown site or region.';
}

echo Json::encode($response, 320, true);

CMain::FinalActions();