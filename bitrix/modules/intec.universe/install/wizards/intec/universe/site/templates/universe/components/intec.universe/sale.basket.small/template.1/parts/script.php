<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
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
        var data;
        var update;
        var tabs;
        var isBackToCatalog = <?= JavaScript::toObject($arVisual['BACK_BUTTON']['USE']) ?>;
        console.log(isBackToCatalog);

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
                } else {
                    tabs.open(code, animate);
                }
            };

            tabs.getCurrent = function () {
                return current;
            };

            return tabs;
        })();

        switches.children('[data-tab]').on('click', function () {
            var self = $(this);
            var tab = self.data('tab');

            tabs.switch(tab, true);
        });

        buttons.on('click', function () {
            var button = $(this);
            var action = button.data('action');

            if (action === 'basket.clear') {
                app.api.basket.clear({'basket': 'Y'}).run();
            } else if (action === 'delayed.clear') {
                app.api.basket.clear({'delay': 'Y'}).run();
            } else if (action === 'close') {
                if (isBackToCatalog)
                    window.location.href = <?= JavaScript::toObject($arResult['URL']['CATALOG']) ?>;
                else
                    tabs.close(true);
            }else if (action === 'backToCatalog') {
                window.location.href = <?= JavaScript::toObject($arResult['URL']['CATALOG']) ?>;
            } else if (action === 'form') {
                app.api.forms.show(<?= JavaScript::toObject([
                    'id' => $arResult['FORM']['ID'],
                    'template' => '.default',
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
            }
        });

        <?php if ($arResult['FORM']['SHOW']) { ?>
            app.api.forms.get(<?= JavaScript::toObject([
                'id' => $arResult['FORM']['ID'],
                'template' => '.default',
                'parameters' => [
                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-FORM',
                    'CONSENT_URL' => $arResult['URL']['CONSENT']
                ]
            ]) ?>).then(function (response) {
                tabs.find('[data-role="area"][data-area="form"]').html(response);
            });
        <?php } ?>

        data = <?= JavaScript::toObject(array(
            'component' => $component->getName(),
            'template' => $this->getName(),
            'parameters' => ArrayHelper::merge($arParams, [
                    'AJAX_MODE' => 'N'
            ])
        )) ?>;

        update = function (tab) {
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
                    update(tab);
            });
        <?php } ?>

        <?php if (!empty($arResult['TAB'])) { ?>
            tabs.open(<?= JavaScript::toObject($arResult['TAB']) ?>, false);
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:sale.basket.small (template.1)',
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