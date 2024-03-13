<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var slider = $('[data-role="slider"]', data.nodes);
        var scroll = $('[data-role="scroll"]', data.nodes);

        if (slider.length > 0) {
            slider.nav = $('[data-role="slider.navigation"]', data.nodes);
            slider.navLeft = $('[data-role="slider.navigation.left"]', slider.nav);
            slider.navRight = $('[data-role="slider.navigation.right"]', slider.nav);
            slider.counter = $('[data-role="slider.navigation.counter"]', data.nodes);

            slider.updateNavDesktop = function (event) {
                if (event.item.count === event.page.size) {
                    slider.nav.css('display', '');

                    return;
                } else {
                    slider.nav.css('display', 'block');
                }

                if (event.item.index + 1 === 1)
                    slider.navLeft.addClass('disabled');
                else
                    slider.navLeft.removeClass('disabled');

                if (event.item.index + 1 === event.item.count - 1)
                    slider.navRight.addClass('disabled');
                else
                    slider.navRight.removeClass('disabled');
            };
            slider.updateNavMobile = function (event) {
                if (event.item.count === event.page.size) {
                    slider.nav.css('display', '');

                    return;
                } else {
                    slider.nav.css('display', 'block');
                }

                if (event.item.index === 0)
                    slider.navLeft.addClass('disabled');
                else
                    slider.navLeft.removeClass('disabled');

                if (event.item.index === event.item.count - 1)
                    slider.navRight.addClass('disabled');
                else
                    slider.navRight.removeClass('disabled');

                slider.counter.html(event.item.index + 1);
            };

            slider.owlCarousel({
                'margin': 30,
                'dots': false,
                'nav': false,
                'itemsAutoHeight': true,
                'itemsAutoHeightRefresh': true,
                'responsive': {
                    '0': {
                        'items': 1,
                        'margin': 8,
                        'onInitialized': slider.updateNavMobile,
                        'onTranslate': slider.updateNavMobile,
                        'onRefreshed': slider.updateNavMobile
                    },
                    '769': {
                        'items': 2,
                        'onInitialized': slider.updateNavDesktop,
                        'onTranslate': slider.updateNavDesktop,
                        'onRefreshed': slider.updateNavDesktop
                    }
                }
            });

            slider.navLeft.on('click', function () {
                slider.trigger('prev.owl.carousel');
            });
            slider.navRight.on('click', function () {
                slider.trigger('next.owl.carousel');
            });
        }

        if (scroll.length > 0)
            scroll.scrollbar({
                'disableBodyScroll': true
            });
    }, {
        'name': '[Component] intec.universe:main.widget (products.reviews.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
