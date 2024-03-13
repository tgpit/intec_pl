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
        var container = $('[data-role="container"]', root);
        var data;
        var add;

        data = <?= JavaScript::toObject(array(
            'component' => $component->getName(),
            'template' => $this->getName(),
            'parameters' => ArrayHelper::merge($arParams, [
                'AJAX_MODE' => 'N'
            ])
        )) ?>;

        add = function (id) {
            app.api.components.get(_.merge({}, data, {
                'parameters': {
                    'ID': id
                }
            })).then(function (result) {
                var item = $(result);
                var element;

                container.append(item);

                element = $('[data-product-id="'+id+'"]', container);
                element.attr('data-active', 'true');
                element.find('[data-role="close"]').on('click', function () {
                    element.attr('data-active', 'false');

                    setTimeout(function () {
                        item.remove();
                    }, 300);
                });

                setTimeout(function () {
                    element.attr('data-active', 'false');

                    setTimeout(function () {
                        item.remove();
                    }, 300);

                }, 5000);
            });
        };

        app.api.basket.on('add', function (data) {
            if (data.delay !== 'Y')
                add(data.id);
        });
    }, {
        'name': '[Component] intec.universe:sale.basket.small (notifications.1)',
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