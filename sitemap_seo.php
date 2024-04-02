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

?><?php header('Content-Type: text/xml'); ?><?= '<?xml version="1.0" encoding="UTF-8"?>'."\r\n" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_gorodskie/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchita_shei/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchitnyy_poyas/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shorty_zashchitnye/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_snegokhodnye/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/veydersy/zhenshchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/veydersy/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kombinezony/muzhchinam/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchita_shei/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/podrostkovyy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/detskiy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektropitbayki/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektropitbayki/apollo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektropitbayki/butch/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektropitbayki/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektrokvadrotsikly/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektrokvadrotsikly/apollo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektrokvadrotsikly/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektrokvadrotsikly/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/apollo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/butch/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/jmc/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/kayo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/ycf/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/sssr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/wels/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/zuum/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/regulmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/progasi/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/brz/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/winner/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/full_crew/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/k2r/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/pitbayki/gr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/apollo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/jmc/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/kayo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/sssr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/wels/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/zuum/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/regulmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/progasi/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/brz/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/winner/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/k2r/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/gr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/lifan/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/koshine/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/racer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/hasky/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/xmotos/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/kews/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/voge/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/bhr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/fuego/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/zontes/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/groza/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/apollo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/kayo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/wels/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/abm/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/applestone/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/irbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/mikilon/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/kvadrotsikly/gladiator/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/snegokhody/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/snegokhody/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/snegokhody/progasi/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/snegokhody/irbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/snegokhody/woideal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mopedy/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mopedy/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mopedy/sachs/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mopedy/vento/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/baggi/scanmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/regulmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/voge/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/fuego/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/zontes/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/dorozhnye_mototsikly/groza/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/motoland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/jmc/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/kayo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/sssr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/wels/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/zuum/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/regulmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/progasi/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/brz/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/k2r/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/gr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/racer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/hasky/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/xmotos/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/mototsikly/krossovye_mototsikly/bhr/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/skutery_1/vento/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/skutery_1/sym/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/skutery_1/tmbk/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/velosipedy/maverick/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/velosipedy/stels/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/motobuksirovshchiki/motax/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/motobuksirovshchiki/irbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/tekhnika/motobuksirovshchiki/rubin_drive/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/elektrotekhnika/elektrobaggi/scanmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/agv_sport/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/scoyco/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/moteq/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/wolf/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/bluegrass/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zhilety_cherepakhi/aim/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/gaerne/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/tiger/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/ryo_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/regulmoto/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/roqvi/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/motoboty/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/agv_sport/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/scoyco/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/wolf/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/pod/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nakolenniki/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/nalokotniki/race_face/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/scoyco/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/moteq/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/aim/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/100/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/vega/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/suomy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/jt_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/masontex/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/ixs/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/shift/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/icon/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/berik/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/replica/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/rs_spurtt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/thor/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/kini/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/axio/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/perchatki/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/shock/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/cucyma/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/ufo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/citadel/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/spy/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/grom/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/menat/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/prochee/extra_options/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shock/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/ufo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/just1/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/faseed/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/gsb/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/gtx/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/nenki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/airoh/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/tiger/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/jt_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/shift/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/replica/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/thor/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/grom/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/troy_lee_designs/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shtany/fasthouse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/aim/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/100/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/thor/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/just1/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/gtx/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/nibbi_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/scott/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/maski/rockot/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/hyperlook/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/windstopper_life/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/podshlemniki/kitay/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_gorodskie/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_gorodskie/gsb/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_gorodskie/gtx/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/tiger/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/shift/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/replica/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/thor/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/grom/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/troy_lee_designs/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/fasthouse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/pitland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dzhersi/bse/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchita_shei/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchita_shei/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchita_shei/wolf/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchitnyy_poyas/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchitnyy_poyas/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/zashchitnyy_poyas/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/ixs/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/dozhdeviki/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kurtki_i_tolstovki/krasivyy_motosport/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/moteq/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/pitland/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/futbolki/krasivyy_motosport/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/madbull/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/hyperlook/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/termobele/starezzi/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/noski/100/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shorty_zashchitnye/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shorty_zashchitnye/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shorty_zashchitnye/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shorty_zashchitnye/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shorty_zashchitnye/bluegrass/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/leatt/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/fly_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/fox_racing/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/ataki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/acerbis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/oneal/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/hizer/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/avantis/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/shock/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/ufo/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/just1/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/faseed/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/gsb/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/gtx/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/nenki/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_krossovye/airoh/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/shlemy/shlemy_snegokhodnye/gsb/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/veydersy/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/veydersy/dragonfly/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kombinezony/finntrail/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kombinezony/vega/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
	<url>
		<loc>http://<?= $domain ?>/catalog/ekipirovka/kombinezony/jethwear/</loc>
		<lastmod>2024-03-20T09:56:43+03:00</lastmod>
		<changefreq>always</changefreq>
	</url>
</urlset>