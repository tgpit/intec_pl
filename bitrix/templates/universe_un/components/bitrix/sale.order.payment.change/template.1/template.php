<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;


if (!Loader::includeModule("intec.core")) {
    return;
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-sale-order-payment-change c-sale-order-payment-change-template-1">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if (!empty($arResult['errorMessage'])) { ?>
                <div class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20">
                    <?php if (!is_array($arResult['errorMessage'])) {
                        ShowError($arResult['errorMessage']);
                    } else {
                        foreach ($arResult['errorMessage'] as $errorMessage) {
                            ShowError($errorMessage);
                        }
                    } ?>
                </div>
            <?php } else { ?>
                <div class="sale-order-payment-change-content">
                    <div class="sale-order-payment-change-container" data-role="content" data-visible="visible">
                        <div class="sale-order-payment-change-header">
                            <div class="intec-grid intec-grid-nowrap intec-grid-450-wrap intec-grid-a-v-center intec-grid-i-h-20 intec-grid-i-v-10">
                                <div class="intec-grid-item">
                                    <div class="sale-order-payment-change-title">
                                        <?php
                                        echo Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_NAME_1', [
                                            '#NUMBER#'=> $arResult['PAYMENT']['ACCOUNT_NUMBER']
                                        ]);

                                        if (isset($arResult['PAYMENT']['DATE_BILL'])) {
                                            echo ' ';
                                            echo Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_NAME_2', [
                                                '#DATE#' => $arResult['PAYMENT']['DATE_BILL']->format("d.m.Y")
                                            ]);
                                        }

                                        echo Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_NAME_3', [
                                            '#PAY_SYSTEM#'=> Html::encode($arResult['PAYMENT']['PAY_SYSTEM_NAME'])
                                        ]);
                                        ?>
                                    </div>
                                </div>
                                <div class="intec-grid-item-auto intec-grid-item-450-1">
                                    <?php
                                    $sState = 'unpaid';

                                    if ($arResult['PAYMENT']['PAID'] === 'Y') {
                                        $sState = 'paid';
                                    } else if ($arResult['IS_ALLOW_PAY'] == 'N') {
                                        $sState = 'restricted';
                                    }
                                    ?>
                                    <div class="sale-order-payment-change-status" data-state="<?= $sState ?>">
                                        <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_STATUS_'.StringHelper::toUpperCase($sState)) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="sale-order-payment-change-price">
                                <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_SUM_TO_PAID', [
                                    '#SUM#' => SaleFormatCurrency($arResult['PAYMENT']['SUM'], $arResult['PAYMENT']['CURRENCY'])
                                ]) ?>
                            </div>
                        </div>
                        <div class="sale-order-payment-change-items" data-role="items">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-a-h-start intec-grid-i-8">
                                <?php foreach ($arResult['PAYSYSTEMS_LIST'] as $key => $paySystem) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'intec-grid-item' => [
                                                '4',
                                                '768-3',
                                                '450-2'
                                            ]
                                        ],
                                        'data' => [
                                            'role' => 'item'
                                        ]
                                    ]) ?>
                                        <div class="sale-order-payment-change-item">
                                            <?= Html::input('hidden', 'PAY_SYSTEM_ID', $paySystem['ID'], [
                                                'checked' => $key == 0 ? 'checked' : '',
                                                'data' => [
                                                    'role' => 'input'
                                                ]
                                            ]) ?>
                                            <?php
                                            if (empty($paySystem['LOGOTIP']))
                                                $paySystem['LOGOTIP'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                            ?>
                                            <?= Html::tag('div', '', [
                                                'class' => 'sale-order-payment-change-item-image',
                                                'style' => [
                                                    'background-image' => 'url('.$paySystem['LOGOTIP'].')'
                                                ]
                                            ]) ?>
                                            <?= Html::tag('div', Html::encode($paySystem['NAME']), [
                                                'class' => 'sale-order-payment-change-item-name'
                                            ]) ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include(__DIR__.'/parts/script.php') ?>
            <?php } ?>
        </div>
    </div>
</div>

