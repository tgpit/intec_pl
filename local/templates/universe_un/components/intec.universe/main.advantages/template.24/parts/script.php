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
        var rows = <?= JavaScript::toObject($arVisual['HIDING']['VISIBLE']) ?>;
        var button = $('[data-role="button"]', root);
        var items = $('[data-role="items"] [data-role="item"]', root);
        var state = true;

        root.getColumns = function () {
            return Math.round(items.container.width() / items.outerWidth());
        };

        root.getVisible = function () {
            return rows * root.getColumns();
        };

        root.refresh = function () {
            root.attr('data-hiding', root.getVisible() <= items.length ? 'true' : 'false');
            root.attr('data-collapsed', state ? 'true' : 'false');
            items.refresh(state);
        };

        items.container = $('[data-role="items"]', root);
        items.show = function (animate) {
            if (state)
                return;

            var container = items.container.stop();

            state = !state;

            if (animate) {
                var height = {
                    'current': container.height(),
                    'target': 0
                };

                root.refresh();
                height.target = container.height();
                container.css({
                    'height': height.current
                }).animate({
                    'height': height.target
                }, 500, function () {
                    container.css({'height': ''});
                });
            } else {
                root.refresh();
            }
        };

        items.toggle = function (animate) {
            if (state) {
                items.hide(animate);
            } else {
                items.show(animate);
            }
        };

        items.refresh = function (state) {
            var count = root.getVisible();
            var counter = 0;

            items.container.attr('data-collapsed', state ? 'true' : 'false');
            items.each(function () {
                var item = $(this);

                item.attr('data-hidden', 'false');
                counter++;

                if (!state && (counter > count))
                    item.attr('data-hidden', 'true');
            })
        };

        items.hide = function (animate) {
            if (!state)
                return;

            var container = items.container.stop();

            state = !state;

            if (animate) {
                var height = {
                    'current': container.height(),
                    'target': 0
                };

                items.refresh(false);
                height.target = container.height();
                items.refresh(true);
                container.css({
                    'height': height.current
                }).animate({
                    'height': height.target
                }, 500, function () {
                    container.css({'height': ''});
                    root.refresh();
                });
            } else {
                root.refresh();
            }
        };

        button.on('click', function () {
            items.toggle(true);
        });

        $(window).on('resize', root.refresh);
        items.toggle(false);
    }, {
        'name': '[Component] intec.universe:main.advantages (template.24)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    })
</script>
