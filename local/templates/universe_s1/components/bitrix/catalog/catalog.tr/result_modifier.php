<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\regionality\Module as Regionality;
use intec\regionality\models\Region;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'REGIONALITY_USE' => 'N',
    'REGIONALITY_FILTER_USE' => 'N',
    'REGIONALITY_FILTER_PROPERTY' => null,
    'REGIONALITY_FILTER_STRICT' => 'N',
    'REGIONALITY_PRICES_TYPES_USE' => 'N',
    'REGIONALITY_STORES_USE' => 'N',
    'SEF_TABS_USE' => 'N',

    'INTEREST_PRODUCTS_SHOW' => 'N',
    'INTEREST_PRODUCTS_TITLE' => null,
    'INTEREST_PRODUCTS_COUNT' => 6,
    'INTEREST_PRODUCTS_POSITION' => 'footer',
    'INTEREST_PRODUCTS_PROPERTY' => null,
    'INTEREST_PRODUCTS_RANDOM_FILLING' => 'N',
    'INTEREST_PRODUCTS_PARENT_ELEMENTS_USE' => 'N',
    'INTEREST_PRODUCTS_ADDITION_COUNT_USE' => 'N',
    'INTEREST_PRODUCTS_ADDITION_COUNT' => null,

    'ADDITIONAL_ARTICLES_SHOW' => 'N',
    'ADDITIONAL_ARTICLES_HEADER_SHOW' => 'N',
    'ADDITIONAL_ARTICLES_HEADER_TEXT' => null,
], $arParams);

if (empty($arParams['FILTER_NAME']))
    $arParams['FILTER_NAME'] = 'arrCatalogFilter';

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arIBlock = null;
$arSection = null;

if (!empty($arParams['IBLOCK_ID'])) {
    $oCache = Cache::createInstance();
    $arFilter = [
        'ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ];

    if ($oCache->initCache(36000, 'IBLOCK'.serialize($arFilter), '/iblock/catalog')) {
        $arIBlock = $oCache->getVars();
    } else if ($oCache->startDataCache()) {
        $arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], $arFilter));
        $arIBlock = $arIBlocks->getFirst();
        $oCache->endDataCache($arIBlock);
    }
}

if (
    !empty($arResult['VARIABLES']['SECTION_ID']) ||
    !empty($arResult['VARIABLES']['SECTION_CODE'])
) {
    $oCache = Cache::createInstance();
    $arFilter = [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y',
        'GLOBAL_ACTIVE' => 'Y'
    ];

    if (!empty($arResult['VARIABLES']['SECTION_ID'])) {
        $arFilter['ID'] = $arResult['VARIABLES']['SECTION_ID'];
    } else {
        $arFilter['CODE'] = $arResult['VARIABLES']['SECTION_CODE'];
    }

    if ($oCache->initCache(36000, 'SECTION'.serialize($arFilter), '/iblock/catalog')) {
        $arSection = $oCache->getVars();
    } else if ($oCache->startDataCache()) {
        $arSections = Arrays::fromDBResult(CIBlockSection::GetList([], $arFilter, false, [
            '*',
            'UF_*'
        ]));

        $arSection = $arSections->getFirst();
        $oCache->endDataCache($arSection);
    }
}

if ($arParams['INTEREST_PRODUCTS_SHOW'] === 'Y') {
    $arSectionsUse = Arrays::fromDBResult(CIBlockSection::GetTreeList(
        [
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ],
        [
            'ID',
            'NAME',
            'IBLOCK_SECTION_ID'
        ]
    ))
        ->indexBy('ID')
        ->asArray();

    $arElementsUse =  Arrays::fromDBResult(CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y',
            'SECTION_GLOBAL_ACTIVE' => 'Y'
        ],
        false,
        false,
        [
            'ID',
            'NAME',
            'IBLOCK_SECTION_ID'
        ]
    ))
        ->indexBy('ID')
        ->asArray();

    $arSection['INTEREST_PRODUCTS'] = [
        'SHOW' => false,
        'TITLE' => $arParams['INTEREST_PRODUCTS_TITLE'],
        'COUNT' => $arParams['INTEREST_PRODUCTS_COUNT'],
        'POSITION' => $arParams['INTEREST_PRODUCTS_POSITION'],
        'ITEMS' => []
    ];

    $iCountInterestProducts = $arParams['INTEREST_PRODUCTS_COUNT'];
    $bParentElementUse = $arParams['INTEREST_PRODUCTS_PARENT_ELEMENTS_USE'] === 'Y';

    if ($arParams['INTEREST_PRODUCTS_ADDITION_COUNT_USE'] == 'Y' && !empty($arParams['INTEREST_PRODUCTS_ADDITION_COUNT'])) {
        $iAdditionCount = Type::toInteger($arParams['INTEREST_PRODUCTS_ADDITION_COUNT']);
        $iActiveElements = CIBlockSection::GetSectionElementsCount($arSection['ID'], ['CNT_ACTIVE' => 'Y', 'CNT_ALL' => 'Y']);
        $iCountInterestProducts = $iAdditionCount != 0 && $iActiveElements < $iAdditionCount ? ($iAdditionCount - $iActiveElements) : 0;

        unset($iAdditionCount, $iActiveElements);
    }

    if ($iCountInterestProducts != 0) {
        if (!empty($arParams['INTEREST_PRODUCTS_PROPERTY']) && !empty($arSection[$arParams['INTEREST_PRODUCTS_PROPERTY']])) {
            $arSection['INTEREST_PRODUCTS']['ITEMS'] = count($arSection[$arParams['INTEREST_PRODUCTS_PROPERTY']]) <= $iCountInterestProducts ? $arSection[$arParams['INTEREST_PRODUCTS_PROPERTY']] : ArrayHelper::slice($arSection[$arParams['INTEREST_PRODUCTS_PROPERTY']], 0, $iCountInterestProducts);
        } else if ($arParams['INTEREST_PRODUCTS_RANDOM_FILLING'] === 'Y') {
            $arCurrentSections = [];
            $arCurrentElements = [];

            foreach ($arSectionsUse as $arSectionUse) {
                if ($arSectionUse['IBLOCK_SECTION_ID'] == $arSection['IBLOCK_SECTION_ID'] && $arSectionUse['ID'] != $arSection['ID'])
                    $arCurrentSections[] = $arSectionUse['ID'];
            }

            foreach ($arElementsUse as $arElementUse) {
                if (ArrayHelper::isIn($arElementUse['IBLOCK_SECTION_ID'], $arCurrentSections))
                    $arCurrentElements[] = $arElementUse['ID'];

                if ($bParentElementUse && $arElementUse['IBLOCK_SECTION_ID'] == $arSection['ID']) {
                    ArrayHelper::unshift($arCurrentElements, $arElementUse['ID']);
                }
            }

            $arSection['INTEREST_PRODUCTS']['ITEMS'] = count($arCurrentElements) <= $iCountInterestProducts ? $arCurrentElements : ArrayHelper::slice($arCurrentElements, 0, $iCountInterestProducts);

            unset($arCurrentSections, $arCurrentElements);
        }
    }

    unset($iCountInterestProducts, $bParentElementUse);

    $arSection['INTEREST_PRODUCTS']['SHOW'] = !empty($arSection['INTEREST_PRODUCTS']['ITEMS']);

    unset($arSectionsUse, $arElementsUse);
}

$arResult['IBLOCK'] = $arIBlock;
$arResult['SECTION'] = $arSection;

$arResult['REGIONALITY'] = [
    'USE' => $arParams['REGIONALITY_USE'] === 'Y',
    'FILTER' => [
        'USE' => $arParams['REGIONALITY_FILTER_USE'] === 'Y',
        'PROPERTY' => $arParams['REGIONALITY_FILTER_PROPERTY'],
        'STRICT' => $arParams['REGIONALITY_FILTER_STRICT'] === 'Y'
    ],
    'PRICES' => [
        'USE' => $arParams['REGIONALITY_PRICES_TYPES_USE'] === 'Y'
    ],
    'STORES' => [
        'USE' => $arParams['REGIONALITY_STORES_USE'] === 'Y'
    ]
];

$arResult['ADDITIONAL'] = [
    'ARTICLES' => [
        'SHOW' => $arParams['ADDITIONAL_ARTICLES_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ADDITIONAL_ARTICLES']),
        'HEADER' => [
            'SHOW' => $arParams['ADDITIONAL_ARTICLES_HEADER_SHOW'] === 'Y',
            'TEXT' => $arParams['~ADDITIONAL_ARTICLES_HEADER_TEXT']
        ]
    ],
];

$arAdditional = function ($paramName) use (&$arResult, &$arParams) {

    $arAdditional = [];

    if (!empty($arParams[$paramName])) {
        $arProperty = ArrayHelper::getValue($arResult, [
            'SECTION',
            $arParams[$paramName]
        ]);

        if (!empty($arProperty)) {
            foreach ($arProperty as $sValue) {
                if (!empty($sValue))
                    $arAdditional[] = $sValue;
            }

        }

    }

    return $arAdditional;
};

$arAdditionalArticles = $arAdditional('PROPERTY_ADDITIONAL_ARTICLES');

if ($arResult['ADDITIONAL']['ARTICLES']['SHOW'] && empty($arAdditionalArticles))
    $arResult['ADDITIONAL']['ARTICLES']['SHOW'] = false;

if (!empty($arAdditionalArticles))
    $arResult['ADDITIONAL']['ARTICLES']['VALUE'] = $arAdditionalArticles;

unset($arAdditionalArticles);

if (empty($arIBlock) || !Loader::includeModule('intec.regionality'))
    $arResult['REGIONALITY']['USE'] = false;

if (empty($arResult['REGIONALITY']['FILTER']['PROPERTY']))
    $arResult['REGIONALITY']['FILTER']['USE'] = false;

$arResult['PARAMETERS'] = [
    'COMMON' => [
        'FORM_ID',
        'FORM_TEMPLATE',
        'FORM_PROPERTY_PRODUCT',
        'FORM_REQUEST_ID',
        'FORM_REQUEST_TEMPLATE',
        'FORM_REQUEST_PROPERTY_PRODUCT',
        'PROPERTY_MARKS_RECOMMEND',
        'PROPERTY_MARKS_NEW',
        'PROPERTY_MARKS_HIT',
        'PROPERTY_MARKS_SHARE',
        'PROPERTY_ORDER_USE',
        'PROPERTY_REQUEST_USE',
        'CONSENT_URL',
        'LAZY_LOAD',
        'LOAD_ON_SCROLL',
        'VOTE_MODE',
        'DELAY_USE',
        'QUANTITY_MODE',
        'QUANTITY_BOUNDS_FEW',
        'QUANTITY_BOUNDS_MANY',

        'VIDEO_IBLOCK_TYPE',
        'VIDEO_IBLOCK_ID',
        'VIDEO_PROPERTY_URL',
        'SERVICES_IBLOCK_TYPE',
        'SERVICES_IBLOCK_ID',
        'REVIEWS_IBLOCK_TYPE',
        'REVIEWS_IBLOCK_ID',
        'REVIEWS_PROPERTY_ELEMENT_ID',
        'REVIEWS_USE_CAPTCHA',
        'PROPERTY_ARTICLE',
        'PROPERTY_BRAND',
        'PROPERTY_PICTURES',
        'PROPERTY_SERVICES',
        'PROPERTY_DOCUMENTS',
        'PROPERTY_ADDITIONAL',
        'PROPERTY_ASSOCIATED',
        'PROPERTY_RECOMMENDED',
        'PROPERTY_VIDEO',
        'OFFERS_PROPERTY_ARTICLE',
        'OFFERS_PROPERTY_PICTURES',

        'CONVERT_CURRENCY',
        'CURRENCY_ID',
        'PRICE_CODE'
    ]
];

if ($arResult['REGIONALITY']['USE']) {
    $oRegion = Region::getCurrent();

    if (!empty($oRegion)) {
        if ($arResult['REGIONALITY']['FILTER']['USE']) {
            if (!isset($GLOBALS[$arParams['FILTER_NAME']]) || !Type::isArray($GLOBALS[$arParams['FILTER_NAME']]))
                $GLOBALS[$arParams['FILTER_NAME']] = [];

            $arConditions = [
                'LOGIC' => 'OR',
                ['PROPERTY_'.$arResult['REGIONALITY']['FILTER']['PROPERTY'] => $oRegion->id]
            ];

            if (!$arResult['REGIONALITY']['FILTER']['STRICT'])
                $arConditions[] = ['PROPERTY_'.$arResult['REGIONALITY']['FILTER']['PROPERTY'] => false];

            $GLOBALS[$arParams['FILTER_NAME']][] = $arConditions;
        }

        if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
            if ($arResult['REGIONALITY']['PRICES']['USE']) {
                $arParams['FILTER_PRICE_CODE'] = $_SESSION[Regionality::VARIABLE][Region::VARIABLE]['PRICES']['CODE'];
                $arParams['PRICE_CODE'] = $_SESSION[Regionality::VARIABLE][Region::VARIABLE]['PRICES']['CODE'];
            }

            if ($arResult['REGIONALITY']['STORES']['USE'])
                $arParams['STORES'] = $_SESSION[Regionality::VARIABLE][Region::VARIABLE]['STORES']['ID'];
        } else if (Loader::includeModule('intec.startshop')) {
            if ($arResult['REGIONALITY']['PRICES']['USE'])
                $arParams['PRICE_CODE'] = $_SESSION[Regionality::VARIABLE][Region::VARIABLE]['PRICES']['CODE'];
        }
    }
}

if ($arParams['SEF_TABS_USE'] !== 'Y') {
    unset($arResult['URL_TEMPLATES']['tabs']);
    unset($arResult['VARIABLES']['TAB']);
}

if ($arParams['SEF_MODE'] === 'N') {
    if ($arParams['SEF_TABS_USE'] === 'Y') {
        $arResult['URL_TEMPLATES']['tabs'] = Html::encode($APPLICATION->GetCurPage()).'?'.
            $arResult['ALIASES']['SECTION_ID'].'=#SECTION_ID#&'.
            $arResult['ALIASES']['ELEMENT_ID'].'=#ELEMENT_ID#&'.
            $arResult['ALIASES']['TAB'].'=#TAB#';
    }
}

$arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS'] = [
    'SHOW' => false,
    'TITLE' => null,
    'IS_CATALOG' => false,
    'ITEMS' => null
];

if (!empty($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID'])) {
    $arFilter = [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID']
    ];

    if ($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_USE'] === 'Y' && !empty($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_FILTER'])) {
        $arFilter['!PROPERTY_'.$arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_FILTER'].'_VALUE'] = false;
    }

    $arEmptyResultElements = array_column(Arrays::fromDBResult(CIBlockElement::GetList(
        [],
        $arFilter,
        false,
        ['nTopCount' => !empty($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_COUNT_ELEMENTS']) ? $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_COUNT_ELEMENTS'] : '6'],
        ['ID'])
    )->asArray(), 'ID');

    if (!empty($arEmptyResultElements) && !empty($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_TEMPLATE'])) {
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['SHOW'] = true;
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IBLOCK_TYPE'] = $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_TYPE'];
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IBLOCK_ID'] = $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID'];
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['TEMPLATE'] = $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_TEMPLATE'];
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['ITEMS'] = $arEmptyResultElements;
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IS_CATALOG'] = Loader::includeModule('catalog') && Loader::includeModule('sale') && $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_IS_CATALOG'] === 'Y';
        $arResult['BLOCK_ON_EMPTY_SEARCH_RESULTS']['TITLE'] = $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS_TITLE'];
    }
}
