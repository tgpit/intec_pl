<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

if ($arResult['FORM']['ASK']['USE']) {
    $arFormAsk = [
        'id' => $arResult['FORM']['ASK']['ID'],
        'template' => $arResult['FORM']['ASK']['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_ASK',
            'CONSENT_URL' => $arResult['FORM']['ASK']['CONSENT']['URL']
        ],
        'settings' => [
            'title' => $arResult['FORM']['ASK']['TITLE']
        ]
    ];

    if (empty($arFormAsk['settings']['title']))
        $arFormAsk['settings']['title'] = Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_TEMPLATE_FORM_ASK_TITLE_DEFAULT');
}

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        var root = data.nodes;
        var item = $('[data-role="item"]', root);
        <?php if ($arResult['FORM']['ASK']['USE']) { ?>
        var formAsk = <?= JavaScript::toObject($arFormAsk) ?>;
        <?php } ?>

        item.each(function () {
            var self = $(this);
            var isAdditional = self.attr('data-additional') === 'true';

            if (isAdditional) {
                var name = $('[data-role="item.name"]', self);
                var text = $('[data-role="item.text"]', self);
                var base = $('[data-role="item.text.base"]', text);
                var additional = $('[data-role="item.text.additional"]', text);
                var additionalHeight;

                <?php if ($arResult['FORM']['ASK']['USE']) { ?>
                var buttonAsk = $('[data-role="item.button"]', self);
                <?php } ?>

                self.on('mouseenter', function () {
                    text.css('height', text.height());
                    additionalHeight = additional.height();
                    self.attr('data-expanded', 'true');
                    base.css('padding-bottom', additionalHeight);
                }).on('mouseleave', function () {
                    self.attr('data-expanded', 'false');
                    base.css('padding-bottom', '');
                    text.css('height', '');
                });

                <?php if ($arResult['FORM']['ASK']['USE']) { ?>
                buttonAsk.on('click', function () {
                    <?php if (!empty($arResult['FORM']['ASK']['FIELD'])) { ?>
                    formAsk.fields = {};
                    formAsk.fields[<?= JavaScript::toObject($arResult['FORM']['ASK']['FIELD']) ?>] = name.text();
                    <?php } ?>

                    app.api.forms.show(formAsk);
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ASK']['ID'].'.open') ?>);
                });
                <?php } ?>
            }
        });
    }, {
        'name': '[Component] bitrix:news.list (staff.blocks.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
