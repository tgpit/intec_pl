<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$arFormParameters = [
    'id' => $arResult['FORM']['ID'],
    'template' => $arResult['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ASK',
        'CONSENT_URL' => $arResult['CONSENT']
    ],
    'settings' => [
        'title' => $arResult['FORM']['NAME']
    ]
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        $('[data-role="form.button"]', data.nodes).on('click', function () {
            app.api.forms.show(<?= JavaScript::toObject($arFormParameters) ?>);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open') ?>);
        });
    }, {
        'name': '[Component] intec.universe:main.form (template.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>