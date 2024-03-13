<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplateId
 * @var string $sTemplateContainer
 * @var array $arVisual
 * @var array $arNavigation
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */
$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;

if ($bBase)
    CJSCore::Init(array('currency'));

$oSigner = new \Bitrix\Main\Security\Sign\Signer;
$sSignedTemplate = $oSigner->sign($templateName, 'catalog.section');
$sSignedParameters = $oSigner->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'catalog.section');

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');
            var root = data.nodes;
            var panel = $('[data-role="section.panel"]', root);
            var items;
            var component;
            var order;
            var quickViewShow;
            var quickItemsId = [];
            var quickItems = Object.create(null);

            <?php if ($arVisual['BUTTONS']['MORE']['SHOW']) { ?>
                var itemsListControl = $('[data-role="showMore"]', root);

                itemsListControl.each(function () {
                    var maxHeight = 0;
                    var minHeight = 0;
                    var minHeightItemsCount = 3;
                    var counter = 0;

                    $(this).closest('[data-role="items.container"]');

                    this.container = $(this).closest('[data-role="items.container"]');

                    $('[data-role="item"]', this.container).each(function () {
                        counter ++;
                        maxHeight += $(this).outerHeight(true);

                        if (counter === minHeightItemsCount)
                            minHeight = maxHeight;
                    });

                    maxHeight = maxHeight + $(this).outerHeight(true);

                    this.maxHeight = maxHeight;
                    this.minHeight = minHeight;
                    this.container.css('height', minHeight);
                });

                itemsListControl.on('click', function () {
                    if ($(this).attr('data-status') === 'close') {
                        $(this).attr('data-status', 'open');
                        this.container.attr('data-status', 'open');
                        this.container.css('height', this.maxHeight);
                    } else {
                        $(this).attr('data-status', 'close');
                        this.container.attr('data-status', 'close');
                        this.container.css('height', this.minHeight);
                    }
                });
            <?php } ?>

            <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
            quickViewShow = function (dataItem, quickItemsId) {
                app.api.components.show({
                    'component': 'bitrix:catalog.element',
                    'template': dataItem.template,
                    'parameters': dataItem.parameters,
                    'settings': {
                        'parameters': {
                            'className': 'popup-window-quick-view',
                            'width': null
                        }
                    }
                }).then(function (popup) {
                    <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
                    var container = $(popup.contentContainer);

                    var indexItem = quickItemsId.indexOf(dataItem.parameters.ELEMENT_ID);
                    var prevItemId = quickItemsId[indexItem - 1];
                    var nextItemId = quickItemsId[indexItem + 1];

                    if (prevItemId === undefined)
                        prevItemId = 0;

                    if (nextItemId === undefined)
                        nextItemId = 0;

                    var load = container.find('.popup-load-container');

                    load.css('display', 'none');

                    container.append('<div class="popup-load-container"><div class="popup-load-whirlpool"></div></div>');

                    if (prevItemId !== 0 || nextItemId !== 0) {
                        container.append('<div class="popup-button btn-prev intec-cl-background-hover" data-role="quickView.button" data-id="' + prevItemId + '">' +
                            '<i class="far fa-angle-left"></i>' +
                            '</div>');
                        container.append('<div class="popup-button btn-next intec-cl-background-hover" data-role="quickView.button" data-id="' + nextItemId + '">' +
                            '<i class="far fa-angle-right"></i>' +
                            '</div>');
                    }
                    <?php } ?>
                });
            };

            <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
            $('body').on('click', '[data-role="quickView.button"]', function () {
                var item = $(this);
                var id = item.data('id');

                item.parent().find('.popup-load-container').css('display', 'block');

                quickViewShow(quickItems[id], quickItemsId);
            });
            <?php } ?>
            <?php } ?>

            <?php if ($arResult['FORM']['SHOW']) { ?>
            order = function (dataItem) {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['ID'],
                    'template' => $arResult['FORM']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_FORM_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

                app.api.forms.show(options);

                if (window.yandex && window.yandex.metrika) {
                    window.yandex.metrika.reachGoal('forms.open');
                    window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open') ?>);
                }
            };
            <?php } ?>

            root.panel = function () {
                if (!panel.length)
                    return false;

                var button = {
                    'element': $('[data-role="panel.add"]', panel),
                    'number': $('[data-role="panel.add.number"]', button)
                };

                var checkbox = {
                    'count': 0,
                    'all': $('[data-role="panel.checkbox"]', panel),
                    'items': $('[data-role="item.checkbox"]', root),
                    'check': function () {
                        if (checkbox.count > 0)
                            button.element.attr('data-basket-state', 'none');
                        else
                            button.element.attr('data-basket-state', 'disabled');
                    },
                    'recount': function () {
                        checkbox.count = 0;

                        checkbox.items.each(function () {
                            if (checkbox.all.is(':checked') && this.disabled !== true && this.checked !== true)
                                checkbox.all.prop('checked', false);

                            if (this.checked === true)
                                checkbox.count++;

                            checkbox.check();

                            button.number.html(checkbox.count);
                        });
                    }
                };

                checkbox.all.on('change', function () {
                    if (this.checked === true)
                        checkbox.items.each(function () {
                            if (this.disabled !== true && this.checked !== true)
                                this.checked = true;
                        });
                    else
                        checkbox.items.each(function () {
                            if (this.disabled !== true && this.checked === true)
                                this.checked = false;
                        });

                    checkbox.recount();
                });
                checkbox.items.on('change', function () {
                    if (this.checked === true)
                        checkbox.count++;
                    else {
                        checkbox.count--;

                        if (checkbox.all.is(':checked'))
                            checkbox.all.prop('checked', false);
                    }

                    checkbox.check();

                    button.number.html(checkbox.count);
                });

                button.element.on('click', function () {
                    var self = $(this);

                    if (self.attr('data-basket-state') !== 'none')
                        return false;

                    var checked = [];

                    checkbox.items.each(function () {
                        var self = $(this);

                        if (self.prop('disabled') !== true && self.prop('checked') === true)
                            checked.push(app.api.basket.add({
                                'id': self.data('basketId'),
                                'quantity': self.data('basketQuantity'),
                                'price': self.data('basketPrice')
                            }));
                    });

                    if (checked.length) {
                        button.element.attr('data-basket-state', 'processing');

                        app.api.runActions(checked).then(function () {
                            button.element.attr('data-basket-state', 'none');
                        });
                    }
                });

                checkbox.recount();
            };

            root.update = function () {
                var handled = [];

                if (!_.isNil(items))
                    handled = items.handled;

                items = $('[data-role="item"]', root);
                items.handled = handled;
                items.each(function () {
                    var item = $(this);
                    var data = item.data('data');
                    var entity = data;

                    quickItems[data.quickView.parameters.ELEMENT_ID] = data.quickView;
                    quickItemsId.push(data.quickView.parameters.ELEMENT_ID);

                    if (handled.indexOf(this) > -1)
                        return;

                    handled.push(this);

                    item.counter = $('[data-role="item.counter"]', item);
                    item.price = $('[data-role="item.price"]', item);
                    item.timer = $('[data-role="timer-holder"]', item);
                    item.price.base = $('[data-role="item.price.base"]', item.price);
                    item.price.discount = $('[data-role="item.price.discount"]', item.price);
                    item.order = $('[data-role="item.order"]', item);
                    item.quantity = app.ui.createControl('numeric', {
                        'node': item.counter,
                        'bounds': {
                            'minimum': entity.quantity.ratio,
                            'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                        },
                        'step': entity.quantity.ratio
                    });

                    <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                    item.quickView = $('[data-role="quick.view"]', item);

                    item.quickView.on('click', function () {
                        quickViewShow(data.quickView, quickItemsId);
                    });
                    <?php } ?>

                    item.update = function () {
                        var price = null;

                        item.attr('data-available', entity.available ? 'true' : 'false');
                        _.each(entity.prices, function (object) {
                            if (object.quantity.from === null || item.quantity.get() >= object.quantity.from)
                                price = object;
                        });

                        if (price !== null) {
                            <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                            var summary = [];

                            var quantity = item.quantity.get();

                            if (quantity == 0)
                                quantity = 1;

                            summary.base = price.base.value * quantity;
                            summary.base = summary.base.toFixed(price.currency.DECIMALS);
                            summary.discount = price.discount.value * quantity;
                            summary.discount = summary.discount.toFixed(price.currency.DECIMALS);

                            BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                            price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                            price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                            <?php } ?>

                            item.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                            item.price.base.html(price.base.display);
                            item.price.discount.html(price.discount.display);
                        } else {
                            item.price.attr('data-discount', 'false');
                            item.price.base.html(null);
                            item.price.discount.html(null);
                        }

                        item.price.attr('data-show', price !== null ? 'true' : 'false');
                        item.quantity.configure({
                            'bounds': {
                                'minimum': entity.quantity.ratio,
                                'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                            },
                            'step': entity.quantity.ratio
                        });

                        item.find('[data-offer]').css('display', '');

                        if (entity !== data) {
                            item.find('[data-offer=' + entity.id + ']').css('display', 'block');
                            item.find('[data-offer="false"]').css('display', 'none');
                        }

                        item.find('[data-basket-id]')
                            .data('basketQuantity', item.quantity.get())
                            .attr('data-basket-quantity', item.quantity.get());

                        <?php if ($arVisual['TIMER']['SHOW']) { ?>
                            timerUpdate(item.timer, entity.id);
                        <?php } ?>
                    };

                    item.update();

                    <?php if ($arResult['FORM']['SHOW']) { ?>
                    item.order.on('click', function () {
                        order(data);
                    });
                    <?php } ?>

                    item.quantity.on('change', function (event, value) {
                        item.update();
                    });
                });
            };

            BX.message(<?= JavaScript::toObject([
                'BTN_MESSAGE_LAZY_LOAD' => '',
                'BTN_MESSAGE_LAZY_LOAD_WAITER' => ''
            ]) ?>);

            component = new JCCatalogSectionComponent(<?= JavaScript::toObject([
                'siteId' => SITE_ID,
                'componentPath' => $componentPath,
                'navParams' => $arNavigation,
                'deferredLoad' => false,
                'initiallyShowHeader' => false,
                'bigData' => $arResult['BIG_DATA'],
                'lazyLoad' => $arVisual['NAVIGATION']['LAZY']['BUTTON'],
                'loadOnScroll' => $arVisual['NAVIGATION']['LAZY']['SCROLL'],
                'template' => $sSignedTemplate,
                'parameters' => $sSignedParameters,
                'ajaxId' => $arParams['AJAX_ID'],
                'container' => $sTemplateContainer
            ], true) ?>);

            component.processItems = (function () {
                var action = component.processItems;

                return function () {
                    var result = action.apply(this, arguments);

                    root.update();
                    root.panel();
                    app.api.basket.update();

                    return result;
                };
            })();

            function timerUpdate(timer, id){

                var timerParameters = <?= JavaScript::toObject($arResult['TIMER']['PROPERTIES']) ?>;

                if (!!timerParameters) {
                    timerParameters.parameters.ELEMENT_ID = id;
                    timerParameters.parameters.RANDOMIZE_ID = 'Y';
                    timerParameters.parameters.AJAX_MODE = 'N';

                    app.api.components.get(timerParameters).then(function (content) {
                        timer.html(content);
                    });
                }
            }

            root.update();
            root.panel();
        }, {
            'name': '[Component] bitrix:catalog.section (catalog.text.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php

unset($sSignedParameters);
unset($sSignedTemplate);
unset($oSigner);