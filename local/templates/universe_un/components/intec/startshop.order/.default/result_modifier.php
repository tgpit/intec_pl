<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'URL_BASKET' => null,
    'URL_RULES_OF_PERSONAL_DATA_PROCESSING' => null
], $arParams);

$arVisual = [
    'CONSENT' => StringHelper::replaceMacros($arParams['URL_RULES_OF_PERSONAL_DATA_PROCESSING'], [
        'SITE_DIR' => SITE_DIR
    ])
];

if (!empty($arResult['ITEMS'])) {
    $arItemsSKUid = [];

    foreach ($arResult['ITEMS'] as $arItem) {
        if ($arItem['STARTSHOP']['OFFER']['OFFER'] && !ArrayHelper::isIn($arItem['STARTSHOP']['OFFER']['LINK'], $arItemsSKUid)) {
            $arItemsSKUid[] = $arItem['STARTSHOP']['OFFER']['LINK'];
        }
    }

    if (!empty($arItemsSKUid)) {
        $arSectionsItemSKU = Arrays::fromDBResult(CIBlockElement::GetList(
            [],
            ['ID' => $arItemsSKUid],
            false,
            false,
            ['ID', 'IBLOCK_SECTION_ID']
        ))->indexBy('ID')->asArray();
    }

    foreach ($arResult['ITEMS'] as $itemKey => $itemValue) {
        if ($itemValue['STARTSHOP']['OFFER']['OFFER']) {
            $arResult['ITEMS'][$itemKey]['IBLOCK_SECTION_ID'] = $arSectionsItemSKU[$itemValue['STARTSHOP']['OFFER']['LINK']]['IBLOCK_SECTION_ID'];
        }
    }

    $arSectionsID = [];

    foreach ($arResult['ITEMS'] as $arItem) {
        if (!empty($arItem['IBLOCK_SECTION_ID']) && !ArrayHelper::isIn($arItem['IBLOCK_SECTION_ID'], $arSectionsID)) {
            $arSectionsID[] = $arItem['IBLOCK_SECTION_ID'];
        }
    }

    if (!empty($arSectionsID)) {
        $arSections = Arrays::fromDBResult(
            CIBlockSection::GetList(
                ["SORT"=>"ASC"],
                ['ID' => $arSectionsID],
                false,
                ['ID', 'NAME','SECTION_PAGE_URL']
            ),
            true
        )->indexBy('ID')->asArray();

        foreach ($arResult['ITEMS'] as $itemKey => $itemValue) {
            $arResult['ITEMS'][$itemKey]['SECTION_INFO'] = $arSections[$itemValue['IBLOCK_SECTION_ID']];
        }
    }
}

$arResult['JS_OBJECT'] = [];

if (!empty($arResult['SUM']))
    $arResult['JS_OBJECT']['summary'] = [
        'value' => $arResult['SUM']['VALUE'],
        'print' => $arResult['SUM']['PRINT_VALUE']
    ];

if (!empty($arResult['CURRENCY']))
    $arResult['JS_OBJECT']['currency'] = [
        'format' => $arResult['CURRENCY']['FORMAT'][LANGUAGE_ID]['FORMAT'],
        'decimals' => [
            'count' => $arResult['CURRENCY']['FORMAT'][LANGUAGE_ID]['DECIMALS_COUNT'],
            'delimiter' => $arResult['CURRENCY']['FORMAT'][LANGUAGE_ID]['DELIMITER_DECIMAL'],
            'zero' => $arResult['CURRENCY']['FORMAT'][LANGUAGE_ID]['DECIMALS_DISPLAY_ZERO'] === 'Y'
        ],
        'thousands' => [
            'delimiter' => $arResult['CURRENCY']['FORMAT'][LANGUAGE_ID]['DELIMITER_THOUSANDS']
        ]
    ];

if (!empty($arResult['DELIVERIES'])) {
    $arResult['JS_OBJECT']['deliveries'] = [];

    foreach ($arResult['DELIVERIES'] as $key => &$arDelivery) {
        $arResult['JS_OBJECT']['deliveries'][$key] = [
            'price' => [
                'value' => $arDelivery['PRICE']['VALUE'],
                'print' => $arDelivery['PRICE']['PRINT_VALUE']
            ]
        ];
    }

    unset($key, $arDelivery);
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);