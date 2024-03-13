<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach($arResult['list'] as $arrSection): ?>
        <url><loc><?=$arResult['city']['domain']?><?=$arrSection['url']?></loc></url>
    <?php endforeach; ?>
</urlset>
