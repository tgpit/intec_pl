<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arFields
 */

?>
<div class="intec-grid intec-grid-wrap intec-grid-i-h-16 intec-grid-i-v-12">
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'catalog-element-main-block-bottom-left' => true,
            'intec-grid-item' => [
                'auto' => true,
                '1024-2' => true,
                '768-1' => true
            ]
        ], true)
    ]) ?>
        <div class="catalog-element-main-block-container" data-sticky="top">
            <?php if (
                $arFields['MARKS']['SHOW'] || $arFields['ARTICLE']['SHOW'] ||
                (($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) && !$bSkuList) ||
                $arVisual['VOTE']['SHOW'] || $arFields['BRAND']['SHOW']
            ) { ?>
                <div class="catalog-element-main-block-additional">
                    <div class="intec-grid intec-grid-1024-wrap intec-grid-a-v-center intec-grid-i-8">
                        <div class="intec-grid-item-auto intec-grid-item-1024-2">
                            <?php if ($arFields['MARKS']['SHOW'])
                                include(__DIR__ . '/marks.php');
                            ?>
                        </div>
                        <?php if ($arVisual['VOTE']['SHOW']) { ?>
                            <div class="catalog-element-main-block-vote intec-grid-item-auto intec-grid-item-1024-2">
                                <?php include(__DIR__ . '/vote.php') ?>
                            </div>
                        <?php } ?>
                        <div class="intec-grid-item intec-grid-item-1024-2">
                            <?php if ($arFields['ARTICLE']['SHOW'])
                                include(__DIR__ . '/article.php');
                            ?>
                        </div>
                        <div class="intec-grid-item-auto intec-grid-item-1024-2">
                            <?php if ($arFields['BRAND']['SHOW'])
                                include(__DIR__ . '/brand.php');
                            ?>
                        </div>
                        <div class="catalog-element-main-block-action intec-grid-item-auto intec-grid-item-1024-2 intec-grid-item-768-1">
                            <?php if (
                                ($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) &&
                                (!$bOffers || $bSkuDynamic)
                            )
                                include(__DIR__ . '/buttons.php');
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
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
                '3' => true,
                '1024-2' => true,
                '768-1' => true
            ]
        ], true)
    ]) ?>
        <div data-sticky="top">
            <?php include(__DIR__ . '/main.container.view.3/purchase.php') ?>
        </div>
    <?= Html::endTag('div') ?>
</div>