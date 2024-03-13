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
        var panel = $('[data-role="panel"]', root);
        var buttons = $('[data-role="button"]', root);
        var area = $(window);
        var scrollPrev = 0;
        var data;
        var update;

        buttons.on('click', function () {
            var button = $(this);
            var action = button.data('action');

            if (action === 'form') {
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
            } else if (action === 'personal') {
                app.api.components.show(<?= JavaScript::toObject([
                    'component' => 'bitrix:system.auth.form',
                    'template' => 'template.1',
                    'parameters' => [
                        "COMPONENT_TEMPLATE" => "template.1",
                        "REGISTER_URL" => $arResult['URL']['REGISTER'],
                        "FORGOT_PASSWORD_URL" => $arResult['URL']['FORGOT_PASSWORD'],
                        "PROFILE_URL" => $arResult['URL']['PROFILE'],
                        "SHOW_ERRORS" => "N"
                    ]
                ]) ?>);
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

            app.api.components.get(_.merge({}, data)).then(function (result) {
                root.replaceWith(result);
            });
        };

        update.disabled = false;
        app.api.basket.once('update', update);
        app.api.compare.once('update', update);

        <?php if ($arVisual['PANEL']['HIDDEN']) { ?>
            area.on('scroll', function () {
                var scrolled = area.scrollTop();

                if (scrolled > 100 && scrolled > scrollPrev) {
                    panel.addClass('sale-basket-small-panel-out');
                } else {
                    panel.removeClass('sale-basket-small-panel-out');
                }

                scrollPrev = scrolled;
            });
        <?php } ?>

    }, {
        'name': '[Component] intec.universe:sale.basket.small (panel.1)',
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