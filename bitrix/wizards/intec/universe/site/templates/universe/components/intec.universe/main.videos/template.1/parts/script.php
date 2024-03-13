<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<?php if (!defined('EDITOR')) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var gallery = $('[data-entity="gallery"]', data.nodes);

            gallery.lightGallery({
                'selector': '[data-play="true"]'
            });
        }, {
            'name': '[Component] intec.universe:main.videos (template.1) > gallery',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arVisual['SLIDER']['USE']) {

    $arResponsive = [
        '0' => [
            'items' => 1,
            'margin' => 8,
            'stagePadding' => 16
        ],
        '401' => [
            'items' => $arVisual['COLUMNS'],
            'margin' => 8,
            'stagePadding' => 16
        ],
        '601' => [
            'items' => $arVisual['COLUMNS'],
            'margin' => 16,
            'stagePadding' => 32
        ],
        '769' => [
            'items' => $arVisual['COLUMNS'],
            'margin' => 32
        ],
        '1025' => [
            'items' => $arVisual['COLUMNS'],
            'margin' => 32
        ]
    ];

    if ($arVisual['COLUMNS'] > 1)
        $arResponsive['401']['items'] = 2;

    if ($arVisual['COLUMNS'] > 2)
        $arResponsive['601']['items'] = 3;

    if ($arVisual['COLUMNS'] > 3)
        $arResponsive['769']['items'] = 4;

    if ($arVisual['COLUMNS'] > 4)
        $arResponsive['1025']['items'] = 5;

    $arOptions = [
        'autoplay' => false,
        'autoplaySpeed' => 0,
        'autoplayTimeout' => 0,
        'autoplayHoverPause' => false,
        'rewind' => false,
        'nav' => false,
        'navText' => ['', ''],
        'dots' => true,
        'responsive' => $arResponsive
    ];

    if ($arVisual['SLIDER']['AUTO']['USE']) {
        $arOptions['autoplay'] = true;
        $arOptions['autoplaySpeed'] = $arVisual['SLIDER']['AUTO']['SPEED'];
        $arOptions['autoplayTimeout'] = $arVisual['SLIDER']['AUTO']['TIME'];
        $arOptions['autoplayHoverPause'] = $arVisual['SLIDER']['AUTO']['PAUSE'];
        $arOptions['rewind'] = true;
    }

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var slider = $('[data-role="slider"]', data.nodes);
            var dots = undefined;
            var options = <?= JavaScript::toObject($arOptions) ?>;
            var handler = function () {
                var items = $('.owl-dot', dots);

                items.removeClass('intec-cl-background intec-cl-border');
                items.filter('.active').addClass('intec-cl-background intec-cl-border');
            };

            slider.owlCarousel({
                'autoplay': options.autoplay,
                'autoplaySpeed': options.autoplaySpeed,
                'autoplayTimeout': options.autoplayTimeout,
                'autoplayHoverPause': options.autoplayHoverPause,
                'rewind': options.rewind,
                'nav': options.nav,
                'navText': options.navText,
                'dots': options.dots,
                'responsive': options.responsive,
                'onInitialized': function () {
                    dots = $('.owl-dots', slider);
                    handler();
                },
                'onTranslated': handler,
                'onResized': handler
            });
        }, {
            'name': '[Component] intec.universe:main.videos (template.1) > slider',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php unset($arResponsive) ?>