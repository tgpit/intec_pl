<?php define('INTEC_REGIONALITY_REGION_RESOLVE', false) ?>
<?php define('INTEC_REGIONALITY_MACROS_REPLACE', false) ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php') ?>
<?php

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use intec\regionality\models\Region;
use intec\regionality\models\SiteSettings;

if (!Loader::includeModule('intec.core') || !Loader::includeModule('intec.regionality'))
	return;

$site = Context::getCurrent()->getSite();

if (empty($site))
	return;

$settings = SiteSettings::get($site);
$region = null;
$domain = null;

if ($settings->domainsUse) {
	$region = Region::resolveByDomain(null, $site);

	if (!empty($region))
		$domain = $region->resolveDomain($site, true);
}
if (empty($domain))
	$domain = $settings->getDomain(true);

if (empty($domain))
	return;

include $_SERVER['DOCUMENT_ROOT'].'/gl.php';
$qh = $_SERVER['HTTP_HOST'];
if ($mDoman == $qh){ ?>

<?php 
header('Content-Type: text/plain'); ?>User-Agent: *
Disallow: */index.php
Disallow: /bitrix/
Disallow: /basket/
Disallow: */filter/*
Disallow: /*show_include_exec_time=
Disallow: /*show_page_exec_time=
Disallow: /*show_sql_stat=
Disallow: /*bitrix_include_areas=
Disallow: /*clear_cache=
Disallow: /*clear_cache_session=
Disallow: /*ADD_TO_COMPARE_LIST
Disallow: /*ORDER_BY
Disallow: /*PAGEN
Disallow: /*?print=
Disallow: /*&print=
Disallow: /*print_course=
Disallow: /*?action=
Disallow: /*&action=
Disallow: /*register=
Disallow: /*forgot_password=
Disallow: /*change_password=
Disallow: /*login=
Disallow: /*logout=
Disallow: /*auth=
Disallow: /*backurl=
Disallow: /*back_url=
Disallow: /*BACKURL=
Disallow: /*BACK_URL=
Disallow: /*back_url_admin=
Disallow: /*?utm_source=
Disallow: /*?bxajaxid=
Disallow: /*RID=false
Disallow: /*quiz=yes
Disallow: /*?q=
Allow: /bitrix/components/
Allow: /bitrix/cache/
Allow: /bitrix/js/
Allow: /bitrix/templates/
Allow: /bitrix/panel/
Crawl-delay: 120
Sitemap: https://<?= $domain ?>/sitemap.xml

User-Agent: Yandex
Disallow: */index.php
Disallow: /bitrix/
Disallow: /basket/
Disallow: */filter/*
Disallow: /*show_include_exec_time=
Disallow: /*show_page_exec_time=
Disallow: /*show_sql_stat=
Disallow: /*bitrix_include_areas=
Disallow: /*clear_cache=
Disallow: /*clear_cache_session=
Disallow: /*ADD_TO_COMPARE_LIST
Disallow: /*ORDER_BY
Disallow: /*PAGEN
Disallow: /*?print=
Disallow: /*&print=
Disallow: /*print_course=
Disallow: /*?action=
Disallow: /*&action=
Disallow: /*register=
Disallow: /*forgot_password=
Disallow: /*change_password=
Disallow: /*login=
Disallow: /*logout=
Disallow: /*auth=
Disallow: /*backurl=
Disallow: /*back_url=
Disallow: /*BACKURL=
Disallow: /*BACK_URL=
Disallow: /*back_url_admin=
Disallow: /*?utm_source=
Disallow: /*?bxajaxid=
Disallow: /*RID=false
Disallow: /*quiz=yes
Disallow: /*?q=
Allow: /bitrix/components/
Allow: /bitrix/cache/
Allow: /bitrix/js/
Allow: /bitrix/templates/
Allow: /bitrix/panel/
Crawl-delay: 120
Host: https://<?= $domain ?>/
<? }else{ 
header('Content-Type: text/plain'); ?>User-Agent: *
Disallow: /

User-Agent: Yandex
Disallow: */index.php
Disallow: /bitrix/
Disallow: /basket/
Disallow: */filter/*
Disallow: /*show_include_exec_time=
Disallow: /*show_page_exec_time=
Disallow: /*show_sql_stat=
Disallow: /*bitrix_include_areas=
Disallow: /*clear_cache=
Disallow: /*clear_cache_session=
Disallow: /*ADD_TO_COMPARE_LIST
Disallow: /*ORDER_BY
Disallow: /*PAGEN
Disallow: /*?print=
Disallow: /*&print=
Disallow: /*print_course=
Disallow: /*?action=
Disallow: /*&action=
Disallow: /*register=
Disallow: /*forgot_password=
Disallow: /*change_password=
Disallow: /*login=
Disallow: /*logout=
Disallow: /*auth=
Disallow: /*backurl=
Disallow: /*back_url=
Disallow: /*BACKURL=
Disallow: /*BACK_URL=
Disallow: /*back_url_admin=
Disallow: /*?utm_source=
Disallow: /*?bxajaxid=
Disallow: /*RID=false
Disallow: /*quiz=yes
Disallow: /*?q=
Allow: /bitrix/components/
Allow: /bitrix/cache/
Allow: /bitrix/js/
Allow: /bitrix/templates/
Allow: /bitrix/panel/
Crawl-delay: 120
Host: https://<?= $domain ?>/
<? } ?>