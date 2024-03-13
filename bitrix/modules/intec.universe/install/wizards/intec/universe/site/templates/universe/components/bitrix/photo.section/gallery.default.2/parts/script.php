<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arSvg
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var component = {
            'main': {
                'container': $('[data-role="main"]', data.nodes),
                'slider': {
                    'container': $('[data-role="main.slider"]', data.nodes),
                    'loader': $('[data-role="main.slider.loader"]', data.nodes),
                    'navigation': $('[data-role="main.slider.navigation"]', data.nodes)
                },
                'count': {
                    'container': $('[data-role="main.count"]', data.nodes),
                    'loader': $('[data-role="main.count.loader"]', data.nodes),
                    'content': $('[data-role="main.count.content"]', data.nodes),
                    'value': $('[data-role="main.count.value"]', data.nodes)
                }
            },
            'preview': {
                'container': $('[data-role="preview"]', data.nodes),
                'loader':$('[data-role="preview.loader"]', data.nodes),
                'slider': {
                    'container': $('[data-role="preview.slider"]', data.nodes),
                    'navigation': $('[data-role="preview.navigation"]', data.nodes),
                    'items': $('[data-role="preview.item"]', data.nodes)
                }
            },
            'nav': {
                'class': {
                    'left': <?= JavaScript::toObject(Html::cssClassFromArray([
                        'photo-section-navigation-left',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover',
                        'intec-ui-picture',
                    ])) ?>,
                    'right': <?= JavaScript::toObject(Html::cssClassFromArray([
                        'photo-section-navigation-right',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover',
                        'intec-ui-picture',
                    ])) ?>,
                },
                'svg': {
                    'left': <?= JavaScript::toObject($arSvg['NAVIGATION']['LEFT']) ?>,
                    'right': <?= JavaScript::toObject($arSvg['NAVIGATION']['RIGHT']) ?>
                }
            },
            'methods': {
                'slide': function (slider, index) {
                    slider.trigger('to.owl.carousel', index);
                }
            }
        };

        component.main.slider.container.owlCarousel({
            'items': 1,
            'lazyLoad': true,
            'dots': false,
            'nav': true,
            'navContainer': component.main.slider.navigation,
            'navClass': [
                component.nav.class.left,
                component.nav.class.right
            ],
            'navText': [
                component.nav.svg.left,
                component.nav.svg.right
            ],
            'onInitialized': function () {
                component.main.container.attr('data-loaded', true);
                component.main.slider.container.attr('data-loaded', true);
                component.main.count.container.attr('data-loaded', true);
                component.main.count.content.attr('data-loaded', true);
                component.main.slider.loader.remove();
                component.main.count.loader.remove();
            },
            'onTranslated': function (event) {
                component.methods.slide(component.preview.slider.container, event.item.index);
                component.preview.slider.items.eq(event.item.index).trigger('click');
            }
        });

        component.preview.slider.container.owlCarousel({
            'stagePadding': 2,
            'margin': 16,
            'lazyLoad': true,
            'lazyLoadEager': 1,
            'dots': false,
            'nav': true,
            'navContainer': component.preview.slider.navigation,
            'navClass': [
                component.nav.class.left,
                component.nav.class.right
            ],
            'navText': [
                component.nav.svg.left,
                component.nav.svg.right
            ],
            'onInitialized': function () {
                component.preview.slider.items.each(function () {
                    var self = $(this);
                    var index = self.closest('.owl-item').index();

                    self.on('click', function () {
                        component.main.count.value.html(index + 1);
                        component.methods.slide(component.main.slider.container, index);
                        component.preview.slider.items.removeClass('intec-cl-border');
                        self.addClass('intec-cl-border');
                    });
                });

                component.preview.slider.items.eq(0).trigger('click');
                component.preview.container.attr('data-loaded', true);
                component.preview.slider.container.attr('data-loaded', true);
                component.preview.loader.remove();
            },
            'responsive': {
                '1025': {
                    'items': 6,
                },
                '769': {
                    'items': 5
                },
                '601': {
                    'items': 4,
                    'stagePadding': 40
                },
                '451': {
                    'items': 3,
                    'margin': 8,
                    'stagePadding': 24
                },
                '0': {
                    'items': 2,
                    'margin': 4,
                    'stagePadding': 16
                }
            }
        });

        component.main.slider.container.lightGallery({
            'share': false,
            'selector': '[data-role="main.slider.item"]',
            'exThumbImage': 'data-exthumbimage'
        });
    }, {
        'name': '[Component] bitrix:photo.section (gallery.default.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>