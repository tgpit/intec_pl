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
        var itemsAnimated = $('[data-role="item"][data-description="true"]', data.nodes);
        var defineHeight = function () {
            itemsAnimated.each(function () {
                var container = $('[data-role="container"]', this);
                var itemHeaderHeight = $('[data-role="item.header"]', this).outerHeight(true);
                var contentHeight = $('[data-role="content"]', this).outerHeight();
                var margin = contentHeight - itemHeaderHeight;

                container.css('margin-top', margin);
            });

            $('[data-role="items"]', data.nodes).attr('data-status', 'loaded');
        };
        
        defineHeight();

        $(window).on('resize', function () {
            defineHeight();
        });

        itemsAnimated.on('mouseenter', function () {
            var container = $('[data-role="container"]', this);
            var itemHeaderHeight = $('[data-role="item.header"]', this).outerHeight(true);
            var itemDescriptionHeight = $('[data-role="description"]', this).outerHeight(true);
            var contentHeight = $('[data-role="content"]', this).outerHeight();

            var openMargin = 0;

            if (contentHeight > itemHeaderHeight + itemDescriptionHeight) {
                openMargin = contentHeight - (itemHeaderHeight + itemDescriptionHeight);
            }

            container.stop().animate({
                'margin-top': openMargin
            }, {
                'duration': 400,
                'complete': function(){

                    if (contentHeight < itemHeaderHeight + itemDescriptionHeight) {
                        $(this).scrollbar();
                    }
                }
            });
        });

        itemsAnimated.on('mouseleave', function () {
            var container = $('[data-role="container"]', this);
            var itemHeaderHeight = $('[data-role="item.header"]', this).outerHeight(true);
            var contentHeight = $('[data-role="content"]', this).outerHeight();
            var margin = contentHeight - itemHeaderHeight;

            container.stop().animate({
                'margin-top': margin
            }, {
                'duration': 400,
                'start': function(){
                    $(this).scrollbar('destroy').scrollTop(0);
                }
            });
        });
    }, {
        'name': '[Component] intec.universe:main.sections (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>