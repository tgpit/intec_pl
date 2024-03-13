<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var bool $bBase
 */

$arResult['ACTIONS'] = [
    'ACTION' => empty($arResult['OFFERS']) ? ArrayHelper::fromRange(['none', 'buy', 'order'], $arParams['ACTION']) : 'none',
    'BUY' => [
        'BASKET' => StringHelper::replaceMacros($arParams['BASKET_URL'], ['SITE_DIR' => SITE_DIR])
    ],
    'DELAY' => [
        'USE' => false
    ],
    'COMPARE' => [
        'USE' => false,
        'CODE' => $arParams['COMPARE_CODE'],
        'IBLOCK' => $arParams['IBLOCK_ID']
    ]
];

if (!empty($arParams['PROPERTY_ORDER_USE'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_ORDER_USE']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            $arResult['ACTIONS']['ACTION'] = 'order';
        }
    }
}

if (empty($arResult['OFFERS'])) {
    if ($arResult['ACTIONS']['ACTION'] === 'buy') {
        if (!$arResult['CAN_BUY']) {
            if ($arResult['CATALOG_SUBSCRIBE'] === 'Y' && $arParams['SUBSCRIBE_USE'] === 'Y') {
                $arResult['ACTIONS']['ACTION'] = 'subscribe';
            } else {
                $arResult['ACTIONS']['ACTION'] = 'none';
            }
        } else {
            if ($bBase) {
                $arResult['ACTIONS']['DELAY']['USE'] = $arParams['DELAY_USE'] === 'Y';
            }
        }
    } else if ($arResult['ACTIONS']['ACTION'] !== 'none') {
        if ($arResult['ACTIONS']['ACTION'] === 'order' && empty($arParams['FORM_ID'])) {
            $arResult['ACTIONS']['ACTION'] = 'none';
        }
    }
} else {
    $arResult['ACTIONS']['ACTION'] = 'none';
}

if (
    !empty($arResult['ACTIONS']['COMPARE']['CODE']) &&
    ($bBase || !$bBase && empty($arResult['OFFERS']))
)
    $arResult['ACTIONS']['COMPARE']['USE'] = $arParams['COMPARE_USE'] === 'Y';