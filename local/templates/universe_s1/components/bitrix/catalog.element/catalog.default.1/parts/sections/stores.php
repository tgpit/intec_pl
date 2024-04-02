<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-section-store-amount">
    <?php $APPLICATION->IncludeComponent(
        'bitrix:catalog.store.amount',
        '.default', [
            'ELEMENT_ID' => $arResult['ID'],
            'STORE_PATH' => $arParams['STORE_PATH'],
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '36000',
            'MAIN_TITLE' => '',
            'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
            'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
            'STORES' => $arParams['STORES'],
            'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
            'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
            'USER_FIELDS' => $arParams['USER_FIELDS'],
            'FIELDS' => $arParams['FIELDS'],
            'STORE_BLOCK_DESCRIPTION_USE' => $arParams['STORE_BLOCK_DESCRIPTION_USE'],
            'STORE_BLOCK_DESCRIPTION_TEXT' => $arParams['STORE_BLOCK_DESCRIPTION_TEXT']
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    ) ?>
</div>