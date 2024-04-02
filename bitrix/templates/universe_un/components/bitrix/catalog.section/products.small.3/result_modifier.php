<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PICTURE_SHOW' => 'N',
    'PRICE_SHOW' => 'N',
    'DISCOUNT_SHOW' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y'
    ],
    'DISCOUNT' => [
        'SHOW' => $arParams['DISCOUNT_SHOW'] === 'Y'
    ]
];

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'OFFER' => !empty($arItem['OFFERS']),
        'ACTION' => 'detail',
        'PRICE' => [
            'SHOW' => $arVisual['PRICE']['SHOW']
        ]
    ];

    $arData = &$arItem['DATA'];

    if (!empty($arParams['PROPERTY_REQUEST_USE'])) {
        $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_REQUEST_USE'],
            'VALUE'
        ]));

        if ($isRequest)
            $arData['ACTION'] = 'request';
    }

    if ($arData['ACTION'] === 'request')
        $arData['PRICE']['SHOW'] = false;

    unset($arData);
}

unset($arItem);

include(__DIR__.'/modifiers/pictures.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;

unset($bBase, $bLite, $arVisual);