<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arData) use (&$arVisual, &$arProductParameters, &$component, &$APPLICATION) { ?>
    <?php
    $arProductParameters['ELEMENT_ID'] = $arData['PRODUCT']['VALUE'];
    $arProductParameters['SCHEME'] = $arData['SCHEME'];
    ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item-product',
            'intec-grid-item' => [
                '2',
                '768-1',
                'a-center'
            ]
        ]
    ]) ?>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:catalog.element',
            'banner.product.1',
            $arProductParameters,
            $component
        ); ?>
    <?= Html::endTag('div') ?>
<?php } ?>