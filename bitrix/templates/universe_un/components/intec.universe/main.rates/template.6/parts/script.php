<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        $('[data-role="scrollbar"]', data.nodes).scrollbar();
    }, {
        'name': '[Component] intec.universe:main.rates (template.6) > scroll',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'default'
        }
    });
</script>
<?php if ($arForm['USE']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var buttons = $('[data-role="rate.button"]', data.nodes);

            if (buttons.length > 0) {
                buttons.each(function () {
                    var self = $(this);

                    self.on('click', function () {
                        var parameters = <?= JavaScript::toObject($arForm['PARAMETERS']) ?>;

                        parameters.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = self.data('value');

                        app.api.forms.show(parameters);
                        app.metrika.reachGoal('forms.open');
                        app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
                    });
                });
            }
        }, {
            'name': '[Component] intec.universe:main.rates (template.6) > order',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>