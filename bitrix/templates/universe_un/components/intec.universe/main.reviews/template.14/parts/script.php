<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

?>
<?php if ($arVisual['SLIDER']['USE']) {

    $arSlider = [
        'items' => 1,
        'margin' => 15,
        'nav' => false,
        'dots' => true,
        'autoHeight' => true
    ];

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');

            var slider = $('[data-role="container"]', data.nodes);
            var dots = $('[data-role="dots"]', data.nodes);

            dots.items = $('[data-role="dots.item"]', dots);

            var dotsHandler = function () {
                dots.items
                    .find('[data-role="dots.item.content"]')
                    .removeClass('intec-cl-border');

                dots.items
                    .filter('.active')
                    .find('[data-role="dots.item.content"]')
                    .addClass('intec-cl-border');
            };

            slider.owlCarousel(_.merge(<?= JavaScript::toObject($arSlider) ?>, {
                'dotsContainer': dots,
                'onInitialized': dotsHandler,
                'onTranslate' : dotsHandler
            }));

            dots.items.on('click', function() {
                slider.trigger('to.owl.carousel', [$(this).index(), 300]);
            });
        }, {
            'name': '[Component] intec.universe:main.reviews (template.14) > Slider',
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
            'name': '[Component] intec.universe:main.reviews (template.14) > Video',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arVisual['SEND']['USE']) {

    if (empty($arResult['SEND']['TITLE']))
        $arResult['SEND']['TITLE'] = Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_14_TEMPLATE_SEND_TITLE_DEFAULT');

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
            'name': '[Component] intec.universe:main.reviews (template.13) > Send popup',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>