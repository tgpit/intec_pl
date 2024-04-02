<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var Closure $vItem(&$arItem)
 */

?>
<div class="news-list-item-container">
    <?php foreach ($arResult['ITEMS'] as $arItem)
        $vItem($arItem);
    ?>
</div>