<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

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
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_ASK',
        'CONSENT_URL' => $arResult['FORM']['ASK']['CONSENT']['URL']
    ],
    'fields' => [],
    'settings' => [
        'title' => $arResult['FORM']['ASK']['TITLE']
    ]
];

if (empty($arFormAsk['settings']['title']))
    $arFormAsk['settings']['title'] = Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_TEMPLATE_FORM_ASK_TITLE_DEFAULT');

if (!empty($arResult['FORM']['ASK']['FIELD']))
    $arFormAsk['fields'][$arResult['FORM']['ASK']['FIELD']] = $arResult['NAME'];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        var root = data.nodes;
        var buttonAsk = $('[data-role="button.ask"]', root);

        buttonAsk.on('click', function () {
            app.api.forms.show(<?= JavaScript::toObject($arFormAsk) ?>);
        });
    }, {
        'name': '[Component] bitrix:news.detail (staff.default.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
