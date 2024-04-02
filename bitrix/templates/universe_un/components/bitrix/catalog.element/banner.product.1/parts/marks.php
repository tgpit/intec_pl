<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arResult
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$APPLICATION->IncludeComponent(
    'intec.universe:main.markers',
    $arVisual['MARKS']['TEMPLATE'], [
        'RECOMMEND' => $arResult['MARKS']['RECOMMEND'] ? 'Y' : 'N',
        'NEW' => $arResult['MARKS']['NEW'] ? 'Y' : 'N',
        'HIT' => $arResult['MARKS']['HIT'] ? 'Y' : 'N',
        'SHARE' => $arResult['MARKS']['SHARE'] ? 'Y' : 'N',
        'ORIENTATION' => 'horizontal'
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
);