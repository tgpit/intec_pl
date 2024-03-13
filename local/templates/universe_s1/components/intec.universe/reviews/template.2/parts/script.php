<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 * @var CBitrixComponentTemplate $this
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
        'root' => '[data-role="reviews.form"]',
        'body' => '[data-role="reviews.form.body"]'
    ],
    'items' => [
        'root' => '[data-role="reviews.content"]'
    ],
    'settings' => [
        'navigationButtonDelete' => true
    ]
]);

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var form = $('[data-role="reviews.form"]', root);
        var content = $('[data-role="reviews.content"]', root);
        var update = {
            'form': null,
            'items': null
        };
        var component = new IntecReviews2Component();

        update.form = function() {
            if (form.length) {
                form.grade = $('[data-role="form.grade"]', form);
                form.toggle = $('[data-role="reviews.form.toggle"]', form);

                if (form.grade.length) {
                    form.grade.items = $('[data-role="form.grade.item"]', form.grade);
                    form.grade.input = $('[data-role="form.grade.input"]', form.grade);
                    form.grade.information = $('[data-role="form.grade.information"]', form.grade);
                    form.grade.information.title = $('[data-role="form.grade.information.title"]', form.grade.information);

                    if (form.grade.items.length) {
                        form.grade.initialize = function () {
                            var value = form.grade.input.attr('value');

                            if (value) {
                                var checked = form.grade.items.filter('[data-value="' + value + '"]');

                                checked.trigger('click');
                            } else {
                                form.grade.items.eq(form.grade.items.length - 1)
                                    .trigger('click');
                            }
                        };

                        form.grade.controller = function (element, attribute) {
                            var index = element.attr('data-index');

                            form.grade.items.attr(attribute, false);

                            element.attr(attribute, true);

                            form.grade.items.filter(function (key, element) {
                                var filtered = $(element);
                                var filteredIndex = filtered.attr('data-index');

                                if (filteredIndex < index)
                                    filtered.attr(attribute, true);
                                else if (filteredIndex > index)
                                    filtered.attr(attribute, false);
                            });
                        };

                        form.grade.items.each(function () {
                            var self = $(this);
                            var index = self.attr('data-index');

                            self.on('click', function () {
                                form.grade.controller(self, 'data-active');
                                form.grade.input.attr('value', self.attr('data-value'));

                                if (form.grade.information.attr('data-active') === 'false')
                                    form.grade.information.attr('data-active', true);

                                form.grade.information.title.text(self.attr('title'));
                            });

                            self.on('mouseenter', function () {
                                form.grade.controller(self, 'data-hover');
                            });

                            self.on('mouseleave', function () {
                                form.grade.items.attr('data-hover', false);
                            });
                        });

                        form.grade.initialize();
                    }
                }

                if (form.toggle.length) {
                    form.body = $('[data-role="reviews.form.body"]', form);

                    if (form.body.length) {
                        form.toggle.on('click', function () {
                            var expanded = form.body.attr('data-expanded') === 'true';

                            form.toggle.css('pointer-events', 'none');

                            if (expanded) {
                                form.body.attr('data-state', 'processing')
                                    .attr('data-expanded', false)
                                    .animate({'height': 0}, 400, function () {
                                        form.body.attr('data-state', 'none')
                                            .css('height', '');

                                        form.toggle.css('pointer-events', 'all');
                                    });
                            } else {
                                var height = form.body.css('height', '').outerHeight();

                                if (height === null || height === undefined)
                                    return;

                                form.body.attr('data-state', 'processing')
                                    .attr('data-expanded', true)
                                    .css('height', 0)
                                    .animate({'height': height}, 400, function () {
                                        form.body.attr('data-state', 'none')
                                            .css('height', '');

                                        form.toggle.css('pointer-events', 'all');
                                    });
                            }
                        });
                    }
                }
            }
        };
        update.items = function () {
            if (content.length) {
                content.items = $('[data-role="reviews.content.item"]', content);

                if (content.items.length) {
                    content.items.each(function () {
                        var self = $(this);
                        var grade = $('[data-role="reviews.content.item.grade"]', self);

                        if (grade.length) {
                            grade.items = $('[data-role="reviews.content.item.grade.item"]', grade);

                            if (grade.items.length) {
                                var value = grade.attr('data-value');
                                var index = grade.items.filter('[data-value="' + value +'"]');

                                if (index !== undefined && index !== null) {
                                    index = index.attr('data-index');

                                    grade.items.filter(function (key, element) {
                                        var filtered = $(element);

                                        if (filtered.attr('data-index') <= index)
                                            filtered.attr('data-active', true);
                                    });
                                }
                            }
                        }
                    });
                }
            }
        };

        update.form();
        update.items();

        document.addEventListener('intec.reviews.form.updated', function () {
            update.form();
        });
        document.addEventListener('intec.reviews.items.updated', function () {
            update.items();
        });

        component.initialize(<?= $arComponentParameters ?>);
    }, {
        'name': '[Component] intec.universe:reviews (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>