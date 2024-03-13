<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var Closure $vItem(&$arItem)
 */

?>
<?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
    <div class="news-list-section">
        <div class="news-list-section-name">
            <?= $arSection['NAME'] ?>
        </div>
        <?php if (!empty($arSection['DESCRIPTION'])) { ?>
            <div class="news-list-section-description">
                <?= $arSection['DESCRIPTION'] ?>
            </div>
        <?php } ?>
        <div class="news-list-section-items news-list-item-container">
            <?php foreach ($arSection['ITEMS'] as $arItem)
                $vItem($arItem);
            ?>
        </div>
    </div>
<?php } ?>