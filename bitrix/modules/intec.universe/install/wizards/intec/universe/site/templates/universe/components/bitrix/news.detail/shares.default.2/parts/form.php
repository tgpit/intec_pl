<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="news-detail-form">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.widget',
        $arResult['BLOCKS']['FORM']['TEMPLATE'],
        $arResult['BLOCKS']['FORM']['PARAMETERS'],
        $component
    ) ?>
</div>