<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-restore">
    <div class="intec-basket-restore-content intec-basket-grid intec-basket-grid-a-v-center">
        <div class="intec-basket-restore-content-main intec-basket-restore-item intec-basket-grid-item">
            <div class="intec-basket-restore-text">
                <span class="intec-basket-restore-text-default">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_RESTORE_PART_1') ?>
                </span>
                <a class="intec-basket-restore-link intec-basket-restore-text-dark intec-basket-scheme-color-hover" href="{{DETAIL_PAGE_URL}}" target="_blank">
                    {{NAME}}
                </a>
                <span class="intec-basket-restore-text-default">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_RESTORE_PART_2') ?>
                </span>
            </div>
        </div>
        <div class="intec-basket-restore-content-restore intec-basket-restore-item intec-basket-grid-item-auto">
            <span class="intec-basket-restore-restore intec-basket-restore-link intec-basket-restore-text intec-basket-scheme-color intec-basket-scheme-color-light-hover" data-entity="basket-item-restore-button">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_RESTORE_ACTION') ?>
            </span>
        </div>
        <div class="intec-basket-restore-content-close intec-basket-restore-item intec-basket-grid-item-auto">
            <div class="intec-basket-restore-close intec-basket-picture" data-entity="basket-item-close-restore-button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3L13 13M13 3L3 13" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
</div>