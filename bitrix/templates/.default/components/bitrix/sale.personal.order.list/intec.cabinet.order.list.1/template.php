<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
use intec\Core;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.cabinet'))
    return;

IntecCabinet::Initialize();

$APPLICATION->SetAdditionalCSS(BX_PERSONAL_ROOT . '/css/intec/style.css', true);

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'SORT' => FileHelper::getFileData(__DIR__.'/images/sort.svg'),
    'SEARCH' => FileHelper::getFileData(__DIR__.'/images/search.svg'),
    'SEARCH_RESET' => FileHelper::getFileData(__DIR__.'/images/search_reset.svg'),
    'DELIMETER' => FileHelper::getFileData(__DIR__ . '/images/delimeter.svg')
];

$bEmpty = count($arResult['ORDERS']) >= 1 ? false : true;
$arGet = Core::$app->getRequest()->get();
$bShowFilterBlockCancelled  = (!isset($arGet['show_canceled']) && $arGet['show_canceled'] !== 'Y') || (isset($arGet['show_all']) && $arGet['show_all'] === 'Y');
$bSearchApply = isset($arGet['filter_id']) && !empty($arGet['filter_id']);

?>
<div id="<?= $sTemplateId ?>" class="ns-bitrix c-sale-personal-order-list c-sale-personal-order-list-template-1">
    <div class="sale-personal-order-list-wrapper intec-content">
        <div class="sale-personal-order-list-wrapper-2 intec-content-wrapper">
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
                <?php if (!empty($arResult['ERRORS']['NONFATAL'])) { ?>
                    <div class="sale-personal-order-detail-errors intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                        <?php foreach ($arResult['ERRORS']['NONFATAL'] as $sError) echo Html::tag('div', $sError) ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['SHOW_SEARCH']) { ?>
                    <?php include(__DIR__.'/parts/search.php') ?>
                    <?php if ($bSearchApply && $bEmpty) { ?>
                        <div class="sale-personal-order-list-message">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_MESSAGES_EMPTY') ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if (!$bSearchApply && $arVisual['SHOW_FILTER']) { ?>
                    <?php include(__DIR__.'/parts/filter.php') ?>
                <?php } ?>

                <div class="sale-personal-order-list-sections">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-4">
                        <?php if ($arVisual['ONLY_CURRENT_ORDERS']) { ?>
                            <div class="intec-grid-item-auto">
                                <div class="sale-personal-order-list-current-orders-title">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_CURRENT_ORDERS_TITLE') ?>
                                </div>
                            </div>
                            <?php if ($arVisual['SHOW_LINK_ALL_ORDERS']) { ?>
                                <div class="intec-grid-item"></div>
                                <div class="intec-grid-item-auto">
                                    <a href="<?= $arParams['CURRENT_ORDERS_LINK'] ?>" class="sale-personal-order-list-current-orders-link intec-ui intec-ui-control-button intec-ui-scheme-current">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_CURRENT_ORDERS_LINK') ?>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <?php foreach ($arResult['FILTER']['TABS'] as $keyTab => $valueTab) { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::beginTag($arResult['FILTER']['VALUE'] == $valueTab['VALUE'] ? 'div' : 'a', [
                                        'class' => Html::cssClassFromArray([
                                            'sale-personal-order-list-section-button' => true,
                                            'active' => $arResult['FILTER']['VALUE'] == $valueTab['VALUE'] ? true : false,
                                            'intec-cl' => [
                                                'border-hover' => true,
                                                'background-hover' => true,
                                                'border' => $arResult['FILTER']['VALUE'] == $valueTab['VALUE'] ? true : false,
                                                'background' => $arResult['FILTER']['VALUE'] == $valueTab['VALUE'] ? true : false
                                            ]
                                        ], true),
                                        'href' => $arResult['FILTER']['VALUE'] != $valueTab['VALUE'] ? $valueTab['URL'] : null
                                    ]) ?>
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_FILTER_'.$keyTab) ?>
                                    <?= Html::endTag($arResult['FILTER']['VALUE'] == $valueTab['VALUE'] ? 'div' : 'a') ?>
                                </div>
                            <?php } ?>
                            <?php unset($valueTab, $keyTab) ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (!$bEmpty) { ?>
                <div class="sale-personal-order-list-items" data-role="items">
                    <div class="sale-personal-order-list-items-header">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-15">
                            <?php foreach ($arResult['HEADER'] as $keyHeader => $valueHeader) { ?>
                                <div class="sale-personal-order-list-item-header intec-grid-item" data-code="<?= $valueHeader['CODE'] ?>">
                                    <?php if (!empty($valueHeader['URL'])) { ?>
                                        <?= Html::beginTag('a', [
                                            'class' => Html::cssClassFromArray([
                                                'sale-personal-order-list-item-header-text' => true,
                                                'intec-grid' => [
                                                    '' => true,
                                                    'nowrap' => true,
                                                    'a-v-center' => true,
                                                    'i-h-3' => true
                                                ]
                                            ], true),
                                            'href' => $valueHeader['URL'],
                                            'data-role' => 'header-link'
                                        ]) ?>
                                            <?= Html::tag('span', Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_'.$keyHeader), [
                                                'class' => 'intec-grid-item-auto'
                                            ]) ?>
                                            <?= Html::tag('span', $arSvg['SORT'], [
                                                'class' => Html::cssClassFromArray([
                                                    'intec-grid-item-auto' => true,
                                                    'intec-ui-picture' => true,
                                                    'intec-cl-svg-path-stroke' => $arParams['DEFAULT_SORT'] == $keyHeader ? true : false
                                                ], true)
                                            ]) ?>
                                        <?= Html::endTag('a') ?>
                                    <?php } else { ?>
                                        <?= Html::tag('span', Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_'.$keyHeader), [
                                            'class' => 'sale-personal-order-list-item-header-text'
                                        ]) ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php unset($keyHeader, $valueHeader) ?>
                        </div>
                    </div>
                    <?php foreach($arResult['ORDERS'] as $arOrder) { ?>
                        <div class="sale-personal-order-list-item" data-role="item">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-15">
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-end" data-code="id">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_ID') ?>
                                    </div>
                                    <a class="sale-personal-order-list-item-text intec-cl-text intec-grid-item intec-grid-item-768-2" href="<?= $arOrder['ORDER']['URL_TO_DETAIL'] ?>">
                                        <?= $arOrder['ORDER']['ID'] ?>
                                    </a>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-end" data-code="date_insert">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_DATE_INSERT') ?>
                                    </div>
                                    <a class="sale-personal-order-list-item-text intec-cl-text intec-grid-item intec-grid-item-768-2" href="<?= $arOrder['ORDER']['URL_TO_DETAIL'] ?>">
                                        <?= $arOrder['ORDER']['DATE_INSERT_FORMATED'] ?>
                                    </a>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-center" data-code="status">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_STATUS') ?>
                                    </div>
                                    <?= Html::tag('span', $arOrder['ORDER']['CANCELED'] === 'Y' ? Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_CANCELLED') : $arOrder['ORDER']['ORDER_STATUS_NAME'], [
                                        'class' => 'sale-personal-order-list-item-status-button',
                                        'style' => [
                                            'background-color' => $arOrder['ORDER']['ORDER_STATUS_COLOR'] ?? null
                                        ]
                                    ]) ?>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-end" data-code="price">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_PRICE') ?>
                                    </div>
                                    <div class="sale-personal-order-list-item-text intec-grid-item intec-grid-item-768-2">
                                        <?= $arOrder['ORDER']['FORMATED_PRICE'] ?>
                                    </div>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-end" data-code="payment">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_PAYMENT') ?>
                                    </div>
                                    <div class="sale-personal-order-list-item-text intec-grid-item intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_'.($arOrder['ORDER']['PAYED'] === 'Y' ? 'YES' : 'NO')) ?>
                                        <?= $arOrder['ORDER']['PAYED'] === 'Y' ? $arOrder['ORDER']['DATE_PAYED_FORMATTED'] : '' ?>
                                    </div>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-center" data-code="shipment">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_SHIPMENT') ?>
                                    </div>
                                    <div class="intec-grid-item intec-grid-item-768-2">
                                        <?php foreach($arOrder['SHIPMENT'] as $arShipment) { ?>
                                            <div class="sale-personal-order-list-item-text">
                                                <?= $arShipment['DELIVERY_STATUS_NAME'] ?>
                                                <?= $arShipment['DEDUCTED'] === 'Y' ? $arShipment['DATE_DEDUCTED_FORMATED'] : '' ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap intec-grid-a-v-end" data-code="product">
                                    <div class="sale-personal-order-list-item-header-text-mobile intec-grid-item-768-2">
                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_HEADER_PRODUCT') ?>
                                    </div>
                                    <div class="sale-personal-order-list-item-text intec-cl-text" data-role="button">
                                        <?= count($arOrder['BASKET_ITEMS']) ?>
                                        <?= Loc::getMessage($arOrder['COUNT_BASKET_ITEMS_TEXT_CODE']) ?>
                                        <span class="sale-personal-order-list-item-icon sale-personal-order-list-item-icon-active">
                                            <i class="fa fa-angle-up"></i>
                                        </span>
                                        <span class="sale-personal-order-list-item-icon sale-personal-order-list-item-icon-inactive">
                                            <i class="fa fa-angle-down"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-h-768-around intec-grid-a-v-center" data-code="button">
                                    <div class="intec-grid-item-auto intec-grid-item-768-1">
                                        <a class="sale-personal-order-list-item-text sale-personal-order-list-item-button-detail intec-cl-text intec-ui intec-ui-control-button intec-ui-scheme-current" href="<?= Html::encode($arOrder['ORDER']['URL_TO_DETAIL']) ?>">
                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_BUTTONS_DETAIL') ?>
                                        </a>
                                    </div>
                                    <?php if ($arOrder['ORDER']['CANCELED'] !== 'Y' && $arOrder['ORDER']['PAYED'] !== 'Y') { ?>
                                        <?= Html::a(Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_BUTTONS_PAY'), $arOrder['ORDER']['URL_TO_DETAIL_PAY'], [
                                            'class' => [
                                                'sale-personal-order-list-item-text',
                                                'intec-cl-text',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'scheme-current'
                                                ]
                                            ]
                                        ]) ?>
                                    <?php } else if ($arOrder['ORDER']['CANCELED'] === 'Y' || $arOrder['ORDER']['CAN_CANCEL'] === 'Y') { ?>
                                        <div class="intec-grid-item-auto intec-grid-item-768-1">
                                            <?php if ($arOrder['ORDER']['CANCELED'] === 'Y') { ?>
                                                <?= Html::beginTag('span', [
                                                    'class' => 'sale-personal-order-list-item-text'
                                                ]) ?>
                                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_ORDER_CANCELLED') ?>
                                                    <b><?= $arOrder['ORDER']['DATE_CANCELED_FORMATED'] ?></b>
                                                <?= Html::endTag('span') ?>
                                            <?php } ?>
                                            <?php if ($arOrder['ORDER']['CAN_CANCEL'] === 'Y') { ?>
                                                <?= Html::beginTag('a', [
                                                    'class' => Html::cssClassFromArray([
                                                        'sale-personal-order-list-item-text' => true,
                                                        'intec-cl-text' => true
                                                    ], true),
                                                    'href' => $arOrder['ORDER']['URL_TO_CANCEL']
                                                ]) ?>
                                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_BUTTONS_CANCEL') ?>
                                                <?= Html::endTag('a') ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="sale-personal-order-list-item-wrap intec-grid-item" data-code="products">
                                    <div class="sale-personal-order-list-item-products-container" data-role="products">
                                        <div class="sale-personal-order-list-item-products sale-personal-order-list-item-products-header">
                                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-15 intec-grid-i-v-8">
                                                <div class="sale-personal-order-list-item-product intec-grid-item" data-code="name">
                                                    <div class="sale-personal-order-list-item-product-header-text">
                                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_NAME') ?>
                                                    </div>
                                                </div>
                                                <div class="sale-personal-order-list-item-product intec-grid-item" data-code="price">
                                                    <div class="sale-personal-order-list-item-product-header-text">
                                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_PRICE') ?>
                                                    </div>
                                                </div>
                                                <div class="sale-personal-order-list-item-product intec-grid-item" data-code="quantity">
                                                    <div class="sale-personal-order-list-item-product-header-text">
                                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_QUANTITY') ?>
                                                    </div>
                                                </div>
                                                <div class="sale-personal-order-list-item-product intec-grid-item" data-code="sum">
                                                    <div class="sale-personal-order-list-item-product-header-text">
                                                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_SUM') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php foreach ($arOrder['BASKET_ITEMS'] as &$arBasketItem) { ?>
                                            <div class="sale-personal-order-list-item-products">
                                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-15 intec-grid-i-v-8">
                                                    <div class="sale-personal-order-list-item-product intec-grid-item intec-grid-item-768-1 intec-grid intec-grid-nowrap intec-grid-a-v-start" data-code="name">
                                                        <div class="sale-personal-order-list-item-product-header-text-mobile intec-grid-item-768-2">
                                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_NAME') ?>
                                                        </div>
                                                        <a class="sale-personal-order-list-item-product-text intec-cl-text intec-grid-item intec-grid-item-768-2" href="<?= $arBasketItem['DETAIL_PAGE_URL'] ?>">
                                                            <?= $arBasketItem['NAME'] ?>
                                                        </a>
                                                    </div>
                                                    <div class="sale-personal-order-list-item-product intec-grid-item intec-grid-item-768-1 intec-grid intec-grid-nowrap intec-grid-a-v-start" data-code="price">
                                                        <div class="sale-personal-order-list-item-product-header-text-mobile intec-grid-item-768-2">
                                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_PRICE') ?>
                                                        </div>
                                                        <div class="sale-personal-order-list-item-product-text intec-grid-item intec-grid-item-768-2">
                                                            <?= $arBasketItem['PRICE_FORMATTED'] ?>
                                                        </div>
                                                    </div>
                                                    <div class="sale-personal-order-list-item-product intec-grid-item intec-grid-item-768-1 intec-grid intec-grid-nowrap intec-grid-a-v-start" data-code="quantity">
                                                        <div class="sale-personal-order-list-item-product-header-text-mobile intec-grid-item-768-2">
                                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_QUANTITY') ?>
                                                        </div>
                                                        <div class="sale-personal-order-list-item-product-text intec-grid-item intec-grid-item-768-2">
                                                            <?= $arBasketItem['QUANTITY'].' '.$arBasketItem['MEASURE_NAME'] ?>
                                                        </div>
                                                    </div>
                                                    <div class="sale-personal-order-list-item-product intec-grid-item intec-grid-item-768-1 intec-grid intec-grid-nowrap intec-grid-a-v-start" data-code="sum">
                                                        <div class="sale-personal-order-list-item-product-header-text-mobile intec-grid-item-768-2">
                                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_PRODUCTS_HEADER_SUM') ?>
                                                        </div>
                                                        <div class="sale-personal-order-list-item-product-text intec-grid-item intec-grid-item-768-2">
                                                            <b><?= $arBasketItem['SUM_FORMATTED'] ?></b>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php unset($arBasketItem) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="sale-personal-order-list-navigation">
                    <?= $arResult["NAV_STRING"] ?>
                </div>
            <?php } ?>
            <?php if (!$bSearchApply && $bEmpty) { ?>
                <div class="sale-personal-order-list-message">
                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_MESSAGES_EMPTY') ?>
                </div>
            <?php } ?>
            <script>
                $(document).ready(function () {
                    var root = $('#' + <?= JavaScript::toObject($sTemplateId) ?>);
                    var items = [];
                    var filter = root.find('[data-role="filter"]');
                    var buttonClear = filter.find('[data-role="clear"]');

                    buttonClear.on('click', function () {
                        filter.find(':input').each(function () {
                            if (this.type == 'text' || this.type == 'textarea' || this.type == 'date') {
                                this.value = '';
                            } else if (this.type == 'radio' || this.type == 'checkbox') {
                                this.checked = false;
                            } else if (this.type == 'select-one' || this.type == 'select-multiple') {
                                this.value = '';
                            } else if (this.type == 'hidden' && (this.name == 'filter_date_from' || this.name == 'filter_date_to')) {
                                this.value = '';
                            }
                        });
                        filter[0].submit();
                    });

                    root.find('[data-role="items"] [data-role="item"]').each(function () {
                        var item;
                        var nodes;
                        var isOpened;

                        nodes = {};
                        nodes.item = $(this);
                        nodes.button = nodes.item.find('[data-role="button"]');
                        nodes.content = nodes.item.find('[data-role="products"]');

                        isOpened = true;

                        item = {};
                        item.getNode = function () { return nodes.item; };
                        item.getNodeButton = function () { return nodes.button; };
                        item.getNodeContent = function () { return nodes.content; };

                        item.isOpened = function () { return isOpened; };

                        item.open = function (callback, animate) {
                            var complete;

                            if (item.isOpened())
                                return;

                            isOpened = true;
                            nodes.item.trigger('open', item);
                            complete = function () {
                                nodes.item.trigger('opened', item);

                                if ($.isFunction(callback))
                                    callback.apply(item);
                            };

                            if (animate) {
                                nodes.content.stop().slideToggle({
                                    'duration': 300,
                                    'complete': complete
                                });
                            } else {
                                nodes.content.stop().toggle(true);
                                complete();
                            }
                        };
                        item.close = function (callback, animate) {
                            var complete;

                            if (!item.isOpened())
                                return;

                            isOpened = false;
                            nodes.item.trigger('close', item);
                            complete = function () {
                                nodes.item.trigger('closed', item);

                                if ($.isFunction(callback))
                                    callback.apply(item);
                            };

                            if (animate) {
                                nodes.content.stop().slideToggle({
                                    'duration': animate ? 300 : 0,
                                    'complete': complete
                                });
                            } else {
                                nodes.content.stop().toggle(false);
                                complete();
                            }
                        };
                        item.toggle = function (callback, animate) {
                            var state;
                            var handler;

                            handler = function () {
                                nodes.item.trigger('toggled', item, state);

                                if ($.isFunction(callback))
                                    callback.apply(this, arguments);
                            };

                            state = item.isOpened();
                            nodes.item.trigger('toggle', item, state);

                            if (state) {
                                item.close(handler, animate);
                            } else {
                                item.open(handler, animate);
                            }
                        };

                        nodes.button.on('click', function () {
                            item.toggle(null, true);
                        });

                        item.toggle(null, false);
                        items.push(item);
                    }).on('open', function (event, item) {
                        item.getNode().addClass('sale-personal-order-list-item-active');
                    }).on('close', function (event, item) {
                        item.getNode().removeClass('sale-personal-order-list-item-active');
                    });
                });
            </script>
        </div>
    </div>
</div>