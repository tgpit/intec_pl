<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arData) use (&$arVisual) { ?>
    <?php if (!$arData['PICTURE']['SHOW']) return ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item-picture',
            'intec-grid-item' => [
                '2',
                'a-stretch'
            ]
        ]
    ]) ?>
        <?= Html::img($arData['PICTURE']['VALUE']['SRC'], [
            'title' => '',
            'alt' => '',
            'loading' => 'lazy',
            'data' => [
                'align-vertical' => $arData['PICTURE']['ALIGN']['VERTICAL']
            ]
        ]) ?>
    <?= Html::endTag('div') ?>
<?php } ?>