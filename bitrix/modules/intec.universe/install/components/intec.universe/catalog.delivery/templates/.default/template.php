<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\template\Properties;

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));

$oRequest = Core::$app->request;
$bIsAjax = false;

if ($oRequest->getIsAjax()) {
    $bIsAjax = $oRequest->post('delivery');
    $bIsAjax = ArrayHelper::getValue($bIsAjax, 'ajax') === 'y';
}

?>
<div class="ns-intec-universe c-catalog-delivery c-catalog-delivery-default" id="<?= $sTemplateId ?>">
    <div class="catalog-delivery-selection-city" data-role="cityBlock" data-expanded="<?=(!empty($arParams['CITY_NAME']))?'false':'true'?>">
        <div class="catalog-delivery-current-city">
            <?=Loc::getMessage('C_CATALOG_DELIVERY_CURRENT_CITY')?>
            <span><?=Loc::getMessage('C_CATALOG_DELIVERY_IN')?></span>
            <span class="catalog-delivery-current-city-name-wrap" data-role="currentCity">
                <span class="catalog-delivery-current-city-name" data-role="currentCityName">
                    <?=$arParams['CITY_NAME']?>
                </span>
                 <i class="catalog-delivery-current-city-icon fal fa-angle-down"></i>
            </span>
        </div>
        <div class="catalog-delivery-city-form">
        <?php
            $APPLICATION->IncludeComponent(
                "bitrix:sale.location.selector.search",
                "template.1",
                array(
                    "COMPONENT_TEMPLATE" => "template.1",
                    "ID" => "",
                    "CODE" => $arParams['CITY_ID'],
                    "INPUT_NAME" => "LOCATION",
                    "PROVIDE_LINK_BY" => "id",
                    "FILTER_BY_SITE" => "N",
                    "SHOW_DEFAULT_LOCATIONS" => "Y",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "JS_CONTROL_GLOBAL_ID" => "",
                    "JS_CALLBACK" => "locationUpdated",
                    "SUPPRESS_ERRORS" => "N",
                    "INITIALIZE_BY_GLOBAL_EVENT" => ""
                ),
                false
            );
        ?>
        </div>
    </div>
    <div class="catalog-delivery-params intec-grid intec-grid intec-grid-400-wrap">
        <div class="catalog-delivery-param catalog-delivery-param-quantity intec-grid-item-auto intec-grid-item-400-1">
            <div class="catalog-delivery-param-title">
                <?=Loc::getMessage('C_CATALOG_DELIVERY_DEFAULT_QUANTITY')?>
            </div>
            <div class="intec-ui intec-ui-control-numeric intec-ui-view-6 intec-ui-size-2 intec-ui-scheme-current" data-role="counter">
                <?= Html::tag('a', '-', [
                    'class' => 'intec-ui-part-decrement',
                    'href' => 'javascript:void(0)',
                    'data-type' => 'button',
                    'data-action' => 'decrement'
                ]) ?>
                <?= Html::input('text', null, 0, [
                    'data-type' => 'input',
                    'class' => 'intec-ui-part-input'
                ]) ?>
                <?= Html::tag('a', '+', [
                    'class' => 'intec-ui-part-increment',
                    'href' => 'javascript:void(0)',
                    'data-type' => 'button',
                    'data-action' => 'increment'
                ]) ?>
            </div>
        </div>
        <div class="catalog-delivery-param catalog-delivery-param-basket intec-grid-item-auto intec-grid-item-400-1">
            <div class="catalog-delivery-param-title">
                <?=Loc::getMessage('C_CATALOG_DELIVERY_DEFAULT_BASKET')?>
            </div>
            <div class="catalog-delivery-param-content">
                <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current intec-ui-size-2">
                    <input type="checkbox" <?=($arParams['USE_BASKET'] == 'Y')?'checked="checked"':''?> data-role="useBasket">
                    <span class="intec-ui-part-selector"></span>
                    <span class="intec-ui-part-content"><?=Loc::getMessage('C_CATALOG_DELIVERY_DEFAULT_USE_BASKET')?></span>
                </label>
            </div>
        </div>
    </div>
    <div class="catalog-delivery-list-wrap">
        <div class="catalog-delivery-list-stub" data-role="deliveries-stub" style="background-image: url('<?=Properties::get('template-images-lazyload-stub')?>')"></div>
        <div data-role="deliveries">
        <?php
            if ($bIsAjax) $APPLICATION->RestartBuffer();

            if (!empty($arResult['DELIVERIES'])) {?>
                <div class="catalog-delivery-list">
                <?php foreach($arResult['DELIVERIES'] as $arDelivery) {?>
                    <div class="catalog-delivery-element" data-role="delivery">
                        <div class="delivery-element-info intec-grid intec-grid-400-wrap intec-grid-a-v-center">
                            <?php if (!empty($arDelivery['LOGO'])) {?>
                                <div class="delivery-element-logo intec-grid-auto">
                                    <img src="<?=$arDelivery['LOGO']?>" class="delivery-element-logo-img">
                                </div>
                            <?php } ?>
                            <div class="delivery-element-name-wrap intec-grid-item intec-grid-item-400-1">
                                <div class="delivery-element-name">
                                    <?=$arDelivery['NAME']?>
                                </div>
                                <?php if (!empty($arDelivery['PERIOD_TEXT'])) {?>
                                    <div class="delivery-element-period">
                                        <?=$arDelivery['PERIOD_TEXT']?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="delivery-element-price intec-grid-item-auto intec-grid-item-400">
                                <?=$arDelivery['PRICE_FORMATED']?>
                            </div>
                            <div class="delivery-element-button-more intec-grid-item-auto intec-grid intec-cl-background" data-role="buttonDetails">
                                <i class="delivery-element-button-more-icon fal fa-angle-down"></i>
                            </div>
                        </div>
                        <?php if (!empty($arDelivery['CALCULATE_ERRORS'])) {?>
                            <div class="delivery-element-error">
                                <?=$arDelivery['CALCULATE_ERRORS']?>
                            </div>
                        <?php } ?>
                        <div class="delivery-element-details" data-role="blockDetails" data-expanded="false">
                            <?php if (!empty($arDelivery['DESCRIPTION'])) {?>
                                <div class="delivery-element-description">
                                    <?=$arDelivery['DESCRIPTION']?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arDelivery['PAY_SYSTEMS'])) {?>
                                <div class="delivery-element-paysystems-block">
                                    <div class="delivery-element-paysystems-title">
                                        <?=Loc::getMessage('C_CATALOG_DELIVERY_DEFAULT_PAYSYSTEMS_TITLE')?>
                                    </div>
                                    <div class="delivery-element-paysystems intec-grid intec-grid-wrap intec-grid-i-h-10">
                                        <?php foreach($arDelivery['PAY_SYSTEMS'] as $arPaySystem) {?>
                                            <div class="delivery-element-paysystem intec-grid-item-2 intec-grid-item-400-1 intec-grid intec-grid-a-v-center">
                                                <?php if (!empty($arPaySystem['LOGO'])) {?>
                                                    <div class="delivery-element-paysystem-logo intec-grid-item-auto">
                                                        <img class="delivery-element-paysystem-img" src="<?=$arPaySystem['LOGO']?>">
                                                    </div>
                                                <?php } ?>
                                                <div class="delivery-element-paysystem-name intec-grid-item">
                                                    <?=$arPaySystem['NAME']?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php }?>
                </div>
            <?php }

            if ($bIsAjax) exit;
        ?>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php');