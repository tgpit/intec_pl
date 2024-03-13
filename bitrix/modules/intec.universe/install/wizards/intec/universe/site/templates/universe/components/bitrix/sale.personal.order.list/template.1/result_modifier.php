<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\net\Url;
use intec\core\helpers\StringHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'USE_SEARCH' => 'Y',
    'USE_FILTER' => 'Y',
    'SHOW_ONLY_CURRENT_ORDERS' => 'N',
    'CURRENT_ORDERS_LINK' => null
], $arParams);

$arVisual = [
    'SHOW_SEARCH' =>  $arParams['USE_SEARCH'] === 'Y',
    'SHOW_FILTER' => $arParams['USE_FILTER'] === 'Y',
    'ONLY_CURRENT_ORDERS' => $arParams['SHOW_ONLY_CURRENT_ORDERS'] === 'Y' && $arParams['USE_SEARCH'] !== 'Y' && $arParams['USE_FILTER'] !== 'Y',
    'SHOW_LINK_ALL_ORDERS' => $arParams['USE_SEARCH'] !== 'Y' && $arParams['USE_FILTER'] !== 'Y' && $arParams['SHOW_ONLY_CURRENT_ORDERS'] === 'Y' && !empty($arParams['CURRENT_ORDERS_LINK'])
];

$oRequest = Core::$app->request;
$arResult['FILTER'] = [
    'VALUE' => 'current',
    'TABS' => [
        'CURRENT' => [
            'URL' => null,
            'VALUE' => 'current'
        ],
        'COMPLETED' => [
            'URL' => null,
            'VALUE' => 'completed'
        ],
        'CANCELED' => [
            'URL' => null,
            'VALUE' => 'canceled'
        ],
        'ALL' => [
            'URL' => null,
            'VALUE' => 'all'
        ]
    ]
];

$oUrl = new Url($oRequest->getUrl());
$oUrl->getQuery()
    ->removeAt('filter_history')
    ->removeAt('show_canceled')
    ->removeAt('show_all')
    ->removeAt('filter_id');

$arResult['FILTER']['TABS']['CURRENT']['URL'] = $oUrl->build();
$oUrl->getQuery()->set('filter_history', 'Y');
$arResult['FILTER']['TABS']['COMPLETED']['URL'] = $oUrl->build();
$oUrl->getQuery()->set('show_canceled', 'Y');
$arResult['FILTER']['TABS']['CANCELED']['URL'] = $oUrl->build();
$oUrl->getQuery()->set('show_all', 'Y');
$arResult['FILTER']['TABS']['ALL']['URL'] = $oUrl->build();

if ($oRequest->get('filter_history') === 'Y') {
    $arResult['FILTER']['VALUE'] = 'completed';

    if ($oRequest->get('show_canceled') === 'Y')
        $arResult['FILTER']['VALUE'] = 'canceled';
}

if ($oRequest->get('show_all') === 'Y')
    $arResult['FILTER']['VALUE'] = 'all';

$arResult['HEADER'] = [
    'ID' => [
        'CODE' => 'id',
        'URL' => ''
    ],
    'DATE_INSERT' => [
        'CODE' => 'date_insert',
        'URL' => ''
    ],
    'STATUS' => [
        'CODE' => 'status',
        'URL' => ''
    ],
    'PRICE' => [
        'CODE' => 'price',
        'URL' => ''
    ]
];

$oUrl = new Url($oRequest->getUrl());
$oUrl->getQuery()->removeAt('PAGEN_1');

foreach ($arResult['HEADER'] as $keyHeader => $valueHeader) {
    $sort = [
        'by' => StringHelper::toUpperCase($valueHeader['CODE']),
        'order' => isset($_REQUEST['order']) && $_REQUEST['order'] == 'ASC' ? 'DESC' : 'ASC'
    ];
    $oUrl->getQuery()->setRange($sort);
    $arResult['HEADER'][$keyHeader]['URL'] = $oUrl->build();
}

unset($keyHeader, $valueHeader);

$arResult['HEADER']['PAYMENT'] = [
    'CODE' => 'payment',
    'URL' => ''
];

$arResult['HEADER']['SHIPMENT'] = [
    'CODE' => 'shipment',
    'URL' => ''
];

$arResult['HEADER']['PRODUCT'] = [
    'CODE' => 'product',
    'URL' => ''
];

$arResult['HEADER']['BUTTON'] = [
    'CODE' => 'button',
    'URL' => ''
];

$arDefaultColors = ['gray', 'green', 'yellow', 'red', 'current'];

$arOrdersStatuses = Arrays::fromDBResult(CSaleStatus::GetList(['SORT' => 'ASC'], [
    'LID' => LANGUAGE_ID
]))->indexBy('ID');

$arOrdersColor = [];

foreach ($arOrdersStatuses as $arOrdersStatus) {
    if ($arParams['STATUS_COLOR_'.$arOrdersStatus['ID']] !== 'current') {
        $arOrdersColor[$arOrdersStatus['ID']] = ArrayHelper::fromRange($arDefaultColors, $arParams['STATUS_COLOR_'.$arOrdersStatus['ID']]);
    } else {
        $arOrdersColor[$arOrdersStatus['ID']] = $arOrdersStatus['COLOR'] ?? ArrayHelper::fromRange($arDefaultColors, $arParams['STATUS_COLOR_'.$arOrdersStatus['ID']]);
    }
}

unset($arOrdersStatus);

$arOrdersColor['PSEUDO_CANCELLED'] = $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] !== 'current' ? ArrayHelper::fromRange($arDefaultColors, $arParams['STATUS_COLOR_PSEUDO_CANCELLED']) : 'red';

$arKeys = [2, 0, 1, 1, 1, 2];

if(is_array($arResult['ORDERS']) && !empty($arResult['ORDERS'])) {
    foreach ($arResult['ORDERS'] as &$arOrder) {
        if ($arOrder['ORDER']['PAYED'] === 'Y') {
            $arOrder['ORDER']['DATE_PAYED_FORMATTED'] = CIBlockFormatProperties::DateFormat(
                $arParams['ACTIVE_DATE_FORMAT'],
                MakeTimeStamp(
                    $arOrder['ORDER']['DATE_PAYED'],
                    CSite::GetDateFormat()
                )
            );
        }

        $arOrder['ORDER']['URL_TO_DETAIL_PAY'] = $arOrder['ORDER']['URL_TO_DETAIL'].($arParams['SEF_MODE'] === 'Y' ? '?data_block=payment' : '&data_block=payment');

        $count = 1;

        if (!empty($arOrder['BASKET_ITEMS']))
            $count = count($arOrder['BASKET_ITEMS']);

        $fMod = $count % 100;
        $iSuffixKey = $fMod > 4 && $fMod < 20 ? 2 : $arKeys[min($fMod % 10, 5)];
        $arOrder['COUNT_BASKET_ITEMS_TEXT_CODE'] = 'C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_COUNT_PRODUCT_'.$iSuffixKey;
        $arOrder['ORDER']['ORDER_STATUS_COLOR'] = $arOrder['ORDER']['CANCELED'] === 'Y' ? $arOrdersColor['PSEUDO_CANCELLED'] : $arOrdersColor[$arOrder['ORDER']['STATUS_ID']];
        $arOrder['ORDER']['ORDER_STATUS_NAME'] = $arOrder['ORDER']['CANCELED'] !== 'Y' ? $arOrdersStatuses[$arOrder['ORDER']['STATUS_ID']]['NAME'] : '';

        if ($arOrder['ORDER']['ORDER_STATUS_COLOR'] === 'yellow')
            $arOrder['ORDER']['ORDER_STATUS_COLOR'] = '#ffd400';

        if (is_array($arOrder['BASKET_ITEMS']) && !empty($arOrder['BASKET_ITEMS'])) {
            foreach ($arOrder['BASKET_ITEMS'] as &$arBasketItem) {
                $arBasketItem['PRICE_FORMATTED'] = CurrencyFormat($arBasketItem['PRICE'], $arBasketItem['CURRENCY']);
                $arBasketItem['SUM_FORMATTED'] = CurrencyFormat($arBasketItem['PRICE'] * $arBasketItem['QUANTITY'], $arBasketItem['CURRENCY']);
            }
            unset($arBasketItem);
        }
    }
}

$arResult['VISUAL'] = $arVisual;
unset($arOrder, $arVisual, $count);