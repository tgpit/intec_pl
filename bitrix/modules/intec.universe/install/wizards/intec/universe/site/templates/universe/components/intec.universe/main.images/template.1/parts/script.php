<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var items = $('[data-role="collections"]', data.nodes);

        if (!items.length > 0)
            return;

        items.each(function () {
            var self = $(this);
            var handler = function () {
                var items = self.find('.owl-stage:first');

                items.children('.owl-item').css('visibility', 'collapse');
                items.children('.owl-item.active').css('visibility', '');
            };

            self.slider = $('[data-role="collections.slider"]', self);
            self.scroll = $('[data-role="collections.scroll"]', self);
            self.information = $('[data-role="collections.information"]', self);
            self.infoToggle = $('[data-role="information.toggle"]', self);

            if (self.slider.length > 0) {
                self.slider.owlCarousel({
                    'items': 1,
                    'nav': true,
                    'navClass': [
                        'widget-navigation-button widget-navigation-left intec-ui-picture intec-cl-background-hover intec-cl-border-hover',
                        'widget-navigation-button widget-navigation-right intec-ui-picture intec-cl-background-hover intec-cl-border-hover'
                    ],
                    'navText': [
                        <?= JavaScript::toObject($arSvg['NAVIGATION']['LEFT']) ?>,
                        <?= JavaScript::toObject($arSvg['NAVIGATION']['RIGHT']) ?>
                    ],
                    'dots': false,
                    'onResized': handler,
                    'onRefreshed': handler,
                    'onInitialized': handler,
                    'onTranslated': handler
                });
            }

            if (window.innerWidth > 768) {
                self.information.scrollbar({
                    'disableBodyScroll': true
                });
            }

            self.scroll.scrollbar({
                'disableBodyScroll': true
            });

            self.infoToggle.on('click', function () {
                self.information.slideToggle();

                if ($(this)[0].dataset.state === 'show') {
                    $(this)[0].dataset.state = 'hide';
                } else if ($(this)[0].dataset.state === 'hide') {
                    $(this)[0].dataset.state = 'show';
                }
            });
        });
    }, {
        'name': '[Component] intec.universe:main.images (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>