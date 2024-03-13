<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplate
 */

?>
<script type="text/javascript">
    template.load(function () {
        var app = this;
        var _ = app.getLibrary('_');
        var $ = app.getLibrary('$');

        var root = arguments[0].nodes;
        var switches = $('[data-role="switches"]', root);
        var products = $('[data-role="product"]', root);
        var buttons = $('[data-role="button"]', root);
        var overlay;
        var data;
        var update;
        var tabs;

        overlay = (function () {
            var overlay = $('[data-role="overlay"]', root);
            var state = false;

            overlay.open = function (animate) {
                if (state)
                    return;

                state = true;

                if (animate) {
                    overlay.css({'width' : '100%', 'height' : '100%', 'opacity': '1'}).stop().animate({
                            'opacity': 1
                        }, 500, function () {}
                    );
                } else {
                    overlay.css({'width' : '100%', 'height' : '100%', 'opacity' : 1});
                }
            };

            overlay.close = function (animate) {
                if (!state)
                    return;

                state = false;

                if (animate) {
                    overlay.css('opacity', 0).stop().animate({
                            'opacity': 0
                        }, 500, function () {
                            overlay.css({ 'width' : '', 'height' : '', 'opacity' : '' });
                        }
                    );
                } else {
                    overlay.css({'opacity' : '', 'width' : '', 'height' : ''});
                }
            };

            return overlay;
        })();

        tabs = (function () {
            var tabs = $('[data-role="tabs"]', root);
            var list = $('[data-tab]', tabs);
            var current = null;

            tabs.open = function (code, animate) {
                var tab;
                var width = {};

                tab = list.filter('[data-tab="' + code + '"]');

                if (tab.length !== 1)
                    return false;

                tabs.trigger('open', [tab]);

                width.current = tabs.width();
                current = code;

                list.css({
                    'display': '',
                    'width': ''
                }).attr('data-active', 'false');

                tab.css('display', 'block').attr('data-active', 'true');
                width.new = tab.width();

                if (animate) {
                    tab.css('width', width.current).stop().animate({
                        'width': width.new
                    }, 500, function () {
                        tab.css('width', '');
                    });
                } else {
                    tab.css('width', '');
                }

                return true;
            };

            tabs.close = function (animate) {
                var tab;

                if (current === null)
                    return;

                tab = list.filter('[data-tab="' + current + '"]');
                current = null;

                if (tab.length !== 1)
                    return;

                tabs.trigger('close', [tab]);

                if (animate) {
                    tab.stop().animate({
                        'width': 0
                    }, 500, function () {
                        list.attr('data-active', 'false');
                        tab.css({
                            'width': '',
                            'display': ''
                        });
                    });
                } else {
                    list.attr('data-active', 'false');
                    tab.css('display', '');
                }
            };

            tabs.switch = function (code, animate) {
                if (code === current) {
                    tabs.close(animate);
                    overlay.close(animate);

                    return false;
                } else {
                    tabs.open(code, animate);
                    overlay.open(animate);

                    return true;
                }
            };

            tabs.getCurrent = function () {
                return current;
            };

            return tabs;
        })();

        switches.activate = function (item) {
            item = switches.children('[data-role="switch"]').filter(item);

            if (item.length !== 1)
                return;

            item.attr('data-active', 'true');
            item.addClass('active');
        };

        switches.deactivate = function () {
            switches.children('[data-role="switch"]').attr('data-active', 'false');
            switches.children('[data-role="switch"]').removeClass('active');
        };

        tabs.on('close', function () {
            switches.deactivate();
        });

        switches.children('[data-role="switch"]').on('click', function () {
            var self = $(this);
            var tab = self.data('tab');

            switches.deactivate();

            if (tabs.switch(tab, true)) {
                switches.activate(self);
            }
        });

        overlay.on('click', function () {
            tabs.close(true);
            overlay.close(true);
        });

        buttons.on('click', function () {
            var button = $(this);
            var action = button.data('action');

            if (action === 'basket.clear') {
                app.api.basket.clear({'basket': 'Y'}).run();
            } else if (action === 'delayed.clear') {
                app.api.basket.clear({'delay': 'Y'}).run();
            } else if (action === 'close') {
                tabs.close(true);
                overlay.close(true);
            } else if (action === 'form') {
                app.api.forms.show(<?= JavaScript::toObject([
                    'id' => $arResult['FORM']['ID'],
                    'template' => 'template.1',
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-FORM-POPUP',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => $arResult['FORM']['TITLE']
                    ]
                ]) ?>);

                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['PARAMETERS']['id'].'.open') ?>);
            } else if (action === 'personal') {
                app.api.components.show(
                    <?= JavaScript::toObject([
                        'component' => 'bitrix:system.auth.form',
                        'template' => 'template.1',
                        'parameters' => [
                            "COMPONENT_TEMPLATE" => "template.1",
                            "REGISTER_URL" => $arResult['URL']['REGISTER'],
                            "FORGOT_PASSWORD_URL" => $arResult['URL']['FORGOT_PASSWORD'],
                            "PROFILE_URL" => $arResult['URL']['PERSONAL'],
                            "SHOW_ERRORS" => "N"
                        ]

                    ]) ?>
                );
            }
        });

        <?php if ($arResult['FORM']['SHOW']) { ?>
            app.api.forms.get(<?= JavaScript::toObject([
                'id' => $arResult['FORM']['ID'],
                'template' => 'template.1',
                'parameters' => [
                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-FORM',
                    'CONSENT_URL' => $arResult['URL']['CONSENT']
                ]
            ]) ?>).then(function (response) {
                tabs.find('[data-role="area"][data-area="form"]').html(response);
            });
        <?php } ?>

        <?php if ($arResult['PERSONAL']['SHOW']) { ?>
            app.api.components.get(<?= JavaScript::toObject([
                'component' => 'bitrix:system.auth.form',
                'template' => 'template.1',
                'parameters' => [
                    "COMPONENT_TEMPLATE" => "template.1",
                    "REGISTER_URL" => $arResult['URL']['REGISTER'],
                    "FORGOT_PASSWORD_URL" => $arResult['URL']['FORGOT_PASSWORD'],
                    "PROFILE_URL" => $arResult['URL']['PERSONAL'],
                    "SHOW_ERRORS" => "N",
                    "CONSENT_URL" => $arParams['CONSENT_URL']
                ]
            ])?>).then(function (response) {
                tabs.find('[data-role="area"][data-area="personal"]').html(response);
            });
        <?php } ?>

        data = <?= JavaScript::toObject(array(
            'component' => $component->getName(),
            'template' => $this->getName(),
            'parameters' => ArrayHelper::merge($arParams, [
                'AJAX_MODE' => 'N'
            ])
        )) ?>;

        update = function (tab, animate) {
            if (update.disabled)
                return;

            update.disabled = true;
            app.api.basket.off('update', update);
            app.api.compare.off('update', update);

            if (tab === true || _.isNil(tab)) {
                tab = tabs.getCurrent();
            } else if (tab === false) {
                tab = null;
            }

            data.parameters['TAB'] = tab;
            data.parameters['ANIMATE'] = animate ? 'Y' : 'N';

            app.api.components.get(data).then(function (result) {
                root.replaceWith(result);
            });
        };

        update.disabled = false;
        app.api.basket.once('update', update);
        app.api.compare.once('update', update);

        products.each(function () {
            var product = $(this);
            var id = product.data('id');
            var counter = $('[data-role="counter"]', product);
            var buttons = $('[data-role="button"]', product);
            var numeric = app.ui.createControl('numeric', _.merge({
                'node': counter
            }, counter.data('settings')));

            var changeCount = function (value) {
                app.api.basket.setQuantity({
                    'id': id,
                    'quantity': value
                }).run();
            };

            var changeCountDebounce = _.debounce(changeCount, 500);

            numeric.on('change', changeCountDebounce);
            buttons.on('click', function () {
                var button = $(this);
                var action = button.data('action');
                var data = {
                    'id': id
                };

                if (action === 'product.add') {
                    data.delay = 'N';
                    app.api.basket.add(data).run();
                } else if (action === 'product.delay') {
                    data.delay = 'Y';
                    app.api.basket.add(data).run();
                } else if (action === 'product.remove') {
                    app.api.basket.remove(data).run();
                }
            });
        });

        <?php if ($arResult['AUTO']) { ?>
            app.api.basket.once('add', function(data) {
                var tab = tabs.getCurrent();

                if (tab === null)
                    tab = 'basket';

                if (data.delay !== 'Y')
                    update(tab, true);
            });
        <?php } ?>

        <?php if (!empty($arResult['TAB'])) { ?>
            tabs.open(<?= JavaScript::toObject($arResult['TAB']) ?>, <?= JavaScript::toObject($arResult['ANIMATE']) ?>);
            overlay.open(<?= JavaScript::toObject($arResult['ANIMATE']) ?>);
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:sale.basket.small (template.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'options': {
                'await': [
                    'composite'
                ]
            }
        }
    })
</script>