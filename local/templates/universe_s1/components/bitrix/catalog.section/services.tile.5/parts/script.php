<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arNavigation
 * @var array $arVisual
 * @var string $sTemplateId
 * @var string $sTemplateContainer
 * @var CBitrixComponentTemplate $this
 */

$oSigner = new Signer;
$sSignedTemplate = $oSigner->sign($this->GetName(), 'catalog.section');
$sSignedParameters = $oSigner->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'catalog.section');

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var component;

        root.update = function () {
            var items = $('[data-role="item"]', root);

            items.each(function () {
                var self = $(this);

                self.picture = $('[data-role="picture"]', self);
                self.information = $('[data-role="information"]', self);

                self.on('mouseenter', function () {
                    if (document.documentElement.clientWidth <= 1024)
                        return false;

                    self.css('height', self.outerHeight())
                        .attr('data-expanded', 'true');

                    self.information.css({
                        'position': 'absolute',
                        'left': 0,
                        'right': 0,
                        'bottom': 0,
                        'min-height': self.height() - self.picture.outerHeight()
                    });
                });

                self.on('mouseleave', function () {
                    if (document.documentElement.clientWidth <= 1024)
                        return false;

                    self.information.css({
                        'position': '',
                        'left': '',
                        'right': '',
                        'bottom': '',
                        'min-height': ''
                    });

                    self.css('height', '')
                        .attr('data-expanded', 'false');
                });
            });
        };

        root.reset = function () {
            var items = $('[data-role="item"]', root);

            items.each(function () {
                var self = $(this);

                self.information = $('[data-role="information"]', self);

                self.information.css({
                    'position': '',
                    'left': '',
                    'right': '',
                    'bottom': '',
                    'min-height': ''
                });

                self.css('height', '')
                    .attr('data-expanded', 'false');
            });
        };

        BX.message(<?= JavaScript::toObject([
            'BTN_MESSAGE_LAZY_LOAD' => '',
            'BTN_MESSAGE_LAZY_LOAD_WAITER' => ''
        ]) ?>);

        component = new JCCatalogSectionComponent(<?= JavaScript::toObject([
            'siteId' => SITE_ID,
            'componentPath' => $componentPath,
            'navParams' => $arNavigation,
            'deferredLoad' => false,
            'initiallyShowHeader' => false,
            'bigData' => $arResult['BIG_DATA'],
            'lazyLoad' => $arVisual['NAVIGATION']['LAZY']['BUTTON'],
            'loadOnScroll' => $arVisual['NAVIGATION']['LAZY']['SCROLL'],
            'template' => $sSignedTemplate,
            'parameters' => $sSignedParameters,
            'ajaxId' => $arParams['AJAX_ID'],
            'container' => $sTemplateContainer
        ], true) ?>);

        component.processItems = (function () {
            var action = component.processItems;

            return function () {
                var result = action.apply(this, arguments);

                root.update();

                return result;
            };
        })();

        document.addEventListener('ready', root.update());
        window.addEventListener('resize', root.reset);
    }, {
        'name': '[Component] bitrix:catalog.section (services.tile.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($oSigner, $sSignedTemplate, $sSignedParameters) ?>