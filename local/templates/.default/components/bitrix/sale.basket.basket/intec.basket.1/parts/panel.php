<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-panel" data-entity="basket-items-list-header">
    <div class="intec-basket-panel-content intec-basket-grid intec-basket-grid-a-v-center">
        <?php if ($arParams['SHOW_FILTER'] === 'Y') { ?>
            <div class="intec-basket-panel-item intec-basket-grid-item-auto">
                <div class="intec-basket-filter" data-entity="basket-filter">
                    <input class="intec-basket-filter-input" data-entity="basket-filter-input" placeholder="<?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_FILTER_PLACEHOLDER') ?>" />
                    <button class="intec-basket-filter-clear intec-basket-picture intec-basket-align-middle" data-entity="basket-filter-clear-btn">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L13 13M13 1L1 13" stroke="#B0B0B0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php } ?>
        <div class="intec-basket-panel-item intec-basket-grid-item">
            <div class="intec-basket-tabs">
                <div class="intec-basket-tabs-content intec-basket-align-middle">
                    <?php foreach ([
                        'all',
                        'similar',
                        'warning',
                        'delayed',
                        'not-available'
                   ] as $item) { ?>
                        <div class="intec-basket-tabs-item<?= $item === 'all' ? ' active intec-basket-scheme-border' : null ?>" data-entity="basket-items-count" data-filter="<?= $item ?>" style="display: none;"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>