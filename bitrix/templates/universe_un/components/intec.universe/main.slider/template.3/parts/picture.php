<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function ($arData) use ($arVisual) { ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item-picture',
            'intec-grid-item' => [
                '2',
                'a-stretch'
            ]
        ]
    ]) ?>
        <div class="widget-item-picture">
            <?= Html::img($arData['PICTURE']['VALUE']['SRC'], [
                'alt' => $arData['HEADER'],
                'title' => $arData['HEADER'],
                'loading' => 'lazy',
                'data' => [
                    'align-vertical' => $arData['PICTURE']['ALIGN']['VERTICAL']
                ]
            ]) ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>