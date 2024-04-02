<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 */

$arFormParameters = [
    'id' => $arResult['FORM']['ID'],
    'template' => $arResult['FORM']['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
        'CONSENT_URL' => $arParams['CONSENT_URL']
    ],
    'settings' => [
        'title' => $arResult['FORM']['TITLE']
    ]
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var form = {
            'node': $('[data-role="contact.form"]', data.nodes),
            'parameters': <?= JavaScript::toObject($arFormParameters) ?>
        };

        form.node.on('click', function () {
            app.api.forms.show(form.parameters);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open') ?>);
        });
    }, {
        'name': '[Component] intec.universe:main.widget (contact.1) > form',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>