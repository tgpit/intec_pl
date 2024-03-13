<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => $arVisual['COLUMNS'],
    'dots' => false,
    'nav' => $arVisual['NAVIGATION']['SHOW'],
    'navContainer' => '[data-role="navigation"]',
    'navClass' => [
        'intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover',
        'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'margin' => 8,
    'responsive' => [
        '0' => [
            'items' => 1
        ],
        '400' => [
            'items' => 2
        ],
        '650' => [
            'items' => 3
        ],
        '769' => [
            'items' => 2
        ],
        '1101' => [
            'items' => $arVisual['COLUMNS']
        ]
    ]
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var itemsContainer = $('[data-role="items"]', root);
        var items = $('[data-role="item"]', itemsContainer);
        var sliderSettings = <?= JavaScript::toObject($arSlider) ?>;

        itemsContainer.owlCarousel({
            'items': sliderSettings.items,
            'dots': sliderSettings.dots,
            'margin': sliderSettings.margin,
            'nav': sliderSettings.nav,
            'navContainer': $(sliderSettings.navContainer, root),
            'navClass': sliderSettings.navClass,
            'navText': sliderSettings.navText,
            'responsive': sliderSettings.responsive,
            'itemsAutoHeight': true,
            'itemsAutoHeightRefresh': true
        });

        items.each(function (index, item) {
            var imagesContainer = $('[data-role="item.images"]', item);

            imagesContainer.owlCarousel({
                'items': 1,
                'nav': false,
                'dots': true,
                'dotsEach': true,
                'overlayNav': true
            });

            imagesContainer.dots = $('.owl-dots', imagesContainer);
            imagesContainer.dots.dot = imagesContainer.dots.find('[role="button"]');
            imagesContainer.dots.dot.active = imagesContainer.dots.dot.filter('.active');
            imagesContainer.dots.addClass('intec-grid');
            imagesContainer.dots.dot.addClass('intec-grid-item');
            imagesContainer.dots.dot.active.find('span').addClass('intec-cl-background');

            imagesContainer.on('changed.owl.carousel', function() {
                imagesContainer.dots.dot = $('[role="button"]' , this);
                imagesContainer.dots.dot.find('span').removeClass('intec-cl-background');
                imagesContainer.dots.dot.filter('.active').find('span').addClass('intec-cl-background');
            });
        });
    }, {
        'name': '[Component] bitrix:catalog.section (products.small.9)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arSlider) ?>