<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var items = $('[data-role="items"]', data.nodes);

        items.lightGallery({
            'selector': '[data-role="item.content"]',
            'exThumbImage': 'data-thumb'
        });
    }, {
        'name': '[Component] bitrix:photo.section (gallery.default.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>