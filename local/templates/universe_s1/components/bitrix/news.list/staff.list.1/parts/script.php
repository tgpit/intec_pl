<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$arFormAsk = [
    'id' => $arResult['FORM']['ASK']['ID'],
    'template' => $arResult['FORM']['ASK']['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ASK',
        'CONSENT_URL' => $arResult['FORM']['ASK']['CONSENT']['URL']
    ],
    'settings' => [
        'title' => $arResult['FORM']['ASK']['TITLE']
    ]
];

if (empty($arFormAsk['settings']['title']))
    $arFormAsk['settings']['title'] = Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_TEMPLATE_FORM_ASK_TITLE_DEFAULT');

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        var root = data.nodes;
        var item = $('[data-role="item"]', root);
        var formAsk = <?= JavaScript::toObject($arFormAsk) ?>;

        item.each(function () {
            var self = $(this);
            var name = $('[data-role="item.name"]', self);
            var buttonAsk = $('[data-role="item.button"]', self);

            buttonAsk.on('click', function () {
                <?php if (!empty($arResult['FORM']['ASK']['FIELD'])) { ?>
                    formAsk.fields = {};
                    formAsk.fields[<?= JavaScript::toObject($arResult['FORM']['ASK']['FIELD']) ?>] = name.text();
                <?php } ?>

                app.api.forms.show(formAsk);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ASK']['ID'].'.open') ?>);
            });
        });
    }, {
        'name': '[Component] bitrix:news.list (staff.list.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>