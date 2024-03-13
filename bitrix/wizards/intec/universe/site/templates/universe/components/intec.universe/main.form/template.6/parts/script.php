<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

if ($arResult['FORMS'][0]['SHOW'])
    $arFirstFormParameters = [
        'id' => $arResult['FORMS'][0]['ID'],
        'template' => $arResult['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_1',
            'CONSENT_URL' => $arResult['CONSENT']
        ],
        'settings' => [
            'title' => $arResult['FORMS'][0]['NAME']
        ]
    ];

if ($arResult['FORMS'][1]['SHOW'])
    $arSecondFormParameters = [
        'id' => $arResult['FORMS'][1]['ID'],
        'template' => $arResult['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_2',
            'CONSENT_URL' => $arResult['CONSENT']
        ],
        'settings' => [
            'title' => $arResult['FORMS'][1]['NAME']
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

        <?php if ($arResult['FORMS'][0]['SHOW']) { ?>
            if (forms.first.length > 0) {
                forms.first.on('click', function () {
                    app.api.forms.show(<?= JavaScript::toObject($arFirstFormParameters) ?>);
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORMS'][0]['ID'].'.open') ?>);
                });
            }
        <?php } ?>
        <?php if ($arResult['FORMS'][1]['SHOW']) { ?>
            if (forms.second.length > 0) {
                forms.second.on('click', function () {
                    app.api.forms.show(<?= JavaScript::toObject($arSecondFormParameters) ?>);
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORMS'][1]['ID'].'.open') ?>);
                });
            }
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:main.form (template.6)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>