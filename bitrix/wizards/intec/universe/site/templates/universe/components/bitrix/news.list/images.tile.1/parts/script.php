<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        var items = $('[data-role="item"]', root);

        items.hover(function () {
            $('[data-role="item.name"]', this).addClass('intec-cl-text');
        }, function () {
            $('[data-role="item.name"]', this).removeClass('intec-cl-text');
        });

    }, {
        'name': '[Component] bitrix:news.list (images.tile.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>