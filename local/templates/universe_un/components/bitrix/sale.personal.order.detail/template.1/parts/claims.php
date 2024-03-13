<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="sale-personal-order-detail-block" data-role="block" data-block="claims">
    <div class="sale-personal-order-detail-block-title">
        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item">
                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_TITLE') ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="sale-personal-order-detail-block-button intec-cl-svg-path-stroke intec-cl-svg-rect-stroke intec-ui-picture" data-role="collapse" data-state="true">
                    <?= $arSvg['BLOCK_TOGGLE'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sale-personal-order-detail-block-content" data-role="content">
        <div class="sale-personal-order-detail-block-claims">
            <?php if (!empty($arResult['TICKETS'])) { ?>
                <div class="sale-personal-order-detail-block-claims-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8">
                        <div class="sale-personal-order-detail-block-claims-text intec-grid-item" data-code="id">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_ID') ?>
                        </div>
                        <div class="sale-personal-order-detail-block-claims-text intec-grid-item" data-code="theme">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_THEME') ?>
                        </div>
                        <div class="sale-personal-order-detail-block-claims-text intec-grid-item" data-code="date">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_DATE') ?>
                        </div>
                    </div>
                </div>
                <div class="sale-personal-order-detail-block-claims-items">
                    <?php foreach ($arResult['TICKETS'] as $arTicket) { ?>
                        <div class="sale-personal-order-detail-block-claims-item">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8">
                                <div class="sale-personal-order-detail-block-claims-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="id">
                                    <div class="sale-personal-order-detail-block-claims-text-header intec-grid-item-1 intec-grid-item-450-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_ID') ?>
                                    </div>
                                    <div class="intec-grid-item-1 intec-grid-item-450-2">
                                        <?= Html::tag(empty($arTicket['TICKET_EDIT_URL']) ? 'div' : 'a', $arTicket['ID'], [
                                            'class' => 'intec-cl-text',
                                            'href' => !empty($arTicket['TICKET_EDIT_URL']) ? $arTicket['TICKET_EDIT_URL'] : null
                                        ]) ?>
                                        <div style="color: #808080;"><?= $arTicket['TIMESTAMP_X'] ?></div>
                                    </div>
                                </div>
                                <div class="sale-personal-order-detail-block-claims-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="theme">
                                    <div class="sale-personal-order-detail-block-claims-text-header intec-grid-item-1 intec-grid-item-450-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_THEME') ?>
                                    </div>
                                    <div class="intec-grid-item-1 intec-grid-item-450-2">
                                        <?= $arTicket['TITLE'] ?>
                                    </div>
                                </div>
                                <div class="sale-personal-order-detail-block-claims-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="date">
                                    <div class="sale-personal-order-detail-block-claims-text-header intec-grid-item-1 intec-grid-item-450-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_DATE') ?>
                                    </div>
                                    <div class="intec-grid-item-1 intec-grid-item-450-2">
                                        <?= $arTicket['TIMESTAMP_X'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="sale-personal-order-detail-block-claims-empty">
                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_EMPTY') ?>
                </div>
            <?php } ?>
            <div class="sale-personal-order-detail-block-claims-buttons">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-end intec-grid-a-h-450-start intec-grid-a-v-center intec-grid-i-4">
                    <?php if (!empty($arResult['PATH_TO_CLAIMS'])) { ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_BUTTON_ALL'), [
                                'class' => [
                                    'sale-personal-order-detail-block-claims-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-2',
                                        'scheme-current'
                                    ]
                                ],
                                'href' => $arResult['PATH_TO_CLAIMS']
                            ]) ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($arResult['PATH_TO_NEW_CLAIM'])) { ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_CLAIMS_BUTTON_NEW'), [
                                'class' => [
                                    'sale-personal-order-detail-block-claims-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-2',
                                        'scheme-current'
                                    ]
                                ],
                                'href' => $arResult['PATH_TO_NEW_CLAIM']
                            ]) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>