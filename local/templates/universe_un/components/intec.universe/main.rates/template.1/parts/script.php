<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

if ($arVisual['SLIDER']['USE']) {
    $arSlider = [
        'items' => $arVisual['COLUMNS'],
        'navText' => [
            FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
            FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
        ],
        'dots' => $arVisual['SLIDER']['DOTS'],
        'loop' => $arVisual['SLIDER']['LOOP'],
        'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
        'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
        'autoplayHoverPause' => false,
        'responsive' => [
            0 => [
                'items' => 1,
                'nav' => false
            ],
            650 => [
                'items' => 2,
                'nav' => $arVisual['SLIDER']['NAV']
            ],
            901 => [
                'items' => 3,
                'nav' => $arVisual['SLIDER']['NAV']
            ],
        ]
    ];

    if ($arVisual['COLUMNS'] >= 4)
        $arSlider['responsive'][1201] = ['items' => 4];
}

?>
<?php if ($arVisual['SLIDER']['USE']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var slider = $('[data-slider="true"]', data.nodes);
            var settings = <?= JavaScript::toObject($arSlider) ?>;

            slider.each(function () {
                var self = $(this);

                var adapt = function () {
                    <?php if (!defined('EDITOR')) { ?>
                        var container = $('.owl-stage', self);
                        var item = $('.owl-item', self);

                        item.css({'height': 'initial'});
                        item.css({'height': container.height()});
                    <?php }  else { ?>
                        return false;
                    <?php } ?>
                };

                self.owlCarousel({
                    'items': settings.items,
                    'loop': settings.loop,
                    'autoplay': settings.autoplay,
                    'autoplayTimeout': settings.autoplayTimeout,
                    'autoplayHoverPause': settings.autoplayHoverPause,
                    'navText': settings.navText,
                    'navContainerClass': 'intec-ui intec-ui-control-navigation',
                    'navClass': ['intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover', 'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover'],
                    'dots': settings.dots,
                    'dotClass': 'owl-dot widget-items-dot intec-grid-item-auto intec-cl-background',
                    'dotsClass': 'owl-dots widget-items-dots intec-grid intec-grid-a-h-center',
                    'responsive': settings.responsive,
                    'onRefreshed': adapt
                });
            });
        }, {
            'name': '[Component] intec.universe:main.rates (template.1) > slider',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arForm['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var buttons = $('[data-role="rate.button"]', data.nodes);

            buttons.each(function () {
                var self = $(this);

                self.on('click', function () {
                    var parameters = <?= JavaScript::toObject($arForm['PARAMETERS']) ?>;

                    parameters.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = self.data('value');

                    app.api.forms.show(parameters);
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
                });
            });
        }, {
            'name': '[Component] intec.universe:main.rates (template.1) > order',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>