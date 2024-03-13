<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\net\Url;
use intec\seo\models\filter\Visit;
use intec\seo\models\SiteSettings;

if (empty($_POST['site']))
    return;

define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('SITE_ID', $_POST['site']);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (
    !Loader::includeModule('intec.core') ||
    !Loader::includeModule('intec.seo')
) return;

global $APPLICATION;

$referrer = Core::$app->request->post('referrer');
$page = Core::$app->request->post('page');

if (empty($page))
    return;

$cookie = null;

$siteSettings = SiteSettings::getCurrent();

if (empty($siteSettings) || !$siteSettings->filterVisitsEnabled)
    return;

$referrers = $siteSettings->getFilterVisitsReferrers();

if (isset($_COOKIE['SEO_FILTER_STATISTIC_VISIT']))
    $cookie = $_COOKIE['SEO_FILTER_STATISTIC_VISIT'];

if (empty($cookie))
    $cookie = bitrix_sessid();

/** @var Visit $visit */
$visit = null;
$referrer = !empty($referrer) ? new Url($referrer) : null;
$page = new Url($page);

if (!empty($cookie) && $cookie === bitrix_sessid())
    $visit = Visit::find()->where(['sessionId' => $cookie])->one();

$date = new DateTime('now', new DateTimeZone('UTC'));

if (!empty($visit)) {
    $visit->pageCount = $visit->pageCount + 1;
    $visit->dateVisit = $date->format('Y-m-d H:i:s');
    $visit->save();

    setcookie('SEO_FILTER_STATISTIC_VISIT', bitrix_sessid(), time()+3*60*60, '/');
} else if (!empty($referrer) && ArrayHelper::isIn($referrer->getHost(), $referrers)) {
    $visit = new Visit();
    $visit->sessionId = bitrix_sessid();
    $visit->referrerUrl = !empty($referrer) ? $referrer->build() : null;
    $visit->pageUrl = $page->build();
    $visit->pageCount = 1;
    $visit->dateVisit = $date->format('Y-m-d H:i:s');
    $visit->save();

    setcookie('SEO_FILTER_STATISTIC_VISIT', bitrix_sessid(), time()+3*60*60, '/');
}