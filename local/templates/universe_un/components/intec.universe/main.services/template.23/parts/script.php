<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var array $arForm
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        $('[data-role="scrollbar"]', data.nodes).scrollbar();
    }, {
        'name': '[Component] intec.universe:main.services (template.23) > scroll',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'default'
        }
    });
</script>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var items = $('[data-role="items"]', data.nodes);
        var checkbox = [];
        var updateButton;
        var button = {};

        button.order = $('[data-role="button.order"]', data.nodes);
        button.clear = $('[data-role="button.clear"]', data.nodes);
        checkbox.all = $('[data-role="checkbox.all"]', data.nodes);
        checkbox.items = $('[data-role="checkbox.item"]', data.nodes);

        function declOfNum(number, titles) {
            var cases = [2, 0, 1, 1, 1, 2];
            return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
        }

        updateButton = function() {
            var counter = $('[data-role="checkbox.item"]:checked', data.nodes).length;
            var buttonText = '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT') ?>';

            var text = declOfNum(counter, [
                '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT_2_1') ?>',
                '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT_2_3') ?>',
                '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT_2_2') ?>'
            ]);

            if (counter > 0) {
                buttonText = '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT') ?> ' + counter + ' ' + text;
            }

            button.order.html(buttonText);
        };

        checkbox.all.on('change', function() {
            var self = $(this);
            var checked = '';

            if (self.is(":checked")) {
                checked = 'checked';
            } else {
                checked = '';
            }

            checkbox.items.each(function() {
                $(this).prop('checked', checked);
            });

            updateButton();
        });


        checkbox.items.on('change', function () {
            updateButton();
        });

        button.clear.on('click', function() {
            checkbox.items.each(function() {
                $(this).prop('checked', '');
            });

            checkbox.all.prop('checked', '');

            updateButton();
        });

        button.order.on('click', function() {
            var options = <?= JavaScript::toObject([
                'id' => $arForm['ID'],
                'template' => $arForm['TEMPLATE'],
                'parameters' => [
                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                    'CONSENT_URL' => $arForm['CONSENT']
                ],
                'settings' => [
                    'title' => $arForm['TITLE']
                ]
            ]) ?>;

            options.fields = {};

            <?php if (!empty($arForm['FIELD'])) { ?>
            var items = [];
            var name = '';

            checkbox.items.each(function() {
                var self = $(this);

                if (self.is(":checked")) {
                    var value;

                    value = self.closest('[data-role="item"]').find('[data-role="item.name"]').html();

                    items.push(value);
                }
            });

            name = items.join(', ');

            options.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = name;
            <?php } ?>

            app.api.forms.show(options);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
        });
    }, {
        'name': '[Component] intec.universe:main.services (template.23)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
