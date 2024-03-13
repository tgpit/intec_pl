<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

?>
<?php if ($arVisual['SLIDER']['USE']) {

    $arSlider = [
        'items' => $arVisual['COLUMNS'],
        'dots' => $arVisual['SLIDER']['DOTS'],
        'dotClass' => Html::cssClassFromArray([
            'owl-dot',
            'widget-items-dot',
            'intec-grid-item-auto',
            'intec-cl-background'
        ]),
        'dotsClass' => Html::cssClassFromArray([
            'owl-dots',
            'widget-items-dots',
            'intec-grid',
            'intec-grid-a-h-center'
        ]),
        'rewind' => $arVisual['SLIDER']['AUTO']['USE'],
        'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
        'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
        'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
        'autoHeight' => true,
        'responsive' => [
            0 => ['items' => 1]
        ]
    ];

    if ($arVisual['COLUMNS'] >= 2)
        $arSlider['responsive'][1025] = ['items' => 2];

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var slider = $('[data-role="container"]', data.nodes);

            slider.owlCarousel(<?= JavaScript::toObject($arSlider) ?>);
        }, {
            'name': '[Component] intec.universe:main.reviews (template.1) > Slider',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        })
    </script>
<?php } ?>
<?php if ($arVisual['VIDEO']['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var container = $('[data-role="container"]', data.nodes);

            container.lightGallery({
                'selector': '[data-role="video"]'
            });
        }, {
            'name': '[Component] intec.universe:main.reviews (template.1) > Video',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arVisual['SEND']['USE']) {

    if (empty($arResult['SEND']['TITLE']))
        $arResult['SEND']['TITLE'] = Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_1_TEMPLATE_SEND_TITLE_DEFAULT');

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
            'name': '[Component] intec.universe:main.reviews (template.1) > Send popup',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>