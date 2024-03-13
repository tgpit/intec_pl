<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

if (!Loader::includeModule("intec.core")) {
    return;
}

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-sale-order-payment-change c-sale-order-payment-change-template-1">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if (!empty($arResult['errorMessage'])) { ?>
                <div class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20">
                    <?php if (!is_array($arResult['errorMessage']))	{
                        ShowError($arResult['errorMessage']);
                    } else {
                        foreach ($arResult['errorMessage'] as $errorMessage) {
                            ShowError($errorMessage);
                        }
                    } ?>
                </div>
            <?php } else { ?>
	            <?php if ($arResult['IS_ALLOW_PAY'] == 'N') { ?>
                    <div class="sale-order-payment-change-content">
                        <p>
                            <b>
                                <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_PAY_SYSTEM_CHANGED') ?>
                            </b>
                        </p>
                        <p>
                            <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_PAY_SYSTEM_NOT_ALLOW_PAY') ?>
                        </p>
                    </div>
		        <?php }	else if ($arResult['SHOW_INNER_TEMPLATE'] == 'Y') { ?>
                    <div class="sale-order-payment-change-content">
                        <div class="sale-order-payment-change-container">
                            <div class="sale-order-payment-change-header">
                                <div class="sale-order-payment-change-title">
                                    <?php
                                    echo Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_NAME_1', [
                                        '#NUMBER#'=> $arResult['PAYMENT']['ACCOUNT_NUMBER']
                                    ]);

                                    if(isset($arResult['PAYMENT']['DATE_BILL'])) {
                                        echo ' ';
                                        echo Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_NAME_2', [
                                            '#DATE#' => $arResult['PAYMENT']['DATE_BILL']->format("d.m.Y")
                                        ]);
                                    }
                                    ?>
                                </div>
                                <div class="sale-order-payment-change-price">
                                    <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_SUM_TO_PAID', [
                                        '#SUM#' => SaleFormatCurrency($arResult['PAYMENT']['SUM'], $arResult['PAYMENT']['CURRENCY'])
                                    ]) ?>
                                </div>
                                <div class="sale-order-payment-change-price">
                                    <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_INNER_BALANCE', [
                                        '#BALANCE#' => SaleFormatCurrency($arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'], $arResult['INNER_PAYMENT_INFO']['CURRENCY'])
                                    ]) ?>
                                </div>
                                <?php
                                $inputSum = $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] > $arResult['PAYMENT']['SUM'] ? $arResult['PAYMENT']['SUM'] : $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'];

                                if (($arParams['ONLY_INNER_FULL'] !== 'Y' && (float) $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] > 0) || ($arParams['ONLY_INNER_FULL'] === 'Y' && $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] >= $arResult['PAYMENT']['SUM'])) {
                                ?>
                                    <?php if ($arParams['ONLY_INNER_FULL'] !== 'Y') { ?>
                                        <div class="sale-order-payment-change-pay-block">
                                            <div class="sale-order-payment-change-pay-block-title">
                                                <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_SUM_OF_PAYMENT') ?>
                                            </div>
                                            <div class="">
                                                <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-4">
                                                    <div class="intec-grid-item">
                                                        <?= Html::input('text', 'payInner', (float) $inputSum, [
                                                            'class' => [
                                                                'intec-ui' => [
                                                                    '',
                                                                    'control-input',
                                                                    'mod-block'
                                                                ]
                                                            ],
                                                            'placeholder' => '0.00',
                                                            'data' => [
                                                                'role' => 'inner-input'
                                                            ]
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-grid-item-auto">
                                                        <?= $arResult['INNER_PAYMENT_INFO']['FORMATED_CURRENCY'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="sale-order-payment-change-price">
                                        <?= Html::tag('div', Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_PAY_BUTTON'), [
                                            'class' => [
                                                'sale-order-payment-change-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'mod-transparent',
                                                    'mod-round-2',
                                                    'scheme-current'
                                                ]
                                            ],
                                            'data' => [
                                                'role' => 'inner-button'
                                            ]
                                        ]) ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="sale-order-payment-change-description">
                                <?php if (($arParams['ONLY_INNER_FULL'] !== 'Y' && (float) $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] > 0) || ($arParams['ONLY_INNER_FULL'] === 'Y' && $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] >= $arResult['PAYMENT']['SUM'])) { ?>
                                    <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_HANDLERS_PAY_SYSTEM_WARNING_RETURN') ?>
                                <?php } else { ?>
                                    <?php ShowError(Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_LOW_BALANCE')) ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php include(__DIR__.'/parts/script.php') ?>
                <?php }	else if (empty($arResult['PAYMENT_LINK']) && !$arResult['IS_CASH'] && mb_strlen($arResult['TEMPLATE']))	{
                    echo $arResult['TEMPLATE'];
                } else { ?>
                    <div class="sale-order-payment-change-description">
                        <p>
                            <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_ORDER_SUC', [
                                '#ORDER_ID#' => Html::encode($arResult['ORDER_ID']),
                                '#ORDER_DATE#' => $arResult['ORDER_DATE']
                            ]) ?>
                        </p>
                        <p>
                            <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_PAYMENT_SUC', [
                                '#PAYMENT_ID#' => Html::encode($arResult['PAYMENT_ID'])
                            ]) ?>
                        </p>
                        <p>
                            <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_PAYMENT_SYSTEM_NAME', [
                                '#PAY_SYSTEM_NAME#' => Html::encode($arResult['PAY_SYSTEM_NAME'])
                            ]) ?>
                        </p>
                        <?php if (!$arResult['IS_CASH'] && !empty($arResult['PAYMENT_LINK'])) { ?>
                            <p>
                                <?= Loc::getMessage('C_SALE_ORDER_PAYMENT_CHANGE_TEMPLATE_1_TEMPLATE_CONFIRM_PAY_LINK', [
                                    '#LINK#' => Html::encode($arResult['PAYMENT_LINK'])
                                ]) ?>
                            </p>
                        <?php } ?>
                    </div>
                    <?php if (!$arResult['IS_CASH'] && !empty($arResult['PAYMENT_LINK'])) { ?>
                        <script type="text/javascript">
                            window.open('<?= CUtil::JSEscape($arResult['PAYMENT_LINK']) ?>');
                        </script>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
