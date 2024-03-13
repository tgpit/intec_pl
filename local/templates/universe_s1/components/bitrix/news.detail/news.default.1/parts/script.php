<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<?php if ($arVisual['ANCHORS']['USE']) {

    $arAnchorsSvg = [
        'LEFT' => FileHelper::getFileData(__DIR__.'/../svg/anchors.navigation.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/../svg/anchors.navigation.right.svg')
    ];

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var body = $('html, body');
            var slider = $('[data-role="news.anchors.slider"]', data.nodes);
            var navigation = $('[data-role="news.anchors.navigation"]', data.nodes);
            var items = $('[data-role="news.anchors.item"]', slider);
            var position = data.nodes.attr('data-position');

            slider.owlCarousel({
                'autoWidth': true,
                'margin': 40,
                'nav': true,
                'navContainer': navigation,
                'navElement': 'div',
                'navClass': [
                    'news-detail-anchors-navigation-left intec-ui-picture intec-cl-svg-path-stroke-hover',
                    'news-detail-anchors-navigation-right intec-ui-picture intec-cl-svg-path-stroke-hover'
                ],
                'navText': [
                    <?= JavaScript::toObject($arAnchorsSvg['LEFT']) ?>,
                    <?= JavaScript::toObject($arAnchorsSvg['RIGHT']) ?>
                ],
                'onDrag': function () {
                    items.css('pointer-events', 'none');
                },
                'onDragged': function () {
                    items.css('pointer-events', '');
                }
            });

            items.each(function () {
                var self = $(this);
                var id = self.attr('href');

                self.on('click', function (event) {
                    event.preventDefault();

                    var offset;
                    var element = $(id);

                    if (position === 'fixed')
                        offset = data.nodes.outerHeight() + data.nodes.position().top;
                    else
                        offset = data.nodes.outerHeight();

                    items.css('pointer-events', 'none');

                    body.animate({'scrollTop': element.offset().top - offset}, 600, function () {
                        items.css('pointer-events', '');
                    });
                });
            });

            if (position === 'default') {
                var _ = this.getLibrary('_');
                var template = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
                var update = function () {
                    var show = body.scrollTop() > template.offset().top;
                    var visible = data.nodes.attr('data-visible') === 'true';

                    if (show) {
                        if (visible)
                            return;

                        data.nodes.attr('data-visible', true)
                            .css('top', '');
                    } else {
                        if (!visible)
                            return;

                        data.nodes.attr('data-visible', false)
                            .css('top', '-' + data.nodes.outerHeight() + 'px');
                    }
                };

                data.nodes.css('top', '-' + data.nodes.outerHeight() + 'px');

                document.addEventListener('scroll', _.debounce(update, 20));

                setTimeout(function () {
                    data.nodes.attr('data-initialized', true);
                    update();
                }, 5);
            }
        }, {
            'name': '[Component] bitrix:news.detail (news.default.1) > anchors',
            'nodes': <?= JavaScript::toObject('#news-news-1-detail-anchors') ?>
        });
    </script>
    <?php unset($arAnchorsSvg) ?>
<?php } ?>
<?php if ($arVisual['PRINT']['SHOW']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var print = $('[data-role="print"]', data.nodes);

            print.on('click', function () {
                window.print();
            });
        }, {
            'name': '[Component] bitrix:news.detail (news.default.1) > print',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
