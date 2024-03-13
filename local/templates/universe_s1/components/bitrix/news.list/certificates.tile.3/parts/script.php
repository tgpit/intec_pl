<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function(data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var content = $('[data-role="content"]', root);

        content.lightGallery({
            selector: '[data-role="zoom"]',
            exThumbImage: 'data-preview-src',
            autoplay: false,
            share: false
        });
    }, {
        'name': '[Component] bitrix:news.list (certificates.tile.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>