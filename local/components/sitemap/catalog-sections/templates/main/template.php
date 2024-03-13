<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
?>
<<?=$arResult['type']?> xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach($arResult['list'] as $item): ?>
      <<?=$item['type']?>><loc><?=$item['src']?></loc></<?=$item['type']?>>
    <?php endforeach; ?>
</<?=$arResult['type']?>>
