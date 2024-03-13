<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var root = data.nodes;

        button = $('[data-role="item.button"]', root);

        //button.on('click', function () {
            app.api.basket.on('add', function () {
                location.reload();
            });
        //});
    }, {
        'name': '[Component] bitrix:catalog.item (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
