<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else
    $bBase = false;

Loader::includeModule('iblock');

$arParams = ArrayHelper::merge([
    'PROPERTY_URL' => null,
    'PROPERTY_ICON' => null,
    'NAME_SHOW' => 'N',
    'PANEL_FIXED' => 'N',
    'SVG_COLOR_MODE' => 'stroke',
    'BASKET_USE' => 'N',
    'BASKET_ELEMENT' => null,
    'DELAY_USE' => 'N',
    'DELAY_ELEMENT' => null,
    'COMPARE_USE' => 'N',
    'COMPARE_IBLOCK_TYPE' => null,
    'COMPARE_IBLOCK_ID' => null,
    'COMPARE_NAME' => 'compare',
    'COMPARE_ELEMENT' => null,
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'NAME' => [
        'SHOW' => $arParams['NAME_SHOW'] === 'Y'
    ],
    'PANEL' => [
        'FIXED' => $arParams['PANEL_FIXED'] === 'Y'
    ],
    'COMPARE' => [
        'USE' => $arParams['COMPARE_USE'] === 'Y' && !empty($arParams['COMPARE_IBLOCK_ID']) && !empty($arParams['COMPARE_NAME']) && !empty($arParams['COMPARE_ELEMENT']),
        'IBLOCK_ID' => $arParams['COMPARE_IBLOCK_ID'],
        'NAME' => $arParams['COMPARE_NAME'],
        'ELEMENT' => $arParams['COMPARE_ELEMENT']
    ],
    'BASKET' => [
        'USE' => $arParams['BASKET_USE'] === 'Y'
    ],
    'DELAY' => [
        'USE' => $bBase && $arParams['DELAY_USE'] === 'Y'
    ],
    'SVG' => [
        'MODE' => ArrayHelper::fromRange(['stroke', 'fill'], $arParams['SVG_COLOR_MODE'])
    ]
];

if (empty($arResult['ITEMS']))
    $arResult['ITEMS'] = [];

if (empty($arResult['ITEMS']) || empty($arParams['COMPARE_ELEMENT']))
    $arVisual['COMPARE']['USE'] = false;

if ($arVisual['COMPARE']['USE'] && !ArrayHelper::keyExists($arParams['COMPARE_ELEMENT'], $arResult['ITEMS']))
    $arVisual['COMPARE']['USE'] = false;

if ($arVisual['DELAY']['USE'] && !$arVisual['BASKET']['USE'])
    $arVisual['DELAY']['USE'] = false;

$arGallery = [];
$sMode = $this->getComponent()->getMode();
$sIdField = $sMode === 'code' ? 'CODE' : 'ID';

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'URL' => [
            'USE' => false,
            'VALUE' => null
        ],
        'ICON' => [
            'SHOW' => false,
            'VALUE' => []
        ],
        'COMPARE' => false,
        'BASKET' => false,
        'DELAY' => false
    ];

    if (!empty($arParams['PROPERTY_URL'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_URL']
        ]);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arProperty['DISPLAY_VALUE'] = StringHelper::replaceMacros(
                    Html::stripTags($arProperty['DISPLAY_VALUE']), [
                        'SITE_DIR' => SITE_DIR
                    ]
                );

                $arItem['DATA']['URL']['USE'] = !empty($arProperty['DISPLAY_VALUE']);
                $arItem['DATA']['URL']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_ICON'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_ICON'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (!ArrayHelper::isIn($arProperty, $arGallery))
                $arGallery[] = $arProperty;
        }

        unset($arProperty);
    }

    if ($arVisual['COMPARE']['USE'] && $arItem[$sIdField] === $arParams['COMPARE_ELEMENT'])
        $arItem['DATA']['COMPARE'] = true;

    if ($arVisual['BASKET']['USE'] && $arItem[$sIdField] === $arParams['BASKET_ELEMENT'])
        $arItem['DATA']['BASKET'] = true;

    if ($arVisual['DELAY']['USE'] && $arItem[$sIdField] === $arParams['DELAY_ELEMENT'])
        $arItem['DATA']['DELAY'] = true;
}

unset($arItem);

if (!empty($arGallery)) {
    $arGallery = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arGallery)
    ]))->indexBy('ID')->each(function ($key, &$value) {
        $value['SRC'] = CFile::GetFileSRC($value);
    });

    foreach ($arResult['ITEMS'] as &$arItem) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_ICON'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if ($arGallery->exists($arProperty)) {
                $arItem['DATA']['ICON']['VALUE'] = $arGallery->get($arProperty);

                if (StringHelper::startsWith($arItem['DATA']['ICON']['VALUE']['CONTENT_TYPE'], 'image/'))
                    $arItem['DATA']['ICON']['SHOW'] = true;
            }
        }

        unset($arProperty);
    }

    unset($arItem);
}

unset($arGallery);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);