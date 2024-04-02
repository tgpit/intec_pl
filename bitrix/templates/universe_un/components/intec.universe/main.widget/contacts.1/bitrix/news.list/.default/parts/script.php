<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var contacts = $('[data-role="contacts"]', data.nodes);

        var initialize;
        var loader;
        var move;
        var map;

        initialize = function () {

            if (!_.isObject(window.maps))
                return false;

            map = window.maps[<?= JavaScript::toObject($arVisual['MAP']['ID']) ?>];

            if (map == null)
                return false;

            contacts.items = $('[data-role="contacts.item"]', contacts);

            contacts.items.each(function () {
                var contact = $(this);

                contact.on('click', function () {
                    var activeContact = contacts.items.filter('[data-state="enabled"]', contacts);

                    activeContact.attr('data-state', 'disabled');
                    contact.attr('data-state', 'enabled');
                    activeContact.removeClass('intec-cl-background');
                    contact.addClass('intec-cl-background');

                    move(
                        contact.data('latitude'),
                        contact.data('longitude')
                    );

                });
            });

            return true;
        };

        move = function (latitude, longitude) {
            latitude = _.toNumber(latitude);
            longitude = _.toNumber(longitude);

            <?php if ($arVisual['MAP']['VENDOR'] == 'google') { ?>
            map.panTo(new google.maps.LatLng(latitude, longitude));
            <?php } else if ($arVisual['MAP']['VENDOR'] == 'yandex') { ?>
            map.panTo([latitude, longitude]);
            <?php } ?>
        };

        <?php if ($arVisual['MAP']['VENDOR'] == 'google') { ?>
        BX.ready(initialize);
        <?php } else if ($arVisual['MAP']['VENDOR'] == 'yandex') { ?>
        loader = function () {
            var load;

            load = function () {
                if (!initialize())
                    setTimeout(load, 100);
            };

            if (window.ymaps) {
                ymaps.ready(load);
            } else {
                setTimeout(loader, 100);
            }
        };

        loader();
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:main.widget (contacts.1) > bitrix:news.list (.default) > Map',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arResult['DATA']['SLIDER']['USE']) {

    $items = $arResult['DATA']['COUNT'] > 4 ? 4 : $arResult['DATA']['COUNT'];

    $arSlider = [
        'loop' => false,
        'nav' => true,
        'navClass' => [
            Html::cssClassFromArray([
                'widget-navigation-button',
                'widget-navigation-button-left',
                'intec-cl-background',
                'intec-cl-border-light',
                'intec-cl-background-light-hover',
                'intec-ui-picture'
            ]),
            Html::cssClassFromArray([
                'widget-navigation-button',
                'widget-navigation-button-right',
                'intec-cl-background',
                'intec-cl-border-light',
                'intec-cl-background-light-hover',
                'intec-ui-picture'
            ])
        ],
        'navText' => [
            FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
            FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
        ],
        'dots' => false,
        'dotClass' => 'widget-dots-item',
        'autoplay' => false,
        'responsive' => [
            '1201' => ['items' => $items],
            '1025' => ['items' => $items],
            '769' => ['items' => $items],
            '501' => [
                'items' => $items,
                'dots' => true
            ],
            '0' => [
                'items' => 1,
                'dots' => true
            ]
        ]
    ];

    if ($items === 4)
        $arSlider['responsive']['1025']['items'] = 3;

    if ($items >= 3) {
        $arSlider['responsive']['769']['items'] = 2;
        $arSlider['responsive']['501']['items'] = 2;
    }

    unset($items);

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');
            var slider = $('[data-role="contacts.slider"]', data.nodes);
            var methods = {
                'slideToItem': function () {
                    var index = $('[data-state="enabled"]', slider).closest('.owl-item').index();

                    if (index < 0)
                        return;

                    slider.trigger('to.owl.carousel', [index, 0]);
                },
                'slideToPage': function (event) {
                    var index = $('[data-state="enabled"]', slider).closest('.owl-item').index();

                    if (index < 0)
                        return;

                    slider.trigger('to.owl.carousel', [_.toInteger(index / event.page.size), 0]);
                },
                'dotsActivity': function () {
                    var dots = $('[data-role="contacts.slider.dots"] button', data.nodes);

                    dots.removeClass('intec-cl-background intec-cl-border')
                        .filter('.active')
                        .addClass('intec-cl-background intec-cl-border');
                },
                'triggerMap': function () {
                    $('.owl-item.active [data-role="contacts.item"]', slider).trigger('click');
                }
            };

            slider.owlCarousel(_.merge(<?= JavaScript::toObject($arSlider) ?>, {
                'navContainer': $('[data-role="contacts.slider.navigation"]', data.nodes),
                'dotsContainer': $('[data-role="contacts.slider.dots"]', data.nodes),
                'onInitialized': function () {
                    setTimeout(function () {
                        methods.slideToItem();
                        methods.dotsActivity();
                    }, 1);
                },
                'onResized': function () {
                    setTimeout(function () {
                        methods.dotsActivity();
                    }, 1);
                },
                'responsive': {
                    '501': {
                        'onInitialized': function (event) {
                            setTimeout(function () {
                                methods.slideToPage(event);
                                methods.dotsActivity();
                            }, 1);
                        },
                        'onTranslated': function () {
                            methods.dotsActivity();
                        }
                    },
                    '0': {
                        'onInitialized': function (event) {
                            setTimeout(function () {
                                methods.slideToPage(event);
                                methods.dotsActivity();
                            }, 1);
                        },
                        'onTranslated': function () {
                            methods.dotsActivity();
                            methods.triggerMap();
                        }
                    }
                }
            }));
        }, {
            'name': '[Component] intec.universe:main.widget (contacts.1) > bitrix:news.list (.default) > Slider',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if (!empty($arResult['DATA']['FORM']['SHOW'])) {

    if (empty($arResult['DATA']['FORM']['TITLE']))
        $arResult['DATA']['FORM']['TITLE'] = Loc::getMessage('C_NEWS_LIST_CONTACTS_2_TEMPLATE_FORM_TITLE_DEFAULT');

    $arForm = [
        'id' => $arResult['DATA']['FORM']['ID'],
        'template' => $arResult['DATA']['FORM']['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_form',
            'CONSENT_URL' => $arVisual['CONSENT']['SHOW'] ? $arVisual['CONSENT']['URL'] : null
        ],
        'settings' => [
            'title' => $arResult['DATA']['FORM']['TITLE']
        ]
    ];

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var form = $('[data-role="form"]', data.nodes);

            form.on('click', function () {
                app.api.forms.show(<?= JavaScript::toObject($arForm) ?>);
                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<? JavaScript::toObject('forms.'.$arResult['DATA']['FORM']['ID'].'.open')?>);
            });
        }, {
            'name': '[Component] intec.universe:main.widget (contacts.1) > bitrix:news.list (.default) > Feedback',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
    <?php unset($arForm) ?>
<?php } ?>