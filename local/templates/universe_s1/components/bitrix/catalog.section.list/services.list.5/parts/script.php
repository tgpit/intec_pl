<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var item = $('[data-role="item"]', root);

        item.each(function () {
            var self = $(this);
            var children = $('[data-role="children"]', self);
            var button = $('[data-role="button"]', self);

            button.on('click', function () {
                var clicked = $(this);
                var state = self.attr('data-expanded') === 'true';
                var height = {
                    'item': {
                        'current': self.height(),
                        'modified': null
                    },
                    'children': {
                        'current': children.height(),
                        'modified': null
                    }
                };

                self.css('pointer-events', 'none');
                clicked.css('pointer-events', 'none');

                if (state) {
                    children.css('display', 'none');
                    children.css('height', height.children.current);
                    height.item.modified = self.height();
                    height.children.modified = 0;
                    children.css('display', '');

                    self.css('height', height.item.current);

                    setTimeout(function () {
                        self.attr('data-expanded', !state);
                        button.attr('data-expanded', !state);
                        self.css('height', height.item.modified);
                        children.css('height', height.children.modified);

                        setTimeout(function () {
                            self.css({
                                'height': '',
                                'pointer-events': ''
                            });
                            children.css('height', '');
                            clicked.css('pointer-events', '');
                        }, 310);
                    }, 10);
                } else {
                    children.css('height', 'auto');
                    height.item.modified = self.height();
                    height.children.modified = children.height();
                    children.css('height', 0);

                    self.css('height', height.item.current);

                    setTimeout(function () {
                        self.attr('data-expanded', !state);
                        button.attr('data-expanded', !state);
                        self.css('height', height.item.modified);
                        children.css('height', height.children.modified);

                        setTimeout(function () {
                            self.css({
                                'height': '',
                                'pointer-events': ''
                            });
                            children.css('height', '');
                            clicked.css('pointer-events', '');
                        }, 310);
                    }, 10);
                }
            });
        });
    }, {
        'name': '[Component] bitrix:catalog.section.list (services.list.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>