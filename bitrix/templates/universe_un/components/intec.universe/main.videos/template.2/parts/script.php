<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var frame = $('[data-role="view"]', data.nodes);
        var container = $('[data-role="items"]', data.nodes);
        var items = $('[data-role="item"]', container);

        container.scrollbar();

        items.each(function () {
            var self = $(this);

            self.on('click', function () {
                var id = self.attr('data-id');

                items.attr('data-active', 'false');
                items.removeClass('intec-cl-text');

                self.attr('data-active', 'true');
                self.addClass('intec-cl-text');

                frame.attr('src', 'https://www.youtube.com/embed/' + id + '?autoplay=1');
            });
        });

        (function () {
            items.eq(0)
                .attr('data-active', 'true')
                .addClass('intec-cl-text');
        })();
    }, {
        'name': '[Component] intec.universe:main.videos (template.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>