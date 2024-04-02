<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplateId
 * @var string $sTemplateContainer
 * @var array $arVisual
 * @var array $arNavigation
 */

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

if ($bBase)
    CJSCore::Init(array('currency'));

$oSigner = new Signer;
$sSignedTemplate = $oSigner->sign($templateName, 'catalog.section');
$sSignedParameters = $oSigner->sign(
    base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])),
    'catalog.section'
);

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');
            var root = data.nodes;
            var items;
            var component;
            var order;
            var request;
            var quickViewShow;
            var quickItemsId = [];
            var quickItems = Object.create(null);

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
            <?php if ($arResult['FORMS']['ORDER']['SHOW']) { ?>
            order = function (dataItem) {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORMS']['ORDER']['ID'],
                    'template' => $arResult['FORMS']['ORDER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_FORM_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORMS']['ORDER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORMS']['ORDER']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

                app.api.forms.show(options);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORMS']['ORDER']['ID'].'.open') ?>);
            };
            <?php } ?>
            <?php if ($arResult['FORMS']['REQUEST']['SHOW']) { ?>
            request = function (dataItem) {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORMS']['REQUEST']['ID'],
                    'template' => $arResult['FORMS']['REQUEST']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form-request',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => $arVisual['BUTTONS']['REQUEST']['TEXT']
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORMS']['REQUEST']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORMS']['REQUEST']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

                app.api.forms.show(options);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORMS']['REQUEST']['ID'].'.open') ?>);
            };
            <?php } ?>
            <?php if ($arVisual['PANEL']['SHOW'] && $arResult['ACTION'] === 'buy') { ?>
            var panel = {
                'root': $('[data-role="section.panel"]', root),
                'count': 0
            };

            panel.button = $('[data-role="panel.add"]', panel.root);
            panel.number = $('[data-role="panel.add.number"]', panel.root);
            panel.checkbox = {
                'all': $('[data-role="panel.checkbox"]', panel.root),
                'items': null
            };
            panel.buttonAction = function () {
                var bundle = [];

                panel.checkbox.items.each(function () {
                    if (this.checked && this.getAttribute('data-basket-state') !== 'added')
                        bundle.push(app.api.basket.add({
                            'id': this.getAttribute('data-basket-id'),
                            'quantity': this.getAttribute('data-basket-quantity'),
                            'price': this.getAttribute('data-basket-price')
                        }));
                });

                if (bundle.length > 0) {
                    panel.button.attr('data-basket-state', 'processing');

                    app.api.runActions(bundle).then(function () {
                        panel.countUpdate();
                    });
                }
            }
            panel.allUpdate = function () {
                panel.checkbox.items.each(function () {
                    if (panel.checkbox.all.prop('checked'))
                        this.checked = true;
                    else
                        this.checked = false;
                });

                panel.countRefresh();
            };
            panel.checkboxUpdate = function () {
                if (this.checked)
                    panel.count++;
                else
                    panel.count--;

                panel.countUpdate();
            };
            panel.countUpdate = function () {
                var active = true;

                panel.checkbox.items.each(function () {
                    if (!this.checked && active)
                        active = false;
                });

                panel.checkbox.all.prop('checked', active);
                panel.number.html(panel.count);
                panel.button.attr('data-basket-state', panel.count > 0 ? 'none' : 'disabled');
            };
            panel.countRefresh = function () {
                panel.count = 0;
                panel.checkbox.items.each(function () {
                    if (this.checked)
                        panel.count++;
                });

                panel.countUpdate();
            };
            panel.update = function () {
                panel.checkbox.items = $('[data-role="item.checkbox"]', root).not(':disabled');

                panel.countRefresh();

                panel.button
                    .off('click', panel.buttonAction)
                    .on('click', panel.buttonAction);

                panel.checkbox.all
                    .off('change', panel.allUpdate)
                    .on('change', panel.allUpdate);

                panel.checkbox.items
                    .off('change', panel.checkboxUpdate)
                    .on('change', panel.checkboxUpdate);
            };
            <?php } ?>

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
                    item.request = $('[data-role="item.request"]', item);
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

                    <?php if ($arResult['FORMS']['ORDER']['SHOW']) { ?>
                    item.order.on('click', function () {
                        order(data);
                    });
                    <?php } ?>
                    <?php if ($arResult['FORMS']['REQUEST']['SHOW']) { ?>
                    item.request.on('click', function () {
                        request(data);
                    });
                    <?php } ?>

                    item.quantity.on('change', function (event, value) {
                        item.update();

                        <?php if ($arVisual['COUNTER']['MESSAGE']['MAX']['SHOW']) { ?>
                            var alertElem = $('[data-role="max-message"]', item);
                            var closeIcon = $('[data-role="max-message-close"]', alertElem);
    
                            if (item.data('available') && !data.quantity.zero && data.quantity.trace && data.quantity.value > 0) {
                                if (item.quantity.get() >= entity.quantity.value) {
                                    $('[data-role="max-message"]', item).fadeIn();
                                }
                            }
    
                            closeIcon.on('click', function (event) {
                                event.stopPropagation();
                                $('[data-role="max-message"]', item).fadeOut();
                            });
    
                            $('[data-action="decrement"]', item).on('click', function () {
                                $('[data-role="max-message"]', item).fadeOut();
                            });
                        <?php } ?>
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
                    <?php if ($arVisual['PANEL']['SHOW'] && $arResult['ACTION'] === 'buy') { ?>
                    panel.update();
                    <?php } ?>
                    app.api.basket.update();

                    return result;
                };
            })();

            function timerUpdate(timer, id) {

                var timerParameters = <?= JavaScript::toObject($arResult['TIMER']['PROPERTIES']) ?>;

                if (!!timerParameters) {
                    timerParameters.parameters.ELEMENT_ID = id;
                    timerParameters.parameters.RANDOMIZE_ID = 'Y';
                    timerParameters.parameters.AJAX_MODE = 'N';

                    app.api.components.get(timerParameters).then(function (content) {
                        timer.html(content);
                    });
                }
            };

            root.update();
            <?php if ($arVisual['PANEL']['SHOW'] && $arResult['ACTION'] === 'buy') { ?>
            panel.update();
            <?php } ?>
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