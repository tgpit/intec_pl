<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-form">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.widget',
        'form.6',
        $arBlock['PARAMETERS'],
        $component
    ) ?>
</div>
