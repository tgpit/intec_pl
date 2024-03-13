<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var sections = $('[data-role="section"]', root);

        sections.each(function() {
            var section = $(this);
            var items = $('[data-role="item"]', section);
            var active = null;
            var duration = 300;

            items.each(function() {
                var self = this;
                var item = $(this);
                var toggle = item.find('[data-action=toggle]');

                toggle.on('click', function () {
                    if (active === self) {
                        close(self);
                        active = null;
                    } else {
                        open(self);
                    }
                });
            });

            var open = function (item) {
                if (active === item)
                    return;

                var block;
                var height;

                close(active);
                active = item;

                item = $(item);
                item.addClass('active');
                block = item.find('[data-role="item.description"]');
                height = block.css({
                    'display': 'block',
                    'height': 'auto'
                }).height();
                block.css({'height': 0}).stop().animate({'height': height + 'px'}, duration, function () {
                    block.css('height', 'auto');
                });
            };

            var close = function (item) {
                var block;

                item = $(item);
                item.removeClass('active');
                block = item.find('[data-role="item.description"]');
                block.stop().animate({'height': 0}, duration, function () {
                    block.css({
                        'display': 'none',
                        'height': 'auto'
                    });
                });
            };
        });
    }, {
        'name': '[Component] bitrix:news.list (vacancies.list.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
