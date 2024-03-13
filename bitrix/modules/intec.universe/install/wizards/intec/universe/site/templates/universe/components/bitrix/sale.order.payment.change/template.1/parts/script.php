<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

$javascriptParams = [
    'url' => CUtil::JSEscape($templateFolder.'/ajax.php'),
    'templateFolder' => CUtil::JSEscape($templateFolder),
    'accountNumber' => $arParams['ACCOUNT_NUMBER'],
    'paymentNumber' => $arParams['PAYMENT_NUMBER'],
    'inner' => $arParams['ALLOW_INNER'],
    'onlyInnerFull' => $arParams['ONLY_INNER_FULL'],
    'refreshPrices' => $arParams['REFRESH_PRICES'],
    'pathToPayment' => $arParams['PATH_TO_PAYMENT'],
    'templateName' => $this->getName(),
    'returnUrl' => $arParams['RETURN_URL'],
    'wrapperId' => $sTemplateId
];
$javascriptParams = CUtil::PhpToJSObject($javascriptParams);
$bUseInnerPaymentInfo = (float) $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] > 0;

if ($bUseInnerPaymentInfo) {
    $javascriptParamsInnerPaymentInfo = [
        'url' => CUtil::JSEscape($templateFolder.'/ajax.php'),
        'templateFolder' => CUtil::JSEscape($templateFolder),
        'accountNumber' => $arParams['ACCOUNT_NUMBER'],
        'paymentNumber' => $arParams['PAYMENT_NUMBER'],
        'valueLimit' => $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'] > $arResult['PAYMENT']['SUM'] ? $arResult['PAYMENT']['SUM'] : $arResult['INNER_PAYMENT_INFO']['CURRENT_BUDGET'],
        'templateName' => $this->getName(),
        'onlyInnerFull' => $arParams['ONLY_INNER_FULL'],
        'wrapperId' => $sTemplateId
    ];
    $javascriptParamsInnerPaymentInfo = CUtil::PhpToJSObject($javascriptParamsInnerPaymentInfo);
}

?>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        BX.namespace('BX.Sale');

        BX.Sale.OrderPaymentChange = (function() {
            var classDescription = function(params) {
                this.ajaxUrl = params.url;
                this.accountNumber = params.accountNumber || {};
                this.paymentNumber = params.paymentNumber || {};
                this.wrapperId = params.wrapperId || '';
                this.onlyInnerFull = params.onlyInnerFull || '';
                this.pathToPayment = params.pathToPayment || '';
                this.returnUrl = params.returnUrl || '';
                this.templateName = params.templateName || '';
                this.refreshPrices = params.refreshPrices || 'N';
                this.inner = params.inner || '';
                this.templateFolder = params.templateFolder;
                this.wrapper = document.getElementById(this.wrapperId);
                this.paySystemsContainer = root.find('[data-role="content"]')[0];
                BX.ready(BX.proxy(this.init, this));
            };

            classDescription.prototype.init = function() {
                var listPaySystems = root.find('[data-role="items"]')[0];

                new BX.easing(
                    {
                        duration: 500,
                        start: {opacity: 0, height: 50},
                        finish: {opacity: 100, height: 'auto'},
                        transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
                        step: function(state)
                        {
                            if (!!listPaySystems) {
                                listPaySystems.style.opacity = state.opacity / 100;
                                listPaySystems.style.height = listPaySystems.height / 450 + 'px';
                            }
                        },
                        complete: function()
                        {
                            if (!!listPaySystems) {
                                listPaySystems.style.height = 'auto';
                            }
                        }
                    }).animate();

                BX.bindDelegate(this.paySystemsContainer, 'click', { 'attribute': {'data-role': 'item'} }, BX.proxy(
                    function(event) {
                        var targetParentNode = event.target.parentNode;
                        var hidden = targetParentNode.querySelector('[data-role="input"]');

                        BX.ajax(
                            {
                                method: 'POST',
                                dataType: 'html',
                                url: this.ajaxUrl,
                                data:
                                    {
                                        sessid: BX.bitrix_sessid(),
                                        paySystemId: hidden.value,
                                        accountNumber: this.accountNumber,
                                        paymentNumber: this.paymentNumber,
                                        inner: this.inner,
                                        templateName: this.templateName,
                                        refreshPrices: this.refreshPrices,
                                        onlyInnerFull: this.onlyInnerFull,
                                        pathToPayment: this.pathToPayment,
                                        returnUrl: this.returnUrl
                                    },
                                onsuccess: BX.proxy(function(result) {
                                    this.paySystemsContainer.setAttribute('data-visible', 'auto');
                                    this.paySystemsContainer.innerHTML = result;
                                }, this),
                                onfailure: BX.proxy(function() {
                                    return this;
                                }, this)
                            }, this);
                        return this;
                    }, this)
                );
                return this;
            };
            return classDescription;
        })();

        BX.Sale.OrderInnerPayment = (function()
        {
            var paymentDescription = function(params)
            {
                this.ajaxUrl = params.url;
                this.accountNumber = params.accountNumber || {};
                this.paymentNumber = params.paymentNumber || {};
                this.wrapperId = params.wrapperId || '';
                this.valueLimit =  parseFloat(params.valueLimit) || 0;
                this.templateFolder = params.templateFolder;
                this.wrapper = document.getElementById(this.wrapperId);
                this.inputElement = root.find('[data-role="inner-input"]')[0];
                this.sendPayment = root.find('[data-role="inner-button"]')[0];
                BX.ready(BX.proxy(this.init, this));
            };

            paymentDescription.prototype.init = function()
            {
                BX.bind(this.inputElement, 'input', BX.delegate(
                    function ()
                    {
                        this.inputElement.value = this.inputElement.value.replace(/[^\d,.]*/g, '')
                            .replace(/,/g, '.')
                            .replace(/([,.])[,.]+/g, '$1')
                            .replace(/^[^\d]*(\d+([.,]\d{0,2})?).*$/g, '$1');

                        var value = parseFloat(this.inputElement.value);

                        if (value > this.valueLimit)
                        {
                            this.inputElement.value = this.valueLimit;
                        }
                        if (value <= 0)
                        {
                            this.inputElement.value = 0;
                            this.sendPayment.classList.add('inactive-button');
                        }
                        else
                        {
                            this.sendPayment.classList.remove('inactive-button');
                        }
                    }, this)
                );

                BX.bind(this.sendPayment, 'click', BX.delegate(
                    function ()
                    {
                        if (event.target.classList.contains("inactive-button"))
                        {
                            return this;
                        }
                        event.target.classList.add("inactive-button");
                        BX.ajax(
                            {
                                method: 'POST',
                                dataType: 'html',
                                url: this.ajaxUrl,
                                data:
                                    {
                                        sessid: BX.bitrix_sessid(),
                                        accountNumber: this.accountNumber,
                                        paymentNumber: this.paymentNumber,
                                        inner: "Y",
                                        onlyInnerFull: this.onlyInnerFull,
                                        paymentSum: this.inputElement.value,
                                        returnUrl: this.returnUrl
                                    },
                                onsuccess: BX.proxy(function(result)
                                {
                                    if (result.length > 0)
                                        this.wrapper.innerHTML = result;
                                    else
                                        window.location.reload();
                                },this),
                                onfailure: BX.proxy(function()
                                {
                                    return this;
                                }, this)
                            }, this
                        );
                        return this;
                    }, this)
                );
            };

            return paymentDescription;
        })();

        var orderPaymentChange = new BX.Sale.OrderPaymentChange(<?= $javascriptParams ?>);
        var useOrderInnerPayment = <?= $bUseInnerPaymentInfo ? 'true' : 'false' ?>;

        if (useOrderInnerPayment) {
            var orderInnerPayment = new BX.Sale.OrderInnerPayment(<?= $javascriptParamsInnerPaymentInfo ?>);
        }

    }, {
        'name': '[Component] bitrix:sale.order.payment.change (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
