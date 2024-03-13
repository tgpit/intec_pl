<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

if (!Loader::includeModule('intec.core'))
    return;

Loc::loadMessages(__FILE__);

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arPaymentData = [];

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'RETURN' => FileHelper::getFileData(__DIR__.'/images/arrow_return.svg'),
    'BLOCK_TOGGLE' => FileHelper::getFileData(__DIR__.'/images/block_toggle.svg'),
    'PREV' => FileHelper::getFileData(__DIR__.'/images/pagination_prev.svg'),
    'NEXT' => FileHelper::getFileData(__DIR__.'/images/pagination_next.svg')
];
$bOrderCanceled = $arResult['CANCELED'] === 'Y';
$sUserName = [];
$sUserNameTitle = Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_FIO');

if (!empty($arResult['USER']['NAME']))
    $sUserName[] = $arResult['USER']['NAME'];

if (!empty($arResult['USER']['SECOND_NAME']))
    $sUserName[] = $arResult['USER']['SECOND_NAME'];

if (!empty($arResult['USER']['LAST_NAME']))
    $sUserName[] = $arResult['USER']['LAST_NAME'];

$sUserName = implode(' ', $sUserName);

if (empty($sUserName)) {
    if (!empty($arResult['FIO'])) {
        $sUserName = $arResult['FIO'];
    } else {
        $sUserName = $arResult['USER']['LOGIN'];
        $sUserNameTitle = Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_LOGIN');
    }
}

$bShowUserLogin = !empty($arResult['USER']['LOGIN']) && !ArrayHelper::isIn('LOGIN', $arParams['HIDE_USER_INFO']);
$bShowUserEmail = !empty($arResult['USER']['EMAIL']) && !ArrayHelper::isIn('EMAIL', $arParams['HIDE_USER_INFO']);
$bShowUserType = !empty($arResult['USER']['PERSON_TYPE_NAME']) && !ArrayHelper::isIn('PERSON_TYPE_NAME', $arParams['HIDE_USER_INFO']);
$bShowUser = !empty($arResult['USER']) && ($bShowUserLogin || $bShowUserEmail || $bShowUserType);

if ($arParams['GUEST_MODE'] !== 'Y') {
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/components/bitrix/sale.order.payment.change/'.$this->getName().'/style.css');
}

CJSCore::Init(['clipboard', 'fx']);

$APPLICATION->SetTitle(Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_NUM', [
    '#NUMBER#' => Html::encode($arResult['ACCOUNT_NUMBER'])
]));

?>
<div id="<?= $sTemplateId ?>" class="ns-bitrix c-sale-personal-order-detail c-sale-personal-order-detail-template-1">
    <div class="sale-personal-order-detail-wrapper intec-content">
        <div class="sale-personal-order-detail-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arResult['ERRORS']['FATAL'])) { ?>
                <div class="sale-personal-order-detail-errors intec-ui intec-ui-control-alert intec-ui-scheme-red">
                    <?php foreach ($arResult['ERRORS']['FATAL'] as $sError) echo Html::tag('div', $sError) ?>
                </div>
                <?php if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) { ?>
                    <div class="sale-personal-order-detail-authorize intec-ui-m-t-20">
                        <?php $APPLICATION->AuthForm('', false, false, 'N', false) ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <?php include(__DIR__.'/parts/back_to_orders.php') ?>
                <?php if (!empty($arResult['ERRORS']['NONFATAL'])) { ?>
                    <div class="sale-personal-order-detail-errors intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                        <?php foreach ($arResult['ERRORS']['NONFATAL'] as $sError) echo Html::tag('div', $sError) ?>
                    </div>
                <?php } ?>
                <div class="sale-personal-order-detail-header">
                    <div class="sale-personal-order-detail-header-top">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-8">
                            <div class="intec-grid-item-auto">
                                <span class="sale-personal-order-detail-header-number">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_NUM', [
                                        '#NUMBER#' => Html::encode($arResult['ACCOUNT_NUMBER'])
                                    ]) ?>
                                </span>
                                <span class="sale-personal-order-detail-header-date">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_DATE', [
                                        '#DATE#' => $arResult['DATE_INSERT_FORMATED']
                                    ]) ?>
                                </span>
                            </div>
                            <div class="intec-grid-item-auto">
                                <?= Html::tag('div', $bOrderCanceled ? Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_STATUS_CANCELED') : $arResult['STATUS']['NAME'], [
                                    'class' => 'sale-personal-order-detail-status-button',
                                    'style' => [
                                        'background-color' => $arResult['STATUS']['COLOR'] ?? null
                                    ]
                                ]) ?>
                            </div>
                            <div class="intec-grid-item"></div>
                            <?php if ($arParams['GUEST_MODE'] !== 'Y') { ?>
                                <?php if ($arResult['CAN_CANCEL'] === 'Y' && $arParams['DISALLOW_CANCEL'] !== 'Y') { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BUTTONS_CANCEL'), [
                                            'class' => [
                                                'sale-personal-order-detail-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'mod-transparent',
                                                    'mod-round-2',
                                                    'scheme-current'
                                                ]
                                            ],
                                            'href' => $arResult['URL_TO_CANCEL']
                                        ]) ?>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BUTTONS_REPEAT'), [
                                        'class' => [
                                            'sale-personal-order-detail-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'mod-transparent',
                                                'mod-round-2',
                                                'scheme-current'
                                            ]
                                        ],
                                        'href' => $arResult['URL_TO_COPY']
                                    ]) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sale-personal-order-detail-header-bottom">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-between intec-grid-a-v-start intec-grid-i-h-8 intec-grid-i-v-8">
                            <div class="intec-grid-item-auto intec-grid-item-425-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <div class="sale-personal-order-detail-field-title intec-grid-item-1 intec-grid-item-425-2">
                                    <?= $sUserNameTitle ?>
                                </div>
                                <div class="sale-personal-order-detail-field-value intec-grid-item-1 intec-grid-item-425-2">
                                    <?= $sUserName ?>
                                </div>
                            </div>
                            <div class="intec-grid-item-auto intec-grid-item-425-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <div class="sale-personal-order-detail-field-title intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_DATE_STATUS', [
                                        '#DATE#' => $arResult['DATE_INSERT_FORMATED']
                                    ]) ?>
                                </div>
                                <div class="sale-personal-order-detail-field-value intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Html::tag('div', $bOrderCanceled ? Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_STATUS_CANCELED') : $arResult['STATUS']['NAME'], [
                                        'class' => 'sale-personal-order-detail-status-button',
                                        'style' => [
                                            'background-color' => $arResult['STATUS']['COLOR'] ?? null
                                        ]
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-grid-item-auto intec-grid-item-425-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <div class="sale-personal-order-detail-field-title intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_ORDER_CANCELED') ?>
                                </div>
                                <div class="sale-personal-order-detail-field-value intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Loc::getMessage($bOrderCanceled ? 'C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_ORDER_CANCELED_YES' : 'C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_ORDER_CANCELED_NO') ?>
                                </div>
                            </div>
                            <div class="intec-grid-item-6 intec-grid-item-768-auto intec-grid-item-425-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <div class="sale-personal-order-detail-field-title intec-grid-item-1 intec-grid-item-425-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_HEADER_SUM') ?>
                                </div>
                                <div class="sale-personal-order-detail-field-value intec-grid-item-1 intec-grid-item-425-2">
                                    <?= $arResult['PRICE_FORMATED'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sale-personal-order-detail-blocks">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-12">
                        <div class="intec-grid-item-2 intec-grid-item-1200-1">
                            <?php if (!empty($arResult['BASKET'])) { ?>
                                <?php include(__DIR__.'/parts/products.php') ?>
                            <?php } ?>
                            <?php if (!empty($arResult['PAYMENT'])) { ?>
                                <?php include(__DIR__.'/parts/payment.php') ?>
                            <?php } ?>
                            <?php if (!empty($arResult['CHANGE_HISTORY'])) { ?>
                                <?php include(__DIR__.'/parts/change_history.php') ?>
                            <?php } ?>
                            <?php if (!empty($arResult['SHIPMENT'])) { ?>
                                <?php include(__DIR__.'/parts/shipment.php') ?>
                            <?php } ?>
                            <?php if (!empty($arResult['DOCUMENTS'])) { ?>
                                <?php include(__DIR__.'/parts/documents.php') ?>
                            <?php } ?>
                        </div>
                        <div class="intec-grid-item-2 intec-grid-item-1200-1">
                            <?php if ($bShowUser) { ?>
                                <?php include(__DIR__.'/parts/user.php') ?>
                            <?php } ?>
                            <?php if (!empty($arResult['INFO_BLOCKS'])) { ?>
                                <?php include(__DIR__.'/parts/information.php') ?>
                            <?php } ?>
                            <?php if ($arVisual['CLAIMS_BLOCK_SHOW']) { ?>
                                <?php include(__DIR__.'/parts/claims.php') ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php include(__DIR__.'/parts/back_to_orders.php') ?>
                <?php include(__DIR__.'/parts/script.php') ?>
            <?php } ?>
        </div>
    </div>
</div>