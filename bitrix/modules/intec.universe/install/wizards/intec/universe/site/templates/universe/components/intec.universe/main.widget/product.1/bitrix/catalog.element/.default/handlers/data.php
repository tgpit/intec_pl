<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;

return function (&$data) {
    $bOffers = !empty($data['OFFERS']);

    $arData = [
        'id' => Type::toInteger($data['ID']),
        'name' => $data['NAME'],
        'available' => Type::toBoolean($data['CAN_BUY']),
        'quantity' => [
            'value' => $data['CATALOG_QUANTITY'],
            'ratio' => $data['CATALOG_MEASURE_RATIO'],
            'measure' => $data['CATALOG_MEASURE_NAME'],
            'trace' => $data['CATALOG_QUANTITY_TRACE'] === 'Y',
            'zero' => $data['CATALOG_CAN_BUY_ZERO'] === 'Y'
        ],
        'prices' => []
    ];

    foreach ($data['ITEM_PRICES'] as &$price) {
        $priceDisplay = !$bOffers ? $price['PRINT_PRICE'] : Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_PRICE_FROM', [
            '#PRICE#' => $price['PRINT_PRICE']
        ]);

        $arData['prices'][] = [
            'title' => !empty($price['TITLE']) ? $price['TITLE'] : $price['CODE'],
            'id' => $price['PRICE_TYPE_ID'],
            'quantity' => [
                'from' => $price['QUANTITY_FROM'] !== null ? Type::toFloat($price['QUANTITY_FROM']) : null,
                'to' => $price['QUANTITY_TO'] !== null ? Type::toFloat($price['QUANTITY_TO']) : null
            ],
            'base' => [
                'value' => $price['BASE_PRICE'],
                'display' => $price['PRINT_BASE_PRICE']
            ],
            'discount' => [
                'use' => $price['DISCOUNT'] > 0,
                'percent' => $price['PERCENT'],
                'value' => $price['PRICE'],
                'display' => $priceDisplay,
                'difference' => $price['PRINT_DISCOUNT']
            ],
            'currency' => Loader::includeModule('currency') ? CCurrencyLang::GetFormatDescription($price['CURRENCY']) : null
        ];

        unset($price);
    }

    return $arData;
};