<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string sTemplateId
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

        items.each(function () {
            var self = $(this);
            var diagramm = $('[data-role="item.diagramm"]', self);
            var maxValue = diagramm.attr('data-max-value');

            circle = Circles.create({
                'id': diagramm.attr('id'),
                'radius': 80,
                'value': 0,
                'maxValue': maxValue,
                'width': 8,
                'styleText': true,
                'text': function(value){ return Math.round(value);},
                'colors': ['#E7F1FF', '#0065ff'],
                'duration': 1000
            });

            myCircles.push(circle);
        });

        $(document).on('scroll', function () {
            items.each(function () {
                var self = $(this);
                var isVisible = isElementViewed(self, thisWindow);

                if (isVisible) {
                    var diagramm = $('[data-role="item.diagramm"]', self);
                    var animated = diagramm.attr('data-animated');

                    if (animated === 'false') {
                        var diagrammIndex = self.index();
                        var value = diagramm.attr('data-value');

                        animated = 'true';
                        diagramm.attr('data-animated', animated);

                        myCircles[diagrammIndex].update(value);
                    }
                }
            });
        });
    }, {
        'name': '[Component] intec.universe:main.advantages (template.33)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
