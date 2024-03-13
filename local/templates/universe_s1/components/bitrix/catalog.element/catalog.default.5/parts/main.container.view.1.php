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
        (($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) && !$bSkuList) ||
        $arVisual['VOTE']['SHOW'] || $arFields['BRAND']['SHOW']
    ) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'catalog-element-main-block-top-left' => true,
                'catalog-element-main-block-1024-hide' => !$arFields['MARKS']['SHOW'] && !$arFields['ARTICLE']['SHOW'],
                'intec-grid-item' => [
                    '3' => true,
                    'a-center' => true,
                    '1024-1' => true
                ]
            ], true)
        ]) ?>
            <div class="catalog-element-main-block-container">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-8">
                    <?php if ($arFields['MARKS']['SHOW']) { ?>
                        <div class="intec-grid-item">
                            <?php include(__DIR__.'/marks.php') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arFields['ARTICLE']['SHOW']) { ?>
                        <div class="intec-grid-item">
                            <?php include(__DIR__.'/article.php') ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'catalog-element-main-block-top-center' => true,
                'catalog-element-main-block-1024-hide' => !$arResult['DELAY']['USE'] &&
                    !$arResult['COMPARE']['USE'] || $bSkuList,
                'intec-grid-item' => [
                    '3' => true,
                    'a-center' => true,
                    '1024-1' => true
                ]
            ], true)
        ]) ?>
            <div class="catalog-element-main-block-container">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-8">
                    <div class="intec-grid-item">
                        <?php if (
                            ($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) &&
                            (!$bOffers || $bSkuDynamic)
                        )
                            include(__DIR__.'/buttons.php');
                        ?>
                    </div>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'catalog-element-main-block-top-right' => true,
                'catalog-element-main-block-1024-hide' => !$arVisual['VOTE']['SHOW'] && !$arFields['BRAND']['SHOW'],
                'intec-grid-item' => [
                    '3' => true,
                    'a-center' => true,
                    '1024-1' => true
                ]
            ], true)
        ]) ?>
            <div class="catalog-element-main-block-container">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-8">
                    <?php if ($arVisual['VOTE']['SHOW']) { ?>
                        <div class="intec-grid-item-auto">
                            <?php include(__DIR__ . '/vote.php') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arFields['BRAND']['SHOW']) { ?>
                        <div class="intec-grid-item">
                            <?php include(__DIR__.'/brand.php') ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'catalog-element-main-block-bottom-left' => true,
            'intec-grid-item' => [
                '3' => $bSkuDynamic || $arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arResult['SIZES']['SHOW'],
                '2' => (!$bOffers || $bSkuList) && !$arVisual['PROPERTIES']['PREVIEW']['SHOW'] && !$arResult['SIZES']['SHOW'],
                '1024-1' => $bSkuDynamic || $arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arResult['SIZES']['SHOW'],
                '768-1' => true
            ]
        ], true)
    ]) ?>
        <div class="catalog-element-main-block-container" data-sticky="top">
            <?php include(__DIR__.'/gallery.php') ?>
            <?php if ($arVisual['DESCRIPTION']['PREVIEW']['SHOW']) { ?>
                <? include(__DIR__ . '/description.preview.php'); ?>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
    <?php if ($bSkuDynamic || $arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arResult['SIZES']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-main-block-bottom-center',
                'intec-grid-item' => [
                    '3',
                    '1024-2',
                    '768-1'
                ]
            ]
        ]) ?>
            <div class="catalog-element-main-block-container" data-sticky="top">
                <div class="catalog-element-middle-container">
                    <?php if ($arResult['SHARES']['SHOW']) {
                        include(__DIR__ . '/shares.php');
                    } ?>
                    <?php if ($arResult['SIZES']['SHOW'])
                        include(__DIR__.'/sizes.php');
                    ?>
                    <?php if (!empty($arResult['OFFERS']) && $arResult['SKU']['VIEW'] === 'dynamic')
                        include(__DIR__.'/offers.php');
                    ?>
                    <?php if ($arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arVisual['PROPERTIES']['PREVIEW']['OFFER_SHOW']) {
                        include(__DIR__ . '/properties.preview.php');
                    }
                    ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'catalog-element-main-block-bottom-right' => true,
            'intec-grid-item' => [
                '3' => $bSkuDynamic || $arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arResult['SIZES']['SHOW'],
                '2' => (!$bOffers || $bSkuList) && !$arVisual['PROPERTIES']['PREVIEW']['SHOW'] && !$arResult['SIZES']['SHOW'],
                '1024-2' => $bSkuDynamic || $arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arResult['SIZES']['SHOW'],
                '768-1' => true
            ]
        ], true)
    ]) ?>
        <?php include(__DIR__ . '/main.container.view.1/purchase.php') ?>
    <?= Html::endTag('div') ?>
</div>