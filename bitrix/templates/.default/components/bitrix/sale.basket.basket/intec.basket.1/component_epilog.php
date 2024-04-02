<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use intec\Core;

/**
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arParams['SCHEME']))
    return;

$asset = Asset::getInstance();
$asset->addString('<style type="text/css">'.Core::$app->web->getScss()->compile('
    $color: #0065FF !default;
    $color-light: lighten($color, 15%);
    $color-dark: darken($color, 15%);

    .intec-basket.intec-basket-template-1 .intec-basket-scheme {
        &-color, &-color-hover:hover {
            color: $color!important;
        }
        &-color-dark, &-color-dark-hover:hover {
            color: $color-dark!important;
        }
        &-color-light, &-color-light-hover:hover {
            color: $color-light!important;
        }

        &-background, &-background-hover:hover {
            background-color: $color!important;
        }
        &-background-dark, &-background-dark-hover:hover  {
            background-color: $color-dark!important;
        }
        &-background-light, &-background-light-hover:hover  {
            background-color: $color-light!important;
        }

        &-border, &-border-hover:hover {
            border-color: $color!important;
        }
        &-border-dark, &-border-dark-hover:hover  {
            border-color: $color-dark!important;
        }
        &-border-light, &-border-light-hover:hover  {
            border-color: $color-light!important;
        }
        
        &-svg-fill, &-svg-fill-hover:hover {
            path {
                fill: $color!important;
            }
        }
        &-svg-fill-dark, &-svg-fill-dark-hover:hover {
            path {
                fill: $color-dark!important;
            }
        }
        &-svg-fill-light, &-svg-fill-light-hover:hover {
            path {
                fill: $color-light!important;
            }
        }
        
        &-svg-stroke, &-svg-stroke-hover:hover {
            path {
                stroke: $color!important;
            }
        }
        &-svg-stroke-dark, &-svg-stroke-dark-hover:hover {
            path {
                stroke: $color-dark!important;
            }
        }
        &-svg-stroke-light, &-svg-stroke-light-hover:hover {
            path {
                stroke: $color-light!important;
            }
        }
    }
', ['color' => $arParams['SCHEME']]).'</style>');