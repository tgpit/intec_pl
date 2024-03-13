<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var filter = root.find('[data-role="filter"]');
        var buttonClear = filter.find('[data-role="clear"]');

        buttonClear.on('click', function () {
            filter.find(':input').each(function () {
                if (this.type == 'text' || this.type == 'textarea' || this.type == 'date') {
                    this.value = '';
                } else if (this.type == 'radio' || this.type == 'checkbox') {
                    this.checked = false;
                } else if (this.type == 'select-one' || this.type == 'select-multiple') {
                    this.value = '';
                }
            });
        });


    }, {
        'name': '[Component] bitrix:support.ticket.list (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>