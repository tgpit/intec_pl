<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 */
?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;

        root.gallery = $('[data-role="gallery"]', root);

        root.gallery.pictures = $('[data-role="gallery.pictures"]', root.gallery);

        root.gallery.pictures.slider = $('[data-role="gallery.pictures.slider"]', root.gallery.pictures);
        root.gallery.pictures.items = $('[data-role="gallery.pictures.item"]', root.gallery.pictures);

        root.gallery.pictures.slider.owlCarousel({
            'items': 1,
            'nav': true,
            'navClass': ['pictures-navigation-left intec-cl-border intec-cl-background-hover', 'pictures-navigation-right intec-cl-border intec-cl-background-hover'],
            'navText': [
                <?= JavaScript::toObject($arSvg['NAVIGATION']['LEFT']) ?>,
                <?= JavaScript::toObject($arSvg['NAVIGATION']['RIGHT']) ?>
            ],
            'dots': false
        });

        root.gallery.pictures.slider.lightGallery({
            'share': false,
            'selector': '[data-role="gallery.pictures.item.picture"]'
        });
    }, {
        'name': '[Component] bitrix:news.detail (store.default.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>