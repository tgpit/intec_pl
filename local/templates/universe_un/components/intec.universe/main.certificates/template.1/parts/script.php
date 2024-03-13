<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

if (defined('EDITOR'))
    return;

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var items = $('[data-role="certificates"]', data.nodes);

        items.lightGallery({
            'selector': '.widget-item-picture'
        });
    }, {
        'name': '[Component] intec.universe:main.certificates (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>