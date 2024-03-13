<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        var root = data.nodes;
        var form = $('form', root);

        form.on('submit', function () {
            app.metrika.reachGoal('forms');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['arForm']['ID']) ?>);
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['arForm']['ID'].'.send') ?>);
        });
    }, {
        'name': '[Component] bitrix:form.result.new (contacts)',
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
            'name': '[Component] bitrix:form.result.new (contacts)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
        });
    </script>
<?php } ?>