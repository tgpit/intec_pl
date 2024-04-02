<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
{{#HAS_SIMILAR_ITEMS}}
    <div class="intec-basket-offers-item intec-basket-notify" data-entity="basket-item-sku-notification">
        <div class="intec-basket-notify-title">
            <span class="intec-basket-notify-active intec-basket-scheme-color intec-basket-scheme-color-light-hover" data-entity="basket-item-show-similar-link">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_OFFERS_SIMILAR_PART_1') ?>
            </span>
            <span>
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_OFFERS_SIMILAR_PART_2') ?>
            </span>
            <span>
                {{SIMILAR_ITEMS_QUANTITY}}
            </span>
            <span>
                {{MEASURE_TEXT}}.
            </span>
        </div>
        <button class="intec-basket-notify-body" data-entity="basket-item-merge-sku-link">
            <span class="intec-basket-grid intec-basket-grid-stretch">
                <span class="intec-basket-grid-item-auto">
                    <span class="intec-basket-notify-icon intec-basket-picture intec-basket-align-middle">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6667 9.99996H3.33333C2.87333 9.99996 2.5 9.62662 2.5 9.16663V7.49996C2.5 7.03996 2.87333 6.66663 3.33333 6.66663H16.6667C17.1267 6.66663 17.5 7.03996 17.5 7.49996V9.16663C17.5 9.62662 17.1267 9.99996 16.6667 9.99996Z" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.6992 2.52502L14.1659 6.64169" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.30065 2.52502L5.83398 6.64169" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.1706 18.3367C11.8622 18.3367 10.0039 16.47 10.0039 14.17C10.0039 11.9117 11.9206 9.99499 14.1706 10.0033C16.4706 10.0033 18.3372 11.87 18.3372 14.17C18.3372 16.47 16.4706 18.3367 14.1706 18.3367Z" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.636 17.4767H6.16266C5.57432 17.4767 5.06599 17.0667 4.94099 16.4917L3.53516 10" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.8333 14.1667H12.5" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.166 15.8333L15.8327 14.1667L14.166 12.5" stroke="#FFF" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </span>
                <span class="intec-basket-grid-item-auto intec-basket-grid-item-shrink">
                    <span class="intec-basket-notify-content">
                        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_OFFERS_SIMILAR_PART_3') ?>
                    </span>
                </span>
            </span>
        </button>
    </div>
{{/HAS_SIMILAR_ITEMS}}