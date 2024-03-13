<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$arFormCall = $arResult['FORMS']['CALL'];
$arFormFeedback = $arResult['FORMS']['FEEDBACK'];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        var root = data.nodes;
        var button = $('[data-role="forms.button"]', root);

        button.on('click', function () {
            var action = $(this).attr('data-action');

            if (action == 'call.open') {
                app.api.forms.show(<?= JavaScript::toObject([
                    'id' => $arFormCall['ID'],
                    'template' => $arFormCall['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_CALL',
                        'CONSENT_URL' => $arResult['FORMS']['CONSENT_URL']
                    ],
                    'settings' => [
                        'title' => $arFormCall['TITLE']
                    ]
                ]) ?>);
            } else if (action == 'feedback.open') {
                app.api.forms.show(<?= JavaScript::toObject([
                    'id' => $arFormFeedback['ID'],
                    'template' => $arFormFeedback['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_CALL',
                        'CONSENT_URL' => $arResult['FORMS']['CONSENT_URL']
                    ],
                    'settings' => [
                        'title' => $arFormFeedback['TITLE']
                    ]
                ]) ?>);
            }

            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arFormCall['ID'].'.open') ?>);
        });
    }, {
        'name': '[Component] bitrix:menu (popup.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>