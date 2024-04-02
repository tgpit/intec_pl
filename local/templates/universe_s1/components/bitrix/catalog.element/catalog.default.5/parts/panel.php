<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<div class="catalog-element-panel" data-role="panel">
    <div class="intec-content intec-content-primary intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-16">
                <div class="catalog-element-panel-block catalog-element-panel-block-information intec-grid-item-auto">
                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-12">
                        <div class="intec-grid-item-auto">
                            <?php include(__DIR__.'/panel/gallery.php') ?>
                        </div>
                        <div class="intec-grid-item">
                            <?= Html::tag('div', $arResult['NAME'], [
                                'class' => 'catalog-element-panel-name',
                                'title' => $arResult['NAME']
                            ]) ?>
                            <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                <div class="catalog-element-panel-quantity">
                                    <?php include(__DIR__.'/purchase/quantity.php') ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="catalog-element-panel-block catalog-element-panel-block-action intec-grid-item intec-grid-item-shrink-1">
                    <div class="intec-grid intec-grid-a-h-end intec-grid-a-v-center intec-grid-i-h-16">
                        <?php if ($arVisual['PRICE']['SHOW']) { ?>
                            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                <?php include(__DIR__.'/panel/price.php') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arResult['ACTION'] !== 'none') { ?>
                            <div class="intec-grid-item-auto">
                                <?php include(__DIR__.'/panel/order.php') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) { ?>
                            <div class="catalog-element-panel-button-action-container intec-grid-item-auto">
                                <?php include(__DIR__.'/panel/buttons.php') ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>