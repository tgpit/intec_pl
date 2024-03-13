<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplate
 * @var IntecBasketLiteComponent $component
 * @var CBitrixComponentTemplate $this
 */

?>
<div class="sale-basket-small-content">
    <div class="sale-basket-small-switches" data-role="switches">
        <?php if ($arResult['BASKET']['SHOW']) { ?>
            <div class="sale-basket-small-switch intec-cl-background-dark" data-role="switch" data-tab="basket">
                <div class="sale-basket-small-switch-icon intec-ui-picture">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.00974 4.33374L3.28925 1.09317H1.20557" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.91355 13.2021L4.01025 4.33374H17.6833C18.3714 4.33374 18.8834 4.96781 18.7397 5.64077L17.1173 13.2021C17.0103 13.7001 16.5707 14.0554 16.0608 14.0554H6.96889C6.46012 14.0554 6.02048 13.7001 5.91355 13.2021Z" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.5058 17.3687C15.093 17.3687 14.758 17.7037 14.762 18.1165C14.762 18.5293 15.097 18.8643 15.5098 18.8643C15.9226 18.8643 16.2576 18.5293 16.2576 18.1165C16.2556 17.7037 15.9206 17.3687 15.5058 17.3687" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.52977 17.3687C7.11697 17.3687 6.78195 17.7037 6.78593 18.1165C6.78394 18.5293 7.11896 18.8643 7.53176 18.8643C7.94456 18.8643 8.27958 18.5293 8.27958 18.1165C8.27958 17.7037 7.94456 17.3687 7.52977 17.3687" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <?php if ($arResult['BASKET']['COUNT'] > 0) { ?>
                    <div class="sale-basket-small-switch-count">
                        <?= $arResult['BASKET']['COUNT'] ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if ($arResult['DELAYED']['SHOW']) { ?>
            <div class="sale-basket-small-switch intec-cl-background-dark" data-tab="delayed">
                <div class="sale-basket-small-switch-icon intec-ui-picture">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4837 2.63113C16.4036 2.63113 18.3656 5.37603 18.3656 7.93671C18.3656 13.1225 10.223 17.3688 10.0756 17.3688C9.92823 17.3688 1.78564 13.1225 1.78564 7.93671C1.78564 5.37603 3.7476 2.63113 6.66751 2.63113C8.34392 2.63113 9.44004 3.46934 10.0756 4.20623C10.7112 3.46934 11.8073 2.63113 13.4837 2.63113Z" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <?php if ($arResult['DELAYED']['COUNT'] > 0) { ?>
                    <div class="sale-basket-small-switch-count">
                        <?= $arResult['DELAYED']['COUNT'] ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if ($arResult['FORM']['SHOW']) { ?>
            <div class="sale-basket-small-switch intec-cl-background-dark" data-tab="form">
                <div class="sale-basket-small-switch-icon intec-ui-picture">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.9041 3.29987V1M15.0025 4.92158L16.6242 3.3097M16.6343 9.02989H18.9243M8.0815 11.8497C6.93156 10.6998 6.06469 9.41222 5.48874 8.10601C5.36687 7.82983 5.43861 7.50647 5.65189 7.29319L6.45685 6.48824C7.11634 5.82874 7.11634 4.89602 6.54039 4.32007L5.38652 3.16718C4.61892 2.39958 3.37463 2.39958 2.60702 3.16718L1.9662 3.80702C1.23791 4.53531 0.934211 5.58598 1.13078 6.6278C1.61631 9.19599 3.10828 12.0079 5.51528 14.4149C7.92228 16.8219 10.7342 18.3139 13.3024 18.7994C14.3442 18.996 15.3949 18.6923 16.1232 17.964L16.763 17.3242C17.5306 16.5566 17.5306 15.3123 16.763 14.5447L15.6101 13.3918C15.0342 12.8158 14.1005 12.8158 13.5255 13.3918L12.638 14.2803C12.4247 14.4936 12.1014 14.5653 11.8252 14.4434C10.519 13.8665 9.23143 12.9986 8.0815 11.8497Z" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        <?php } ?>
        <?php if ($arResult['PERSONAL']['SHOW']) { ?>
            <a rel="nofollow" href="<?= $arResult['URL']['PERSONAL'] ?>" class="sale-basket-small-switch intec-cl-background-dark">
                <div class="sale-basket-small-switch-icon intec-ui-picture">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.3333 12.25L9.90541 14.5L8.44444 13.1504M6.11111 7.75V4.85714C6.11111 2.7269 7.85223 1 10 1C12.1478 1 13.8889 2.7269 13.8889 4.85714V7.75M15 19H5C3.89543 19 3 18.0842 3 16.9545V9.79545C3 8.66578 3.89543 7.75 5 7.75H15C16.1046 7.75 17 8.66578 17 9.79545V16.9545C17 18.0842 16.1046 19 15 19Z" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </a>
        <?php } ?>
        <?php if ($arResult['COMPARE']['SHOW']) { ?>
            <a rel="nofollow" href="<?= $arResult['URL']['COMPARE'] ?>" class="sale-basket-small-switch intec-cl-background-dark">
                <div class="sale-basket-small-switch-icon intec-ui-picture">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.17546 10.807V18.8766" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.8768 6.77213V18.8766" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.8244 1.12337V18.8766" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.12337 6.77213V18.8766" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </a>
        <?php } ?>
    </div>
    <div class="sale-basket-small-tabs" data-role="tabs" >
        <?php if ($arResult['BASKET']['SHOW']) { ?>
            <div class="sale-basket-small-tab sale-basket-small-tab-products" data-role="tab" data-tab="basket" data-active="false">
                <div class="sale-basket-small-tab-wrapper">
                    <div class="sale-basket-small-tab-header intec-grid intec-grid-nowrap intec-grid-a-v-center">
                        <div class="sale-basket-small-tab-title intec-grid-item-auto">
                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BASKET_TITLE') ?>
                        </div>
                        <div class="intec-grid-item"></div>
                        <div class="sale-basket-small-tab-buttons intec-grid-item-auto">
                            <?php if ($arResult['BASKET']['COUNT'] > 0) { ?>
                                <div data-role="button" data-action="basket.clear" class="intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current intec-ui-size-2">
                                    <div class="intec-ui-part-icon">
                                        <i class="fal fa-times"></i>
                                    </div>
                                    <div class="intec-ui-part-content">
                                        <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_CLEAR') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php $arItems = &$arResult['BASKET']['ITEMS'] ?>
                    <?php if (!empty($arItems)) { ?>
                        <div class="sale-basket-small-tab-content">
                            <?php include(__DIR__.'/content/products.php') ?>
                            <div class="sale-basket-small-products-total">
                                <div class="sale-basket-small-products-total-title">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_TOTAL') ?>:
                                </div>
                                <div class="sale-basket-small-products-total-value">
                                    <?= $arResult['BASKET']['SUM']['DISCOUNT']['DISPLAY'] ?>
                                </div>
                                <?php if ($arResult['BASKET']['SUM']['ECONOMY']['VALUE'] > 0) { ?>
                                    <div class="sale-basket-small-products-total-economy">
                                        <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_ECONOMY'); ?>
                                        <?= $arResult['BASKET']['SUM']['ECONOMY']['DISPLAY']; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="sale-basket-small-tab-footer">
                            <div class="sale-basket-small-tab-buttons intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-5">
                                <?php if (true) { ?>
                                    <div class="intec-grid-item-auto">
                                        <div data-role="button" data-action="close" class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_SHOPPING') ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item"></div>
                                <?php if (!empty($arResult['URL']['BASKET'])) { ?>
                                    <div class="intec-grid-item-auto">
                                        <a href="<?= $arResult['URL']['BASKET'] ?>" class="intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current intec-ui-size-2">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_BASKET') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($arResult['URL']['ORDER'])) { ?>
                                    <div class="intec-grid-item-auto">
                                        <a href="<?= $arResult['URL']['ORDER'] ?>" class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_ORDER') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="sale-basket-small-tab-content">
                            <div class="sale-basket-small-products-empty">
                                <div class="sale-basket-small-products-icon">
                                    <img src="<?= $this->GetFolder().'/images/basket.empty.png' ?>" alt="" />
                                </div>
                                <div class="sale-basket-small-products-title">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BASKET_EMPTY_TITLE') ?>
                                </div>
                                <div class="sale-basket-small-products-description">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BASKET_EMPTY_DESCRIPTION') ?>
                                </div>
                                <?php if (!empty($arResult['URL']['CATALOG'])) { ?>
                                    <div class="sale-basket-small-products-button">
                                        <a href="<?= $arResult['URL']['CATALOG'] ?>" class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-4">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_CATALOG') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($arResult['DELAYED']['SHOW']) { ?>
            <div class="sale-basket-small-tab sale-basket-small-tab-products" data-role="tab" data-tab="delayed" data-active="false">
                <div class="sale-basket-small-tab-wrapper">
                    <div class="sale-basket-small-tab-header intec-grid intec-grid-nowrap intec-grid-a-v-center">
                        <div class="sale-basket-small-tab-title intec-grid-item-auto">
                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_DELAYED_TITLE') ?>
                        </div>
                        <div class="intec-grid-item"></div>
                        <div class="sale-basket-small-tab-buttons intec-grid-item-auto">
                            <?php if ($arResult['DELAYED']['COUNT'] > 0) { ?>
                                <div data-role="button" data-action="delayed.clear" class="intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current intec-ui-size-2">
                                    <div class="intec-ui-part-icon">
                                        <i class="fal fa-times"></i>
                                    </div>
                                    <div class="intec-ui-part-content">
                                        <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_CLEAR') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php $arItems = &$arResult['DELAYED']['ITEMS'] ?>
                    <?php if (!empty($arItems)) { ?>
                        <div class="sale-basket-small-tab-content">
                            <?php include(__DIR__.'/content/products.php') ?>
                        </div>
                        <div class="sale-basket-small-tab-footer">
                            <div class="sale-basket-small-tab-buttons intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-5">
                                <?php if (true) { ?>
                                    <div class="intec-grid-item-auto">
                                        <div data-role="button" data-action="close" class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_SHOPPING') ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item"></div>
                                <?php if (!empty($arResult['URL']['DELAYED'])) { ?>
                                    <div class="intec-grid-item-auto">
                                        <a href="<?= $arResult['URL']['DELAYED'] ?>" class="intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current intec-ui-size-2">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_BASKET') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="sale-basket-small-tab-content">
                            <div class="sale-basket-small-products-empty">
                                <div class="sale-basket-small-products-icon">
                                    <img src="<?= $this->GetFolder().'/images/delayed.empty.png' ?>" alt="" />
                                </div>
                                <div class="sale-basket-small-products-title">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_DELAYED_EMPTY_TITLE') ?>
                                </div>
                                <div class="sale-basket-small-products-description">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_DELAYED_EMPTY_DESCRIPTION') ?>
                                </div>
                                <?php if (!empty($arResult['URL']['CATALOG'])) { ?>
                                    <div class="sale-basket-small-products-button">
                                        <a href="<?= $arResult['URL']['CATALOG'] ?>" class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-4">
                                            <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BUTTONS_CATALOG') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($arResult['FORM']['SHOW']) { ?>
            <div class="sale-basket-small-tab sale-basket-small-tab-form" data-tab="form" data-active="false">
                <div class="sale-basket-small-tab-wrapper">
                    <?php if (!empty($arResult['FORM']['TITLE'])) { ?>
                        <div class="sale-basket-small-tab-header">
                            <div class="sale-basket-small-tab-title">
                                <?= $arResult['FORM']['TITLE'] ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div data-role="area" data-area="form" class="sale-basket-small-tab-content"></div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>