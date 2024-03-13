<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arData
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var button = $('[data-action="forms.call.open"]', data.nodes);

        button.on('click', function () {
            app.api.forms.show(<?= JavaScript::toObject([
                'id' => $arResult['FORMS']['CALL']['ID'],
                'template' => $arResult['FORMS']['CALL']['TEMPLATE'],
                'parameters' => [
                    'AJAX_OPTION_ADDITIONAL' => $arData['id'].'_FORM_CALL',
                    'CONSENT_URL' => $arResult['URL']['CONSENT']
                ],
                'settings' => [
                    'title' => $arResult['FORMS']['CALL']['TITLE']
                ]
            ]) ?>);

            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORMS']['CALL']['ID'].'.open') ?>);
        });
    }, {
        'name': '[Component] intec.universe:main.footer (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$arData['id']) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
