<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];

?>
<div class="ns-bitrix c-news c-news-reviews-1">
    <div class="c-news-reviews-list">
        <div class="c-news-reviews-send">
            <?php include(__DIR__.'/parts/send.php') ?>
        </div>
        <div class="c-news-reviews-items">
            <?php include(__DIR__.'/parts/list/list.php') ?>
        </div>
    </div>
</div>