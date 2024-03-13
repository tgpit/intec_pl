<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arFields
 */

?>
<div class="intec-grid intec-grid-wrap intec-grid-i-h-16 intec-grid-i-v-12">
    <?php if (
        $arFields['MARKS']['SHOW'] || $arFields['ARTICLE']['SHOW'] ||
        $arVisual['VOTE']['SHOW'] || $arFields['BRAND']['SHOW']
    ) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'catalog-element-main-block-top-left' => true,
                'catalog-element-main-block-1024-hide' => !$arFields['MARKS']['SHOW'] && !$arFields['VOTE']['SHOW'],
                'intec-grid-item' => [
                    '2' => true,
                    'a-center' => true,
                    '1024-1' => true
                ]
            ], true)
        ]) ?>
            <div class="catalog-element-main-block-container">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-8">
                    <div class="intec-grid-item">
                        <?php if ($arFields['MARKS']['SHOW'])
                            include(__DIR__ . '/marks.php');
                        ?>
                    </div>
                    <?php if ($arVisual['VOTE']['SHOW']) { ?>
                        <div class="intec-grid-item-auto">
                            <?php include(__DIR__ . '/vote.php') ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'catalog-element-main-block-top-right' => true,
                'catalog-element-main-block-1024-hide' => !$arFields['BRAND']['SHOW'] && !$arVisual['ARTICLE']['SHOW'],
                'intec-grid-item' => [
                    '2' => true,
                    'a-center' => true,
                    '1024-1' => true
                ]
            ], true)
        ]) ?>
            <div class="catalog-element-main-block-container">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-8">
                    <div class="intec-grid-item">
                        <?php if ($arFields['ARTICLE']['SHOW'])
                            include(__DIR__ . '/article.php');
                        ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?php if ($arFields['BRAND']['SHOW'])
                            include(__DIR__ . '/brand.php');
                        ?>
                    </div>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'catalog-element-main-block-bottom-left' => true,
            'intec-grid-item' => [
                '2' => true,
                '768-1' => true
            ]
        ], true)
    ]) ?>
        <div class="catalog-element-main-block-container" data-sticky="top">
            <?php include(__DIR__ . '/gallery.php') ?>
            <?php if ($arVisual['DESCRIPTION']['PREVIEW']['SHOW']) { ?>
                <? include(__DIR__ . '/description.preview.php'); ?>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>

    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'catalog-element-main-block-bottom-right' => true,
            'intec-grid-item' => [
                '2' => true,
                '768-1' => true
            ]
        ], true)
    ]) ?>
        <div data-sticky="top">
            <?php include(__DIR__ . '/main.container.view.2/purchase.php') ?>
        </div>
    <?= Html::endTag('div') ?>
</div>