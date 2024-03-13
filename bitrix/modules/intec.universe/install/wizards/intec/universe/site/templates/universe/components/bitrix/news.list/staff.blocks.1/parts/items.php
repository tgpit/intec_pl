<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var Closure $vItem(&$arItem)
 */

?>
<div class="news-list-items">
    <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-16">
        <?php foreach ($arResult['ITEMS'] as $arItem)
            $vItem($arItem);
        ?>
    </div>
</div>
