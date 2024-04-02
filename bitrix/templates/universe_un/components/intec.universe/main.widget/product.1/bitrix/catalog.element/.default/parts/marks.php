<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-marks">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.markers',
        'template.2', [
            'HIT' => $arResult['DATA']['MARKS']['HIT'],
            'NEW' => $arResult['DATA']['MARKS']['NEW'],
            'RECOMMEND' => $arResult['DATA']['MARKS']['RECOMMEND'],
            'SHARE' => $arResult['DATA']['MARKS']['SHARE'],
            'ORIENTATION' => 'horizontal'
        ],
        $component
    ) ?>
</div>