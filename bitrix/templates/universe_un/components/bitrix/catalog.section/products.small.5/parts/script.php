<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var array $arNavigation
 * @var string $sTemplateId
 * @var string $sTemplateContainer
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
            var properties = root.data('properties');
            var items;
            var component;
            var order;
            var request;

            <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
            order = function (dataItem) {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['ORDER']['ID'],
                    'template' => $arResult['FORM']['ORDER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form-order',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['ORDER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['ORDER']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

                app.api.forms.show(options);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ORDER']['ID'].'.open') ?>);
            };
            <?php } ?>
            <?php if ($arResult['FORM']['REQUEST']['SHOW']) { ?>
            request = function (dataItem) {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['REQUEST']['ID'],
                    'template' => $arResult['FORM']['REQUEST']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form-request',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_REQUEST_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['REQUEST']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['REQUEST']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

                app.api.forms.show(options);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['REQUEST']['ID'].'.open') ?>);
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

                    if (handled.indexOf(this) > -1)
                        return;

                    handled.push(this);
                    item.offers = new app.classes.catalog.Offers({
                        'properties': properties.length !== 0 ? properties : data.properties,
                        'list': data.offers
                    });

                    item.counter = $('[data-role="item.counter"]', item);
                    item.price = $('[data-role="item.price"]', item);
                    item.price.measure = $('[data-role="price.measure"]', item.price);
                    item.price.base = $('[data-role="item.price.base"]', item.price);
                    item.price.discount = $('[data-role="item.price.discount"]', item.price);
                    item.price.percent = $('[data-role="item.price.percent"]', item.price);
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
                    item.toggle = $('[data-role="item.toggle"]', item);
                    item.toggleButtons = $('[data-toggle]', item);
                    item.toggleButtons.open = $('[data-toggle="open"]', item);
                    item.toggleButtons.close = $('[data-toggle="close"]', item);

                    <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                    item.quickView = $('[data-role="quick.view"]', item);

                    item.quickView.on('click', function () {
                        app.api.components.show({
                            'component': 'bitrix:catalog.element',
                            'template': data.quickView.template,
                            'parameters': data.quickView.parameters,
                            'settings': {
                                'parameters': {
                                    'className': 'popup-window-quick-view',
                                    'width': null
                                }
                            }
                        });
                    });
                    <?php } ?>

                    item.toggle.open = function (timer) {
                        item.toggle.slideDown(timer);
                        item.toggleButtons.close.addClass('active');
                        item.toggleButtons.open.removeClass('active');
                    };

                    item.toggle.close = function (timer) {
                        item.toggle.slideUp(timer);
                        item.toggleButtons.open.addClass('active');
                        item.toggleButtons.close.removeClass('active');
                    };

                    item.toggleButtons.open.click(item.toggle.open);
                    item.toggleButtons.close.click(item.toggle.close);

                    item.update = function () {
                        var price = null;

                        item.attr('data-available', entity.available ? 'true' : 'false');
                        _.each(entity.prices, function (object) {
                            if (object.quantity.from === null || item.quantity.get() >= object.quantity.from)
                                price = object;
                        });

                        if (price !== null) {
                            <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                            if (price.quantity.from === null && price.quantity.to === null) {
                                var summary = [];
                                var summaryQuantity;

                                if (item.quantity.get() <= 0) {
                                    summaryQuantity = entity.quantity.ratio;
                                } else {
                                    summaryQuantity = item.quantity.get();
                                }

                                summary.base = price.base.value * summaryQuantity;
                                summary.discount = price.discount.value * summaryQuantity;
                                BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                                price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                                price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                            }
                            <?php } ?>

                            item.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                            item.price.attr('data-measure', price.discount.measure !== null ? 'true' : 'false');
                            item.price.attr('data-range', ((price.quantity.from !== null) || (price.quantity.to !== null)) ? 'true' : 'false');
                            item.price.measure.text(price.discount.measure);
                            item.price.base.html(price.base.display);
                            item.price.discount.html(price.discount.display);
                            item.price.percent.text('-' + price.discount.percent + '%');
                        } else {
                            item.price.attr('data-discount', 'false');
                            item.price.attr('data-measure', 'false');
                            item.price.attr('data-range', 'false');
                            item.price.base.html(null);
                            item.price.discount.html(null);
                            item.price.percent.text(null);
                        }

                        item.price.attr('data-show', price !== null ? 'true' : 'false');
                        item.quantity.configure({
                            'bounds': {
                                'minimum': entity.quantity.ratio,
                                'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                            },
                            'step': entity.quantity.ratio
                        });

                        item.find('[data-offer]').css({
                            'display': '',
                            'opacity': ''
                        });

                        if (entity !== data) {
                            item.find('[data-offer=' + entity.id + ']').css('display', 'block');
                            item.find('[data-offer="false"]').css('display', 'none');
                            item.find('[data-offer=' + entity.id + '][data-role="item.quantity"]').animate({'opacity': 1}, 500);
                        }

                        item.find('[data-basket-id]')
                            .data('add', item.quantity.get())
                            .data('basketQuantity', item.quantity.get());
                    };

                    item.update();

                    <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
                    item.order.on('click', function () {
                        order(data);
                    });
                    <?php } ?>
                    <?php if ($arResult['FORM']['REQUEST']['SHOW']) { ?>
                    item.request.on('click', function () {
                        request(data);
                    });
                    <?php } ?>

                    item.quantity.on('change', function (event, value) {
                        var purchaseButton = item.find('[data-basket-id=' + entity.id + ']');
                        item.update();

                        if (!(purchaseButton.attr('data-basket-state') === 'none' || purchaseButton.attr('data-basket-state') === 'buy')) {
                            purchaseButton.attr('data-basket-state', 'update');
                        }
                    });

                    if (!item.offers.isEmpty()) {
                        item.properties = $('[data-role="item.property"]', item);
                        item.properties.values = $('[data-role="item.property.value"]', item.properties);
                        item.properties.attr('data-visible', 'false');
                        item.properties.each(function () {
                            var self = $(this);
                            var property = self.data('property');
                            var values = self.find(item.properties.values);

                            values.each(function () {
                                var self = $(this);
                                var value = self.data('value');

                                self.on('click', function () {
                                    item.offers.setCurrentByValue(property, value);

                                    if (!entity.available) {
                                        item.toggle.close(200);
                                    } else {
                                        item.toggle.open(400);
                                    }
                                });
                            });
                        });

                        _.each(item.offers.getList(), function (offer) {
                            _.each(offer.values, function (value, key) {
                                if (value == 0)
                                    return;

                                item.properties
                                    .filter('[data-property=' + key + ']')
                                    .attr('data-visible', 'true');
                            });
                        });

                        item.offers.on('change', function (event, offer, values) {
                            entity = offer;

                            _.each(values, function (values, state) {
                                _.each(values, function (values, property) {
                                    property = item.properties.filter('[data-property="' + property + '"]');

                                    _.each(values, function (value) {
                                        value = property.find(item.properties.values).filter('[data-value="' + value + '"]');
                                        value.attr('data-state', state);
                                    });
                                });
                            });

                            item.update();
                        });

                        item.offers.setCurrentById(item.offers.getList()[0].id);
                    }

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
                    app.api.basket.update();

                    return result;
                };
            })();

            root.update();
        }, {
            'name': '[Component] bitrix:catalog.section (products.small.5)',
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