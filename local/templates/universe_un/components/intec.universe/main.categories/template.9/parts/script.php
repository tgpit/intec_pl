<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

$arOptions = [
    'items' => 1,
    'loop' => false,
    'nav' => true,
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/slider.arrow.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/slider.arrow.right.svg')
    ],
    'navClass' => [
        'owl-prev intec-cl-background-hover intec-cl-border-hover',
        'owl-next intec-cl-background-hover intec-cl-border-hover'
    ],
    'dots' => false,
    'autoHeight' => true,
    'navContainer' => ".c-categories-template-9 .widget-navigation"
];

?>

<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var owl = $('[data-role="slider"]', data.nodes);

        owl.owlCarousel(<?= JavaScript::toObject($arOptions) ?>);
    }, {
        'name': '[Component] intec.universe:main.categories (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
