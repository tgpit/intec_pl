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
        var $ = this.getLibrary('$');

        var root = arguments[0].nodes;
        var data;
        var update;

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

            app.api.components.get(data).then(function (result) {
                root.replaceWith(result);
            });
        };

        update.disabled = false;

        app.api.basket.once('update', update);
        app.api.compare.once('update', update);
    }, {
        'name': '[Component] bitrix:menu (popup.3) > intec.universe:sale.basket.small (template.1)',
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