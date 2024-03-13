<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
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
 */

$bBase = false;

if (Loader::includeModule('sale') && Loader::includeModule('catalog'))
    $bBase = true;

if ($bBase)
    CJSCore::Init(array('currency'));

$arSliderParams = [
    'items' => 1,
    'nav' => true,
    'dots' => true,
    'navContainer' => '[data-role="container-nav"]',
    'dotsContainer' => '[data-role="container-dots"]',
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../../../../svg/navigation.arrow.left.svg'),
        FileHelper::getFileData(__DIR__.'/../../../../svg/navigation.arrow.right.svg')
    ],
    'loop' => $arParams['SLIDER_LOOP_USE'] === 'Y'
];

if ($arVisual['BUTTONS']['NAVIGATION']['VIEW'] === 'border') {
    $arSliderParams ['navClass'] = [
        'nav-prev intec-ui-picture intec-cl-background-hover intec-cl-border-hover',
        'nav-next intec-ui-picture intec-cl-background-hover intec-cl-border-hover'
    ];
} else {
    $arSliderParams ['navClass'] = [
        'nav-prev intec-ui-picture intec-cl-background-hover',
        'nav-next intec-ui-picture intec-cl-background-hover'
    ];
}

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var _ = app.getLibrary('_');
        var quickViewShow;
        var quickItemsId = [];
        var quickItems = Object.create(null);

        var root = data.nodes;

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

        $(function () {
            var properties = root.data('properties');
            var items;
            var component;
            var order;
            var directoryProperty = '<?= $arParams['OFFERS_PROPERTY_PICTURE_DIRECTORY'] ?>';

            if (!!directoryProperty)
                directoryProperty = 'P_' + directoryProperty;

            var settings = <?= JavaScript::toObject($arSliderParams) ?>;
            var productsSlider = $('[data-role="items"]', root);

            var setProductDayTimers = function () {
                var productDayTimers = $('[data-role="product.day.timer"]', productsSlider);
                var productDayTimerBlock = $('[data-role="product.day.timer.block"]', root);
                var content;

                if (productDayTimers.length === 1) {
                    var productDayTimer = $('[data-role="product.day.timer"]', productsSlider);
                    content = productDayTimer.html();

                    productDayTimerBlock.html(content);
                }

                if (productDayTimers.length > 0) {
                    var activeSliderItem = $('.widget-product-day-owl-item.active', productsSlider);
                    var activeProductDayTimer = $('[data-role="product.day.timer"]', activeSliderItem);
                    content = activeProductDayTimer.html();

                    productDayTimerBlock.html(content);
                }
            };

            var carouselHandler = function () {
                var dots = $(settings.dotsContainer + ' button', root);

                if (settings.dots) {
                    dots.removeClass('intec-cl-background intec-cl-border')
                        .filter('.active')
                        .addClass('intec-cl-background intec-cl-border');
                }

                setProductDayTimers();

                productsSlider.find('.owl-item').css('height', '100%');

                productsSlider.find('.owl-item').height(productsSlider.height());

                if (document.documentElement.offsetWidth <= 768) {
                    setTimeout(function () {
                        $('[data-role="products.slider-content"]', root).css('padding-right', 0);
                    }, 100);
                }
            };

            var margin = 8;

            if (document.documentElement.offsetWidth > 768) {
                margin = 0;
            }

            <?php if ($bSlideUse) { ?>
                productsSlider.owlCarousel({
                    'items': settings.items,
                    'itemClass': 'widget-product-day-owl-item owl-item',
                    'nav': settings.nav,
                    'navContainer': $(settings.navContainer, root),
                    'navClass': settings.navClass,
                    'navText': settings.navText,
                    'dots': settings.dots,
                    'dotsContainer': $(settings.dotsContainer, root),
                    'loop': document.documentElement.offsetWidth > 768 ? settings.loop : false,
                    'onInitialized': carouselHandler,
                    'onTranslated': carouselHandler,
                    'margin': margin,
                    'responsive': {
                        0: {
                            'items': settings.items
                        },
                        570: {
                            'items': 2
                        },
                        769: {
                            'items': settings.items
                        }
                    }
                });

                $(window).on('resize', function () {
                    productsSlider.find('.owl-item').css('height', '100%');
                    productsSlider.find('.owl-item').height(productsSlider.height());
                });
            <?php } else { ?>
                setProductDayTimers();
            <?php } ?>

            setTimeout(function () {
                if (document.documentElement.offsetWidth < 768) {
                    $('[data-role="products.slider.content"]', root).css('padding-right', 0);
                }
            }, 200);

            <?php if ($arResult['FORM']['SHOW']) { ?>
                order = function (data) {
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
                        options.fields[<?= JavaScript::toObject($arResult['FORM']['PROPERTIES']['PRODUCT']) ?>] = data.name;
                    <?php } ?>

                    app.api.forms.show(options);

                    if (window.yandex && window.yandex.metrika) {
                        window.yandex.metrika.reachGoal('forms.open');
                        window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open') ?>);
                    }
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

                    item.timer = $('[data-role="timer-holder"]', item);
                    item.article = $('[data-role="article"]', item);
                    item.article.value = $('[data-role="article.value"]', item.article);
                    item.gallery = $('[data-role="gallery"]', item);
                    item.counter = $('[data-role="item.counter"]', item);
                    item.price = $('[data-role="item.price"]', item);
                    item.price.base = $('[data-role="item.price.base"]', item.price);
                    item.price.discount = $('[data-role="item.price.discount"]', item.price);
                    item.price.extended = $('[data-role="price.extended.popup.window"]', item.price);
                    item.price.extended.toggle = $('[data-role="price.extended.popup.toggle"]', item.price);
                    item.price.extended.close = $('[data-role="price.extended.popup.close"]', item.price);
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
                        var article = entity.article;
                        var price = null;

                        item.attr('data-available', entity.available ? 'true' : 'false');

                        if (article === null)
                            article = data.article;

                        item.article.attr('data-show', article === null ? 'false' : 'true');
                        item.article.value.text(article);

                        _.each(entity.prices, function (object) {
                            if (object.quantity.from === null || item.quantity.get() >= object.quantity.from)
                                price = object;
                        });

                        if (price !== null) {
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

                        item.find('[data-offer]').css({
                            'display': '',
                            'opacity': ''
                        });

                        if (entity !== data) {
                            item.find('[data-offer=' + entity.id + ']').css('display', 'block');
                            item.find('[data-offer="false"]').css('display', 'none');
                            item.find('[data-offer=' + entity.id + '][data-role="item.quantity"]').animate({'opacity': 1}, 500);

                            if (item.gallery.filter('[data-offer=' + entity.id + ']').length === 0)
                                item.gallery.filter('[data-offer="false"]').css('display', 'block');

                            $.each(entity.values, function (key, value) {
                                var property = item.find('[data-property=' + key + ']');
                                var selectedValue = property.find('[data-value=' + value + ']');
                                var selectedValueContainer = property.find('[data-role="item.property.value.selected"]');

                                var valueName = selectedValue.find('[data-role="item.property.value.name"]').html();

                                selectedValueContainer.html(valueName);
                            });

                            item.find('[data-role="item.property.value"]')
                                .removeClass('intec-cl-background intec-cl-border');
                            item.find('[data-role="item.property.value"][data-state="selected"]')
                                .addClass('intec-cl-background intec-cl-border');

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

                    item.quantity.on('change', function () {
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

                        item.properties.each(function () {
                            var self = $(this);
                            var showMoreOffersPropsButtons = $('[data-role="show.more.offers.props"]', self);
                            var hiddenOfferProps = $('[data-hidden-more="true"]', self);

                            showMoreOffersPropsButtons.on('click', function () {
                                var button = $(this);

                                hiddenOfferProps.each(function () {
                                   var prop = $(this);

                                   prop.attr('data-prop-visibility', 'true');

                                   button.fadeOut();
                                });

                                window.dispatchEvent(new Event('resize'));

                                <?php if ($bSlideUse) { ?>
                                    productsSlider.trigger('refresh.owl.carousel');
                                <?php } ?>
                            });
                        });

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

                                        if (self.data('property') === directoryProperty && offer.values[directoryProperty] === value.data('value') && !!offer.img) {
                                            value.find('[data-role="item.property.value.image"]').attr('style', 'background-image: url(' + offer.img + ')');
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

                    item.price.extended.show = function () {
                        item.price.extended.toggleClass('active');
                    };

                    item.price.extended.hide = function () {
                        item.price.extended.removeClass('active');
                    };

                    item.on('mouseleave', item.price.extended.hide);

                    item.offers.on('change', item.price.extended.hide);

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
        'name': '[Component] intec.universe:main.widget (products.small.1) > bitrix:catalog.section (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>