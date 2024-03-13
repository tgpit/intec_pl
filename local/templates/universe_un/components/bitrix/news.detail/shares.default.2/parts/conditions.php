<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="news-detail-conditions">
    <?$APPLICATION->IncludeComponent(
        'intec.universe:main.advantages',
        $arResult['BLOCKS']['CONDITIONS']['TEMPLATE'],
        $arResult['BLOCKS']['CONDITIONS']['PARAMETERS'],
        $component
    );?>
</div>