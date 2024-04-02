<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>

<?php $APPLICATION->IncludeComponent(
    'intec.universe:main.markers',
    'template.1', [
        'RECOMMEND' => $arResult['MARKS']['RECOMMEND'] ? 'Y' : 'N',
        'NEW' => $arResult['MARKS']['NEW'] ? 'Y' : 'N',
        'HIT' => $arResult['MARKS']['HIT'] ? 'Y' : 'N',
        'SHARE' => $arResult['MARKS']['SHARE'] ? 'Y' : 'N',
        'ORIENTATION' => 'horizontal'
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
) ?>