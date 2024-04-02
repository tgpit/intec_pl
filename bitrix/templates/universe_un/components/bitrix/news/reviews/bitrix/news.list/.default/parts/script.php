<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var gallery = $('[data-role="items"]', root);

        gallery.lightGallery({
            'selector': '[data-role="document"]'
        });
    }, {
        'name': '[Component] bitrix:news (reviews) > bitrix:news.list (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>