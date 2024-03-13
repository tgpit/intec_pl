<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var node = {
            'consent': $('[data-role="form.consent"]', data.nodes),
            'submit': $('[data-role="form.submit"]', data.nodes),
            'rating': $('[data-role="form.rating"]', data.nodes)
        };

        if (node.consent.length > 0) {
            var consent = function () {
                if (node.consent.prop('checked'))
                    node.submit.prop('disabled', false);
                else
                    node.submit.prop('disabled', true);
            };

            node.consent.on('change', consent);

            consent();
        }

        if (node.rating.length > 0) {
            var rating = {
                'nodes': {
                    'input': $('[data-role="rating.input"]', node.rating),
                    'items': $('[data-role="rating.item"]', node.rating),
                    'information': $('[data-role="rating.information"]', node.rating),
                    'caption': $('[data-role="rating.caption"]', node.rating)
                },
                'methods': {}
            };

            rating.methods.initialize = function () {
                var value = rating.nodes.input.attr('value');

                if (value)
                    rating.nodes.items.filter('[data-value="' + value + '"]').trigger('click');
                else
                    rating.nodes.items.eq(rating.nodes.items.length - 1).trigger('click');
            };

            rating.methods.activate = function (item, attribute) {
                var index = item.attr('data-index');

                rating.nodes.items.attr(attribute, false);

                item.attr(attribute, true);

                rating.nodes.items.filter(function (key, element) {
                    var current = $(element);
                    var currentIndex = current.attr('data-index');

                    if (currentIndex < index)
                        current.attr(attribute, true);
                    else if (currentIndex > index)
                        current.attr(attribute, false);
                });
            };

            rating.nodes.items.each(function () {
                var item = $(this);

                item.on('click', function () {
                    rating.methods.activate(item, 'data-active');
                    rating.nodes.input.attr('value', item.attr('data-value'));
                    rating.nodes.caption.html(item.attr('title'));

                    if (rating.nodes.information.attr('data-active') !== 'true')
                        rating.nodes.information.attr('data-active', true);
                });

                item.on('mouseenter', function () {
                    rating.methods.activate(item, 'data-hover');
                });

                item.on('mouseleave', function () {
                    rating.nodes.items.attr('data-hover', false);
                });
            });

            rating.methods.initialize();
        }
    }, {
        'name': '[Component] intec.universe:reviews (popup.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>