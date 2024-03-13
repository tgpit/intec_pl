<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $APPLICATION->IncludeComponent(
    'bitrix:iblock.vote',
    'template.2', [
        'COMPONENT_TEMPLATE' => 'template.1',
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'MESSAGE_404' => $arParams['VOTE_PREFIX_ID'],
        'ELEMENT_ID' => $arItem['ID'],
        'ELEMENT_CODE' => $arItem['CODE'],
        'MAX_VOTE' => '5',
        'VOTE_NAMES' => [
            0 => '1',
            1 => '2',
            2 => '3',
            3 => '4',
            4 => '5'
        ],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'DISPLAY_AS_RATING' => $arVisual['VOTE']['TYPE'],
        'SHOW_RATING' => 'Y'
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
) ?>