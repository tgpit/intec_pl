<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var selectors = {
            'menu': '[data-role=links]',
            'item': '[data-role=item]',
            'items': '[data-role=items]',
            'more': '[data-role=more]'
        };
        var menu;
        var adapt;
        var menuMobile = root.find('[data-role="mobile-links"]');
        var menuMobileButton = menuMobile.find('[data-action="menu.open"]');

        if (root.is(selectors.menu)) {
            menu = root;
        } else {
            menu = root.find(selectors.menu).eq(0);
        }

        menu.getItemsWrappers = function (submenu) {
            if (!submenu) {
                return menu
                    .find(selectors.items);
            }

            if (menu.get(0) === submenu.get(0)) {
                submenu = menu;
            } else {
                submenu = menu
                    .find(submenu);
            }

            return submenu
                .find(selectors.items)
                .eq(0);
        };

        menu.getItems = function (submenu) {
            if (!submenu) {
                return menu
                    .find(selectors.item);
            }

            return menu
                .getItemsWrappers(submenu)
                .children(selectors.item);
        };

        menu.getMenu = function (item) {
            if (item)
                return menu
                    .find(item)
                    .find(selectors.menu)
                    .eq(0);

            return menu
                .find(selectors.menu);
        };

        menu.more = {};

        menu.more.getItem = function () {
            return menu
                .find(selectors.more);
        };

        menu.more.getMenu = function () {
            return menu.getMenu(menu.more.getItem());
        };

        menu.more.add = function (add) {
            var items;

            add = $(add);
            items = menu.getItems(menu.more.getMenu());
            add.each(function () {
                var self = $(this);
                var item = items.eq(self.index());

                self.hide();
                item.show();
            });
        };

        menu.more.remove = function (remove) {
            var items;

            remove = $(remove);
            items = menu.getItems(menu.more.getMenu());
            remove.each(function () {
                var self = $(this);
                var item = items.eq(self.index());

                self.show();
                item.hide();
            });
        };

        adapt = {};

        adapt.items = function () {
            var items = {};
            var width = {};
            var wrapper = menu.getItemsWrappers(menu);

            items.all = menu.getItems(menu);
            items.visible = $([]);
            items.hidden = $([]);

            items.all.hide();
            width.available = wrapper.width() - menu.more.getItem().show().width();
            items.all.show();
            width.total = 0;

            menu.more.remove(items.all);
            items.all.each(function () {
                var item = $(this);

                item.css({'width': 'auto'});
                width.total += item.width();

                if (width.total < width.available) {
                    items.visible = items.visible.add(item);
                } else {
                    items.hidden = items.hidden.add(item);
                }
            });

            if (items.hidden.length > 0) {
                menu.more.add(items.hidden);
            } else {
                menu.more.getItem().hide();
                width.available = wrapper.width();
            }

            var last = null;

            width.total = {
                'original': 0,
                'rounded': 0
            };

            items.visible.each(function () {
                width.total.original += $(this).width();
            }).each(function () {
                var item = $(this);
                var size = Math.floor((width.available / 100) * (item.width() / width.total.original) * 100);

                width.total.rounded += size;
                item.css('width', size + 'px');
                last = item;
            });

            if (last != null)
                last.css('width', last.width() + (width.available - width.total.rounded) + 'px');
        };

        root.on('update', function () {
            adapt.items();
            menu
                .find(selectors.items)
                .css('overflow', 'initial');
        });

        $(window).on('resize', function () {
            root.trigger('update');
        });

        root.trigger('update');

        menuMobileButton.on('click', function () {
            var content = menuMobile.find('[data-role="content"]');
            if ($(this)[0].dataset.state == 'true') {
                $(this)[0].dataset.state = 'false';
            } else {
                $(this)[0].dataset.state = 'true';
            }
            content.slideToggle(400);
        });

    }, {
        'name': '[Component] bitrix:sale.personal.section (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
