<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var boolean $bSearchApply
 * @var array $arGet
 * @var array $arSvg
 */

CJSCore::Init(['date']);

?>

<div class="sale-personal-order-list-filter">
    <div class="sale-personal-order-list-filter-title">
        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_TITLE') ?>
    </div>
    <div class="sale-personal-order-list-filter-wrap">
        <form action="" method="get" class="sale-personal-order-list-filter-form" data-role="filter">
            <?php if (isset($arGet['filter_history'])) { ?>
                <input type="hidden" name="filter_history" value="<?= $arGet['filter_history'] ?>">
            <?php } ?>
            <?php if (isset($arGet['show_canceled'])) { ?>
                <input type="hidden" name="show_canceled" value="<?= $arGet['show_canceled'] ?>">
            <?php } ?>
            <?php if (isset($arGet['show_all'])) { ?>
                <input type="hidden" name="show_all" value="<?= $arGet['show_all'] ?>">
            <?php } ?>
            <?php if (isset($arGet['by'])) { ?>
                <input type="hidden" name="by" value="<?= $arGet['by'] ?>">
            <?php } ?>
            <?php if (isset($arGet['order'])) { ?>
                <input type="hidden" name="order" value="<?= $arGet['order'] ?>">
            <?php } ?>
            <?php if (isset($arGet['SECTION'])) { ?>
                <input type="hidden" name="SECTION" value="<?= $arGet['SECTION'] ?>">
            <?php } ?>

            <div class="intec-grid intec-grid-i-8 intec-grid-wrap intec-grid-a-v-end">
                <div class="intec-grid-item-auto intec-grid-item-768-1">
                    <div class="sale-personal-order-list-filter-form-label"><?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_LABEL_DATE') ?></div>
                    <div class="sale-personal-order-list-filter-form-date-wrap">
                        <span><?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_INPUT_DATE_FROM') ?></span>
                        <input class="sale-personal-order-list-filter-form-date-input" placeholder="<?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_INPUT_DATE_PLACEHOLDER') ?>" autocomplete="off" value="<?= isset($arGet['filter_date_from']) ? $arGet['filter_date_from'] : null ?>" name="filter_date_from" type="text" onclick="BX.calendar({node: this, field: this, bTime: false});">
                    </div>
                    <div class="sale-personal-order-list-filter-form-date-delimetr intec-ui-picture">
                        <?= $arSvg['DELIMETER'] ?>
                    </div>
                    <div class="sale-personal-order-list-filter-form-date-wrap">
                        <span><?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_INPUT_DATE_TO') ?></span>
                        <input class="sale-personal-order-list-filter-form-date-input" placeholder="<?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_INPUT_DATE_PLACEHOLDER') ?>" autocomplete="off" value="<?= isset($arGet['filter_date_to']) ? $arGet['filter_date_to'] : null ?>" name="filter_date_to" type="text" onclick="BX.calendar({node: this, field: this, bTime: false});">
                    </div>
                </div>
                <?php if ($bShowFilterBlockCancelled) { ?>
                    <div class="intec-grid-item-auto intec-grid-item-768-1">
                        <div class="sale-personal-order-list-filter-form-label"><?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_LABEL_STATUS') ?></div>
                        <select class="sale-personal-order-list-filter-form-status" name="filter_status">
                            <option value=""><?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_SELECT_ALL') ?></option>
                            <?php foreach ($arResult['INFO']['STATUS'] as $status) { ?>
                                <option value="<?= $status['ID'] ?>" <?= isset($arGet['filter_status']) && $arGet['filter_status'] == $status['ID'] ? 'selected=""' : null ?>><?= $status['NAME'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                <?php if ($bShowFilterBlockCancelled) { ?>
                    <div class="intec-grid-item-auto intec-grid-item-768-1">
                        <label class="sale-personal-order-list-filter-form-checkbox intec-ui intec-ui-control-checkbox intec-ui-scheme-current intec-ui-size-2">
                            <input type="checkbox" name="filter_payed" value="Y" <?= isset($arGet['filter_payed']) && $arGet['filter_payed'] === 'Y' ? 'checked=""' : null ?>>
                            <span class="intec-ui-part-selector"></span>
                            <span class="intec-ui-part-content"><?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_CHECKBOX_PAYED') ?></span>
                        </label>
                    </div>
                <?php } ?>
                <div class="intec-grid-item-auto">
                    <button type="submit" class="sale-personal-order-list-filter-form-button-apply intec-ui intec-ui-control-button intec-ui-scheme-current">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_BUTTON_APPLY') ?>
                    </button>
                </div>
                <div class="intec-grid-item-auto">
                    <div class="sale-personal-order-list-filter-form-button-clear intec-cl-text-hover intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current" data-role="clear">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_BUTTON_CLEAR') ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
