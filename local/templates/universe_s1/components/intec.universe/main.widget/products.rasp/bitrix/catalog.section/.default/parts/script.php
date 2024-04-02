<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var string $sTemplateContainer
 * @var array $arVisual
 * @var array $arNavigation
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var _ = app.getLibrary('_');
        var $ = app.getLibrary('$');

        var root = data.nodes;

        $(function () {
            var properties = root.data('properties');
            var items;
            var order;
            var request;
            var quickViewShow;
            var quickItemsId = [];
            var quickItems = Object.create(null);
            var directoryProperty = '<?= $arParams['OFFERS_PROPERTY_PICTURE_DIRECTORY'] ?>';
            var offerVariableSelect = '<?= $arParams['OFFERS_VARIABLE_SELECT'] ?>';
            var lazyLoadUse = '<?= $arParams['LAZYLOAD_USE'] === 'Y' ? 'true' : 'false' ?>';

            if (!!directoryProperty)
                directoryProperty = 'P_' + directoryProperty;

            <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                quickViewShow = function (data, quickItemsId) {
                    app.api.components.show({
                        'component': 'bitrix:catalog.element',
                        'template': data.template,
                        'parameters': data.parameters,
                        'settings': {
                            'parameters': {
                                'className': 'popup-window-quick-view',
                                'width': null
                            }
                        }
                    }).then(function (popup) {
                        <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
                            var container = $(popup.contentContainer);

                            var indexItem = quickItemsId.indexOf(data.parameters.ELEMENT_ID);
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
            order = function (data) {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORMS']['ORDER']['ID'],
                    'template' => $arResult['FORMS']['ORDER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_WIDGET_PRODUCTS_1_FORM_ORDER_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORMS']['ORDER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORMS']['ORDER']['PROPERTIES']['PRODUCT']) ?>] = data.name;
                <?php } ?>

                app.api.forms.show(options);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORMS']['ORDER']['ID'].'.open') ?>);
            };
            <?php } ?>
            <?php if ($arResult['FORMS']['REQUEST']['SHOW']) { ?>
            request = function (data) {
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
                options.fields[<?= JavaScript::toObject($arResult['FORMS']['REQUEST']['PROPERTIES']['PRODUCT']) ?>] = data.name;
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

                items = $('[data-role="item"]', root);
                items.handled = handled;
                items.each(function () {
                    var item = $(this);
                    var data = item.data('data');
                    var entity = data;
                    var expanded = false;

                    quickItems[data.quickView.parameters.ELEMENT_ID] = data.quickView;
                    quickItemsId.push(data.quickView.parameters.ELEMENT_ID);

                    if (handled.indexOf(this) > -1)
                        return;

                    handled.push(this);

                    item.offers = new app.classes.catalog.Offers({
                        'properties': properties.length !== 0 ? properties : data.properties,
                        'list': data.offers
                    });

                    item.image = $('[data-role="image"]', item);
                    item.timer = $('[data-role="timer-holder"]', item);
                    item.price = $('[data-role="item.price"]', item);
                    item.price.base = $('[data-role="item.price.base"]', item.price);
                    item.price.discount = $('[data-role="item.price.discount"]', item.price);
                    item.order = $('[data-role="item.order"]', item);
                    item.request = $('[data-role="item.request"]', item);
                    item.links = $('[data-role="offer.link"]', item);
                    item.price.percent = $('[data-role="price.percent"]', item.price);
                    item.price.difference = $('[data-role="price.difference"]', item.price);

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
                            if (object.quantity.from === null || object.quantity.from <= 1)
                                price = object;
                        });

                        if (price !== null) {
                            item.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                            item.price.base.html(price.base.display);
                            item.price.discount.html(price.discount.display);
                            item.price.percent.text('-' + price.discount.percent + '%');
                            item.price.difference.html(price.discount.difference);
                        } else {
                            item.price.attr('data-discount', 'false');
                            item.price.base.html(null);
                            item.price.discount.html(null);
                            item.price.percent.text(null);
                            item.price.difference.html(null);
                        }

                        item.price.attr('data-show', price !== null ? 'true' : 'false');
                        item.find('[data-offer]').css({
                            'display': '',
                            'opacity': ''
                        });

                        if (entity !== data) {
                            item.find('[data-offer=' + entity.id + ']').css('display', 'block');
                            item.find('[data-offer="false"]').css('display', 'none');

                            if (item.image.filter('[data-offer=' + entity.id + ']').length === 0)
                                item.image.filter('[data-offer="false"]').css('display', 'block');
                        }

                        item.find('[data-basket-id]')
                            .data('basketQuantity', 1)
                            .attr('data-basket-quantity', 1);
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
                                    <?php if ($arVisual['TIMER']['SHOW']) { ?>
                                        timerUpdate(item.timer, entity.id);
                                    <?php } ?>
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

                            if (!!offerVariableSelect && item.links.length !== 0) {
                                var currentUrl = new URL(item.links[0].href);
                                var getParamsUrl = currentUrl.searchParams;

                                getParamsUrl.set(offerVariableSelect, offer.id);
                                currentUrl.searchParams = getParamsUrl;

                                item.links.each(function () {
                                    $(this)[0].setAttribute('href', currentUrl.pathname + currentUrl.search);
                                });
                            }

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
                            if (!offerCurrent || Number(offerCurrent.sort) > Number(offer.sort))
                                offerCurrent = offer;
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

                    item.expandUpdate = function () {
                        item.css('height', '');

                        setTimeout(function(){
                            var rectangle = item[0].getBoundingClientRect();
                            var height = rectangle.bottom - rectangle.top;
                            expanded = true;
                            item.css('height', height);
                            item.attr('data-expanded', 'true');
                        }, 100);
                    };

                    item.on('mouseenter', item.expand)
                        .on('mouseleave', item.narrow);

                    <?php if ($arVisual['TIMER']['SHOW']) { ?>
                        $('[data-role="item.property"]', item).on('click', item.expandUpdate);
                    <?php } ?>

                    $(window).on('resize', function () {
                        if (expanded) {
                            item.narrow();
                            item.expand();
                        }
                    });
                });

                <?php if ($arVisual['IMAGE']['SLIDER']) {

                    $arSlider = [
                        'items' => 1,
                        'nav' => !$arVisual['IMAGE']['NAV'],
                        'dots' => false
                    ];

                ?>
                    (function () {
                        var slider = $('.widget-item-image-slider', root);
                        var parameters = <?= JavaScript::toObject($arSlider) ?>

                        slider.owlCarousel({
                            'items': parameters.items,
                            'nav': parameters.nav,
                            'navText': [
                                '<i class="far fa-chevron-left"></i>',
                                '<i class="far fa-chevron-right"></i>'
                            ],
                            'dots': parameters.dots
                        });
                    })();
                <?php } ?>
            };

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
        });
    }, {
        'name': '[Component] intec.universe:main.widget (products.1) > bitrix:catalog.section (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>