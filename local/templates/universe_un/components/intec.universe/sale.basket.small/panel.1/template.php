<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var IntecSaleBasketSmallComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!defined('EDITOR')) {
    if (empty($arResult['CURRENCY']))
        return;

    if (!$component->getIsBase() && !$component->getIsLite())
        return;
}

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));

$arVisual = $arResult['VISUAL'];

?>
<?php if (!defined('EDITOR')) { ?>
    <?php $oFrame = $this->createFrame()->begin() ?>
        <div id="<?= $sTemplateId ?>" class="ns-intec-universe c-sale-basket-small c-sale-basket-small-panel-1">
            <!--noindex-->
            <div class="sale-basket-small-panel intec-content-wrap" data-role="panel">
                <div class="sale-basket-small-panel-wrapper intec-grid intec-grid-nowrap">
                    <?php if ($arResult['BASKET']['SHOW']) { ?>
                        <?= Html::beginTag('a', [
                            'class' => 'sale-basket-small-panel-button intec-grid-item',
                            'href' => $arResult['URL']['BASKET']
                        ]) ?>
                        <div class="sale-basket-small-panel-button-wrapper">
                            <div class="sale-basket-small-panel-button-icon-wrap intec-ui-align">
                                <div class="sale-basket-small-panel-button-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.96905 6.625L5.30205 3.625H3.37305" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.73099 14.835L5.96899 6.625H18.627C19.264 6.625 19.738 7.212 19.605 7.835L18.103 14.835C18.004 15.296 17.597 15.625 17.125 15.625H8.70799C8.23699 15.625 7.82999 15.296 7.73099 14.835Z" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17.465 19.25C17.258 19.25 17.09 19.418 17.092 19.625C17.092 19.832 17.26 20 17.467 20C17.674 20 17.842 19.832 17.842 19.625C17.841 19.418 17.673 19.25 17.465 19.25" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.85593 19.25C8.64893 19.25 8.48093 19.418 8.48293 19.625C8.48193 19.832 8.64993 20 8.85693 20C9.06393 20 9.23193 19.832 9.23193 19.625C9.23193 19.418 9.06393 19.25 8.85593 19.25" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <?php if ($arResult['BASKET']['COUNT'] > 0) { ?>
                                <div class="sale-basket-small-panel-button-counter intec-cl-background">
                                    <?= $arResult['BASKET']['COUNT'] ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?= Html::endTag('a') ?>
                    <?php } ?>
                    <?php if ($arResult['DELAYED']['SHOW']) { ?>
                        <?= Html::beginTag('a', [
                            'class' => 'sale-basket-small-panel-button intec-grid-item',
                            'href' => $arResult['URL']['DELAYED']
                        ]) ?>
                        <div class="sale-basket-small-panel-button-wrapper">
                            <div class="sale-basket-small-panel-button-icon-wrap intec-ui-align">
                                <div class="sale-basket-small-panel-button-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7 4C18.87 4 21 6.98 21 9.76C21 15.39 12.16 20 12 20C11.84 20 3 15.39 3 9.76C3 6.98 5.13 4 8.3 4C10.12 4 11.31 4.91 12 5.71C12.69 4.91 13.88 4 15.7 4Z" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <?php if ($arResult['DELAYED']['COUNT'] > 0) { ?>
                                <div class="sale-basket-small-panel-button-counter intec-cl-background">
                                    <?= $arResult['DELAYED']['COUNT'] ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?= Html::endTag('a') ?>
                    <?php } ?>
                    <?php if ($arResult['COMPARE']['SHOW']) { ?>
                        <?= Html::beginTag('a', [
                            'class' => 'sale-basket-small-panel-button intec-grid-item',
                            'href' => $arResult['URL']['COMPARE']
                        ]) ?>
                        <div class="sale-basket-small-panel-button-wrapper">
                            <div class="sale-basket-small-panel-button-icon-wrap intec-ui-align">
                                <div class="sale-basket-small-panel-button-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 13V21" stroke="#808080" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 8V21" stroke="#808080" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M15 3V21" stroke="#808080" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M3 8V21" stroke="#808080" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <?php if ($arResult['COMPARE']['COUNT'] > 0) { ?>
                                <div class="sale-basket-small-panel-button-counter intec-cl-background">
                                    <?= $arResult['COMPARE']['COUNT'] ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?= Html::endTag('a') ?>
                    <?php } ?>
                    <?php if ($arResult['FORM']['SHOW']) { ?>
                        <div data-role="button" data-action="form" class="sale-basket-small-panel-button intec-grid-item">
                            <div class="sale-basket-small-panel-button-wrapper">
                                <div class="sale-basket-small-panel-button-icon-wrap intec-ui-align">
                                    <div class="sale-basket-small-panel-button-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20 20V5.778C20 4.796 19.204 4 18.222 4H5.778C4.796 4 4 4.796 4 5.778V16.445C4 17.427 4.796 18.223 5.778 18.223H16.667L20 20Z" stroke="#808080" stroke-width="1.3333" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12.177 11.073C12.275 11.171 12.275 11.329 12.177 11.427C12.079 11.525 11.921 11.525 11.823 11.427C11.725 11.329 11.725 11.171 11.823 11.073C11.921 10.975 12.079 10.976 12.177 11.073" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.177 11.073C16.275 11.171 16.275 11.329 16.177 11.427C16.079 11.525 15.921 11.525 15.823 11.427C15.725 11.329 15.725 11.171 15.823 11.073C15.921 10.975 16.079 10.976 16.177 11.073" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.177 11.073C8.275 11.171 8.275 11.329 8.177 11.427C8.079 11.525 7.921 11.525 7.823 11.427C7.725 11.329 7.725 11.171 7.823 11.073C7.921 10.975 8.079 10.976 8.177 11.073" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($arResult['PERSONAL']['SHOW']) { ?>
                        <?= Html::beginTag('a', [
                            'class' => 'sale-basket-small-panel-button intec-grid-item',
                            'href' => $arResult['URL']['PERSONAL']
                        ]) ?>
                        <div class="sale-basket-small-panel-button-wrapper">
                            <div class="sale-basket-small-panel-button-icon-wrap intec-ui-align">
                                <div class="sale-basket-small-panel-button-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.3639 5.63604C21.8787 9.15076 21.8787 14.8492 18.3639 18.3639C14.8492 21.8787 9.15074 21.8787 5.63604 18.3639C2.12132 14.8492 2.12132 9.15074 5.63604 5.63604C9.15076 2.12132 14.8492 2.12132 18.3639 5.63604" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M13.9891 8.3239C15.0876 9.42244 15.0876 11.2035 13.9891 12.3021C12.8906 13.4006 11.1095 13.4006 10.0109 12.3021C8.91238 11.2035 8.91238 9.42244 10.0109 8.3239C11.1095 7.22537 12.8906 7.22537 13.9891 8.3239" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17.707 18.958C16.272 17.447 14.248 16.5 12 16.5C9.75197 16.5 7.72797 17.447 6.29297 18.959" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <?= Html::endTag('a') ?>
                    <?php } ?>
                </div>
            </div>
            <?php include(__DIR__.'/parts/script.php') ?>
            <!--/noindex-->
        </div>
    <?php $oFrame->beginStub() ?>
    <?php $oFrame->end() ?>
<?php } else { ?>
    <div class="intec-editor-element-stub">
        <div class="intec-editor-element-stub-wrapper">
            <?= Loc::getMessage('C_SALE_BASKET_SMALL_PANEL_1_EDITOR') ?>
        </div>
    </div>
<?php } ?>
