<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
            $arVisual['MOBILE']['PICTURE']['USE'] && $arData['MOBILE']['PICTURE']['USE'] ? 'display-none' : null,
            'intec-grid-item' => [
                '2',
                'a-stretch'
            ]
        ]
    ]) ?>
        <div class="widget-item-picture">
            <?= Html::img($arData['PICTURE']['VALUE']['SRC'], [
                'title' => '',
                'alt' => '',
                'loading' => 'lazy',
                'data' => [
                    'align-vertical' => $arData['PICTURE']['ALIGN']['VERTICAL']
                ]
            ]) ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>