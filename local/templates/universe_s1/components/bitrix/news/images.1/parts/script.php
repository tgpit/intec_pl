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

        var menuItems = $('[data-role="menu.item"]', root);
        var menuLinks = $('[data-role="menu.item.link"]', root);
        var menuQuantity = $('[data-role="menu.item.quantity"]', root);

        var mobile = {
            'opener': $('[data-role="mobile.menu.opener"]', root),
            'menu': $('[data-role="mobile.menu.items.list"]', root),
            'close': true,
            'openHeight': 0
        };

        mobile.menu.css('top', mobile.opener.position().top + 48);

        $('[data-role="menu.item"]', root).each(function () {
            mobile.openHeight = mobile.openHeight + $(this).outerHeight(true) + 2;

            if (mobile.openHeight >= 300) {
                mobile.openHeight = mobile.openHeight - ($(this).outerHeight(true) + 2);
                return false;
            }
        });

        function changeStatus (set) {
            if (set) {
                mobile.opener.attr('data-status', 'close');
                mobile.close = true;
            } else {
                mobile.opener.attr('data-status', 'open');
                mobile.close = false;
            }
        }

        mobile.opener.on('click', function () {
            if (mobile.close) {
                mobile.menu.stop().animate({
                    'opacity': 1,
                    'height': mobile.openHeight
                }, 250, function () {
                    setTimeout(function () {
                        $('[data-role="scrollbar"]', root).scrollbar();
                    }, 200);
                });

                changeStatus(false);
            } else {
                mobile.menu.stop().animate({
                    'opacity': 0,
                    'height': 0
                }, 250, function () {
                    setTimeout(function () {
                        $('[data-role="scrollbar"]', root).scrollbar('destroy');
                    }, 200);
                });

                changeStatus(true);
            }
        });

        $(window).on('resize', function () {
            <?php if ($arVisual['MENU']['POSITION'] === 'top') { ?>
                if ($(window).width() > 768) {
                    $(mobile.menu).removeAttr('style');
                    $('[data-role="scrollbar"]', root).scrollbar();
                    changeStatus(true);
                } else if (mobile.close) {
                    $('[data-role="scrollbar"]', root).scrollbar('destroy');
                }
            <?php } elseif ($arVisual['MENU']['POSITION'] === 'left') { ?>
                if ($(window).width() > 768) {
                    $(mobile.menu).removeAttr('style');
                    changeStatus(true);
                }
                if ($(window).width() > 768 && $(window).width() < 1024) {
                    $('[data-role="scrollbar"]', root).scrollbar();
                } else if (mobile.close) {
                    $('[data-role="scrollbar"]', root).scrollbar('destroy');
                }
            <?php } ?>

            mobile.menu.css('top', mobile.opener.position().top + 48);
        });

        <?php if ($arVisual['MENU']['POSITION'] === 'top') { ?>
            if ($(window).width() > 768) {
                $('[data-role="scrollbar"]', root).scrollbar();
            }
        <?php } elseif ($arVisual['MENU']['POSITION'] === 'left') { ?>
            if ($(window).width() > 768 && $(window).width() <= 1024) {
                $('[data-role="scrollbar"]', root).scrollbar();
            }
        <?php } ?>

        menuLinks.on('click', function () {
            var item = $('[data-role="menu.item"]', this);
            var quantity = $('[data-role="menu.item.quantity"]', this);

            menuLinks.attr('data-active', false);
            menuItems.attr('data-active', false);
            menuItems.removeClass('intec-cl-background');
            menuQuantity.removeClass('intec-cl-background-light');

            $(this).attr('data-active', true);
            item.attr('data-active', true);
            item.addClass('intec-cl-background');
            quantity.addClass('intec-cl-background-light');
        });

        menuLinks.hover(function () {
            $('[data-role="menu.item.quantity"]', this).addClass('intec-cl-background-light');
        }, function () {
            if (!$(this).data('active')) {
                $('[data-role="menu.item.quantity"]', this).removeClass('intec-cl-background-light');
            }
        });

    }, {
        'name': '[Component] bitrix:news (images.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>