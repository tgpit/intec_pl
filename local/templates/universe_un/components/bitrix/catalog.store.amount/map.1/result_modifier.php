<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'MAP_TYPE' => 'yandex',
    'PICTURE_SHOW' => 'N',
    'SCHEDULE_SHOW' => 'N',
    'PHONE_SHOW' => 'N',
    'EMAIL_SHOW' => 'N',
    'DESCRIPTION_SHOW' => 'N',
    'SHOW_EMPTY_STORE' => 'N',
    'STORE_BLOCK_DESCRIPTION_USE' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'] === 'Y',
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null,
    ],
    'MAP' => [
        'TYPE' => ArrayHelper::fromRange(['yandex', 'google'], $arParams['MAP_TYPE'])
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'SCHEDULE' => [
        'SHOW' => $arParams['SCHEDULE_SHOW'] === 'Y'
    ],
    'PHONE' => [
        'SHOW' => $arParams['PHONE_SHOW'] === 'Y'
    ],
    'EMAIL' => [
        'SHOW' => $arParams['EMAIL_SHOW'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'MIN_AMOUNT' => [
        'USE' => $arParams['USE_MIN_AMOUNT'] === 'Y',
        'VALUE' => Type::toInteger($arParams['MIN_AMOUNT'])
    ],
    'GENERAL' => $arParams['SHOW_GENERAL_STORE_INFORMATION'] === 'Y',
    'DESCRIPTION_BLOCK' => [
        'SHOW' => $arParams['STORE_BLOCK_DESCRIPTION_USE'] === 'Y',
        'VALUE' => !empty($arParams['STORE_BLOCK_DESCRIPTION_TEXT']) ? Html::encode($arParams['STORE_BLOCK_DESCRIPTION_TEXT']) : Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_INFO')
    ]
];

if (!$arVisual['MIN_AMOUNT']['VALUE'])
    $arVisual['MIN_AMOUNT']['VALUE'] = 10;

$arIds = [];

if ($arResult['IS_SKU']) {
    $arEmptyStores = [];

    foreach ($arResult['JS']['STORES'] as $key => $arStore)
        $arEmptyStores[$arStore] = 0;

    unset($key, $arStore);

    foreach ($arResult['JS']['SKU'] as $key => &$arSku) {
        $arIds[] = $key;

        if (empty($arSku))
            $arSku = $arEmptyStores;
        else {
            foreach ($arEmptyStores as $store => $value) {
                if (!ArrayHelper::keyExists($store, $arSku))
                    $arSku[$store] = $value;
            }

            unset($store, $value);
        }
    }

    unset($arEmptyStores, $key, $arSku);
} else {
    $arIds[] = Type::toInteger($arParams['ELEMENT_ID']);
}

if (!empty($arIds)) {
    $arMeasures = [];
    $arIds = ProductTable::getCurrentRatioWithMeasure($arIds);

    foreach ($arIds as $key => $arMeasure)
        $arMeasures[$key] = $arMeasure['MEASURE']['SYMBOL'];

    unset($key, $arMeasure);

    if (!empty($arMeasures))
        $arResult['MEASURES'] = $arMeasures;

    unset($arMeasures);
}

unset($arIds);

$arMarkers = [];

if (!empty($arResult['STORES'])) {
    if ($arVisual['GENERAL'] && $arVisual['MIN_AMOUNT']['USE']) {
        $arFilter = [
            'PRODUCT_ID' => $arParams['ELEMENT_ID'],
            'STORE_ID' => array_filter($arParams['STORES'])
        ];

        if (!empty($arFilter['PRODUCT_ID']) && !empty($arFilter['STORE_ID'])) {
            $iCalculateAmount = 0;

            $arStores = Arrays::fromDBResult(CCatalogStoreProduct::GetList(
                ['SORT' => 'ASC'],
                $arFilter,
                false,
                false,
                ['ID', 'PRODUCT_ID', 'STORE_ID', 'AMOUNT']
            ))->asArray();

            if (!empty($arStores)) {
                foreach ($arStores as $arStore)
                    $iCalculateAmount += $arStore['AMOUNT'];
            }

            $arResult['STORES'][0]['TITLE'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_STORES_ALL');
            $arResult['STORES'][0]['AMOUNT'] = $iCalculateAmount;
        }
    }

    $arPictures = [];

    foreach ($arResult['STORES'] as $key => &$arStore) {
        if (!empty($arStore['PHONE']))
            $arStore['PHONE'] = [
                'PRINT' => $arStore['PHONE'],
                'HTML' => StringHelper::replace($arStore['PHONE'], [
                    '(' => '',
                    ')' => '',
                    '-' => '',
                    ' ' => ''
                ])
            ];
        else
            $arStore['PHONE'] = [];

        $arStore['PICTURE'] = [];

        if (!empty($arStore['IMAGE_ID']))
            $arPictures[] = $arStore['IMAGE_ID'];

        if ($arVisual['MIN_AMOUNT']['USE']) {
            if ($arStore['REAL_AMOUNT'] > $arVisual['MIN_AMOUNT']['VALUE']) {
                $arStore['AMOUNT_STATUS'] = 'many';
                $arStore['AMOUNT_PRINT'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_MANY');
            } else if ($arStore['REAL_AMOUNT'] <= $arVisual['MIN_AMOUNT']['VALUE'] && $arStore['REAL_AMOUNT'] > 0) {
                $arStore['AMOUNT_STATUS'] = 'few';
                $arStore['AMOUNT_PRINT'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_FEW');
            } else {
                $arStore['AMOUNT_STATUS'] = 'empty';
                $arStore['AMOUNT_PRINT'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_EMPTY');
            }
        } else {
            if ($arStore['REAL_AMOUNT'] > 0)
                $arStore['AMOUNT_STATUS'] = 'many';
            else
                $arStore['AMOUNT_STATUS'] = 'empty';

            $arStore['AMOUNT_PRINT'] = $arStore['REAL_AMOUNT'];
        }

        if (!empty($arStore['COORDINATES'])) {
            $arMarkers['store-'.$arStore['ID']] = [
                'name' => $arStore['TITLE'],
                'id' => $arStore['ID'],
                'lat' => $arStore['COORDINATES']['GPS_N'],
                'lng' => $arStore['COORDINATES']['GPS_S']
            ];
        }
    }

    unset($key, $arStore);

    if (!empty($arPictures)) {
        $arPictures = Arrays::fromDBResult(CFile::GetList([], [
            '@ID' => implode(',', $arPictures)
        ]))->indexBy('ID');

        if (!$arPictures->isEmpty()) {
            foreach ($arResult['STORES'] as &$arStore) {
                if ($arPictures->exists($arStore['IMAGE_ID'])) {
                    $arStore['PICTURE'] = $arPictures->get($arStore['IMAGE_ID']);
                    $arStore['PICTURE']['SRC'] = CFile::GetFileSRC($arStore['PICTURE']);
                }
            }

            unset($arStore);
        }
    }

    unset($arPictures);
}

$arResult['JS'] = [
    'stores' => $arResult['JS']['STORES'],
    'offers' => $arResult['JS']['SKU'],
    'measures' => $arResult['MEASURES'],
    'messages' => [
        Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_MANY'),
        Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_FEW'),
        Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_EMPTY')
    ],
    'states' => [
        0 => 'many',
        1 => 'few',
        2 => 'empty'
    ],
    'parameters' => [
        'showEmptyStore' => $arParams['SHOW_EMPTY_STORE'] === 'Y',
        'useMinAmount' => $arVisual['MIN_AMOUNT']['USE'],
        'minAmount' => $arVisual['MIN_AMOUNT']['VALUE']
    ],
    'map' => ArrayHelper::fromRange(['yandex', 'google'], $arParams['MAP_TYPE']),
    'markers' => $arMarkers
];

unset($arMarkers);

$arResult['VISUAL'] = $arVisual;