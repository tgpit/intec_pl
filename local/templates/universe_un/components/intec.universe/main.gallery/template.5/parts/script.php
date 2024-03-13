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

        $('[data-role="items"]', data.nodes).lightGallery({
            'selector': '[data-role="item"]',
            'exThumbImage': 'data-preview-src'
        });
    }, {
        'name': '[Component] intec.universe:main.gallery (template.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
