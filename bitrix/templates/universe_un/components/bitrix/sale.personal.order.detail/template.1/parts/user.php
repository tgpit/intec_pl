<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>

<div class="sale-personal-order-detail-block" data-role="block" data-block="user">
    <div class="sale-personal-order-detail-block-title">
        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item">
                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_USER_TITLE') ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="sale-personal-order-detail-block-button intec-cl-svg-path-stroke intec-cl-svg-rect-stroke intec-ui-picture" data-role="collapse" data-state="true">
                    <?= $arSvg['BLOCK_TOGGLE'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sale-personal-order-detail-block-content" data-role="content">
        <div class="sale-personal-order-detail-block-user">
            <?php if ($bShowUserLogin) { ?>
                <div class="sale-personal-order-detail-block-user-field intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8" style="margin-bottom: 24px;">
                    <div class="sale-personal-order-detail-block-user-field-header intec-grid-item-1 intec-grid-item-425-2">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_USER_LOGIN') ?>
                    </div>
                    <div class="sale-personal-order-detail-block-user-field-text intec-grid-item-1 intec-grid-item-425-2">
                        <?= $arResult['USER']['LOGIN'] ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($bShowUserEmail || $bShowUserType) { ?>
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-12">
                    <?php if ($bShowUserType) { ?>
                        <div class="intec-grid-item-2 intec-grid-item-425-1">
                            <div class="sale-personal-order-detail-block-user-field intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <div class="sale-personal-order-detail-block-user-field-header intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_USER_PERSON_NAME') ?>
                                </div>
                                <div class="sale-personal-order-detail-block-user-field-text intec-grid-item-1 intec-grid-item-425-2">
                                    <?= $arResult['USER']['PERSON_TYPE_NAME'] ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($bShowUserEmail) { ?>
                        <div class="intec-grid-item-2 intec-grid-item-425-1">
                            <div class="sale-personal-order-detail-block-user-field intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <div class="sale-personal-order-detail-block-user-field-header intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_USER_EMAIL') ?>
                                </div>
                                <div class="sale-personal-order-detail-block-user-field-text intec-grid-item-1 intec-grid-item-425-2">
                                    <?= $arResult['USER']['EMAIL'] ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>