<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var items = $('[data-role="photo.item"]', data.nodes);

        items.each(function () {
            var self = $(this);
            var nodes = {
                'loader': $('[data-role="photo.item.loader"]', self),
                'content': $('[data-role="photo.item.content"]', self),
                'slider': $('[data-role="photo.item.slider"]', self),
                'dots': $('[data-role="photo.item.navigation"]', self)
            };

            nodes.slider.owlCarousel({
                'items': 1,
                'nav': false,
                'dots': true,
                'dotsContainer': nodes.dots,
                'lazyLoad': true,
                'overlayNav': true,
                'onInitialized': function () {
                    $('button', nodes.dots).addClass('intec-grid-item');

                    nodes.content.attr('data-loaded', true);
                    nodes.loader.remove();
                }
            });
        });
    }, {
        'name': '[Component] bitrix:photo.sections.top (gallery.default.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>