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
        var tabsContainer = $('[data-role="tabs"]', root);
        var tabs = $('[data-role="tab"]', tabsContainer);
        var products = $('[data-role="product"]', root);
        var buttons = $('[data-role="button"]', root);
        var data;
        var update;
        var current = null;

        tabs.each(function () {
            var tab = $(this);
            var icon = $('[data-role="tab.icon"]', tab);

            icon.on('mouseenter', function () {
                tab.attr('data-active', 'true');
                current = tab.data('tab');
            });

            tab.on('mouseleave', function () {
                tab.attr('data-active', 'false');
                current = null;
            });
        });

        buttons.on('click', function () {
            var button = $(this);
            var action = button.data('action');

            if (action === 'basket.clear') {
                app.api.basket.clear({
                    'basket': 'Y'
                }).run();
            } else if (action === 'delayed.clear') {
                app.api.basket.clear({
                    'delay': 'Y'
                }).run();
            } else if (action === 'close') {
                tabs.attr('data-active', 'false');
            }
        });

        data = <?= JavaScript::toObject(array(
            'component' => $component->getName(),
            'template' => $this->getName(),
            'parameters' => ArrayHelper::merge($arParams, [
                'AJAX_MODE' => 'N'
            ])
        )) ?>;

        update = function () {
            if (update.disabled)
                return;

            update.disabled = true;
            app.api.basket.off('update', update);
            app.api.compare.off('update', update);

            if (current === false)
                current = null;

            data.parameters['TAB'] = current;

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
            var buttons = $('[data-role="button"]', product);

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

        <?php if (!empty($arResult['TAB'])) { ?>
            tabsContainer.find('[data-tab="<?= $arResult['TAB'] ?>"]').attr('data-active', 'true');
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:sale.basket.small (icons.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'options': {
                'await': [
                    'composite'
                ]
            }
        }
    });
</script>