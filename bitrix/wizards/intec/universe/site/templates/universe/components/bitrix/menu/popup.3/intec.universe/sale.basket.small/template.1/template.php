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
        <div id="<?= $sTemplateId ?>" class="menu-basket">
            <!--noindex-->
            <div class="menu-basket-panel intec-content-wrap" data-role="panel">
                <div class="menu-basket-panel-wrapper">
                    <?php if ($arResult['BASKET']['SHOW']) { ?>
                        <div class="menu-basket-panel-button-wrap">
                            <?= Html::beginTag('a', [
                                'class' => Html::cssClassFromArray([
                                    'menu-basket-panel-button' => true,
                                    'intec-grid' => true,
                                    'intec-grid-a-v-center' => true,
                                    'intec-grid-i-h-6' => true,
                                    'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false,
                                    'intec-cl-svg-path-stroke-hover' => $arVisual['THEME'] == 'light' ? true : false
                                ], true),
                                'href' => $arResult['URL']['BASKET']
                            ]) ?>
                                <span class="intec-grid-item-auto intec-ui-picture">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.00974 4.33374L3.28925 1.09317H1.20557" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.91355 13.2021L4.01025 4.33374H17.6833C18.3714 4.33374 18.8834 4.96781 18.7397 5.64077L17.1173 13.2021C17.0103 13.7001 16.5707 14.0554 16.0608 14.0554H6.96889C6.46012 14.0554 6.02048 13.7001 5.91355 13.2021Z" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M15.5058 17.3687C15.093 17.3687 14.758 17.7037 14.762 18.1165C14.762 18.5293 15.097 18.8643 15.5098 18.8643C15.9226 18.8643 16.2576 18.5293 16.2576 18.1165C16.2556 17.7037 15.9206 17.3687 15.5058 17.3687" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M7.52977 17.3687C7.11697 17.3687 6.78195 17.7037 6.78593 18.1165C6.78394 18.5293 7.11896 18.8643 7.53176 18.8643C7.94456 18.8643 8.27958 18.5293 8.27958 18.1165C8.27958 17.7037 7.94456 17.3687 7.52977 17.3687" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <span class="menu-basket-panel-button-text intec-grid-item-auto">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_BASKET') ?>
                                </span>
                                <?php if ($arResult['BASKET']['COUNT'] > 0) { ?>
                                    <?= Html::tag('span', $arResult['BASKET']['COUNT'], [
                                        'class' => Html::cssClassFromArray([
                                            'menu-basket-panel-button-counter' => true,
                                            'intec-grid-item-auto' => true,
                                            'intec-cl-background' => $arVisual['THEME'] == 'light' ? true : false,
                                            'intec-cl-text' => $arVisual['THEME'] == 'dark' ? true : false,
                                        ], true)
                                    ]) ?>
                                <?php } ?>
                            <?= Html::endTag('a') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arResult['DELAYED']['SHOW']) { ?>
                        <div class="menu-basket-panel-button-wrap">
                            <?= Html::beginTag('a', [
                                'class' => Html::cssClassFromArray([
                                    'menu-basket-panel-button' => true,
                                    'intec-grid' => true,
                                    'intec-grid-a-v-center' => true,
                                    'intec-grid-i-h-6' => true,
                                    'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false,
                                    'intec-cl-svg-path-stroke-hover' => $arVisual['THEME'] == 'light' ? true : false
                                ], true),
                                'href' => $arResult['URL']['DELAYED']
                            ]) ?>
                                <span class="intec-grid-item-auto intec-ui-picture">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4837 2.63113C16.4036 2.63113 18.3656 5.37603 18.3656 7.93671C18.3656 13.1225 10.223 17.3688 10.0756 17.3688C9.92823 17.3688 1.78564 13.1225 1.78564 7.93671C1.78564 5.37603 3.7476 2.63113 6.66751 2.63113C8.34392 2.63113 9.44004 3.46934 10.0756 4.20623C10.7112 3.46934 11.8073 2.63113 13.4837 2.63113Z" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <span class="menu-basket-panel-button-text intec-grid-item-auto">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_DELAYED') ?>
                                </span>
                                <?php if ($arResult['DELAYED']['COUNT'] > 0) { ?>
                                    <?= Html::tag('span', $arResult['DELAYED']['COUNT'], [
                                        'class' => Html::cssClassFromArray([
                                            'menu-basket-panel-button-counter' => true,
                                            'intec-grid-item-auto' => true,
                                            'intec-cl-background' => $arVisual['THEME'] == 'light' ? true : false,
                                            'intec-cl-text' => $arVisual['THEME'] == 'dark' ? true : false,
                                        ], true)
                                    ]) ?>
                                <?php } ?>
                            <?= Html::endTag('a') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arResult['COMPARE']['SHOW']) { ?>
                        <div class="menu-basket-panel-button-wrap">
                            <?= Html::beginTag('a', [
                                'class' => Html::cssClassFromArray([
                                    'menu-basket-panel-button' => true,
                                    'intec-grid' => true,
                                    'intec-grid-a-v-center' => true,
                                    'intec-grid-i-h-6' => true,
                                    'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false,
                                    'intec-cl-svg-path-stroke-hover' => $arVisual['THEME'] == 'light' ? true : false
                                ], true),
                                'href' => $arResult['URL']['COMPARE']
                            ]) ?>
                                <span class="intec-grid-item-auto intec-ui-picture">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.17546 10.807V18.8766" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M18.8768 6.77213V18.8766" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M12.8244 1.12337V18.8766" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M1.12337 6.77213V18.8766" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <span class="menu-basket-panel-button-text intec-grid-item-auto">
                                    <?= Loc::getMessage('C_SALE_BASKET_SMALL_TEMPLATE_1_COMPARE') ?>
                                </span>
                                <?php if ($arResult['COMPARE']['COUNT'] > 0) { ?>
                                    <?= Html::tag('span', $arResult['COMPARE']['COUNT'], [
                                        'class' => Html::cssClassFromArray([
                                            'menu-basket-panel-button-counter' => true,
                                            'intec-grid-item-auto' => true,
                                            'intec-cl-background' => $arVisual['THEME'] == 'light' ? true : false,
                                            'intec-cl-text' => $arVisual['THEME'] == 'dark' ? true : false,
                                        ], true)
                                    ]) ?>
                                <?php } ?>
                            <?= Html::endTag('a') ?>
                        </div>
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
