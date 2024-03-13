<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => $arVisual['COLUMNS'],
    'stagePadding' => 1,
    'margin' => 8,
    'dots' => false,
    'nav' => $arVisual['SLIDER']['NAVIGATION'],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'navContainerClass' => 'catalog-section-navigation',
    'navClass' => [
        Html::cssClassFromArray([
            'catalog-section-navigation-left',
            'intec-cl-background-hover',
            'intec-cl-border-hover',
            'intec-ui-picture'
        ]),
        Html::cssClassFromArray([
            'catalog-section-navigation-right',
            'intec-cl-background-hover',
            'intec-cl-border-hover',
            'intec-ui-picture'
        ])
    ],
    'dotClass' => Html::cssClassFromArray([
        'catalog-section-dot',
        'intec-grid-item-auto'
    ]),
    'loop' => $arVisual['SLIDER']['LOOP'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplaySpeed' => $arVisual['SLIDER']['AUTO']['SPEED'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['PAUSE'],
    'responsive' => [
        '0' => [
            'items' => 1,
            'nav' => false,
            'dots' => true
        ],
        '601' => [
            'items' => 2
        ],
        '1025' => [
            'items' => $arVisual['COLUMNS']
        ],
        '1201' => [
            'items' => $arVisual['COLUMNS']
        ]
    ]
];

if ($arVisual['COLUMNS'] > 3)
    $arSlider['responsive']['1025']['items'] = 3;

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);
        var parameters = <?= JavaScript::toObject($arSlider) ?>;

        parameters.navContainer = data.nodes.find('[data-role="slider.navigation"]');
        parameters.dotsContainer = data.nodes.find('[data-role="slider.dots"]');
        parameters.onTranslate = function () {
            var dots = parameters.dotsContainer.find('[role="button"]');

            dots.find('span').removeClass('intec-cl-background intec-cl-border');
            dots.filter('.active')
                .find('span')
                .addClass('intec-cl-background intec-cl-border');
        };
        parameters.onInitialized = parameters.onTranslate;
        parameters.onResized = parameters.onTranslate;

        slider.owlCarousel(parameters);
    }, {
        'name': '[Component] bitrix:catalog.section (products.small.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arVisual['SLIDER']) ?>