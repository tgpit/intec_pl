<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var root = data.nodes;

        var gallery = $('[data-role="gallery"]', root);
        var preview = $('[data-role="gallery.preview"]', root);
        preview.list = $('[data-role="preview.list"]', preview);
        preview.items = $('[data-role="preview.item"]', preview);
        preview.prevButton = $('.news-detail-content-header-preview-prev-button', preview);
        preview.nextButton = $('.news-detail-content-header-preview-next-button', preview);
        var tabs = $('[data-role="tabs"]', root);
        tabs.items = $('[data-role="tabs.item"]', tabs);
        var orderButton = $('[data-role="order.button"]', root);
        var askButton = $('[data-role="ask.button"]', root);

        gallery.owlCarousel({
            'items': 1,
            'nav': false,
            'dots': false,
            'lazyLoad': true
        });

        preview.list.owlCarousel({
            'items': 4,
            'nav': true,
            'dots': false,
            'animateOut': 'slideOutUp',
            'animateIn': 'slideInUp'
        });

        gallery.lightGallery({
            'thumbnail':true,
            'share': false,
            'selector': '[data-lightGallery="true"]',
            'exThumbImage': 'data-exthumbimage'
        });

        if (document.documentElement.clientWidth <= 560) {
            tabs.addClass('owl-carousel');
            tabs.owlCarousel({
                'items': 2,
                'nav': true,
                'dots': false
            });
        }

        preview.set = function (number) {
            var activeSlide = preview.items.eq(number);

            preview.items.attr('data-active', 'false');
            activeSlide.attr('data-active', 'true');

            if (!activeSlide.closest('.owl-item').hasClass('active')) {
                var index = activeSlide.closest('.owl-item').index();

                preview.list.trigger('to.owl.carousel', [index]);
            }
        };

        preview.items.on('click', function () {
            var item = $(this);
            var owlItem = item.closest('.owl-item');
            var previewIndex = owlItem.index();

            gallery.trigger('to.owl.carousel', [previewIndex]);
            preview.set(previewIndex);
        });

        gallery.on('changed.owl.carousel', function (event) {
            preview.set(event.item.index);
        });

        orderButton.on('click', function() {
            app.api.forms.show(<?= JavaScript::toObject($arForms['ORDER']) ?>);

            if (window.yandex && window.yandex.metrika) {
                window.yandex.metrika.reachGoal('forms.open');
                window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForms['ORDER']['id'].'.open') ?>);
            }
        });

        askButton.on('click', function() {
            app.api.forms.show(<?= JavaScript::toObject($arForms['ASK']) ?>);

            if (window.yandex && window.yandex.metrika) {
                window.yandex.metrika.reachGoal('forms.open');
                window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForms['ASK']['id'].'.open') ?>);
            }
        });

    }, {
        'name': '[Component] bitrix:news.detail (projects.default.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>