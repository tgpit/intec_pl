<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\Core;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!empty($arParams['SCHEME'])) {
    $assets = Asset::getInstance();
    $assets->addString('<style type="text/css">' . Core::$app->web->getScss()->compile('
        $color: #0065ff !default;
    
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-checkbox input:checked + .bx-soa-part-selector,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-radiobox input:checked + .bx-soa-part-selector {
          background-color: $color;
          border-color: $color;
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-checkbox:hover .bx-soa-part-selector,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-radiobox:hover .bx-soa-part-selector {
          border-color: lighten($color, 10);
          background-color: lighten($color, 10);
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-checkbox input:focus + .bx-soa-part-selector,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-radiobox input:focus + .bx-soa-part-selector {
          border-color: lighten($color, 10);
          background-color: lighten($color, 10);
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-checkbox:active .bx-soa-part-selector,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-radiobox:active .bx-soa-part-selector {
          border-color: lighten($color, 10);
          background-color: lighten($color, 10);
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-checkbox input:disabled + .bx-soa-part-selector,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-radiobox input:disabled + .bx-soa-part-selector {
          border-color: #efefef;
          background-color: #f6f6f6;
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-checkbox input:disabled + .bx-soa-part-selector:before,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-radiobox input:disabled + .bx-soa-part-selector:before {
          background-color: #9f9f9f;
        }
    
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-button.bx-soa-button-colored {
          background-color: $color !important;
          border-color: $color !important;
          color: #fff;
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-button.bx-soa-button-colored:hover {
          background-color: lighten($color, 10) !important;
          border-color: lighten($color, 10) !important;
        }
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-pp-item-container .bx-soa-pp-company.bx-selected .bx-soa-pp-company-graf-container,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .bx-soa-pp-item-container .bx-soa-pp-company:hover .bx-soa-pp-company-graf-container,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .radio-inline:hover label:before,
        .ns-bitrix.c-sale-order-ajax.c-sale-order-ajax-simple-1 .radio-inline.radio-inline-checked label:before {
            background-color: $color !important;
            border-color: $color !important;
        }
    ', ['color' => $arParams['SCHEME']]) . '</style>');
}