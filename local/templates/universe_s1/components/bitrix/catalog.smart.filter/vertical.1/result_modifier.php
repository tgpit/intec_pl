<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\net\Url;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'COLLAPSED' => 'N',
    'PRICES_EXPANDED' => [],
    'TYPE_A_PRECISION' => 2,
    'TYPE_F_VIEW' => 'default',
    'TYPE_G_VIEW' => 'default',
    'TYPE_G_SIZE' => 'default',
    'MOBILE' => 'N',
    'SEARCH_SHOW' => 'Y',
    'SEARCH_SHOW_MODE' => 'quantity',
    'SEARCH_SHOW_QUANTITY' => 8,
    'SEARCH_SHOW_SELECT' => [],
    'POPUP_USE' => 'Y'
], $arParams);

$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';

if (!Type::isArray($arParams['PRICES_EXPANDED']))
    $arParams['PRICES_EXPANDED'] = [];

$arResult['VISUAL'] = [
    'DISPLAY' => false,
    'VIEW' => $arParams['FILTER_VIEW_MODE'],
    'COLLAPSED' => $arParams['COLLAPSED'] == 'Y',
    'TYPE' => [
        'A' => [
            'DATA' => 'track',
            'PRECISION' => $arParams['TYPE_A_PRECISION']
        ],
        'B' => [
            'DATA' => 'track',
            'PRECISION' => $arParams['TYPE_B_PRECISION']
        ],
        'F' => [
            'VIEW' => ArrayHelper::fromRange(['default', 'block', 'tile'], $arParams['TYPE_F_VIEW']),
            'DATA' => 'checkbox'
        ],
        'G' => [
            'VIEW' => ArrayHelper::fromRange(['default', 'tile'], $arParams['TYPE_G_VIEW']),
            'SIZE' => ArrayHelper::fromRange(['default', 'big'], $arParams['TYPE_G_SIZE']),
            'DATA' => 'checkbox-picture'
        ],
        'H' => [
            'DATA' => 'checkbox-text-picture'
        ]
    ],
    'MOBILE' => $arParams['MOBILE'] === 'Y',
    'SEARCH' => [
        'SHOW' => $arParams['SEARCH_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['all', 'quantity', 'properties'], $arParams['SEARCH_SHOW_MODE']),
        'QUANTITY' => 0,
        'PROPERTIES' => []
    ],
    'POPUP' => [
        'USE' => $arParams['POPUP_USE'] === 'Y'
    ]
];

if (Loader::includeModule('intec.seo')) {
    $APPLICATION->IncludeComponent('intec.seo:filter.loader', '', [
        'FILTER_RESULT' => $arResult
    ], $component);
}

if ($arResult['VISUAL']['MOBILE'])
    $arResult['VISUAL']['COLLAPSED'] = false;

if (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/modifier/lite.php');

foreach ($arResult['ITEMS'] as $sKey => &$arItem) {
    if (!isset($arItem['DISPLAY_EXPANDED']))
        $arItem['DISPLAY_EXPANDED'] = 'N';

    if (isset($arItem['PRICE']) && $arItem['PRICE']) {
        $arItem['DISPLAY_TYPE'] = 'A';

        if (ArrayHelper::isIn($sKey, $arParams['PRICES_EXPANDED']))
            $arItem['DISPLAY_EXPANDED'] = 'Y';

        if ($arItem['VALUES']['MIN']['VALUE'] !== $arItem['VALUES']['MAX']['VALUE'])
            $arResult['VISUAL']['DISPLAY'] = true;
    } else {
        if ($arItem['DISPLAY_TYPE'] === 'A' || $arItem['DISPLAY_TYPE'] === 'B') {
            if (isset($arItem['VALUES']['MIN']['VALUE']) && isset($arItem['VALUES']['MAX']['VALUE']))
                if ($arItem['VALUES']['MIN']['VALUE'] !== $arItem['VALUES']['MAX']['VALUE'])
                    $arResult['VISUAL']['DISPLAY'] = true;
        } else if (!empty($arItem['VALUES'])) {
            $arResult['VISUAL']['DISPLAY'] = true;
        }
    }
}

unset($arItem);

if ($arResult['VISUAL']['COLLAPSED'])
    foreach ($arResult['ITEMS'] as $arItem)
        if ($arItem['DISPLAY_EXPANDED'] == 'Y') {
            $arResult['VISUAL']['COLLAPSED'] = false;
            break;
        }

if ($arResult['VISUAL']['SEARCH']['SHOW']) {
    if ($arResult['VISUAL']['SEARCH']['MODE'] === 'quantity') {
        if (empty($arParams['SEARCH_SHOW_QUANTITY']) || $arParams['SEARCH_SHOW_QUANTITY'] <= 0)
            $arResult['VISUAL']['SEARCH']['MODE'] = 'all';
        else
            $arResult['VISUAL']['SEARCH']['QUANTITY'] = $arParams['SEARCH_SHOW_QUANTITY'];
    } elseif ($arResult['VISUAL']['SEARCH']['MODE'] === 'properties') {
        if (empty($arParams['SEARCH_SHOW_PROPERTIES']))
            $arResult['VISUAL']['SEARCH']['SHOW'] = false;
        else
            $arResult['VISUAL']['SEARCH']['PROPERTIES'] = $arParams['SEARCH_SHOW_PROPERTIES'];
    }
}

$oRequest = Core::$app->request;

if (($oRequest->getIsAjax() || isset($_SERVER['HTTP_BX_AJAX'])) && $oRequest->get('ajax') === 'y') {

    $oUrl = new Url(Html::decode($arResult['FILTER_URL']));
    $sQuery = $oUrl->getQuery()->get('q');
    $sQuery = Encoding::convert($sQuery, null, Encoding::UTF8);

    $oUrl->getQuery()->set('q', $sQuery);
    $arResult['FILTER_URL'] = Html::encode($oUrl->build());
}