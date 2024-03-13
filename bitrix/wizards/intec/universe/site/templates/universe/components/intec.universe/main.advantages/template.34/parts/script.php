<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var thisWindow = $(window);
        var root = data.nodes;
        var items = $('[data-role="item"]', root);
        var myCircles = [];

        function isElementViewed($element, $window) {
            var docViewTop = $window.scrollTop();
            var docViewBottom = docViewTop + $window.height();

            var elementTop = $element.offset().top;
            var elementBottom = elementTop + $element.height();

            return ((elementBottom <= docViewBottom) && (elementTop >= docViewTop));
        }

        $(document).on('scroll', function () {
            items.each(function () {
                var self = $(this);
                var isVisible = isElementViewed(self, thisWindow);

                if (isVisible) {
                    var diagramm = $('[data-role="item.diagramm"]', self);
                    var animated = diagramm.attr('data-animated');

                    if (animated === 'false') {

                        animated = 'true';
                        diagramm.attr('data-animated', animated);

                        var value = diagramm.attr('data-value');
                        var maxValue = diagramm.attr('data-max-value');

                        var bLine = $('[data-role="diagramm.line"]', diagramm);
                        var bSubLine = $('span', bLine);

                        var percentage = 0;
                        percentage = (value * 100) / maxValue;

                        bSubLine.css('width', percentage+'%');

                    }
                }
            });
        });
    }, {
        'name': '[Component] intec.universe:main.advantages (template.34)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
