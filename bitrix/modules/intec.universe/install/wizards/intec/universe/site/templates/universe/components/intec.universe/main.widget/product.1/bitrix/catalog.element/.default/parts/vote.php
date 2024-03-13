<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-vote">
    <?php $APPLICATION->IncludeComponent(
        'bitrix:iblock.vote',
        'template.2',
        $arResult['DATA']['VOTE']['PARAMETERS'],
        $component
    ) ?>
</div>