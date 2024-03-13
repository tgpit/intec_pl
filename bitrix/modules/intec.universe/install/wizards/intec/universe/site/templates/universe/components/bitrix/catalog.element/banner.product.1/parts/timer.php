<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CBitrixComponent $component
 */

?>
<div data-role="timer-holder">
    <?php $APPLICATION->IncludeComponent(
        $arResult['TIMER']['PROPERTIES']['component'],
        $arResult['TIMER']['PROPERTIES']['template'],
        $arResult['TIMER']['PROPERTIES']['parameters'],
        $component
    ) ?>
</div>