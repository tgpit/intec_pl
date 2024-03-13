<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\template\Properties;

if (defined('EDITOR'))
    return;

if ($arParams['SETTINGS_PROFILE'] === 'services') {
    $arParams['SECTIONS_ROOT_MENU_SHOW'] = Properties::get('services-root-menu-show') ? 'Y' : 'N';
    $arParams['SECTIONS_CHILDREN_MENU_SHOW'] = Properties::get('services-children-menu-show') ? 'Y' : 'N';

    $arSettings = [
        'ROOT' => [
            'SECTIONS_TEMPLATE' => Properties::get('services-root-sections-template'),
            'LIST_TEMPLATE' => Properties::get('services-root-list-template')
        ],
        'CHILDREN' => [
            'SECTIONS_TEMPLATE' => Properties::get('services-children-sections-template'),
            'LIST_TEMPLATE' => Properties::get('services-children-list-template')
        ]
    ];

    foreach ($arSettings as $sLevel => $arSetting) {
        switch ($arSetting['SECTIONS_TEMPLATE']) {
            case 2: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_TYPE'] = 'round';
                $arParams['SECTIONS_'.$sLevel.'_NAME_POSITION'] = 'center';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'N';
                break;
            }
            case 3: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_TYPE'] = 'round';
                $arParams['SECTIONS_'.$sLevel.'_NAME_POSITION'] = 'center';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_POSITION'] = 'center';
                break;
            }
            case 4: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_INDENTS'] = 'N';
                $arParams['SECTIONS_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'N';
                break;
            }
            case 5: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_INDENTS'] = 'N';
                $arParams['SECTIONS_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_POSITION'] = 'left';
                break;
            }
            case 6: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_INDENTS'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_POSITION'] = 'left';
                break;
            }
            case 7: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_INDENTS'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'N';
                break;
            }
            case 8: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'list.1';
                $arParams['SECTIONS_'.$sLevel.'_ROUNDED'] = 'N';
                break;
            }
            case 9: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'list.2';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 2;
                $arParams['SECTIONS_'.$sLevel.'_ROUNDING_USE'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_ROUNDING_VALUE'] = 100;
                break;
            }
            case 10: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'list.3';
                break;
            }
            case 11: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'list.4';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_SVG_FILE_USE'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_SVG_FILE_COLOR'] = 'theme';
                $arParams['SECTIONS_'.$sLevel.'_CHILDREN_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_CHILDREN_COUNT'] = '3';
                break;
            }
            case 12: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'list.5';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_SVG_FILE_USE'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_SVG_FILE_COLOR'] = 'theme';
                $arParams['SECTIONS_'.$sLevel.'_CHILDREN_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_CHILDREN_COUNT'] = '3';
                break;
            }
            case 13: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.3';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                break;
            }
            case 14: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.4';
                $arParams['SECTIONS_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_SVG_FILE_USE'] = 'Y';
                $arParams['SECTIONS_'.$sLevel.'_SVG_FILE_COLOR'] = 'theme';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                break;
            }
            default: {
                $arParams['SECTIONS_'.$sLevel.'_TEMPLATE'] = 'tile.1';
                $arParams['SECTIONS_'.$sLevel.'_COLUMNS'] = 3;
                break;
            }
        }
        switch ($arSetting['LIST_TEMPLATE']) {
            case 2: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_TYPE'] = 'round';
                $arParams['LIST_'.$sLevel.'_NAME_POSITION'] = 'center';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_SHOW'] = 'N';
                break;
            }
            case 3: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_TYPE'] = 'round';
                $arParams['LIST_'.$sLevel.'_NAME_POSITION'] = 'center';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_POSITION'] = 'center';
                break;
            }
            case 4: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['LIST_'.$sLevel.'_PICTURE_INDENTS'] = 'N';
                $arParams['LIST_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_SHOW'] = 'N';
                break;
            }
            case 5: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['LIST_'.$sLevel.'_PICTURE_INDENTS'] = 'N';
                $arParams['LIST_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_POSITION'] = 'left';
                break;
            }
            case 6: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['LIST_'.$sLevel.'_PICTURE_INDENTS'] = 'Y';
                $arParams['LIST_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_POSITION'] = 'left';
                break;
            }
            case 7: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_TYPE'] = 'square';
                $arParams['LIST_'.$sLevel.'_PICTURE_INDENTS'] = 'Y';
                $arParams['LIST_'.$sLevel.'_NAME_POSITION'] = 'left';
                $arParams['LIST_'.$sLevel.'_DESCRIPTION_SHOW'] = 'N';
                break;
            }
            case 8: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'list.1';
                $arParams['LIST_'.$sLevel.'_ROUNDED'] = 'N';
                break;
            }
            case 9: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'list.2';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 2;
                $arParams['LIST_'.$sLevel.'_ROUNDING_USE'] = 'Y';
                $arParams['LIST_'.$sLevel.'_ROUNDING_VALUE'] = 100;
                break;
            }
            case 10: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'list.3';
                break;
            }
            case 11: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'list.6';
                $arParams['LIST_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PRICE_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PRICE_OLD_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_FORMAT_USE'] = 'Y';
                break;
            }
            case 12: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.4';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PROPERTIES_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PROPERTIES_COUNT'] = '3';
                $arParams['LIST_'.$sLevel.'_PRICE_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PRICE_OLD_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_FORMAT_USE'] = 'Y';
                break;
            }
            case 13: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.5';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                $arParams['LIST_'.$sLevel.'_PICTURE_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PROPERTIES_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PROPERTIES_COUNT'] = '1';
                $arParams['LIST_'.$sLevel.'_PRICE_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_PRICE_OLD_SHOW'] = 'Y';
                $arParams['LIST_'.$sLevel.'_FORMAT_USE'] = 'Y';
                break;
            }
            default: {
                $arParams['LIST_'.$sLevel.'_TEMPLATE'] = 'tile.1';
                $arParams['LIST_'.$sLevel.'_COLUMNS'] = 3;
                break;
            }

        }
    }

    $arParams['USE_FILTER'] = Properties::get('services-filter-use') ? 'Y' : 'N';
    $arParams['FILTER_TEMPLATE'] = Properties::get('services-filter-template');

    switch ($arParams['FILTER_TEMPLATE']) {
        case 'vertical.1':
            $arParams['FILTER_TYPE'] = 'vertical';
            $arParams['FILTER_TEMPLATE'] = 1;
            break;
        case 'vertical.2':
            $arParams['FILTER_TYPE'] = 'vertical';
            $arParams['FILTER_TEMPLATE'] = 2;
            break;
        case 'horizontal.1':
            $arParams['FILTER_TYPE'] = 'horizontal';
            $arParams['FILTER_TEMPLATE'] = 1;
            break;
        case 'horizontal.2':
            $arParams['FILTER_TYPE'] = 'horizontal';
            $arParams['FILTER_TEMPLATE'] = 2;
    }

    $sDetailTemplate = Properties::get('services-element-template');

    if (Properties::get('services-element-type') == 'type.2') {
        $arParams['DETAIL_TEMPLATE'] = 'default.4';
    } else {
        $arParams['DETAIL_TEMPLATE'] = 'default.3';
    }


    switch ($sDetailTemplate) {
        case 'wide.1': {
            $arParams['DETAIL_BLOCKS_BANNER_WIDE'] = 'Y';
            $arParams['DETAIL_BLOCKS_BANNER_SPLIT'] = 'N';
            break;
        }
        case 'narrow.2': {
            $arParams['DETAIL_BLOCKS_BANNER_WIDE'] = 'N';
            $arParams['DETAIL_BLOCKS_BANNER_SPLIT'] = 'Y';
            $arParams['DETAIL_BLOCKS_BANNER_TEXT_POSITION'] = 'inside';
            break;
        }
        default: {
            $arParams['DETAIL_BLOCKS_BANNER_WIDE'] = 'N';
            $arParams['DETAIL_BLOCKS_BANNER_SPLIT'] = 'N';
            break;
        }
    }

    unset($sDetailTemplate);
}

if (Properties::get('template-images-lazyload-use')) {
    $arParams['SECTIONS_ROOT_LAZYLOAD_USE'] = 'Y';
    $arParams['SECTIONS_CHILDREN_LAZYLOAD_USE'] = 'Y';
    $arParams['LIST_ROOT_LAZYLOAD_USE'] = 'Y';
    $arParams['LIST_CHILDREN_LAZYLOAD_USE'] = 'Y';
    $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
}