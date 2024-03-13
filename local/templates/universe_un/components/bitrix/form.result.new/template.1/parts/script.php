<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data, options) {
        var app = this;
        var $ = app.getLibrary('$');
        var elements = {};

        //for adaptation window
        window.dispatchEvent(new Event('resize'));

        elements.root = $(options.nodes);
        elements.buttons = $('[data-role="buttons"]', elements.root);
        elements.closeButton = $('[data-role="closeButton"]', elements.buttons);
        elements.form = $('form', elements.root);
        elements.popup = elements.root.closest('.popup-window');

        if (elements.buttons.length > 0 && elements.popup.length > 0) {
            elements.buttons.show();
            elements.closeButton.on('click', function () {
                elements.popup.find('.popup-window-close-icon').trigger('click');
            });
        }

        elements.form.on('submit', function () {
            app.metrika.reachGoal('forms');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['arForm']['ID']) ?>);
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['arForm']['ID'].'.send') ?>);
        });
    }, {
        'name': '[Component] bitrix:form.result.new (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
    });
</script>
<?php if ($arResult['CONSENT']['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function () {
            var $ = this.getLibrary('$');
            var node = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            var form = $('form', node);
            var consent = $('[name="licenses_popup"]', form);
            var submit = $('[type="submit"]', form);

            if (!form.length || !consent.length || !submit.length)
                return;

            var update = function () {
                submit.prop('disabled', !consent.prop('checked'));
            };

            form.on('submit', function () {
                return consent.prop('checked');
            });

            consent.on('change', update);

            update();
        }, {
            'name': '[Component] bitrix:form.result.new (template.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
        });
    </script>
<?php } ?>