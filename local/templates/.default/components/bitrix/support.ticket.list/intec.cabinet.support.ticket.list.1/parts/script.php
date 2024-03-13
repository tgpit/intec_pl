<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

?>
<script type="text/javascript">
    $(document).ready(function () {
        var root = $('#' + <?= JavaScript::toObject($sTemplateId) ?>);
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


    });
</script>