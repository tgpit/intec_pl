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

        button = $('[data-role="item.button"]', root);

        //button.on('click', function () {
            /*app.api.basket.on('add', function () {
                location.reload();
            });*/
        //});

        /*var btnMore = $('[data-role="description.more"]', root);

        btnMore.on('click', function(){
            var self = $(this);
            var description = self.closest('[data-role="description"]');
            var text = $('[data-role="description.text"]', description);

            var expanded = description.attr('data-expanded');

            if (expanded == 'true') {
                description.attr('data-expanded', 'false');
            } else {
                description.attr('data-expanded', 'true');
            }

            text.slideToggle();
        });*/

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

    }, {
        'name': '[Component] bitrix:catalog.item (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
