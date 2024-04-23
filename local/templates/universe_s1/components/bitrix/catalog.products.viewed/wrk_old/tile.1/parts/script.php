<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
?>

<script type="text/javascript">
    template.load(function () {
        var $ = this.getLibrary('$');
        var root = arguments[0].nodes;
        var data = <?= JavaScript::toObject([
            'columns' => $arVisual['COLUMNS'],
            'navigation' => $arVisual['SLIDER']['NAVIGATION'],
            'navText' => [
                FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
                FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
            ]
        ]) ?>;
        var handler = function () {
            var items = root.find('.owl-stage');
            items.children('.owl-item').css('visibility', 'collapse');
            items.children('.owl-item.active').css('visibility', '');
        };
        var slider = $('[data-role="slider"]', root);
        var responsive = {
            0: {'items': 1},
            450: {'items': 2},
            600: {'items': 3,'margin': 16},
            820: {'items': 4}
        };

        if (data.columns > 4)
            responsive[1100] = {'items': 5};

        responsive[1200] = {'items': data.columns};

        slider.owlCarousel({
            'center': false,
            'loop': false,
            'nav': data.navigation,
            'margin': 32,
            'stagePadding': 1,
            'navText': data.navText,
            'navClass': [
                'intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover',
                'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover'
            ],
            'navContainerClass': 'owl-nav intec-ui intec-ui-control-navigation',
            'dots': false,
            'responsive': responsive,
            'onResized': handler,
            'onRefreshed': handler,
            'onInitialized': handler,
            'onTranslated': handler
        });
    }, {
        'name': '[Component] bitrix:catalog.products.viewed (tile.1) [Fix composite]',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy',
            'options': {
                'await': [
                    'composite'
                ]
            }
        }
    });
</script>

