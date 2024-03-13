<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

$bShowSeconds = $arVisual['BLOCKS']['SECONDS'];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var desktopMenu = {};

        desktopMenu.menu = $('[data-role="services-menu"]', data.nodes);

        if (desktopMenu.menu.length) {
            desktopMenu.items = $('[data-role="menu-item"]', desktopMenu.menu);
        }

        var mobileMenu = {
            menu: null,
            items: null
        };

        var hasMobileMenu = desktopMenu.menu.attr('data-mobile-column') === 'true';
        var contentItems = $('[data-role="section"]', data.nodes);
        var menuId = '';

        var firstMenuItemId = desktopMenu.items.first().attr('data-menu-id');

        if (hasMobileMenu) {
            mobileMenu.menu = $('[data-role="services-menu-mobile"]', data.nodes);
            mobileMenu.items = $('[data-role="menu-item"]', mobileMenu.menu);

            mobileMenu.items.on('click', function(){
                menuId = $(this).attr('data-menu-id');
                updateAllClass(menuId);
            });
        }

        desktopMenu.items.on('click', function(){
            menuId = $(this).attr('data-menu-id');
            updateAllClass(menuId);
        });

        updateAllClass(firstMenuItemId);

        desktopMenu.menu.owlCarousel({
            dots: false,
            loop: false,
            autoWidth:true
        });

        function updateAllClass(blockId) {
            if (hasMobileMenu) {
                var mobileMenuItem =  $('[data-menu-id=' + blockId + ']', mobileMenu.menu);
                mobileMenu.items.removeClass('intec-cl-border intec-cl-text');
                mobileMenuItem.addClass('intec-cl-border intec-cl-text');
            }

            var desktopMenuItem =  $('[data-menu-id=' + blockId + ']', desktopMenu.menu);
            desktopMenu.items.removeClass('intec-cl-border intec-cl-text');
            desktopMenuItem.addClass('intec-cl-border intec-cl-text');

            var contentItem = $('[data-content-id=' + blockId + ']', data.nodes);
            contentItems.removeClass('active');
            contentItem.addClass('active');
        }
    }, {
        'name': '[Component] intec.universe:main.services (template.24)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>