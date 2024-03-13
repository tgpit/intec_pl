<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>

<div class="sale-personal-order-detail-block" data-role="block" data-block="change-history">
    <div class="sale-personal-order-detail-block-title">
        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item">
                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CHANGE_HISTORY_TITLE') ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="sale-personal-order-detail-block-button intec-cl-svg-path-stroke intec-cl-svg-rect-stroke intec-ui-picture" data-role="collapse" data-state="true">
                    <?= $arSvg['BLOCK_TOGGLE'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sale-personal-order-detail-block-content" data-role="content">
        <div class="sale-personal-order-detail-block-change-history">
            <div class="sale-personal-order-detail-block-change-history-item sale-personal-order-detail-block-change-history-header">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8">
                    <div class="sale-personal-order-detail-block-change-history-text intec-grid-item" data-code="date">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CHANGE_HISTORY_DATE') ?>
                    </div>
                    <div class="sale-personal-order-detail-block-change-history-text intec-grid-item" data-code="operation">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CHANGE_HISTORY_OPERATION') ?>
                    </div>
                    <div class="sale-personal-order-detail-block-change-history-text intec-grid-item" data-code="description">
                    </div>
                </div>
            </div>
            <div class="sale-personal-order-detail-block-change-history-pages" data-role="history-pages">
                <?php
                    $iCountElementOnPage = 5;
                    $iCountPage = ceil(count($arResult['CHANGE_HISTORY']) / $iCountElementOnPage);
                    $iCounter = 1;
                ?>
                <?php while ($iCounter <= $iCountPage) {
                        $iStartIndex = $iCounter * $iCountElementOnPage - $iCountElementOnPage;
                ?>
                    <div class="sale-personal-order-detail-block-change-history-page" data-id="<?= $iCounter ?>" data-role="history-page" data-expanded="<?= $iCounter == 1 ? 'true' : 'false' ?>">
                        <?php for ($i = $iStartIndex; $i < $iStartIndex + $iCountElementOnPage; $i++) { ?>
                            <?php if (isset($arResult['CHANGE_HISTORY'][$i])) { ?>
                                <div class="sale-personal-order-detail-block-change-history-item">
                                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-8">
                                        <div class="sale-personal-order-detail-block-change-history-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="date">
                                            <div class="sale-personal-order-detail-block-change-history-text-header intec-grid-item-1 intec-grid-item-450-2">
                                                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CHANGE_HISTORY_DATE') ?>
                                            </div>
                                            <div class="intec-grid-item-1 intec-grid-item-450-2">
                                                <?= $arResult['CHANGE_HISTORY'][$i]['DATE'] ?>
                                            </div>
                                        </div>
                                        <div class="sale-personal-order-detail-block-change-history-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="operation">
                                            <div class="sale-personal-order-detail-block-change-history-text-header intec-grid-item-1 intec-grid-item-450-2">
                                                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CHANGE_HISTORY_OPERATION') ?>
                                            </div>
                                            <div class="intec-grid-item-1 intec-grid-item-450-2">
                                                <?= $arResult['CHANGE_HISTORY'][$i]['NAME'] ?>
                                            </div>
                                        </div>
                                        <div class="sale-personal-order-detail-block-change-history-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="description">
                                            <div class="sale-personal-order-detail-block-change-history-text-header intec-grid-item-1 intec-grid-item-450-2"></div>
                                            <div class="intec-grid-item-1 intec-grid-item-450-2">
                                                <?= $arResult['CHANGE_HISTORY'][$i]['INFO'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php $iCounter++ ?>
                <?php } ?>
                <?php if ($iCountPage > 1) { ?>
                    <div class="sale-personal-order-detail-block-change-history-pagination">
                        <div class="sale-personal-order-detail-block-change-history-pagination-wrap" data-role="buttons">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-center intec-grid-a-v-center">
                                <div class="intec-grid-item-auto">
                                    <div class="sale-personal-order-detail-block-change-history-pagination-switch intec-ui-picture" data-role="prev" data-active="false">
                                        <?= $arSvg['PREV'] ?>
                                    </div>
                                </div>
                                <?php $iCounter = 1; ?>
                                <?php while ($iCounter <= $iCountPage) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::tag('div', $iCounter, [
                                            'class' => Html::cssClassFromArray([
                                                'sale-personal-order-detail-block-change-history-pagination-item' => true,
                                                'intec-cl-background' => $iCounter == 1 ? true : false
                                            ], true),
                                            'data' => [
                                                'role' => 'button',
                                                'id' => $iCounter,
                                                'active' => $iCounter == 1 ? 'true' : 'false',
                                                'disabled' => $iCounter <= 4 || $iCounter == $iCountPage ? 'true' : 'false'
                                            ]

                                        ]) ?>

                                    </div>
                                <?php $iCounter++ ?>
                                <?php } ?>
                                <div class="intec-grid-item-auto">
                                    <div class="sale-personal-order-detail-block-change-history-pagination-switch intec-ui-picture" data-role="next" data-active="true">
                                        <?= $arSvg['NEXT'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
