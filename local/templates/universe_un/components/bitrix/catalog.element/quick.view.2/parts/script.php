<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

/**
 * @var string $sTemplateId
 */

$arEcommerce = [
    'name' => $arResult['NAME'],
    'id' => $arResult['ID'],
    'category' => $arResult['SECTION']['NAME']
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var properties = root.data('properties');
        var dataItem = root.data('data');
        var entity = dataItem;
        var offerVariableSelect = '<?= $arParams['OFFERS_VARIABLE_SELECT'] ?>';

        root.css('opacity', '');

        app.ecommerce.sendData({
            'ecommerce': {
                'detail': {
                    'products': <?= JavaScript::toObject([$arEcommerce]) ?>
                }
            }
        });

        app.api.basket.update();

        root.offers = new app.classes.catalog.Offers({
            'properties': properties,
            'list': dataItem.offers
        });

        root.weight = $('[data-role="weight"]', root);
        root.gallery = $('[data-role="gallery"]', root);
        root.gallery.previews = $('[data-role="gallery.previews"]', root.gallery);
        root.gallery.previews.items = $('[data-role="gallery.preview"]', root.gallery.previews);
        root.article = $('[data-role="article"]', root);
        root.article.value = $('[data-role="article.value"]', root.article);
        root.counter = $('[data-role="counter"]', root);
        root.timer = $('[data-role="timer"]', root);
        root.price = $('[data-role="price"]', root);
        root.price.base = $('[data-role="price.base"]', root.price);
        root.price.discount = $('[data-role="price.discount"]', root.price);
        root.price.percent = $('[data-role="price.percent"]', root.price);
        root.quantity = app.ui.createControl('numeric', {
            'node': root.counter,
            'bounds': {
                'minimum': entity.quantity.ratio,
                'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
            },
            'step': entity.quantity.ratio
        });
        root.links = $('[data-role="offer.link"]', root);
        root.scroll = $('[data-role="scroll"]', root);
        root.scroll.scrollbar();
        root.scrollRight = $('[data-role="scroll.right"]', root);
        root.scrollRight.scrollbar();

        root.update = function () {
            var article = entity.article;
            var price = null;

            root.attr('data-available', entity.available ? 'true' : 'false');

            if (article == null)
                article = dataItem.article;

            root.article.attr('data-show', article == null ? 'false' : 'true');
            root.article.value.text(article);

            _.each(entity.prices, function (object) {
                if (object.quantity.from === null || root.quantity.get() >= object.quantity.from)
                    price = object;
            });

            if (price !== null) {
                root.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                root.price.base.html(price.base.display);
                <?php if ($arVisual['MEASURE']['SHOW']) { ?>
                    root.price.discount.html(price.discount.display+"/"+entity.quantity.measure);
                <?php } else { ?>
                    root.price.discount.html(price.discount.display);
                <?php } ?>
                root.price.percent.text('-' + price.discount.percent + '%');
            } else {
                root.price.attr('data-discount', 'false');
                root.price.base.html(null);
                root.price.discount.html(null);
                root.price.percent.text(null);
            }

            root.price.attr('data-show', price !== null ? 'true' : 'false');
            root.quantity.configure({
                'bounds': {
                    'minimum': entity.quantity.ratio,
                    'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                },
                'step': entity.quantity.ratio
            });

            root.find('[data-offer]').css({
                'display': '',
                'opacity': ''
            });

            if (entity.quantity.weight)
                root.weight.text(entity.quantity.weight);
            else
                root.weight.text('');

            if (entity !== dataItem) {
                root.find('[data-offer=' + entity.id + ']').css('display', 'block');
                root.find('[data-offer="false"]').css('display', 'none');

                var quantity = root.find('[data-offer=' + entity.id + '][data-role="item.quantity"]');

                if (quantity.attr('data-active') == 'false') {
                    quantity.animate({'opacity': 1}, 500).attr('data-active', 'true');
                }

                if (root.gallery.filter('[data-offer=' + entity.id + ']').length === 0)
                    root.gallery.filter('[data-offer="false"]').css('display', '');
            }

            root.find('[data-basket-id]')
                .data('basketQuantity', root.quantity.get())
                .attr('data-basket-quantity', root.quantity.get());
        };

        root.update();

        root.quantity.on('change', function () {
            root.update();

            var alertElem = $('[data-role="max-message"]', root);
            var closeIcon = $('[data-role="max-message-close"]', alertElem);

            if (!root.offers.isEmpty()) {
                var currentOffer = root.offers.getCurrent();

                if (currentOffer.available && !currentOffer.quantity.zero && currentOffer.quantity.trace && currentOffer.quantity.value > 0) {
                    if (root.quantity.get() >= entity.quantity.value) {
                        $('[data-role="max-message"]', root).fadeIn();
                    }
                }
            } else {
                if (dataItem.available && !dataItem.quantity.zero && dataItem.quantity.trace && dataItem.quantity.value > 0) {
                    if (root.quantity.get() >= entity.quantity.value) {
                        $('[data-role="max-message"]', root).fadeIn();
                    }
                }
            }

            closeIcon.on('click', function (event) {
                event.stopPropagation();
                $('[data-role="max-message"]', root).fadeOut();
            });

            $('[data-action="decrement"]', root).on('click', function () {
                $('[data-role="max-message"]', root).fadeOut();
            });
        });

        if (!root.offers.isEmpty()) {
            root.properties = $('[data-role="property"]', root);
            root.properties.values = $('[data-role="property.value"]', root.properties);
            root.properties.each(function () {
                var self = $(this);
                var property = self.data('property');
                var values = self.find(root.properties.values);

                values.each(function () {
                    var self = $(this);
                    var value = self.data('value');

                    self.on('click', function () {
                        root.offers.setCurrentByValue(property, value);
                    });
                });
            });

            root.offers.on('change', function (event, offer, values) {
                entity = offer;

                $('[data-role="max-message"]', root).fadeOut();

                if (!!offerVariableSelect && root.links.length !== 0) {
                    var currentUrl = new URL(root.links[0].href);
                    var getParamsUrl = currentUrl.searchParams;

                    getParamsUrl.set(offerVariableSelect, offer.id);
                    currentUrl.searchParams = getParamsUrl;

                    root.links.each(function () {
                        $(this)[0].setAttribute('href', currentUrl.pathname + currentUrl.search);
                    });
                }

                _.each(values, function (values, state) {
                    _.each(values, function (values, property) {
                        property = root.properties.filter('[data-property="' + property + '"]');

                        _.each(values, function (value) {
                            value = property.find(root.properties.values).filter('[data-value="' + value + '"]');
                            value.attr('data-state', state);
                        });
                    });
                });

                root.update();

                <?php if ($arVisual['TIMER']['SHOW']) { ?>
                    timerUpdate(root.timer, offer.id);
                <?php } ?>
            });

            var offerCurrent;

            _.each(root.offers.getList(), function (offer) {
                if (!offerCurrent || Number(offerCurrent.sort) > Number(offer.sort))
                    offerCurrent = offer;
            });

            root.offers.setCurrentById(offerCurrent.id);
        }

        root.gallery.each(function () {
            var gallery = $(this);
            var pictures;
            var previews;

            pictures = $('[data-role="gallery.pictures"]', gallery);
            pictures.items = $('[data-role="gallery.picture"]', pictures);
            previews = $('[data-role="gallery.previews"]', gallery);
            previews.items = $('[data-role="gallery.preview"]', previews);

            pictures.owlCarousel({
                'items': 1,
                'nav': false,
                'dots': false
            });

            previews.owlCarousel({
                'items': 5,
                'nav': false,
                'dots': false
            });

            previews.set = function (number) {
                var item = previews.items.eq(number);

                previews.items.attr('data-active', 'false').removeClass('intec-cl-border');
                item.attr('data-active', 'true').addClass('intec-cl-border');
            };

            previews.items.on('click', function () {
                var previewIndex = $(this).parent('.owl-item').index();

                pictures.items
                    .add(previews.items)
                    .trigger('to.owl.carousel', [previewIndex]);

                previews.set(previewIndex);

            });

            pictures.on('changed.owl.carousel', function (event) {
                previews.set(event.item.index);
            });

            previews.set(0);
        });

        function timerUpdate(timer, id){

            var timerParameters = <?= JavaScript::toObject($arTimerProperties) ?>;

            if (!!timerParameters) {
                timerParameters.parameters.ELEMENT_ID = id;
                timerParameters.parameters.RANDOMIZE_ID = 'Y';
                timerParameters.parameters.AJAX_MODE = 'N';

                app.api.components.get(timerParameters).then(function (content) {
                    timer.html(content);
                });
            }
        }
    }, {
        'name': '[Component] bitrix:catalog.element (quick.view.2)',
        'loader': {
            'name': 'default',
            'options': {
                'await': [
                    Promise.await(function () {
                        return $(<?= JavaScript::toObject('#'.$sTemplateId) ?>).length > 0;
                    }, 100)
                ]
            }
        }
    });
</script>
<?php unset($arEcommerce) ?>