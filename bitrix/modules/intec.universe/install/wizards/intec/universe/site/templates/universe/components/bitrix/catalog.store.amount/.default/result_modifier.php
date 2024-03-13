<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SHOW_EMPTY_STORE' => 'N',
    'STORE_BLOCK_DESCRIPTION_USE' => 'N'
], $arParams);

$arVisual = [
    'TITLE' => [
        'SHOW' => !empty($arParams['MAIN_TITLE']),
        'VALUE' => $arParams['MAIN_TITLE']
    ],
    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'] === 'Y',
    'MIN_AMOUNT' => [
        'USE' => $arParams['USE_MIN_AMOUNT'] === 'Y',
        'VALUE' => !empty($arParams['MIN_AMOUNT']) ? Type::toInteger($arParams['MIN_AMOUNT']) : 10
    ],
    'GENERAL' => $arParams['SHOW_GENERAL_STORE_INFORMATION'] === 'Y',
    'DESCRIPTION_BLOCK' => [
        'SHOW' => $arParams['STORE_BLOCK_DESCRIPTION_USE'] === 'Y',
        'VALUE' => !empty($arParams['STORE_BLOCK_DESCRIPTION_TEXT']) ? Html::encode($arParams['STORE_BLOCK_DESCRIPTION_TEXT']) : Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_INFO')
    ]
];

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

if (!empty($arResult['STORES'])) {
    if ($arVisual['GENERAL']) {
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
                $arVisual['SHOW_STORES'] = true;

                foreach ($arStores as $arStore)
                    $iCalculateAmount += $arStore['AMOUNT'];
            }

            $arResult['STORES'][0]['TITLE'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_STORES_ALL');
            $arResult['STORES'][0]['AMOUNT'] = $iCalculateAmount;
            $arResult['STORES'][0]['REAL_AMOUNT'] = $iCalculateAmount;
        }
    }

    foreach ($arResult['STORES'] as &$arStore) {
        $arStore['SHOW_STORE'] = false;

        if ($arVisual['MIN_AMOUNT']['USE']) {
            if ($arStore['REAL_AMOUNT'] > $arVisual['MIN_AMOUNT']['VALUE']) {
                $arStore['AMOUNT_STATUS'] = 'many';
                $arStore['AMOUNT_PRINT'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_MANY');
            } else if ($arStore['REAL_AMOUNT'] <= $arVisual['MIN_AMOUNT']['VALUE'] && $arStore['REAL_AMOUNT'] > 0) {
                $arStore['AMOUNT_STATUS'] = 'few';
                $arStore['AMOUNT_PRINT'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_FEW');
            } else {
                $arStore['AMOUNT_STATUS'] = 'empty';
                $arStore['AMOUNT_PRINT'] = Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_EMPTY');
            }
        } else {
            if ($arStore['REAL_AMOUNT'] > 0)
                $arStore['AMOUNT_STATUS'] = 'many';
            else
                $arStore['AMOUNT_STATUS'] = 'empty';

            $arStore['AMOUNT_PRINT'] = $arStore['REAL_AMOUNT'];
        }
    }

    unset($arStore);
}

if ($arResult['IS_SKU']) {
    $arJsParameters = [
        'stores' => $arResult['JS']['STORES'],
        'offers' => $arResult['JS']['SKU'],
        'measures' => $arResult['MEASURES'],
        'messages' => [
            Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_MANY'),
            Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_FEW'),
            Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_EMPTY')
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
        ]
    ];

    $arResult['JS'] = $arJsParameters;
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);