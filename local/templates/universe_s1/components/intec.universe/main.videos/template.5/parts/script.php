<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arVisual['COLUMNS'] = 5;

$arResponsive = [
    '0' => ['items' => 2]
];

/*if ($arVisual['COLUMNS'] >= 2)
    $arResponsive['551'] = ['items' => 3];
*/
if ($arVisual['COLUMNS'] >= 3)
    $arResponsive['451'] = ['items' => 3];

if ($arVisual['COLUMNS'] >= 4)
    $arResponsive['768'] = ['items' => 4];

if ($arVisual['COLUMNS'] >= 5)
    $arResponsive['1051'] = ['items' => 5];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var frame = $('[data-role="view"]', data.nodes);
        var container = $('[data-role="items"]', data.nodes);
        var items = $('[data-role="item"]', container);
        var slider = $('[data-role="slider"]', container);

        container.lightGallery({
            'selector': '[data-role="view"]'
        });

        slider.owlCarousel(<?= JavaScript::toObject([
            'items' => $arVisual['COLUMNS'],
            'autoplay' => false,
            'loop' => false,
            'margin' => 16,
            'nav' => true,
            'navText' => [
                FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
                FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
            ],
            'navContainerClass' => 'intec-ui intec-ui-control-navigation',
            'navClass' => [
                'intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover',
                'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover'
            ],
            'dots' => false,
            'responsive' => $arResponsive
        ]) ?>);

        items.each(function () {
            var self = $(this);

            self.on('click', function () {
                var id = self.attr('data-id');
                var picture = self.attr('data-picture-src');

                items.attr('data-active', 'false');
                items.removeClass('intec-cl-text');

                self.attr('data-active', 'true');
                self.addClass('intec-cl-text');

                frame.attr('data-src', 'https://www.youtube.com/embed/' + id);
                frame.css('background-image', 'url(\'' + picture + '\')');
            });
        });

        (function () {
            items.eq(0)
                .attr('data-active', 'true')
                .addClass('intec-cl-text');
        })();
    }, {
        'name': '[Component] intec.universe:main.videos (template.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>