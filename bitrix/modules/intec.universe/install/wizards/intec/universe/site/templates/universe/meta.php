<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use intec\Core;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;
use intec\constructor\Module as Constructor;
use intec\constructor\models\Build;
use intec\constructor\models\build\File;
use intec\constructor\models\build\preset\Group as PresetGroup;
use intec\constructor\models\build\presets\Component as ComponentPreset;
use intec\constructor\models\Font;
use intec\core\helpers\Type;
use intec\core\io\Path;

/**
 * @var Build $this
 */

Loc::loadMessages(__FILE__);

$oFonts = Font::findAvailable()->indexBy('code');
$oFont = $oFonts->getFirst();

$meta = [
    'solution' => 'universe',
    'files' => [
        ['path' => 'css/bundle.css', 'type' => File::TYPE_CSS],
        ['path' => 'css/scheme.scss', 'type' => File::TYPE_SCSS],
        ['path' => 'css/elements.scss', 'type' => File::TYPE_SCSS],
        ['path' => 'js/bundle.js', 'type' => File::TYPE_JAVASCRIPT],
        [
            'content' => '<script type="text/javascript">
                $(function () {
                    window.template = window.template('.JavaScript::toObject([
                        'debug' => false,
                        'environment' => Core::$app->browser->getIsMobile() || Core::$app->browser->getIsTablet() ? (Core::$app->browser->getIsMobile() ? 'mobile' : 'tablet') : 'desktop',
                        'language' => LANGUAGE_ID,
                        'public' => !defined('EDITOR'),
                        'site' => [
                            'id' => SITE_ID,
                            'directory' => SITE_DIR
                        ],
                        'template' => [
                            'id' => SITE_TEMPLATE_ID,
                            'directory' => SITE_TEMPLATE_PATH.'/'
                        ],
                        'styles' => [
                            SITE_TEMPLATE_PATH.'/icons/fontawesome/style.min.css',
                            SITE_TEMPLATE_PATH.'/icons/glyphter/style.min.css',
                            SITE_TEMPLATE_PATH.'/icons/intec/style.min.css'
                        ]
                    ], true).');
                });
            </script>',
            'type' => File::TYPE_VIRTUAL
        ]
    ],
    'properties-categories' => [
        'base' => ['name' => Loc::getMessage('template.meta.properties-categories.base')],
        'main' => ['name' => Loc::getMessage('template.meta.properties-categories.main')],
        'header' => ['name' => Loc::getMessage('template.meta.properties-categories.header')],
        'catalog' => ['name' => Loc::getMessage('template.meta.properties-categories.catalog')],
        'catalog-sections' => ['name' => Loc::getMessage('template.meta.properties-categories.catalog-sections')],
        'catalog-detail' => ['name' => Loc::getMessage('template.meta.properties-categories.catalog-detail')],
        'services' => ['name' => Loc::getMessage('template.meta.properties-categories.services')],
        'basket' => ['name' => Loc::getMessage('template.meta.properties-categories.basket')],
        'profile' => ['name' => Loc::getMessage('template.meta.properties-categories.profile')],
        'sections' => ['name' => Loc::getMessage('template.meta.properties-categories.sections')],
        'footer' => ['name' => Loc::getMessage('template.meta.properties-categories.footer')],
        'mobile' => ['name' => Loc::getMessage('template.meta.properties-categories.mobile')]
    ],
    'properties' => [
        'template-color' => [
            'name' => Loc::getMessage('template.meta.properties.template-color'),
            'type' => 'color',
            'category' => 'base',
            'default' => '#13181f',
            'values' => [
                '#69102f', '#e05615', '#383b47',
                '#074d90', '#d03349', '#1e8988',
                '#5bcab2', '#352ca6', '#f78e16',
                '#8dc6c7', '#772056', '#838ed9',
                '#143a52', '#81ae64', '#ff6f3c',
                '#f5b553', '#388e3c', '#44558f',
                '#2bb3c0', '#303481', '#0065ff',
                '#3498db', '#c50000'
            ]
        ],
        'template-background-show' => [
            'name' => Loc::getMessage('template.meta.properties.template-background-show'),
            'type' => 'boolean',
            'category' => 'base',
            'default' => false
        ],
        'template-background-color' => [
            'name' => Loc::getMessage('template.meta.properties.template-background-color'),
            'type' => 'color',
            'category' => 'base',
            'default' => '#c8c8cd',
            'values' => [
                '#fff', '#c8c8cd'
            ]
        ],
        'template-width' => [
            'name' => Loc::getMessage('template.meta.properties.template-width'),
            'type' => 'list',
            'category' => 'base',
            'default' => 1200,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.template-width.1200'),
                'value' => 1200
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-width.1344'),
                'value' => 1344
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-width.1500'),
                'value' => 1500
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-width.1700'),
                'value' => 1700
            ]]
        ],
        'template-font' => [
            'name' => Loc::getMessage('template.meta.properties.template-font'),
            'type' => 'list',
            'category' => 'base',
            'default' => !empty($oFont) ? $oFont->code : null,
            'values' => $oFonts->asArray(function ($sCode, $oFont) {
                /** @var Font $oFont */

                return [
                    'value' => [
                        'name' => $oFont->name,
                        'value' => $sCode
                    ]
                ];
            })
        ],
        'template-titles-size' => [
            'name' => Loc::getMessage('template.meta.properties.template-titles-size'),
            'type' => 'list',
            'category' => 'base',
            'default' => 24,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.template-titles-size.20'),
                'value' => 20
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-titles-size.24'),
                'value' => 24
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-titles-size.28'),
                'value' => 28
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-titles-size.32'),
                'value' => 32
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-titles-size.36'),
                'value' => 36
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-titles-size.40'),
                'value' => 40
            ]]
        ],
        'template-text-size' => [
            'name' => Loc::getMessage('template.meta.properties.template-text-size'),
            'type' => 'list',
            'category' => 'base',
            'default' => 14,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.template-text-size.12'),
                'value' => 12
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-text-size.13'),
                'value' => 13
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-text-size.14'),
                'value' => 14
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-text-size.15'),
                'value' => 15
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-text-size.16'),
                'value' => 16
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-text-size.17'),
                'value' => 17
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-text-size.18'),
                'value' => 18
            ]]
        ],
        'template-images-effect' => [
            'name' => Loc::getMessage('template.meta.properties.template-images-effect'),
            'type' => 'list',
            'category' => 'base',
            'default' => 'flash',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.template-images-effect.none'),
                'value' => 'none'
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-images-effect.flash'),
                'value' => 'flash'
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-images-effect.circle'),
                'value' => 'circle'
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-images-effect.opacity'),
                'value' => 'opacity'
            ]]
        ],
        'template-images-lazyload-use' => [
            'name' => Loc::getMessage('template.meta.properties.template-images-lazyload-use'),
            'type' => 'boolean',
            'category' => 'base',
            'default' => true
        ],
        'template-menu-show' => [
            'name' => Loc::getMessage('template.meta.properties.template-menu-show'),
            'type' => 'boolean',
            'category' => 'base',
            'default' => true
        ],
        'template-cache' => [
            'name' => Loc::getMessage('template.meta.properties.template-cache'),
            'type' => 'boolean',
            'category' => 'base',
            'visible' => !Constructor::isLite(),
            'default' => true,
        ],
        'base-regionality-use' => [
            'name' => Loc::getMessage('template.meta.properties.base-regionality-use'),
            'type' => 'boolean',
            'category' => 'base',
            'visible' => ModuleManager::isModuleInstalled('intec.regionality'),
            'default' => true,
        ],
        'base-consent' => [
            'name' => Loc::getMessage('template.meta.properties.base-consent'),
            'type' => 'boolean',
            'category' => 'base',
            'default' => true,
        ],
        'breadcrumb-dropdown-use' => [
            'name' => Loc::getMessage('template.meta.properties.breadcrumb-dropdown-use'),
            'type' => 'boolean',
            'category' => 'base',
            'default' => true,
        ],
        'base-map-vendor' => [
            'name' => Loc::getMessage('template.meta.properties.base-map-vendor'),
            'type' => 'list',
            'category' => 'base',
            'default' => 'yandex',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.base-map-vendor.yandex'),
                'value' => 'yandex'
            ], [
                'name' => Loc::getMessage('template.meta.properties.base-map-vendor.google'),
                'value' => 'google'
            ]]
        ],
        'base-search-mode' => [
            'name' => Loc::getMessage('universe.meta.properties.base-search-mode'),
            'type' => 'list',
            'category' => 'base',
            'default' => 'site',
            'values' => [[
                'name' => Loc::getMessage('universe.meta.properties.base-search-mode.site'),
                'value' => 'site'
            ], [
                'name' => Loc::getMessage('universe.meta.properties.base-search-mode.catalog'),
                'value' => 'catalog'
            ]]
        ],
        'basket-use' => [
            'name' => Loc::getMessage('template.meta.properties.basket-use'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => true,
        ],
        'basket-delay-use' => [
            'name' => Loc::getMessage('template.meta.properties.basket-delay-use'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => true,
        ],
        'basket-compare-use' => [
            'name' => Loc::getMessage('template.meta.properties.basket-compare-use'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => true,
        ],
        'basket-position' => [
            'name' => Loc::getMessage('template.meta.properties.basket-position'),
            'type' => 'list',
            'category' => 'basket',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.basket-position.header'),
                'value' => 'header'
            ], [
                'name' => Loc::getMessage('template.meta.properties.basket-position.fixed.right'),
                'value' => 'fixed.right'
            ]],
            'default' => 'header'
        ],
        'basket-fixed-template' => [
            'name' => Loc::getMessage('template.meta.properties.basket-fixed-template'),
            'type' => 'list',
            'category' => 'basket',
            'values' => [[
                'name' => Loc::getMessage('universe.meta.properties.basket-fixed-template.template.1'),
                'value' => 'template.1'
            ], [
                'name' => Loc::getMessage('universe.meta.properties.basket-fixed-template.template.2'),
                'value' => 'template.2'
            ]],
            'default' => 'template.1'
        ],
        'basket-fixed-auto' => [
            'name' => Loc::getMessage('template.meta.properties.basket-fixed-auto'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => true
        ],
        'basket-header-popup-show' => [
            'name' => Loc::getMessage('template.meta.properties.basket-header-popup-show'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => true
        ],
        'basket-notifications-use' => [
            'name' => Loc::getMessage('template.meta.properties.basket-notifications-use'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => false
        ],
        'basket-total-fixed' => [
            'name' => Loc::getMessage('template.meta.properties.basket-total-fixed'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => false
        ],
        'basket-quick-view-use' => [
            'name' => Loc::getMessage('template.meta.properties.basket-quick-view-use'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => false
        ],
        'basket-confirm-remove-product-use' => [
            'name' => Loc::getMessage('template.meta.properties.basket-confirm-remove-product-use'),
            'type' => 'boolean',
            'category' => 'basket',
            'default' => false
        ],
        'profile-claims-use' => [
            'name' => Loc::getMessage('template.meta.properties.profile-claims-use'),
            'type' => 'boolean',
            'category' => 'profile',
            'visible' => ModuleManager::isModuleInstalled('sale'),
            'default' => true,
        ],
        'profile-add-use' => [
            'name' => Loc::getMessage('template.meta.properties.profile-add-use'),
            'type' => 'boolean',
            'category' => 'profile',
            'visible' => ModuleManager::isModuleInstalled('sale'),
            'default' => true,
        ],
        'profile-crm-use' => [
            'name' => Loc::getMessage('template.meta.properties.profile-crm-use'),
            'type' => 'boolean',
            'category' => 'profile',
            'visible' => ModuleManager::isModuleInstalled('sale'),
            'default' => true,
        ],
        'profile-viewed-product-use' => [
            'name' => Loc::getMessage('template.meta.properties.profile-viewed-product-use'),
            'type' => 'boolean',
            'category' => 'profile',
            'visible' => ModuleManager::isModuleInstalled('sale'),
            'default' => true,
        ],
        'catalog-root-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-root-template'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.1'),
                'value' => 'tile.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.2'),
                'value' => 'tile.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.3'),
                'value' => 'tile.3'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.4'),
                'value' => 'tile.4'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.5'),
                'value' => 'tile.5'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.slider.1'),
                'value' => 'slider.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.6'),
                'value' => 'tile.6'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.7'),
                'value' => 'tile.7'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.8'),
                'value' => 'tile.8'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-template.tile.9'),
                'value' => 'tile.9'
            ]],
            'default' => 'tile.1'
        ],
        'catalog-root-layout' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-root-layout'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-root-layout.1'),
                'value' => '1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-root-layout.2'),
                'value' => '2'
            ]],
            'default' => '1'
        ],
        'catalog-root-menu-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-root-menu-show'),
            'type' => 'boolean',
            'category' => 'catalog-sections',
            'default' => false
        ],
        'catalog-quick-view-use' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-use'),
            'type' => 'boolean',
            'category' => 'catalog',
            'default' => true
        ],
        'catalog-quick-view-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-template'),
            'type' => 'list',
            'category' => 'catalog',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-template.2'),
                'value' => 2
            ]],
            'default' => 1
        ],
        'catalog-quick-view-timer-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-timer-show'),
            'type' => 'boolean',
            'category' => 'catalog',
            'default' => false
        ],
        'catalog-quick-view-slider-use' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-slider-use'),
            'type' => 'boolean',
            'category' => 'catalog',
            'default' => true
        ],
        'catalog-quick-view-detail' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-quick-view-detail'),
            'type' => 'boolean',
            'category' => 'catalog',
            'default' => false
        ],
        'catalog-recalculation-price-use' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-recalculation-price-use'),
            'type' => 'boolean',
            'category' => 'catalog',
            'default' => false
        ],
        'catalog-filter-ajax' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-filter-ajax'),
            'type' => 'boolean',
            'category' => 'catalog',
            'default' => false
        ],
        'catalog-filter-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-filter-template'),
            'type' => 'list',
            'category' => 'catalog',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-filter-template.vertical.1'),
                'value' => 'vertical.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-filter-template.vertical.2'),
                'value' => 'vertical.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-filter-template.horizontal.1'),
                'value' => 'horizontal.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-filter-template.horizontal.2'),
                'value' => 'horizontal.2'
            ]],
            'default' => 'vertical.1'
        ],

        'catalog-sections-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-sections-template'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.1'),
                'value' => 'tile.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.2'),
                'value' => 'tile.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.3'),
                'value' => 'tile.3'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.4'),
                'value' => 'tile.4'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.5'),
                'value' => 'tile.5'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.slider.1'),
                'value' => 'slider.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.6'),
                'value' => 'tile.6'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.7'),
                'value' => 'tile.7'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.8'),
                'value' => 'tile.8'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-template.tile.9'),
                'value' => 'tile.9'
            ]],
            'default' => 'tile.1'
        ],
        'catalog-products-view-mode' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-products-view-mode'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-products-view-mode.text'),
                'value' => 'text'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-products-view-mode.list'),
                'value' => 'list'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-products-view-mode.tile'),
                'value' => 'tile'
            ]],
            'default' => 'tile'
        ],
        'catalog-elements-tile-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.1'),
                'value' => 'tile.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.1.columns.4'),
                'value' => 'tile.1.columns.4'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.2'),
                'value' => 'tile.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.2.columns.4'),
                'value' => 'tile.2.columns.4'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.3'),
                'value' => 'tile.3'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.4'),
                'value' => 'tile.4'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-template.tile.4.columns.4'),
                'value' => 'tile.4.columns.4'
            ]],
            'default' => 'tile.1'
        ],
        'catalog-elements-text-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-elements-text-template'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-text-template.text.1'),
                'value' => 'text.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-text-template.text.2'),
                'value' => 'text.2'
            ]],
            'default' => 'text.1'
        ],
        'catalog-elements-list-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-elements-list-template'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-list-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-list-template.list.2'),
                'value' => 'list.2'
            ]],
            'default' => 'list.1'
        ],
        'catalog-elements-tile-image-aspect-ratio' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-image-aspect-ratio'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-image-aspect-ratio.standard'),
                'value' => '1:1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-elements-tile-image-aspect-ratio.long'),
                'value' => '5:7'
            ]],
            'default' => '1:1'
        ],
        'catalog-sections-layout' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-sections-layout'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-layout.1'),
                'value' => '1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-sections-layout.2'),
                'value' => '2'
            ]],
            'default' => '1'
        ],
        'catalog-sections-menu-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-sections-menu-show'),
            'type' => 'boolean',
            'category' => 'catalog-sections',
            'default' => true
        ],
        'catalog-menu-root-view' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-menu-root-view'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-menu-root-view.simple'),
                'value' => 'simple'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-menu-root-view.pictures'),
                'value' => 'pictures'
            ]],
            'default' => 'simple'
        ],
        'catalog-menu-view' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-menu-view'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-menu-view.simple.1'),
                'value' => 'simple.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-menu-view.pictures.1'),
                'value' => 'pictures.1'
            ]],
            'default' => 'simple.1'
        ],
        'catalog-menu-submenu-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-menu-submenu-template'),
            'type' => 'list',
            'category' => 'catalog-sections',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-menu-submenu-template.type.1'),
                'value' => 'type.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-menu-submenu-template.type.2'),
                'value' => 'type.2'
            ]],
            'default' => 'type.1'
        ],
        'catalog-list-timer-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-list-timer-show'),
            'type' => 'boolean',
            'category' => 'catalog-sections',
            'default' => false
        ],
        'catalog-list-interest-products-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-list-interest-products-show'),
            'type' => 'boolean',
            'category' => 'catalog-sections',
            'default' => false
        ],
        'catalog-section-articles-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-section-articles-show'),
            'type' => 'boolean',
            'category' => 'catalog-sections',
            'default' => false
        ],
        'catalog-list-gift-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-list-gift-show'),
            'type' => 'boolean',
            'category' => 'catalog-sections',
            'default' => true
        ],
        'catalog-detail-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-template'),
            'type' => 'list',
            'category' => 'catalog-detail',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.1.wide'),
                'value' => 'default.1.wide'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.1.tabs.top'),
                'value' => 'default.1.tabs.top'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.1.tabs.right'),
                'value' => 'default.1.tabs.right'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.2.wide'),
                'value' => 'default.2.wide'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.2.narrow'),
                'value' => 'default.2.narrow'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.2.tabs.top'),
                'value' => 'default.2.tabs.top'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.3.wide'),
                'value' => 'default.3.wide'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.4.wide'),
                'value' => 'default.4.wide'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-template.default.4.tabs.top'),
                'value' => 'default.4.tabs.top'
            ]],
            'default' => 'default.1.wide'
        ],
        'catalog-detail-main-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-main-template'),
            'type' => 'list',
            'category' => 'catalog-detail',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-main-template.1'),
                'value' => 1
            ],[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-main-template.2'),
                'value' => 2
            ],[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-main-template.3'),
                'value' => 3
            ]],
            'default' => 1
        ],
        'catalog-detail-menu-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-menu-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => false
        ],
        'catalog-detail-panel-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-panel-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => true
        ],
        'catalog-detail-gallery-modes' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-gallery-modes'),
            'type' => 'list',
            'multiple' => true,
            'category' => 'catalog-detail',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-gallery-modes.zoom'),
                'value' => 'zoom'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-gallery-modes.popup'),
                'value' => 'popup'
            ]],
            'default' => [
                'zoom',
                'popup'
            ]
        ],
        'catalog-detail-sku-view' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-sku-view'),
            'type' => 'list',
            'category' => 'catalog-detail',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-sku-view.dynamic'),
                'value' => 'dynamic'
            ], [
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-sku-view.list'),
                'value' => 'list'
            ]],
            'default' => 'dynamic'
        ],
        'catalog-detail-fast-order-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-fast-order-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => true
        ],
        'catalog-detail-sizes-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-sizes-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => true
        ],
        'catalog-detail-delivery-calculation-use' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-delivery-calculation-use'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => false
        ],
        'catalog-detail-information-shipment-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-information-shipment-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => true
        ],
        'catalog-detail-information-payment-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-information-payment-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => true
        ],
        'catalog-detail-gift-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-gift-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => true
        ],
        'catalog-detail-gift-template' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-gift-template'),
            'type' => 'list',
            'category' => 'catalog-detail',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-gift-template.tile'),
                'value' => 'tile'
            ],[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-gift-template.block'),
                'value' => 'block'
            ],[
                'name' => Loc::getMessage('template.meta.properties.catalog-detail-gift-template.list'),
                'value' => 'list'
            ]],
            'default' => 'tile.1'
        ],
        'catalog-detail-form-cheaper-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-form-cheaper-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => false
        ],
        'catalog-detail-price-credit-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-price-credit-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => false
        ],
        'catalog-detail-articles-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-articles-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => false
        ],
        'catalog-detail-timer-show' => [
            'name' => Loc::getMessage('template.meta.properties.catalog-detail-timer-show'),
            'type' => 'boolean',
            'category' => 'catalog-detail',
            'default' => false
        ],
        'footer-blocks' => [
            'name' => Loc::getMessage('template.meta.properties.footer-blocks'),
            'type' => 'blocks',
            'category' => 'footer',
            'blocks' => [
                'form' => [
                    'name' => Loc::getMessage('template.meta.properties.footer-blocks.form'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.footer-blocks.form.wide.1'),
                        'value' => 'wide.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.footer-blocks.form.wide.2'),
                        'value' => 'wide.2'
                    ]]
                ],
                'contacts' => [
                    'name' => Loc::getMessage('template.meta.properties.footer-blocks.contacts'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.footer-blocks.contacts.wide.1'),
                        'value' => 'wide.1'
                    ]]
                ]
            ]
        ],
        'footer-theme' => [
            'name' => Loc::getMessage('template.meta.properties.footer-theme'),
            'type' => 'list',
            'category' => 'footer',
            'default' => 'light',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.footer-theme.light'),
                'value' => 'light'
            ], [
                'name' => Loc::getMessage('template.meta.properties.footer-theme.dark'),
                'value' => 'dark'
            ]]
        ],
        'footer-products-viewed-show' => [
            'name' => Loc::getMessage('template.meta.properties.footer-products-viewed-show'),
            'type' => 'boolean',
            'category' => 'footer',
            'default' => false
        ],
        'footer-template' => [
            'name' => Loc::getMessage('template.meta.properties.footer-template'),
            'type' => 'list',
            'category' => 'footer',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.footer-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.footer-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.footer-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.footer-template.4'),
                'value' => 4
            ], [
                'name' => Loc::getMessage('template.meta.properties.footer-template.5'),
                'value' => 5
            ], [
                'name' => Loc::getMessage('template.meta.properties.footer-template.6'),
                'value' => 6
            ]]
        ],
        'header-template' => [
            'name' => Loc::getMessage('template.meta.properties.header-template'),
            'type' => 'list',
            'category' => 'header',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.4'),
                'value' => 4
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.5'),
                'value' => 5
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.6'),
                'value' => 6
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.7'),
                'value' => 7
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.8'),
                'value' => 8
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.9'),
                'value' => 9
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.10'),
                'value' => 10
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.11'),
                'value' => 11
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.12'),
                'value' => 12
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.13'),
                'value' => 13
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.14'),
                'value' => 14
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.15'),
                'value' => 15
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.16'),
                'value' => 16
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.17'),
                'value' => 17
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.18'),
                'value' => 18
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-template.19'),
                'value' => 19
            ]]
        ],
        'header-menu-uppercase-use' => [
            'name' => Loc::getMessage('template.meta.properties.header-menu-uppercase-use'),
            'type' => 'boolean',
            'category' => 'header',
            'default' => false
        ],
        'header-menu-overlay-use' => [
            'name' => Loc::getMessage('template.meta.properties.header-menu-overlay-use'),
            'type' => 'boolean',
            'category' => 'header',
            'default' => true
        ],
        'header-fixed-use' => [
            'name' => Loc::getMessage('template.meta.properties.header-fixed-use'),
            'type' => 'boolean',
            'category' => 'header',
            'default' => true
        ],
        'header-fixed-menu-popup-show' => [
            'name' => Loc::getMessage('template.meta.properties.header-fixed-menu-popup-show'),
            'type' => 'boolean',
            'category' => 'header',
            'default' => true
        ],
        'header-registration-use' => [
            'name' => Loc::getMessage('template.meta.properties.header-registration-use'),
            'type' => 'boolean',
            'category' => 'header',
            'default' => true
        ],

        'header-menu-main-section-template' => [
            'name' => Loc::getMessage('template.meta.properties.header-menu-main-section-template'),
            'type' => 'list',
            'category' => 'header',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-menu-main-section-template.default'),
                'value' => 'default'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-menu-main-section-template.images'),
                'value' => 'images'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-menu-main-section-template.information'),
                'value' => 'information'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-menu-main-section-template.banner'),
                'value' => 'banner'
            ]],
            'default' => 'images'
        ],
        'header-menu-popup-template' => [
            'name' => Loc::getMessage('template.meta.properties.header-menu-popup-template'),
            'type' => 'list',
            'category' => 'header',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-menu-popup-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-menu-popup-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-menu-popup-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-menu-popup-template.4'),
                'value' => 4
            ]],
            'default' => 1
        ],
        'header-search-popup-template' => [
            'name' => Loc::getMessage('template.meta.properties.header-search-popup-template'),
            'type' => 'list',
            'category' => 'header',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-search-popup-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-search-popup-template.list.2'),
                'value' => 'list.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-search-popup-template.list.3'),
                'value' => 'list.3'
            ]],
            'default' => 'list.1'
        ],
        'header-mobile-template' => [
            'name' => Loc::getMessage('template.meta.properties.header-mobile-template'),
            'type' => 'list',
            'category' => 'mobile',
            'default' => 'white',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-mobile-template.white'),
                'value' => 'white'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-template.colored'),
                'value' => 'colored'
            ]/*, [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-template.white-with-icons'),
                'value' => 'white-with-icons'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-template.colored-with-icons'),
                'value' => 'colored-with-icons'
            ]*/]
        ],
        'template-mobile-titles-size' => [
            'name' => Loc::getMessage('template.meta.properties.template-mobile-titles-size'),
            'type' => 'list',
            'category' => 'mobile',
            'default' => 20,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.template-mobile-titles-size.16'),
                'value' => 16
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-mobile-titles-size.20'),
                'value' => 20
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-mobile-titles-size.24'),
                'value' => 24
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-mobile-titles-size.28'),
                'value' => 28
            ], [
                'name' => Loc::getMessage('template.meta.properties.template-mobile-titles-size.32'),
                'value' => 32
            ]]
        ],
        'header-mobile-fixed' => [
            'name' => Loc::getMessage('template.meta.properties.header-mobile-fixed'),
            'type' => 'boolean',
            'category' => 'mobile',
            'default' => true
        ],
        'header-mobile-hidden' => [
            'name' => Loc::getMessage('template.meta.properties.header-mobile-hidden'),
            'type' => 'boolean',
            'category' => 'mobile',
            'default' => true
        ],
        'header-mobile-banner-view' => [
            'name' => Loc::getMessage('template.meta.properties.header-mobile-banner-view'),
            'type' => 'list',
            'category' => 'mobile',
            'default' => 'apart',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-mobile-banner-view.apart'),
                'value' => 'apart'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-banner-view.join'),
                'value' => 'join'
            ]]
        ],
        'header-mobile-search-type' => [
            'name' => Loc::getMessage('template.meta.properties.header-mobile-search-type'),
            'type' => 'list',
            'category' => 'mobile',
            'default' => 'popup',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-mobile-search-type.page'),
                'value' => 'page'
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-search-type.popup'),
                'value' => 'popup'
            ]]
        ],
        'header-mobile-menu-template' => [
            'name' => Loc::getMessage('template.meta.properties.header-mobile-menu-template'),
            'type' => 'list',
            'category' => 'mobile',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.header-mobile-menu-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-menu-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-menu-template.3'),
                'value' => 3
            ]/*, [
                'name' => Loc::getMessage('template.meta.properties.header-mobile-menu-template.4'),
                'value' => 4
            ]*/]
        ],
        'pages-main-template' => [
            'name' => Loc::getMessage('template.meta.properties.pages-main-template'),
            'type' => 'list',
            'category' => 'main',
            'default' => 'wide',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.pages-main-template.wide'),
                'value' => 'wide'
            ], [
                'name' => Loc::getMessage('template.meta.properties.pages-main-template.narrow.left'),
                'value' => 'narrow.left'
            ]]
        ],
        'pages-main-blocks' => [
            'name' => Loc::getMessage('template.meta.properties.pages-main-blocks'),
            'type' => 'blocks',
            'category' => 'main',
            'blocks' => [
                'banner' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.1'),
                        'value' => 1
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.2'),
                        'value' => 2
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.3'),
                        'value' => 3
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.4'),
                        'value' => 4
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.5'),
                        'value' => 5
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.6'),
                        'value' => 6
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.7'),
                        'value' => 7
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.8'),
                        'value' => 8
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.9'),
                        'value' => 9
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.10'),
                        'value' => 10
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.11'),
                        'value' => 11
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.banner.12'),
                        'value' => 12
                    ]]
                ],
                'icons' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.icons')
                ],
                'advantages' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.icons.1'),
                        'value' => 'icons.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.chess.1'),
                        'value' => 'chess.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.tiles.2'),
                        'value' => 'tiles.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.icons.2'),
                        'value' => 'icons.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.numbers.1'),
                        'value' => 'numbers.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.numbers.2'),
                        'value' => 'numbers.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.numbers.3'),
                        'value' => 'numbers.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.numbers.4'),
                        'value' => 'numbers.4'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.numbers.5'),
                        'value' => 'numbers.5'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.advantages.slider.1'),
                        'value' => 'slider.1'
                    ]]
                ],
                'sections' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.sections'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.sections.list.1'),
                        'value' => 'list.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.sections.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.sections.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'categories' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.chess.1'),
                        'value' => 'chess.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.mosaic.1'),
                        'value' => 'mosaic.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.2'),
                        'value' => 'tiles.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.list.1'),
                        'value' => 'list.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.3'),
                        'value' => 'tiles.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.4'),
                        'value' => 'tiles.4'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.5'),
                        'value' => 'tiles.5'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.chess.2'),
                        'value' => 'chess.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.6'),
                        'value' => 'tiles.6'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.7'),
                        'value' => 'tiles.7'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.chess.3'),
                        'value' => 'chess.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.block.1'),
                        'value' => 'block.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.tiles.8'),
                        'value' => 'tiles.8'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.categories.list.2'),
                        'value' => 'list.2'
                    ]]
                ],
                'products' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products.slider.1'),
                        'value' => 'slider.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products.tiles.2'),
                        'value' => 'tiles.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products.tiles.3'),
                        'value' => 'tiles.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products.tiles.3.big'),
                        'value' => 'tiles.3.big'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products.list.1'),
                        'value' => 'list.1'
                    ]]
                ],
                'products-reviews' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products-reviews'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.products-reviews.slider.1'),
                        'value' => 'slider.1'
                    ]]
                ],
                'images' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.images'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.images.blocks.1'),
                        'value' => 'blocks.1'
                    ]]
                ],
                'collections' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.collections'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.collections.blocks.1'),
                        'value' => 'blocks.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.collections.blocks.2'),
                        'value' => 'blocks.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.collections.tiles.1'),
                        'value' => 'tiles.1'
                    ]]
                ],
                'product-day' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.product-day'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.product-day.block.1'),
                        'value' => 'block.1'
                    ]]
                ],
                'services' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.blocks.1'),
                        'value' => 'blocks.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.2'),
                        'value' => 'tiles.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.3'),
                        'value' => 'tiles.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.blocks.2'),
                        'value' => 'blocks.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.4'),
                        'value' => 'tiles.4'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.5'),
                        'value' => 'tiles.5'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.6'),
                        'value' => 'tiles.6'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.wide.1'),
                        'value' => 'wide.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.7'),
                        'value' => 'tiles.7'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tabs.1'),
                        'value' => 'tabs.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tabs.2'),
                        'value' => 'tabs.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.8'),
                        'value' => 'tiles.8'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.tiles.9'),
                        'value' => 'tiles.9'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.list.1'),
                        'value' => 'list.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.services.slider.1'),
                        'value' => 'slider.1'
                    ]]
                ],
                'stories' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.stories'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.stories.blocks.1'),
                        'value' => 'blocks.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.stories.blocks.2'),
                        'value' => 'blocks.2'
                    ]]
                ],
                'articles' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.articles'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.articles.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.articles.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'shares' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.shares'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.shares.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.shares.blocks.1'),
                        'value' => 'blocks.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.shares.blocks.2'),
                        'value' => 'blocks.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.shares.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'reviews' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.list.1'),
                        'value' => 'list.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.1'),
                        'value' => 'slider.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.blocks.1'),
                        'value' => 'blocks.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.blocks.2'),
                        'value' => 'blocks.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.list.2'),
                        'value' => 'list.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.2'),
                        'value' => 'slider.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.3'),
                        'value' => 'slider.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.4'),
                        'value' => 'slider.4'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.5'),
                        'value' => 'slider.5'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.6'),
                        'value' => 'slider.6'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.7'),
                        'value' => 'slider.7'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.8'),
                        'value' => 'slider.8'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.video.1'),
                        'value' => 'video.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.9'),
                        'value' => 'slider.9'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.slider.10'),
                        'value' => 'slider.10'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.blocks.3'),
                        'value' => 'blocks.3'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.video.2'),
                        'value' => 'video.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.reviews.blocks.4'),
                        'value' => 'blocks.4'
                    ]]
                ],
                'about' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.about'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.about.block.1'),
                        'value' => 'block.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.about.block.2'),
                        'value' => 'block.2'
                    ]]
                ],
                'stages' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.stages'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.stages.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.stages.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'staff' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.staff'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.staff.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.staff.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'certificates' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.certificates'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.certificates.tiles.1'),
                        'value' => 'tiles.1'
                    ]]
                ],
                'projects' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.projects'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.projects.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.projects.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'video' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.video'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.video.wide.1'),
                        'value' => 'wide.1'
                    ]]
                ],
                'gallery' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.gallery'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.gallery.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.gallery.tiles.2'),
                        'value' => 'tiles.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.gallery.tiles.3'),
                        'value' => 'tiles.3'
                    ]]
                ],
                'rates' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.rates'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.rates.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.rates.tiles.2'),
                        'value' => 'tiles.2'
                    ]]
                ],
                'faq' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.faq'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.faq.wide.1'),
                        'value' => 'wide.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.faq.wide.2'),
                        'value' => 'wide.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.faq.narrow.1'),
                        'value' => 'narrow.1'
                    ]]
                ],
                'videos' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.videos'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.videos.slider.1'),
                        'value' => 'slider.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.videos.list.1'),
                        'value' => 'list.1'
                    ]]
                ],
                'brands' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.brands'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.brands.slider.1'),
                        'value' => 'slider.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.brands.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.brands.tiles.2'),
                        'value' => 'tiles.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.brands.slider.2'),
                        'value' => 'slider.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.brands.tiles.3'),
                        'value' => 'tiles.3'
                    ]]
                ],
                'vk' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.vk'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.vk.type.1'),
                        'value' => 'type.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.vk.type.2'),
                        'value' => 'type.2'
                    ],  [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.vk.type.3'),
                        'value' => 'type.3'
                    ]]
                ],
                'instagram' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.instagram'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.instagram.type.1'),
                        'value' => 'type.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.instagram.type.2'),
                        'value' => 'type.2'
                    ]]
                ],
                'news' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.news'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.news.blocks.1'),
                        'value' => 'blocks.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.news.blocks.2'),
                        'value' => 'blocks.2'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.news.tiles.1'),
                        'value' => 'tiles.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.news.slider.1'),
                        'value' => 'slider.1'
                    ]]
                ],
                'solutions' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.solutions'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.solutions.slider.1'),
                        'value' => 'slider.1'
                    ]]
                ],
                'contacts' => [
                    'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.contacts'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.contacts.simple.1'),
                        'value' => 'simple.1'
                    ], [
                        'name' => Loc::getMessage('template.meta.properties.pages-main-blocks.contacts.list.1'),
                        'value' => 'list.1'
                    ]]
                ]
            ]
        ],
        'sections-shares-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-shares-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'list.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-shares-template.blocks.1'),
                'value' => 'blocks.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-shares-template.blocks.2'),
                'value' => 'blocks.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-shares-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-shares-template.blocks.3'),
                'value' => 'blocks.3'
            ]]
        ],
        'sections-staff-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-staff-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'blocks.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-staff-template.blocks.1'),
                'value' => 'blocks.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-staff-template.list.1'),
                'value' => 'list.1'
            ]]
        ],
        'sections-contacts-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-contacts-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'simple.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-contacts-template.simple.1'),
                'value' => 'simple.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-contacts-template.shops.1'),
                'value' => 'shops.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-contacts-template.offices.1'),
                'value' => 'offices.1'
            ]]
        ],
        'sections-news-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-news-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'list.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-news-template.blocks.1'),
                'value' => 'blocks.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-news-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-news-template.tiles.1'),
                'value' => 'tiles.1'
            ]]
        ],
        'sections-articles-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-articles-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'list.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-articles-template.blocks.1'),
                'value' => 'blocks.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-articles-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-articles-template.tiles.1'),
                'value' => 'tiles.1'
            ]]
        ],
        'sections-articles-products-categories-show' => [
            'name' => Loc::getMessage('template.meta.properties.sections-articles-products-categories-show'),
            'type' => 'boolean',
            'category' => 'sections',
            'default' => false
        ],
        'sections-articles-products-show' => [
            'name' => Loc::getMessage('template.meta.properties.sections-articles-products-show'),
            'type' => 'boolean',
            'category' => 'sections',
            'default' => false
        ],
        'sections-brands-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-brands-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'tiles.2',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-brands-template.tiles.1'),
                'value' => 'tiles.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-brands-template.tiles.2'),
                'value' => 'tiles.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-brands-template.list.1'),
                'value' => 'list.1'
            ]]
        ],
        'sections-brands-filter-use' => [
            'name' => Loc::getMessage('template.meta.properties.sections-brands-filter-use'),
            'type' => 'boolean',
            'category' => 'sections',
            'default' => false
        ],
        'sections-brands-sections-show' => [
            'name' => Loc::getMessage('template.meta.properties.sections-brands-sections-show'),
            'type' => 'boolean',
            'category' => 'sections',
            'default' => false
        ],
        'sections-brands-products-show' => [
            'name' => Loc::getMessage('template.meta.properties.sections-brands-products-show'),
            'type' => 'boolean',
            'category' => 'sections',
            'default' => false
        ],
        'sections-jobs-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-jobs-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'list.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-jobs-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-jobs-template.list.2'),
                'value' => 'list.2'
            ]]
        ],
        'sections-blog-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-blog-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'list.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-blog-template.blocks.1'),
                'value' => 'blocks.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-blog-template.list.1'),
                'value' => 'list.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-blog-template.tiles.1'),
                'value' => 'tiles.1'
            ]]
        ],
        'sections-certificates-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-certificates-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'tile.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-certificates-template.tile.1'),
                'value' => 'tile.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-certificates-template.tile.2'),
                'value' => 'tile.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-certificates-template.tile.3'),
                'value' => 'tile.3'
            ]]
        ],
        'sections-collections-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-collections-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'tile.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-collections-template.tile.1'),
                'value' => 'tile.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-collections-template.tile.2'),
                'value' => 'tile.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-collections-template.list.1'),
                'value' => 'list.1'
            ]]
        ],
        'sections-collections-products-show' => [
            'name' => Loc::getMessage('template.meta.properties.sections-collections-products-show'),
            'type' => 'boolean',
            'category' => 'sections',
            'default' => true
        ],
        'sections-photo-template' => [
            'name' => Loc::getMessage('template.meta.properties.sections-photo-template'),
            'type' => 'list',
            'category' => 'sections',
            'default' => 'default.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.sections-photo-template.default.1'),
                'value' => 'default.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.sections-photo-template.default.2'),
                'value' => 'default.2'
            ]]
        ],
        'services-root-menu-show' => [
            'name' => Loc::getMessage('template.meta.properties.services-root-menu-show'),
            'type' => 'boolean',
            'category' => 'services',
            'default' => false
        ],
        'services-root-sections-template' => [
            'name' => Loc::getMessage('template.meta.properties.services-root-sections-template'),
            'type' => 'list',
            'category' => 'services',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.4'),
                'value' => 4
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.5'),
                'value' => 5
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.6'),
                'value' => 6
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.7'),
                'value' => 7
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.8'),
                'value' => 8
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.9'),
                'value' => 9
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.10'),
                'value' => 10
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.11'),
                'value' => 11
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.12'),
                'value' => 12
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.13'),
                'value' => 13
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-sections-template.14'),
                'value' => 14
            ]]
        ],
        'services-root-list-template' => [
            'name' => Loc::getMessage('template.meta.properties.services-root-list-template'),
            'type' => 'list',
            'category' => 'services',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.4'),
                'value' => 4
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.5'),
                'value' => 5
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.6'),
                'value' => 6
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.7'),
                'value' => 7
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.8'),
                'value' => 8
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.9'),
                'value' => 9
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.10'),
                'value' => 10
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.11'),
                'value' => 11
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.12'),
                'value' => 12
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-root-list-template.13'),
                'value' => 13
            ]]
        ],
        'services-children-menu-show' => [
            'name' => Loc::getMessage('template.meta.properties.services-children-menu-show'),
            'type' => 'boolean',
            'category' => 'services',
            'default' => true
        ],
        'services-children-sections-template' => [
            'name' => Loc::getMessage('template.meta.properties.services-children-sections-template'),
            'type' => 'list',
            'category' => 'services',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.4'),
                'value' => 4
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.5'),
                'value' => 5
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.6'),
                'value' => 6
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.7'),
                'value' => 7
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.8'),
                'value' => 8
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.9'),
                'value' => 9
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.10'),
                'value' => 10
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.11'),
                'value' => 11
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.12'),
                'value' => 12
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.13'),
                'value' => 13
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-sections-template.14'),
                'value' => 14
            ]]
        ],
        'services-children-list-template' => [
            'name' => Loc::getMessage('template.meta.properties.services-children-list-template'),
            'type' => 'list',
            'category' => 'services',
            'default' => 1,
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.2'),
                'value' => 2
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.3'),
                'value' => 3
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.4'),
                'value' => 4
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.5'),
                'value' => 5
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.6'),
                'value' => 6
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.7'),
                'value' => 7
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.8'),
                'value' => 8
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.9'),
                'value' => 9
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.10'),
                'value' => 10
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.11'),
                'value' => 11
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.12'),
                'value' => 12
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-children-list-template.13'),
                'value' => 13
            ]]
        ],
        'services-filter-use' => [
            'name' => Loc::getMessage('template.meta.properties.services-filter-use'),
            'type' => 'boolean',
            'category' => 'services',
            'default' => true
        ],
        'services-filter-template' => [
            'name' => Loc::getMessage('template.meta.properties.services-filter-template'),
            'type' => 'list',
            'category' => 'services',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-filter-template.vertical.2'),
                'value' => 'vertical.2'
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-filter-template.horizontal.2'),
                'value' => 'horizontal.2'
            ]],
            'default' => 'vertical.2'
        ],
        'services-element-template' => [
            'name' => Loc::getMessage('template.meta.properties.services-element-template'),
            'type' => 'list',
            'category' => 'services',
            'default' => 'wide.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-element-template.narrow.1'),
                'value' => 'narrow.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-element-template.wide.1'),
                'value' => 'wide.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-element-template.narrow.2'),
                'value' => 'narrow.2'
            ]]
        ],
        'services-element-type' => [
            'name' => Loc::getMessage('template.meta.properties.services-element-type'),
            'type' => 'list',
            'category' => 'services',
            'default' => 'type.1',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.services-element-type.1'),
                'value' => 'type.1'
            ], [
                'name' => Loc::getMessage('template.meta.properties.services-element-type.2'),
                'value' => 'type.2'
            ]]
        ],
        'mobile-blocks' => [
            'name' => Loc::getMessage('template.meta.properties.mobile-blocks'),
            'type' => 'blocks',
            'category' => 'mobile',
            'blocks' => [
                'panel' => [
                    'name' => Loc::getMessage('template.meta.properties.mobile-blocks.panel'),
                    'templates' => [[
                        'name' => Loc::getMessage('template.meta.properties.mobile-blocks.panel.1'),
                        'value' => 'template.1'
                    ]]
                ]
            ]
        ],
        'mobile-panel-hidden' => [
            'name' => Loc::getMessage('template.meta.properties.mobile.panel.hidden'),
            'type' => 'boolean',
            'category' => 'mobile',
            'default' => true
        ],
        'mobile-panel-breadcrumbs-compact' => [
            'name' => Loc::getMessage('template.meta.properties.mobile.panel.breadcrumbs.compact'),
            'type' => 'boolean',
            'category' => 'mobile',
            'default' => true
        ],
        'mobile-panel-breadcrumbs-compact-slider' => [
            'name' => Loc::getMessage('template.meta.properties.mobile.panel.breadcrumbs.compact.slider'),
            'type' => 'boolean',
            'category' => 'mobile',
            'default' => true
        ],
        'mobile-catalog-elements-tile-columns' => [
            'name' => Loc::getMessage('template.meta.properties.mobile-catalog-elements-tile-columns'),
            'type' => 'list',
            'category' => 'mobile',
            'values' => [[
                'name' => Loc::getMessage('template.meta.properties.mobile-catalog-elements-tile-columns.1'),
                'value' => 1
            ], [
                'name' => Loc::getMessage('template.meta.properties.mobile-catalog-elements-tile-columns.2'),
                'value' => 2
            ]],
            'default' => 2
        ],
        'mobile-catalog-detail-panel-show' => [
            'name' => Loc::getMessage('template.meta.properties.mobile-catalog-detail-panel-show'),
            'type' => 'boolean',
            'category' => 'mobile',
            'default' => true
        ]
    ]
];

$cache = Cache::createInstance();
$components = [];
$groups = new Collection();

foreach ([
    'header',
    'categories',
    'certificates',
    'faq',
    'contacts',
    'about',
    'advantages',
    'articles',
    'brands',
    'gallery',
    'news',
    'products',
    'projects',
    'rates',
    'reviews',
    'sections',
    'services',
    'shares',
    'staff',
    'stages',
    'social',
    'video',
    'videos',
    'footer'
] as $index => $group) {
    $groups->set($group, new PresetGroup([
        'name' => Loc::getMessage('template.meta.components.presets.groups.'.$group),
        'code' => $group,
        'sort' => ($index + 1) * 100
    ]));
}

$components = [
    'presets' => []
];

if ($cache->initCache(3600000, 'components', SITE_ID.'/meta')) {
    $components = $cache->getVars();
} else if ($cache->startDataCache()) {
    $directory = __DIR__ . '/components';
    $entries = FileHelper::getDirectoryEntries($directory, false);

    foreach ($entries as $namespace) {
        $namespaceDirectory = $directory.'/'.$namespace;
        $namespaceEntries = FileHelper::getDirectoryEntries($namespaceDirectory, false);

        foreach ($namespaceEntries as $component) {
            $componentDirectory = $namespaceDirectory.'/'.$component;
            $componentEntries = FileHelper::getDirectoryEntries($componentDirectory, false);

            foreach ($componentEntries as $template) {
                $templateDirectory = $componentDirectory.'/'.$template;
                $presets = $templateDirectory.'/.presets.php';

                if (!FileHelper::isFile($presets))
                    continue;

                $presets = include($presets);

                if (!Type::isArray($presets))
                    continue;

                foreach ($presets as $key => $preset) {
                    $preset['code'] = $namespace.':'.$component;
                    $preset['template'] = $template;

                    $presetPicture = $templateDirectory.'/images/preset.'.$key.'.png';

                    if (FileHelper::isFile($presetPicture))
                        $preset['picturePath'] = Path::from($presetPicture)
                            ->toRelative()
                            ->asAbsolute()
                            ->getValue('/');

                    $components['presets'][] = $preset;
                }
            }
        }
    }

    $cache->endDataCache($components);
}

foreach ($components['presets'] as $key => $preset) {
    $preset = ArrayHelper::merge([
        'code' => null,
        'template' => null,
        'name' => null,
        'group' => null,
        'sort' => 0,
        'properties' => []
    ], $preset);

    if (empty($preset['code']) || empty($preset['name']))
        continue;

    $preset['group'] = $groups->get($preset['group']);
    $components['presets'][$key] = new ComponentPreset($preset);
}

$meta['components'] = $components;

/** CUSTOM START */

unset($meta['properties']['pages-main-blocks']['blocks']['solutions']);

/** CUSTOM END */

if (FileHelper::isFile($this->getDirectory().'/parts/custom/meta.php'))
    include($this->getDirectory().'/parts/custom/meta.php');

return $meta;