<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var gallery = $('[data-role="gallery"]', data.nodes);

        gallery.lightGallery({
            'exThumbImage': 'data-thumb',
            'selector': '[data-role="gallery.item"]',
            'share': false
        });
    }, {
        'name': '[Component] bitrix:news.list (reviews.list.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>