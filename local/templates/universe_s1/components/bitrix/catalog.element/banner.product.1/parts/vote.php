<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

?>
<div class="catalog-element-vote">
    <?php $APPLICATION->IncludeComponent(
        'bitrix:iblock.vote',
        'template.3', [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ELEMENT_ID' => $arResult['ID'],
            'ELEMENT_CODE' => $arResult['CODE'],
            'MAX_VOTE' => '5',
            'VOTE_NAMES' => [
                0 => '1',
                1 => '2',
                2 => '3',
                3 => '4',
                4 => '5',
            ],
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'COMPONENT_TEMPLATE' => 'template.3',
            'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
            'SHOW_RATING' => 'N'
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    ) ?>
</div>