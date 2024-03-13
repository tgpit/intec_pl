<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => 5,
    'itemClass' => Html::cssClassFromArray([
        'owl-item',
        'widget-owl-item'
    ]),
    'stageOuterClass' => Html::cssClassFromArray([
        'owl-stage-outer',
        'widget-outer-items'
    ]),
    'loop' => $arVisual['SLIDER']['LOOP'],
    'center' => true,
    'nav' => false,
    'dots' => false,
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
    'autoHeight' => true,
    'responsive' => [
        '0' => [
            'items' => 1
        ],
        '400' => [
            'items' => 3
        ],
        '690' => []
    ]
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var slider = $('[data-role="container"]', data.nodes);
        var sliderContent = $('[data-role="slider.content"]', data.nodes);
        var sliderNavigation = $('[data-role="slider.navigation"]', data.nodes);

        var showContent = function () {
            var content = $('.owl-item.center [data-role="slide.content"]', slider).html();

            sliderContent.css('opacity', 1).html(content);
        };

        var hideContent = function () {
            sliderContent.css('opacity', 0);
        };

        slider.owlCarousel(_.merge(<?= JavaScript::toObject($arSlider) ?>, {
            'onInitialized': showContent,
            'onTranslated': showContent,
            'onTranslate': hideContent
        }));

        $('[data-role="slider.navigation.right"]', sliderNavigation).on('click', function () {
            slider.trigger('next.owl.carousel');
        });

        $('[data-role="slider.navigation.left"]', sliderNavigation).on('click', function () {
            slider.trigger('prev.owl.carousel');
        });
    }, {
        'name': '[Component] intec.universe:main.reviews (template.15) > Slider',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arVisual['SEND']['USE']) {

    if (empty($arResult['SEND']['TITLE']))
        $arResult['SEND']['TITLE'] = Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_15_TEMPLATE_SEND_TITLE_DEFAULT');

    $arSendParameters = JavaScript::toObject([
        'component' => $arResult['SEND']['COMPONENT'],
        'template' => $arResult['SEND']['TEMPLATE'],
        'parameters' => $arResult['SEND']['PARAMETERS'],
        'settings' => [
            'title' => $arResult['SEND']['TITLE'],
            'parameters' => [
                'width' => null
            ]
        ]
    ]);

    ?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var sender = $('[data-role="review.send"]', data.nodes);

            sender.on('click', function () {
                app.api.components.show(<?= $arSendParameters ?>);
            });
        }, {
            'name': '[Component] intec.universe:main.reviews (template.15) > Send popup',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>