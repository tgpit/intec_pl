<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 */

if (!empty($arParams['FORM1_ID']))
    $arFirstFormParameters = [
        'id' => $arParams['FORM1_ID'],
        'template' => $arResult['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_1_ASK',
            'CONSENT_URL' => $arResult['CONSENT']
        ],
        'settings' => [
            'title' => $arParams['FORM1_NAME']
        ]
    ];

if (!empty($arParams['FORM2_ID']))
    $arSecondFormParameters = [
        'id' => $arParams['FORM2_ID'],
        'template' => $arResult['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_2_ASK',
            'CONSENT_URL' => $arResult['CONSENT']
        ],
        'settings' => [
            'title' => $arParams['FORM2_NAME']
        ]
    ];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var forms = {
            'first': $('[data-role="form.button.1"]', data.nodes),
            'second': $('[data-role="form.button.2"]', data.nodes)
        };

        <?php if (!empty($arParams['FORM1_ID'])) { ?>
            if (forms.first.length > 0) {
                forms.first.on('click', function () {
                    app.api.forms.show(<?= JavaScript::toObject($arFirstFormParameters) ?>);
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arParams['FORM1_ID'].'.open') ?>);
                });
            }
        <?php } ?>
        <?php if (!empty($arParams['FORM2_ID'])) { ?>
            if (forms.second.length > 0) {
                forms.second.on('click', function () {
                    app.api.forms.show(<?= JavaScript::toObject($arSecondFormParameters) ?>);
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.' . $arParams['FORM2_ID'] . '.open') ?>);
                });
            }
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:main.form (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
