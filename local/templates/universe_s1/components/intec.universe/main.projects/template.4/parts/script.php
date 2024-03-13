<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 * @var array $arForm
 */

$arSlider = [
    'use' => $arVisual['SLIDER']['USE'],
    'items' => $arVisual['COLUMNS'],
    'nav' => $arVisual['SLIDER']['NAV'],
    'navContainer' => '[data-role="slider.navigation"]',
    'navClass' => [
        'navigation-left intec-cl-background-hover intec-cl-border-hover',
        'navigation-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'responsive' => [
        '0' => [
            'items' => 1,
            'autoHeight'=> true,
            'itemsAutoHeight'=> false,
            'itemsAutoHeightRefresh'=> false
        ],
        '501' => [
            'items'=> 2,
            'autoHeight'=> true,
            'itemsAutoHeight'=> false,
            'itemsAutoHeightRefresh'=> false
        ],
        '550' => [
            'items'=> 2,
            'autoHeight'=> false,
            'itemsAutoHeight'=> true,
            'itemsAutoHeightRefresh'=> true
        ]
    ]
];


$arSlider['responsive']['1024'] = ['items' => $arSlider['items']];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var form = {
            'nodes': $('[data-role="form"]', data.nodes),
            'parameters': <?= JavaScript::toObject($arForm['PARAMETERS']) ?>
        };
        var slider = $('[data-role="slider"]', data.nodes);
        var sliderSettings = <?= JavaScript::toObject($arSlider) ?>;
        var navContainer = $(sliderSettings.navContainer, data.nodes);

        if (sliderSettings.use) {

            var adapt = function () {

                if ($(window).width() < 550) {
                    var item = $('.owl-item', slider);
                    item.css({'height': 'auto'});
                } else {
                    return false;
                }
            };

            slider.owlCarousel({
                'margin': 10,
                'autoHeight': false,
                'itemsAutoHeight': true,
                'itemsAutoHeightRefresh': true,
                'nav': sliderSettings.nav,
                'navContainer': navContainer,
                'navClass': sliderSettings.navClass,
                'navText': sliderSettings.navText,
                'items': sliderSettings.items,
                'responsive': sliderSettings.responsive,
                'onResize': adapt
            });
        }

        form.nodes.each(function () {
            var self = $(this);

            self.on('click', function () {
                form.parameters.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = self.attr('data-name');

                app.api.forms.show(form.parameters);
                form.parameters.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = null;
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
            });
        });
    }, {
        'name': '[Component] intec.universe:main.projects (template.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>