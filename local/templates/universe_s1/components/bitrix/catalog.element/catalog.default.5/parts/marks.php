<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arFields
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-marks">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.markers',
        'template.2', [
            'HIT' => $arFields['MARKS']['VALUES']['HIT'],
            'NEW' => $arFields['MARKS']['VALUES']['NEW'],
            'RECOMMEND' => $arFields['MARKS']['VALUES']['RECOMMEND'],
            'SHARE' => $arFields['MARKS']['VALUES']['SHARE'],
            'ORIENTATION' => 'horizontal'
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    ) ?>
</div>