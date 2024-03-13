<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var string $sTemplateContainer
 * @var array $arVisual
 * @var array $arNavigation
 * @var boolean $bBase
 */

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

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
        var quickViewShow;
        var quickItemsId = [];
        var quickItems = Object.create(null);
        var columnCount = <?= JavaScript::toObject($arVisual['COLUMNS']['DESKTOP']) ?>;
        var arOfferId = $('[data-role="items"]', root)[0].dataset.filtered;
        var filterApply = arOfferId.length > 0;
        var directoryProperty = '<?= $arParams['OFFERS_PROPERTY_PICTURE_DIRECTORY'] ?>';
        var offerVariableSelect = '<?= $arParams['OFFERS_VARIABLE_SELECT'] ?>';
        var lazyLoadUse = '<?= $arParams['LAZYLOAD_USE'] === 'Y' ? 'true' : 'false' ?>';

        if (!!directoryProperty)
            directoryProperty = 'P_' + directoryProperty;

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
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_ORDER')
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
                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
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

        root.update = function () {
            var handled = [];

            if (!_.isNil(items))
                handled = items.handled;

            items = $('[data-role="item"][data-products="main"]', root);

            <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'middle') { ?>
                $(window).on('resize', function(){
                    giftAdaptation(items, root, columnCount);
                });
    
                giftAdaptation(items, root, columnCount);
            <?php } ?>
            items.handled = handled;
            items.each(function () {
                var item = $(this);
                var data = item.data('data');
                var entity = data;
                var expanded = false;
                var offerProps = item.data('properties');

                quickItems[data.quickView.parameters.ELEMENT_ID] = data.quickView;
                quickItemsId.push(data.quickView.parameters.ELEMENT_ID);

                if (handled.indexOf(this) > -1)
                    return;

                handled.push(this);
                item.offers = new app.classes.catalog.Offers({
                    'properties': offerProps.length !== 0 ? offerProps : properties,
                    'list': data.offers
                });

                item.recalculation = item.data('recalculation');
                item.summary = $('[data-role="item.summary"]', item);
                item.summary.price = $('[data-role="item.summary.price"]', item.summary);
                item.timer = $('[data-role="timer.holder"]', item);
                item.article = $('[data-role="article"]', item);
                item.article.value = $('[data-role="article.value"]', item.article);
                item.stores = $('[data-role="stores.popup.window"]', item);
                item.stores.controls = $('[data-role="stores.popup.button"]', item);
                item.stores.controls.toggle = item.stores.controls.filter('[data-popup="toggle"]');
                item.stores.controls.close = item.stores.controls.filter('[data-popup="close"]');
                item.gallery = $('[data-role="gallery"]', item);
                item.counter = $('[data-role="item.counter"]', item);
                item.price = $('[data-role="item.price"]', item);
                item.price.base = $('[data-role="item.price.base"]', item.price);
                item.price.discount = $('[data-role="item.price.discount"]', item.price);
                item.price.measure = $('[data-role="item.price.measure"]', item.price);
                item.price.extended = $('[data-role="price.extended.popup.window"]', item.price);
                item.price.extended.toggle = $('[data-role="price.extended.popup.toggle"]', item.price);
                item.price.extended.close = $('[data-role="price.extended.popup.close"]', item.price);
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
                item.links = $('[data-role="offer.link"]', item);
                item.priceRange = $('[data-role="price.ranges"]', item);
                item.price.percent = $('[data-role="price.percent"]', item.price);
                item.price.difference = $('[data-role="price.difference"]', item.price);

                <?php if ($arVisual['MEASURES']['USE']) { ?>
                item.measure = [];
                item.measure.multiplier = 1;
                item.measure.id = '';
                item.measure.symbol = '';
                item.measure.items = entity.measures.items;

                item.measures = $('[data-role="measures"]', item);
                item.measures.container = $('[data-role="measures.container"]', item);
                item.measures.price = $('[data-role="measures.price"]', item.measures);
                item.measures.select = $('[data-role="measures.select"]', item.measures);

                item.measures.select.value = $('[data-role="measures.select.value"]', item.measures.select);
                item.measures.select.options = $('[data-role="measures.select.options"]', item.measures.select);
                item.measures.multiplier = $('[data-role="measures.multiplier"]', item);

                $(document).on('click', function(event) {
                    if (!$(event.target).closest('[data-role="measures.select"]').length && $('[data-role="measures.select.value"]', item).hasClass('active'))
                        item.measures.hide();
                    else
                        return;
                });

                item.measures.create = function (measures) {
                    if (entity.measures.use === 'true')
                        item.measures.container.show();
                    else
                        item.measures.container.hide();

                    if (!$('[data-role="measures.select.value"]', item.measures.select).length) {
                        item.measures.select.append('<div data-role="measures.select.value" data-value=""><span></span><i class="intec-cl-text far fa-angle-down"></i></div>');
                    }

                    if (!$('[data-role="measures.select.options"]', item.measures.select).length) {
                        item.measures.select.append('<div data-role="measures.select.options" data-expanded="false"></div>');
                    } else {
                        $('[data-role="measures.select.options"]', item.measures.select).empty();
                    }

                    item.measures.select.value = $('[data-role="measures.select.value"]', item.measures.select);
                    item.measures.select.options = $('[data-role="measures.select.options"]', item.measures.select);

                    _.each(measures, function (value) {
                        var self = '<div class="intec-cl-text-hover" data-role="measures.select.option" data-id="'+value.id+'" data-active="'+value.base+'" data-multiplier="'+value.multiplier+'">'+value.symbol+'</div>';

                        item.measures.select.options.append(self);

                        if (value.base == true) {
                            item.measures.select.value.find('span').html('<?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_MEASURES')?>' + value.symbol);
                            item.measures.select.value.attr({
                                'data-value': value.id,
                                'data-multiplier': value.multiplier
                            });

                            item.measure.multiplier = value.multiplier;
                            item.measure.id = value.id;
                            item.measure.symbol = value.symbol;
                        }
                    });
                };

                if (item.measures.length !== 0) {

                    item.measures.create(entity.measures.items);

                    item.measures.show = function() {
                        var heightSummary;

                        item.measures.select.value.addClass('active');

                        item.measures.select.options.css({
                            'height': 'auto',
                            'display': 'block'
                        });
                        heightSummary = item.measures.select.options.outerHeight();
                        item.measures.select.options.css('height', 0);

                        item.measures.select.options.animate({'height': heightSummary}, 500, function () {
                            item.measures.select.options.css('top', {
                                'height': '',
                                'display': ''
                            });
                        });

                        item.measures.select.options.attr('data-expanded', 'true');
                    };

                    item.measures.hide = function() {
                        item.measures.select.value.removeClass('active');

                        item.measures.select.options.animate({'height': 0}, 500, function () {
                            item.measures.select.options.css({
                                'height': '',
                                'display': ''
                            });
                        });

                        item.measures.select.options.attr('data-expanded', 'false');
                    };

                    item.measures.select.value.on('click', function() {
                        var expanded = item.measures.select.options.attr('data-expanded');

                        if (expanded == 'true') {
                            item.measures.hide();
                        } else {
                            item.measures.show();
                        }
                    });

                    item.measures.select.on('click', '[data-role="measures.select.option"]', function() {
                        var self = $(this);
                        var id = self.attr('data-id');
                        var multiplierItem = self.attr('data-multiplier');

                        $('[data-role="measures.select.option"]', item.measures.select).each(function() {
                            $(this).attr('data-active', 'false');
                        });
                        self.attr('data-active', 'true');

                        item.measures.select.value.attr('data-value', id);
                        item.measures.select.value.attr('data-multiplier', multiplierItem);
                        item.measures.select.value.find('span').html('<?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_MEASURES')?>' + self.html());

                        item.measures.hide();

                        item.measure.multiplier = multiplierItem;
                        item.measure.id = id;
                        item.measure.symbol = item.measure.items[id].symbol;

                        item.update();
                    });
                }
                <?php } ?>

                <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                item.quickView = $('[data-role="quick.view"]', item);

                item.quickView.on('click', function () {
                    quickViewShow(data.quickView, quickItemsId);
                });
                <?php } ?>

                item.update = function () {
                    var article = entity.article;
                    var price = null;
                    var quantity = {
                        'bounds': {
                            'minimum': entity.quantity.ratio,
                            'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                        },
                        'step': entity.quantity.ratio
                    };

                    item.attr('data-available', entity.available ? 'true' : 'false');

                    if (article === null)
                        article = data.article;

                    item.article.attr('data-show', article === null ? 'false' : 'true');
                    item.article.value.text(article);
                    /**/
                    if (entity.measures.items) {
                        item.measure.items = entity.measures.items;

                        var measuresMin = entity.default.quantity.ratio * item.measure.multiplier;
                        var measuresMax = parseFloat(entity.default.quantity.value * item.measure.multiplier).toFixed(2);

                        if ((measuresMin | 0) === measuresMin)
                            measuresMin = parseFloat(measuresMin);
                        else
                            measuresMin = parseFloat(measuresMin).toFixed(2);

                        item.measures.multiplier.html(measuresMin + ' '+ item.measure.symbol);

                        quantity = {
                            'bounds': {
                                'minimum': measuresMin,
                                'maximum': entity.default.quantity.trace && !entity.default.quantity.zero ? measuresMax : false
                            },
                            'step': measuresMin
                        };

                        _.each(entity.default.prices, function (object, index) {
                            var priceBase = object.base.value / item.measure.multiplier;
                            var priceDiscount = object.discount.value / item.measure.multiplier;
                            var from = null;
                            var to = null;

                            BX.Currency.setCurrencyFormat(object.currency.CURRENCY, object.currency);

                            entity.prices[index].base.value = priceBase;
                            entity.prices[index].base.display = BX.Currency.currencyFormat(priceBase, object.currency.CURRENCY, true);

                            entity.prices[index].discount.value = priceDiscount;
                            entity.prices[index].discount.display = BX.Currency.currencyFormat(priceDiscount, object.currency.CURRENCY, true);

                            if (entity.prices[index].quantity.from != null) {
                                from = object.quantity.from * item.measure.multiplier;

                                if ((from | 0) === from)
                                    from = parseFloat(from);
                                else
                                    from = parseFloat(from).toFixed(2);

                                entity.prices[index].quantity.from = from;
                            }

                            if (entity.prices[index].quantity.to !== null) {
                                to = object.quantity.to * item.measure.multiplier;

                                if ((to | 0) === to)
                                    to = parseFloat(to);
                                else
                                    to = parseFloat(to).toFixed(2);

                                entity.prices[index].quantity.to = to;
                            }

                        });

                        _.each(entity.prices, function (object, index) {
                            if (object.quantity.from === null || item.quantity.get() >= object.quantity.from)
                                price = object;
                            defaultPrice = entity.default.prices[index];
                        });

                        if (price !== null) {
                            if (item.recalculation == true) {
                                var summary = [];

                                summary.value = price.discount.value * item.quantity.get();
                                summary.value = summary.value.toFixed(price.currency.DECIMALS);

                                BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                                summary.display = BX.Currency.currencyFormat(summary.value, price.currency.CURRENCY, true);

                                item.summary.price.html(summary.display);
                            }

                            item.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                            item.price.base.html(price.base.display);
                            item.price.discount.html(price.discount.display);
                            item.price.measure.html(entity.quantity.measure);
                            item.measures.price.html(BX.Currency.currencyFormat(price.discount.value, price.currency.CURRENCY, true));
                            item.price.percent.text('-' + price.discount.percent + '%');
                            item.price.difference.html(price.discount.difference);

                            var ranges = item.priceRange.filter('[data-offer="false"]');

                            if (data.id != entity.id) {
                                ranges = item.priceRange.filter('[data-offer="'+entity.id+'"]');
                            }

                            ranges.find('[data-role="price.range"]').each(function (index, el) {
                                var rangeTo = $(el).find('[data-role="price.range.to"]');
                                var rangeFrom = $(el).find('[data-role="price.range.from"]');
                                var rangeValue = $(el).find('[data-role="price.range.value"]');
                                var rangeMeasure = $(el).find('[data-role="price.range.measure"]');

                                if (rangeTo.length !== 0 && entity.prices[index].quantity.to != null)
                                    rangeTo.html('<?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_TO', ['#TO#' => '']) ?>' + entity.prices[index].quantity.to);

                                if (rangeFrom.length !== 0 && entity.prices[index].quantity.from != null)
                                    rangeFrom.html('<?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_FROM', ['#FROM#' => '']) ?>' + entity.prices[index].quantity.from);

                                if (rangeValue.length !== 0 && entity.prices[index].discount.display != null)
                                    rangeValue.html(entity.prices[index].discount.display);

                                if (rangeMeasure.length !== 0)
                                    rangeMeasure.html(item.measure.symbol);
                            });

                        } else {
                            item.price.attr('data-discount', 'false');
                            item.price.base.html(null);
                            item.price.discount.html(null);
                            item.price.measure.html(null);
                            item.price.percent.text(null);
                            item.price.difference.html(null);
                        }

                    } else {
                        _.each(entity.prices, function (object) {
                            if (object.quantity.from === null || item.quantity.get() >= object.quantity.from)
                                price = object;
                        });

                        if (price !== null) {
                            if (item.recalculation == true) {
                                var summary = [];

                                summary.value = price.discount.value * item.quantity.get();
                                summary.value = summary.value.toFixed(price.currency.DECIMALS);

                                BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                                summary.display = BX.Currency.currencyFormat(summary.value, price.currency.CURRENCY, true);

                                item.summary.price.html(summary.display);
                            }

                            item.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                            item.price.base.html(price.base.display);
                            item.price.discount.html(price.discount.display);
                            item.price.measure.html(entity.quantity.measure);
                            item.price.percent.text('-' + price.discount.percent + '%');
                            item.price.difference.html(price.discount.difference);
                        } else {
                            item.price.attr('data-discount', 'false');
                            item.price.base.html(null);
                            item.price.discount.html(null);
                            item.price.measure.html(null);
                            item.price.percent.text(null);
                            item.price.difference.html(null);
                        }
                    }
                    /**/

                    item.price.attr('data-show', price !== null ? 'true' : 'false');
                    item.quantity.configure(quantity);

                    item.find('[data-offer]').css('display', '');

                    if (entity !== data) {
                        item.find('[data-offer=' + entity.id + ']').css('display', 'block');
                        item.find('[data-offer="false"]').css('display', 'none');

                        if (item.gallery.filter('[data-offer=' + entity.id + ']').length === 0)
                            item.gallery.filter('[data-offer="false"]').css('display', 'block');

                        $.each(entity.values, function (key, value) {
                            var property = item.find('[data-property="' + key + '"]');
                            var selectedValue = property.find('[data-value="' + value + '"]');
                            var selectedValueContainer = property.find('[data-role="item.property.value.selected"]');

                            var valueName = selectedValue.find('[data-role="item.property.value.name"]').html();

                            selectedValueContainer.html(valueName);
                        });

                        item.find('[data-role="item.property.value"]')
                            .removeClass('intec-cl-background intec-cl-border');
                        item.find('[data-role="item.property.value"][data-state="selected"]')
                            .addClass('intec-cl-background intec-cl-border');

                    }

                    var basketQuantity = item.quantity.get();

                    if (entity.measures.items) {
                        basketQuantity = basketQuantity * item.measure.multiplier;
                    }

                    item.find('[data-basket-id]')
                        .data('basketQuantity', basketQuantity)
                        .attr('data-basket-quantity', basketQuantity);

                    if (item.summary.length !== 0) {
                        if (item.quantity.get() === 1) {
                            if (!item.summary.activated) {
                                item.summary.addClass('hidden');
                                item.attr('data-recalculation', 'false');
                            }
                            item.summary.activated = true;
                        } else if (item.quantity.get() > 0) {
                            item.summary.removeClass('hidden');
                            item.attr('data-recalculation', 'true');
                            item.summary.activated = true;
                        }
                    }

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

                <?php if ($arResult['ORDER_FAST']['USE']) { ?>
                item.orderFast = $('[data-role="orderFast"]', item);
                item.orderFast.on('click', function () {
                    var template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                    var parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

                    parameters['PRODUCT'] = entity.id;
                    parameters['QUANTITY'] = item.quantity.get();

                    app.api.components.show({
                        'component': 'intec.universe:sale.order.fast',
                        'template': template,
                        'parameters': parameters,
                        'settings': {
                            'parameters': {
                                'width': null
                            }
                        }
                    });
                });
                <?php } ?>

                item.quantity.on('change', function (event, value) {
                    item.update();

                    <?php if ($arVisual['COUNTER']['MESSAGE']['MAX']['SHOW']) { ?>
                        var alertElem = $('[data-role="max-message"]', item);
                        var closeIcon = $('[data-role="max-message-close"]', alertElem);
    
                        if (!item.offers.isEmpty()) {
                            var currentOffer = item.offers.getCurrent();
    
                            if (currentOffer.available && !currentOffer.quantity.zero && currentOffer.quantity.trace && currentOffer.quantity.value > 0) {
                                if (item.quantity.get() >= entity.quantity.value) {
                                    $('[data-role="max-message"]', item).fadeIn();
                                }
                            }
                        } else {
                            if (item.data('available') && !data.quantity.zero && data.quantity.trace && data.quantity.value > 0) {
                                if (item.quantity.get() >= entity.quantity.value) {
                                    $('[data-role="max-message"]', item).fadeIn();
                                }
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
                            });
                        });
                    });

                    _.each(item.offers.getList(), function (offer) {
                        if (!!directoryProperty) {
                            item.properties.each(function () {
                                var self = $(this);
                                var values = self.find(item.properties.values);

                                values.each(function () {
                                    var value = $(this);

                                    if (self.data('property') === directoryProperty && offer.values[directoryProperty] == value.data('value') && !!offer.img) {
                                        value.find('[data-role="item.property.value.image"]').attr('style', 'background-image: url(' + offer.img + ')');

                                        if (lazyLoadUse === 'true') {
                                            value.find('[data-role="item.property.value.image"]').attr('data-original', offer.img);
                                        }
                                    }
                                });
                            });
                        }

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

                        $('[data-role="max-message"]', item).fadeOut();

                        if (!!offerVariableSelect && item.links.length !== 0) {
                            var currentUrl = new URL(item.links[0].href);
                            var getParamsUrl = currentUrl.searchParams;

                            getParamsUrl.set(offerVariableSelect, offer.id);
                            currentUrl.searchParams = getParamsUrl;

                            item.links.each(function () {
                                $(this)[0].setAttribute('href', currentUrl.pathname + currentUrl.search);
                            });
                        }

                        <?php if ($arVisual['MEASURES']['USE']) { ?>
                            item.measures.create(entity.measures.items);
                        <?php } ?>

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

                    var offerCurrent;

                    _.each(item.offers.getList(), function (offer) {
                        if (filterApply && arOfferId.includes(offer.id)) {
                            offerCurrent = offer;
                        } else if (!offerCurrent || Number(offerCurrent.sort) > Number(offer.sort)) {
                            offerCurrent = offer;
                        }
                    });

                    item.offers.setCurrentById(offerCurrent.id);
                }

                item.expand = function () {
                    var rectangle = item[0].getBoundingClientRect();
                    var height = rectangle.bottom - rectangle.top;

                    if (expanded)
                        return;

                    expanded = true;
                    item.css('height', height);
                    item.attr('data-expanded', 'true');
                };

                item.narrow = function () {
                    if (!expanded)
                        return;

                    expanded = false;
                    item.attr('data-expanded', 'false');
                    item.css('height', '');
                };

                item.stores.show = function () {
                    item.stores.toggleClass('active');
                };

                item.stores.hide = function () {
                    item.stores.removeClass('active');
                };

                item.price.extended.show = function () {
                    item.price.extended.toggleClass('active');
                };

                item.price.extended.hide = function () {
                    item.price.extended.removeClass('active');
                };

                item.on('mouseenter', item.expand)
                    .on('mouseleave', item.narrow)
                    .on('mouseleave', item.stores.hide)
                    .on('mouseleave', item.price.extended.hide);

                item.offers.on('change', item.price.extended.hide);

                $(window).on('resize', function () {
                    if (expanded) {
                        item.narrow();
                        item.expand();
                    }
                });

                item.stores.controls.toggle.on('click', item.stores.show);
                item.stores.controls.close.on('click', item.stores.hide);

                item.price.extended.toggle.on('click', item.price.extended.show);
                item.price.extended.close.on('click', item.price.extended.hide);
            });

            <?php if ($arVisual['IMAGE']['SLIDER']) {

            $arSlider = [
                'items' => 1,
                'nav' => $arVisual['IMAGE']['NAV'],
                'dots' => $arVisual['IMAGE']['OVERLAY'],
                'dotsEach' => $arVisual['IMAGE']['OVERLAY'] ? 1 : false,
                'overlayNav' => $arVisual['IMAGE']['OVERLAY']
            ];

            ?>
            $(function () {
                var slider = $('.owl-carousel', root);
                var parameters = <?= JavaScript::toObject($arSlider) ?>

                    slider.owlCarousel({
                        'items': parameters.items,
                        'nav': parameters.nav,
                        'smartSpeed': 600,
                        'navText': [
                            '<i class="far fa-chevron-left"></i>',
                            '<i class="far fa-chevron-right"></i>'
                        ],
                        'dots': parameters.dots,
                        'dotsEach': parameters.dotsEach,
                        'overlayNav': parameters.overlayNav
                    });

                <?php if ($arVisual['IMAGE']['OVERLAY']) { ?>

                slider.dots = $('.owl-dots', slider);
                slider.dots.dot = slider.dots.find('[role="button"]');
                slider.dots.dot.active = slider.dots.dot.filter('.active');
                slider.dots.addClass('intec-grid');
                slider.dots.dot.addClass('intec-grid-item');
                slider.dots.dot.active.find('span').addClass('intec-cl-background');

                slider.on('changed.owl.carousel', function() {
                    slider.dots.dot = $('[role="button"]' , this);
                    slider.dots.dot.find('span').removeClass('intec-cl-background');
                    slider.dots.dot.filter('.active').find('span').addClass('intec-cl-background');
                });

                <?php } ?>
            });
            <?php } ?>
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
            'container' => $sTemplateContainer,
            'columnCount' => $arVisual['COLUMNS']['DESKTOP'],
            'giftShowed' => $bGiftShowed
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

        function giftAdaptation (catalogItems, container, count = 4) {
            var giftBlock = $('[data-role="gift.section"]', container);

            if (_.isNil(catalogItems) || _.isNil(giftBlock) || _.isNil(container))
                return false;

            var containerWidth = container.width();
            var lastItem = catalogItems[count - 1];
            var itemsWidth = 0;
            var counter = 0;

            catalogItems.each(function () {
                itemsWidth = itemsWidth + $(this).outerWidth(true);

                if (itemsWidth > containerWidth) {
                    lastItem = $(catalogItems[counter - 1]);
                    return false;
                }

                counter++;
            });

            if (_.isNil(lastItem))
                return false;

            $(lastItem).after(giftBlock);
        }

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
    }, {
        'name': '[Component] bitrix:catalog.section (catalog.tile.4)',
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