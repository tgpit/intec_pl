<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$arFormParameters = [
    'id' => $arResult['ID'],
    'template' => $arResult['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_ASK',
        'CONSENT_URL' => $arResult['CONSENT']
    ],
    'settings' => [
        'title' => $arResult['NAME']
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
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
        });
    }, {
        'name': '[Component] intec.universe:main.form (template.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>