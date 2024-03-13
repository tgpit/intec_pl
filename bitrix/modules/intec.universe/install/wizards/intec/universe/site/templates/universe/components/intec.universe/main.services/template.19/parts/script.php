<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var tabs = $('[data-role="services.tabs"]', data.nodes);
        var content = $('[data-role="services.content"]', data.nodes);

        if (tabs.length && content.length) {
            tabs.items = $('[data-role="services.tabs.item"]', tabs);
            content.items = $('[data-role="services.content.item"]', content);

            tabs.items.on('click', function () {
                var self = $(this);
                var active = self.attr('data-active') === 'true';

                if (!active) {
                    var id = self.attr('data-id');

                    tabs.items.attr('data-active', false)
                        .removeClass('intec-cl-border');

                    content.items.attr('data-active', false);

                    self.attr('data-active', true)
                        .addClass('intec-cl-border');

                    content.items.filter('[data-id=' + id + ']')
                        .attr('data-active', true);
                }
            });
        }
    }, {
        'name': '[Component] intec.universe:main.services (template.19) > tabs',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>