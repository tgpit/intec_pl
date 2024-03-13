<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 * @var array $arForm
 * @var string $sTemplateId
 */

$arSlider = [
    'items' => 1,
    'nav' => $arVisual['SLIDER']['NAV'],
    'navClass' => [
        'owl-prev intec-cl-background-hover intec-cl-border-hover',
        'owl-next intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'dots' => false
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var gallery = $('[data-role="items"]', data.nodes);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        gallery.owlCarousel({
            'items': settings.items,
            'nav': settings.nav,
            'navClass': settings.navClass,
            'navText': settings.navText,
            'dots': false,
            'autoHeight': true
        });
    }, {
        'name': '[Component] intec.universe:main.staff (template.5) > slider',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arForm['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var form = {
                'nodes': $('[data-role="form"]', data.nodes),
                'parameters': <?= JavaScript::toObject($arForm['PARAMETERS']) ?>
            };

            form.nodes.each(function () {
                var self = $(this);

                self.on('click', function () {
                    form.parameters.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = self.attr('data-name');

                    app.api.forms.show(form.parameters);
                    form.parameters.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = null;
                    app.metrika.reachGoal('forms.open');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
                });
            });
        }, {
            'name': '[Component] intec.universe:main.staff (template.5) > form',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>