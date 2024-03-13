<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$oSigner = new Signer();

$arParams['SETTINGS_USE'] = 'N';
$arParams['AJAX_UPDATE'] = 'Y';

$arComponentParameters = JavaScript::toObject([
    'root' => '#'.$sTemplateId,
    'component' => [
        'path' => $this->getComponent()->getPath().'/ajax.php',
        'template' => $oSigner->sign(
            base64_encode($this->GetName()),
            'intec.reviews'
        ),
        'parameters' => $oSigner->sign(
            base64_encode(serialize($arParams)),
            'intec.reviews'
        )
    ],
    'navigation' => [
        'id' => $arResult['NAVIGATION']['ID'],
        'container' => '[data-role="navigation"]',
        'root' => '[data-role="navigation.button"]',
        'current' => $arResult['NAVIGATION']['PAGE']['CURRENT'],
        'count' => $arResult['NAVIGATION']['PAGE']['COUNT']
    ],
    'form' => [
        'root' => '[data-role="form.root"]',
        'body' => '[data-role="form.body"]'
    ],
    'items' => [
        'root' => '[data-role="reviews.items"]'
    ],
    'settings' => [
        'navigationButtonDelete' => true
    ]
]);

?>
<?php if ($arResult['FORM']['ACCESS'] || $arResult['NAVIGATION']['USE']) { ?>
    <script type="text/javascript">
        template.load(function () {
            var component = new IntecReviews2Component();

            component.initialize(<?= $arComponentParameters ?>);
        }, {
            'name': '[Component] intec.universe:reviews (template.1) > ajax form & pagination',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arResult['FORM']['ACCESS']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var update = function () {
                var form = {
                    'toggle': $('[data-role="form.toggle"]', data.nodes),
                    'root': $('[data-role="form.content"]', data.nodes)
                };

                if (!form.toggle.length || !form.root.length)
                    return false;

                form.toggle.on('click', function () {
                    var expanded = form.root.attr('data-expanded') === 'true';

                    form.toggle.css('pointer-events', 'none');

                    if (expanded) {
                        form.root.attr('data-expanded', 'processing')
                            .animate({'height': 0, 'opacity': 0}, 400, function () {
                                form.root.attr('data-expanded', false)
                                    .css({'height': '', 'opacity': ''});

                                form.toggle.css('pointer-events', '');
                            });
                    } else {
                        var height = form.root.outerHeight();

                        form.root.attr('data-expanded', 'processing')
                            .css({'height': 0, 'opacity': 0})
                            .animate({'height': height, 'opacity': 1}, 400, function () {
                                form.root.attr('data-expanded', true)
                                    .css({'height': '', 'opacity': ''});

                                form.toggle.css('pointer-events', '');
                            });
                    }
                });
            };

            update();

            document.addEventListener('intec.reviews.form.updated', function () {
                update();
            });
        }, {
            'name': '[Component] intec.universe:reviews (template.1) > form toggle',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>