<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */
$arParams = ArrayHelper::merge([
    'WIDE' => 'N',
    'TYPE_A_PRECISION' => 2,
    'TYPE_B_PRECISION' => 2,
    'POPUP_USE' => 'N'
], $arParams);

$arParams['FILTER_VIEW_MODE'] = 'HORIZONTAL';
$arResult['VISUAL'] = [
    'DISPLAY' => false,
    'VIEW' => $arParams['FILTER_VIEW_MODE'],
    'WIDE' => [
        'USE' => $arParams['WIDE'] === 'Y',
    ],
    'TYPE' => [
        'A' => [
            'PRECISION' => $arParams['TYPE_A_PRECISION'],
            'DATA' => 'track'
        ],
        'B' => [
            'PRECISION' => $arParams['TYPE_B_PRECISION'],
            'DATA' => 'between'
        ],
        'F' => [
            'DATA' => 'checkbox'
        ],
        'G' => [
            'DATA' => 'checkbox-picture'
        ],
        'H' => [
            'DATA' => 'checkbox-text-picture'
        ],
        'K' => [
            'DATA' => 'radio'
        ]
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

if (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/modifier/lite.php');

foreach ($arResult['ITEMS'] as $sKey => &$arItem) {
    if (!isset($arItem['DISPLAY_EXPANDED']))
        $arItem['DISPLAY_EXPANDED'] = 'N';

    if (isset($arItem['PRICE']) && $arItem['PRICE']) {
        $arItem['DISPLAY_TYPE'] = 'A';

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