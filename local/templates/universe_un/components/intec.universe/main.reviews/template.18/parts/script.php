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
        'items' => 1,
        'rewind' => $arVisual['SLIDER']['AUTO']['USE'],
        'nav' => false,
        'dots' => $arVisual['SLIDER']['DOTS'],
        'dotsClass' => Html::cssClassFromArray([
            'owl-dots',
            'widget-items-dots',
            'intec-grid',
            'intec-grid-a-h-center'
        ]),
        'dotClass' => Html::cssClassFromArray([
            'owl-dot',
            'widget-items-dot',
            'intec-grid-item-auto',
            'intec-cl-background'
        ]),
        'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
        'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
        'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
        'autoHeight' => true
    ];

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var slider = $('[data-role="container"]', data.nodes);

            slider.owlCarousel(<?= JavaScript::toObject($arSlider) ?>);
        }, {
            'name': '[Component] intec.universe:main.reviews (template.18) > Slider',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arVisual['VIDEO']['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var video = $('[data-role="container"]', data.nodes);

            video.lightGallery({
                'selector': '[data-role="video"]'
            });
        }, {
            'name': '[Component] intec.universe:main.reviews (template.18) > Video',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arVisual['SEND']['USE']) {

    if (empty($arResult['SEND']['TITLE']))
        $arResult['SEND']['TITLE'] = Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_18_TEMPLATE_SEND_TITLE_DEFAULT');

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
            'name': '[Component] intec.universe:main.reviews (template.18) > Send popup',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>

