<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="news-detail-icons">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.advantages',
        $arResult['BLOCKS']['ICONS']['TEMPLATE'],
        $arResult['BLOCKS']['ICONS']['PARAMETERS'],
        $component
    ) ?>
</div>