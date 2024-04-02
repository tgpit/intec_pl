<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var field = $('[data-role="field"]', data.nodes);
        var input = $('[data-role="input"]', data.nodes);

        field.on('click', function(){
            $(this).attr('data-active', 'true');
        });

        field.on('focusin', function() {
            $(this).attr('data-active', 'true');
        });

        input.on('focusout', function() {
            var self = $(this);
            var field = self.closest('[data-role="field"]');
            var input = self;

            if (input.length) {
                if (input.val().length === 0)
                    field.attr('data-active', 'false');
            }
        });
    }, {
        'name': '[Component] intec.universe:main.widget (form.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arVisual['CONSENT']['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var component = {};

            component.form = $('form', data.nodes);
            component.consent = $('[name="licenses_popup"]', component.form);
            component.submit = $('[type="submit"]', component.form);

            if (!component.form.length || !component.consent.length || !component.submit.length)
                return;

            component.handler = {
                'submit': function () {
                    return component.consent.prop('checked');
                }
            };

            component.form.on('submit', component.handler.submit);
        }, {
            'name': '[Component] intec.universe:main.widget (form.3)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>