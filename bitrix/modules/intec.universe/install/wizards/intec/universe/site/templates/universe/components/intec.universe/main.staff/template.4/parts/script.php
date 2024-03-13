<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

$arSlider = [
    'items' => 1,
    'nav' => $arVisual['SLIDER']['NAV'],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'dots' => false
];


?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var gallery = $('[data-role="items"]', root);
        var dots = $('[data-role="dots"]', root);
        var navigation = $('[data-role="navigation"]', root);
        //var panel = $('[data-role="panel"]', root);

        gallery.items = $('[data-role="item"]', gallery);
        //panel.counter = $('[data-role="panel.counter"]', panel);
        //panel.quantity = api.controls.numeric({}, panel.counter);

        var settings = <?= JavaScript::toObject($arSlider) ?>;

        gallery.owlCarousel({
            'items': settings.items,
            'nav': settings.nav,
            'navContainer': navigation,
            'navText': settings.navText,
            'navClass': [
                'intec-ui-part-button-left intec-cl-background-light-hover intec-cl-border-light-hover',
                'intec-ui-part-button-right intec-cl-background-light-hover intec-cl-border-light-hover'
            ],
            'dots': settings.dots,
            'dotsContainer': dots,
            'dotClass': 'intec-ui-part-dot'
        });

        gallery.on('changed.owl.carousel', function (event) {
            //panel.current.set(event.item.index + 1);
        });

        //panel.current = $('[data-role="panel.current"]', panel);
        /*panel.current.set = function (number) {
            this.value = number;
            this.text(number + '/' + gallery.items.length);
        };
        panel.current.set(1);*/

    }, {
        'name': '[Component] intec.universe:main.staff (template.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
