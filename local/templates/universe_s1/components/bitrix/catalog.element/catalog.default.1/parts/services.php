<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$arResult['SERVICES']['PARAMETERS']['COLUMNS'] = $arParams['MENU_SHOW'] === "Y" ? 2 : $arResult['SERVICES']['PARAMETERS']['COLUMNS'];

?>
<div class="catalog-element-section-services">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.services',
        'template.18',
        $arResult['SERVICES']['PARAMETERS'],
        $component
    ) ?>
</div>