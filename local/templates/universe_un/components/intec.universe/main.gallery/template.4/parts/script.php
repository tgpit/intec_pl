<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        root.gallery = $('[data-role="gallery"]', root);
        root.gallery.lightGallery({
            'selector': '[data-role="gallery.item"]',
            'exThumbImage': 'data-preview-src'
        });
        root.panel = $('[data-role="panel"]', root);
        root.panel.counter = $('[data-role="panel.counter"]', root.panel);
        root.panel.quantity = this.ui.createControl('numeric', {
            'node': root.panel.counter
        });

        root.gallery.each(function () {
            var gallery = $(this);
            var pictures;
            var panel;

            pictures = $('[data-role="gallery.items"]', gallery);
            pictures.items = $('[data-role="gallery.item"]', pictures);

            panel = $('[data-role="gallery.panel"]', gallery);
            panel.buttons = {
                'previous': $('[data-role="gallery.previous"]', panel),
                'next': $('[data-role="gallery.next"]', panel),
            };

            panel.current = $('[data-role="gallery.current"]', panel);
            panel.current.set = function (number) {
                this.value = number;
                this.text(number + '/' + pictures.items.length);
            };

            pictures.owlCarousel({
                'items': 1,
                'nav': false,
                'dots': false
            });

            pictures.on('changed.owl.carousel', function (event) {
                panel.current.set(event.item.index + 1);
            });

            panel.buttons.previous.on('click', function () {
                pictures.trigger('prev.owl.carousel');
            });

            panel.buttons.next.on('click', function () {
                pictures.trigger('next.owl.carousel');
            });

            panel.current.set(1);

        });
    }, {
        'name': '[Component] intec.universe:main.gallery (template.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
