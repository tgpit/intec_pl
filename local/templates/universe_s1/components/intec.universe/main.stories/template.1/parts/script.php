<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
use Bitrix\Main\Localization\Loc;

/**
 * @var string $sTemplateId
 */

$arSlider = [
    'navContainer' => '[data-role="slider.navigation"]',
    'navClass' => [
        'navigation-left intec-cl-background-hover intec-cl-border-hover',
        'navigation-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'responsive' => [
        '480' => [
            'items' => 3,
            'margin' => 25
        ],
        '580' => [
            'items' => 4
        ],
        '870' => [
            'items' => 5
        ]
    ]
];

if ($arVisual['COLUMNS'] >= 6)
    $arSlider['responsive']['950'] = ['items' => 6];

if ($arVisual['COLUMNS'] >= 7)
    $arSlider['responsive']['1100'] = ['items' => 7];

if ($arVisual['COLUMNS'] >= 8)
    $arSlider['responsive']['1200'] = ['items' => 8];

$arData = [
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'SECTIONS_MODE' => $arParams['SECTIONS_MODE'],
    'SECTIONS' => $arParams['SECTIONS'],
    'ELEMENTS_COUNT' => $arParams['ELEMENTS_COUNT'],
    'ELEMENT_ITEMS_COUNT' => $arParams['ELEMENT_ITEMS_COUNT'],
    'POPUP_TIME' => $arParams['POPUP_TIME'],
    'PROPERTY_BUTTON_TEXT' => $arParams['PROPERTY_BUTTON_TEXT'],
    'PROPERTY_LINK' => $arParams['PROPERTY_LINK'],
    'BUTTON_TEXT' => !empty($arParams['BUTTON_TEXT']) ? $arParams['BUTTON_TEXT'] : Loc::getMessage('C_MAIN_STORIES_TEMPLATE_1_BUTTON_TEXT_DEFAULT'),
    'POPUP_ACTIVE' => 'Y',
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'SORT_BY' => $arParams['SORT_BY'],
    'ORDER_BY' => $arParams['ORDER_BY']
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var app = this;
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);
        var sliderSettings = <?= JavaScript::toObject($arSlider) ?>;
        var dataView = <?= JavaScript::toObject($arData) ?>;
        var sliderItems = $('[data-role="story.item"]', root);
        var showMoreButton = {
            'show': <?= JavaScript::toObject($arVisual['LIST']['BUTTONS']['MORE']['SHOW']) ?>,
            'button': null
        };

        if (showMoreButton.show) {
            showMoreButton.button = $('[data-role="show.more"]', root);

            showMoreButton.button.on('click', function () {
                dataView['POPUP_SELECTED'] = sliderItems.first().data('id');
                app.api.components.show({
                    'component': 'intec.universe:main.stories',
                    'template': 'template.1',
                    'parameters': dataView,
                    'settings': {
                        'parameters': {
                            'className': 'popup-window-stories',
                            'width': null
                        }
                    }
                });
            });
        }


        slider.on('changed.owl.carousel', function(event) {
            updateNavigationButton(this);
        });

        slider.on('translated.owl.carousel', function(event) {
            updateNavigationButton(this);
        });

        slider.on('resized.owl.carousel', function(event) {
            updateNavigationButton(this);
        });

        slider.owlCarousel({
            'loop': false,
            'margin': 10,
            'responsive': sliderSettings.responsive,
            'dots': false,
            'navText': sliderSettings.navText,
            'navContainer': $(sliderSettings.navContainer, root),
            'navClass': sliderSettings.navClass
        });

        sliderItems.on('click', function () {
            dataView['POPUP_SELECTED'] = $(this).data('id');
            app.api.components.show({
                'component': 'intec.universe:main.stories',
                'template': 'template.1',
                'parameters': dataView,
                'settings': {
                    'parameters': {
                        'className': 'popup-window-stories',
                        'width': null
                    }
                }
            });
        });

        function updateNavigationButton (container) {
            setTimeout(function () {
                var counter = 0;
                var prevButton = $('button:first', sliderSettings.navContainer, root);
                var nextButton = $('button:last',  sliderSettings.navContainer, root);

                if ($('.owl-item:first', container).hasClass('active')) {
                    $(prevButton).addClass('disabled');
                    counter++;
                } else if ($(prevButton).hasClass('disabled')) {
                    $(prevButton).removeClass('disabled');
                }

                if ($('.owl-item:last', container).hasClass('active')) {
                    $(nextButton).addClass('disabled');
                    counter++;
                } else if ($(nextButton).hasClass('disabled')) {
                    $(nextButton).removeClass('disabled');
                }

                if (counter >= 2 )
                    $('.widget-items-navigation', root).addClass('disabled');
                else
                    $('.widget-items-navigation', root).removeClass('disabled');
            }, 0);
        }

    }, {
        'name': '[Component] intec.universe:main.stories (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arSlider, $arData); ?>