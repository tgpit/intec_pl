<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_TAGS' => null,
    'PROPERTY_ADDITIONAL_NEWS' => null,
    'DATE_SHOW' => 'N',
    'DATE_TYPE' => 'DATE_ACTIVE_FROM',
    'DATE_FORMAT' => 'd.m.Y',
    'TAGS_SHOW' => 'N',
    'TAGS_POSITION' => 'top',
    'ANCHORS_USE' => 'N',
    'ANCHORS_TAG' => 'h2',
    'ANCHORS_POSITION' => 'default',
    'ANCHORS_NUMBER' => 'N',
    'PRINT_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'IMAGE_SHOW' => 'N',
    'ADDITIONAL_NEWS_SHOW' => 'N',
    'ADDITIONAL_NEWS_HEADER_SHOW' => 'N',
    'ADDITIONAL_NEWS_HEADER_TEXT' => null,
    'ADDITIONAL_PRODUCTS_SHOW' => 'N',
    'ADDITIONAL_PRODUCTS_HEADER_SHOW' => 'N',
    'ADDITIONAL_PRODUCTS_HEADER_TEXT' => null,
    'ADDITIONAL_PRODUCTS_CATEGORIES_SHOW' => 'N',
    'ADDITIONAL_PRODUCTS_CATEGORIES_HEADER_SHOW' => 'N',
    'ADDITIONAL_PRODUCTS_CATEGORIES_HEADER_TEXT' => null,
    'BUTTON_BACK_SHOW' => 'N',
    'BUTTON_SOCIAL_SHOW' => 'N',
    'BUTTON_SOCIAL_HANDLERS' => [],
    'MICRODATA_TYPE' => 'Article',
    'MICRODATA_AUTHOR' => null,
    'MICRODATA_PUBLISHER' => null,
    'LINKING_SHOW' => 'N',
    'LINKING_PICTURE_SHOW' => 'N'
], $arParams);

if (!empty($arParams['BUTTON_SOCIAL_HANDLERS']))
    $arParams['BUTTON_SOCIAL_HANDLERS'] = array_filter($arParams['BUTTON_SOCIAL_HANDLERS']);

$arParams['TAGS_POSITION'] = ArrayHelper::fromRange([
    'top',
    'bottom',
    'both'
], $arParams['TAGS_POSITION']);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'DATE' => [
        'SHOW' => $arParams['DATE_SHOW'] === 'Y',
        'TYPE' => ArrayHelper::fromRange([
            'DATE_ACTIVE_FROM',
            'DATE_CREATE',
            'DATE_ACTIVE_TO',
            'TIMESTAMP_X'
        ],$arParams['DATE_TYPE']),
        'FORMAT' => $arParams['DATE_FORMAT']
    ],
    'TAGS' => [
        'SHOW' => $arParams['TAGS_SHOW'] === 'Y' && !empty($arParams['PROPERTY_TAGS']),
        'POSITION' => [
            'TOP' => $arParams['TAGS_POSITION'] !== 'bottom',
            'BOTTOM' => $arParams['TAGS_POSITION'] !== 'top'
        ]
    ],
    'ANCHORS' => [
        'USE' => $arParams['ANCHORS_USE'] === 'Y',
        'TAG' => !empty($arParams['ANCHORS_TAG']) ? $arParams['ANCHORS_TAG'] : 'h2',
        'POSITION' => ArrayHelper::fromRange(['default', 'fixed'], $arParams['ANCHORS_POSITION']),
        'NUMBER' => $arParams['ANCHORS_NUMBER'] === 'Y'
    ],
    'PRINT' => [
        'SHOW' => $arParams['PRINT_SHOW'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y' && !empty($arResult['PREVIEW_TEXT'])
    ],
    'IMAGE' => [
        'SHOW' => $arParams['IMAGE_SHOW'] === 'Y' && !empty($arResult['DETAIL_PICTURE'])
    ],
    'ADDITIONAL' => [
        'NEWS' => [
            'SHOW' => $arParams['ADDITIONAL_NEWS_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ADDITIONAL_NEWS']),
            'HEADER' => [
                'SHOW' => $arParams['ADDITIONAL_NEWS_HEADER_SHOW'] === 'Y',
                'TEXT' => $arParams['~ADDITIONAL_NEWS_HEADER_TEXT']
            ]
        ],
        'PRODUCTS' => [
            'SHOW' => $arParams['ADDITIONAL_PRODUCTS_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ADDITIONAL_PRODUCTS']),
            'HEADER' => [
                'SHOW' => $arParams['ADDITIONAL_PRODUCTS_HEADER_SHOW'] === 'Y',
                'TEXT' => $arParams['~ADDITIONAL_PRODUCTS_HEADER_TEXT']
            ]
        ],
        'PRODUCTS_CATEGORIES' => [
            'SHOW' => $arParams['ADDITIONAL_PRODUCTS_CATEGORIES_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ADDITIONAL_PRODUCTS_CATEGORIES']),
            'HEADER' => [
                'SHOW' => $arParams['ADDITIONAL_PRODUCTS_CATEGORIES_HEADER_SHOW'] === 'Y',
                'TEXT' => $arParams['~ADDITIONAL_PRODUCTS_CATEGORIES_HEADER_TEXT']
            ]
        ]
    ],
    'LINKING' => [
        'SHOW' => $arParams['LINKING_SHOW'] === 'Y',
        'PICTURE' => [
            'SHOW' => $arParams['LINKING_PICTURE_SHOW'] === 'Y',
        ]
    ],
    'BUTTON' => [
        'BACK' => [
            'SHOW' => $arParams['BUTTON_BACK_SHOW'] === 'Y'
        ],
        'SOCIAL' => [
            'SHOW' => $arParams['BUTTON_SOCIAL_SHOW'] === 'Y' && !empty($arParams['BUTTON_SOCIAL_HANDLERS'])
        ]
    ],
    'MICRODATA' => [
        'TYPE' => ArrayHelper::fromRange(['Article', 'NewsArticle', 'BlogPosting'],$arParams['MICRODATA_TYPE']),
        'AUTHOR' => $arParams['MICRODATA_AUTHOR'],
        'PUBLISHER' => $arParams['MICRODATA_PUBLISHER']
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['ADDITIONAL']['NEWS']['HEADER']['SHOW'] && empty($arVisual['ADDITIONAL']['NEWS']['HEADER']['TEXT']))
    $arVisual['ADDITIONAL']['NEWS']['HEADER']['SHOW'] = false;

if ($arVisual['ADDITIONAL']['PRODUCTS']['HEADER']['SHOW'] && empty($arVisual['ADDITIONAL']['PRODUCTS']['HEADER']['TEXT']))
    $arVisual['ADDITIONAL']['PRODUCTS']['HEADER']['SHOW'] = false;

if ($arVisual['ADDITIONAL']['PRODUCTS_CATEGORIES']['HEADER']['SHOW'] && empty($arVisual['ADDITIONAL']['PRODUCTS_CATEGORIES']['HEADER']['TEXT']))
    $arVisual['ADDITIONAL']['PRODUCTS_CATEGORIES']['HEADER']['SHOW'] = false;

$arData = [
    'DATE' => null,
    'TAGS' => [],
    'ADDITIONAL' => [
        'NEWS' => []
    ]
];

/** Дата */
$sDate = $arResult['DATE_CREATE'];

if (!empty($arResult[$arVisual['DATE']['TYPE']]))
    $sDate = $arResult[$arVisual['DATE']['TYPE']];

if (!empty($sDate))
    $arData['DATE'] = CIBlockFormatProperties::DateFormat(
        $arVisual['DATE']['FORMAT'],
        MakeTimeStamp($sDate, CSite::GetDateFormat())
    );

if ($arVisual['DATE']['SHOW'] && empty($sDate))
    $arVisual['DATE']['SHOW'] = false;

unset($sDate);

/** Теги */
$arTags = [];

if (!empty($arParams['PROPERTY_TAGS'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_TAGS']
    ]);

    if (!empty($arProperty['VALUE']) && !Type::isArray($arProperty['VALUE']))
        $arProperty['VALUE'] = explode(',', $arProperty['VALUE']);

    if (!empty($arProperty['VALUE'])) {

        foreach ($arProperty['VALUE'] as $sValue) {
            if (!empty($sValue))
                $arTags[] = $sValue;
        }

        unset($sValue);
    }

    unset($arProperty);
}

if ($arVisual['TAGS']['SHOW'] && empty($arTags))
    $arVisual['TAGS']['SHOW'] = false;

if (!empty($arTags))
    $arData['TAGS'] = $arTags;

unset($arTags);

$arAdditional = function ($paramName) use (&$arResult, &$arParams) {
    $arAdditional = [];

    if (!empty($arParams[$paramName])) {
        $arProperty = ArrayHelper::getValue($arResult, [
            'PROPERTIES',
            $arParams[$paramName]
        ]);

        if (!empty($arProperty['VALUE']) && !Type::isArray($arProperty['VALUE']))
            $arProperty['VALUE'] = explode(',', $arProperty['VALUE']);

        if (!empty($arProperty['VALUE'])) {
            foreach ($arProperty['VALUE'] as $sValue) {
                if (!empty($sValue))
                    $arAdditional[] = $sValue;
            }
        }
    }

    return $arAdditional;
};

/** Читайте также */
$arAdditionalNews = $arAdditional('PROPERTY_ADDITIONAL_NEWS');

if ($arVisual['ADDITIONAL']['NEWS']['SHOW'] && empty($arAdditionalNews))
    $arVisual['ADDITIONAL']['NEWS']['SHOW'] = false;

if (!empty($arAdditionalNews))
    $arData['ADDITIONAL']['NEWS'] = $arAdditionalNews;

unset($arAdditionalNews);

/** Товары */
$arAdditionalProducts = $arAdditional('PROPERTY_ADDITIONAL_PRODUCTS');

if ($arVisual['ADDITIONAL']['PRODUCTS']['SHOW'] && empty($arAdditionalProducts))
    $arVisual['ADDITIONAL']['PRODUCTS']['SHOW'] = false;

if (!empty($arAdditionalProducts))
    $arData['ADDITIONAL']['PRODUCTS'] = $arAdditionalProducts;

unset($arAdditionalProducts);

/**Разделы товаров*/
$arAdditionalProductsCategories = $arAdditional('PROPERTY_ADDITIONAL_PRODUCTS_CATEGORIES');

if ($arVisual['ADDITIONAL']['PRODUCTS_CATEGORIES']['SHOW'] && empty($arAdditionalProductsCategories))
    $arVisual['ADDITIONAL']['PRODUCTS_CATEGORIES']['SHOW'] = false;

if (!empty($arAdditionalProductsCategories))
    $arData['ADDITIONAL']['PRODUCTS_CATEGORIES'] = $arAdditionalProductsCategories;

unset($arAdditionalProductsCategories);

/** Перелинковка */
if ($arVisual['LINKING']['SHOW']) {
    $arResult['LINKING'] = [];

    $arParams['SORT_BY1'] = trim($arParams['SORT_BY1']);

    if($arParams['SORT_BY1'] == '')
        $arParams['SORT_BY1'] = 'ACTIVE_FROM';

    if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams['SORT_ORDER1']))
        $arParams['SORT_ORDER1'] = 'DESC';

    if($arParams['SORT_BY2'] === '') {
        if (mb_strtoupper($arParams['SORT_BY1']) === 'SORT') {
            $arParams['SORT_BY2'] = 'ID';
            $arParams['SORT_ORDER2'] = 'DESC';
        } else {
            $arParams['SORT_BY2'] = 'SORT';
        }
    }

    if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams['SORT_ORDER2']))
        $arParams['SORT_ORDER2'] = 'ASC';

    $arSort = [
        $arParams['SORT_BY1'] => $arParams['SORT_ORDER1'],
        $arParams['SORT_BY2'] => $arParams['SORT_ORDER2'],
    ];

    if(!array_key_exists('ID', $arSort))
        $arSort['ID'] = 'DESC';

    $arNavigation = Arrays::fromDBResult(CIBlockElement::GetList(
        $arSort, [
            'IBLOCK_ID' => $arResult['IBLOCK_ID'],
            'ACTIVE_DATE' => 'Y',
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => $arResult['IBLOCK_SECTION_ID']
        ],
        false, [
            'nPageSize' => 1,
            'nElementID' => $arResult['ID']
        ], [
            'ID',
            'IBLOCK_ID',
            'SORT',
            'NAME',
            'DETAIL_PAGE_URL',
            'PREVIEW_PICTURE',
            'DETAIL_PICTURE',
            'SORT'
        ]
    ), true)->asArray();

    $arNav = [];
    $iCurrentItemRank = null;

    foreach ($arNavigation as &$arNavItem) {
        if ($arNavItem['ID'] === $arResult['ID']) {
            $iCurrentItemRank = $arNavItem['RANK'];

            continue;
        }

        $arNav[$arNavItem['ID']] = $arNavItem;

        if (!empty($arNavItem['PREVIEW_PICTURE']) && !Type::isArray($arNavItem['PREVIEW_PICTURE']))
            $arFiles[] = $arNavItem['PREVIEW_PICTURE'];

        if (!empty($arNavItem['DETAIL_PICTURE']) && !Type::isArray($arNavItem['DETAIL_PICTURE']))
            $arFiles[] = $arNavItem['DETAIL_PICTURE'];
    }

    if (!empty($arFiles)) {
        $arFiles = Arrays::fromDBResult(CFile::GetList([], [
            '@ID' => implode(',', $arFiles)
        ]))->each(function ($iIndex, &$arFile) {
            $arFile['SRC'] = CFile::GetFileSRC($arFile);
        })->indexBy('ID');
    } else {
        $arFiles = new Arrays();
    }

    foreach ($arNav as &$arItem) {
        if ($arItem['RANK'] > $iCurrentItemRank)
            $arItem['SUBTITLE'] = Loc::getMessage('C_NEWS_DETAIL_DEFAULT_1_NEXT_ITEM_TEXT');

        if ($arItem['RANK'] < $iCurrentItemRank)
            $arItem['SUBTITLE'] = Loc::getMessage('C_NEWS_DETAIL_DEFAULT_1_PREVIOUS_ITEM_TEXT');

        if (!empty($arItem['PREVIEW_PICTURE']) && !Type::isArray($arItem['PREVIEW_PICTURE']))
            $arItem['PREVIEW_PICTURE'] = $arFiles->get($arItem['PREVIEW_PICTURE']);

        if (!empty($arItem['DETAIL_PICTURE']) && !Type::isArray($arItem['DETAIL_PICTURE']))
            $arItem['DETAIL_PICTURE'] = $arFiles->get($arItem['DETAIL_PICTURE']);
    }

    $arResult['LINKING'] = $arNav;

    unset($arNavigation, $arNav);
}

/** Оглавление */
$arData['ANCHORS'] = [
    'ITEMS' => []
];

if ($arVisual['ANCHORS']['USE']) {
    if (!empty($arResult['DETAIL_TEXT'])) {
        $anchors = [];

        preg_match_all(
            '|<[^>]+>(.*)<[\/'.$arVisual['ANCHORS']['TAG'].']+>|U',
            $arResult['DETAIL_TEXT'],
            $anchors
        );

        if (!empty($anchors[0])) {
            $tagBeginOffset = StringHelper::length($arVisual['ANCHORS']['TAG']) + 1;

            foreach ($anchors[0] as $key => $anchor) {
                if (StringHelper::startsWith($anchor, '<'.$arVisual['ANCHORS']['TAG'])) {
                    $tagEndOffset = StringHelper::position('>', $anchor);
                    $tagInner = StringHelper::cut(
                        $anchor,
                        $tagBeginOffset,
                        $tagEndOffset - $tagBeginOffset
                    );

                    if (!empty($tagInner)) {
                        $idStart = StringHelper::position(' id="', $anchor);

                        if ($idStart) {
                            $idStart = $idStart + 5;
                            $idEnd = StringHelper::position('"', $anchor, $idStart);
                            $id = StringHelper::cut($anchor, $idStart, $idEnd - $idStart);

                            if (!empty($id)) {
                                $arData['ANCHORS']['ITEMS'][] = [
                                    'ID' => $id,
                                    'PRINT' => Html::stripTags($anchors[1][$key])
                                ];
                            }

                            unset($idEnd, $id);
                        }

                        unset($idStart);
                    }

                    unset($tagEndOffset, $tagInner);
                }
            }

            unset($anchor);
        }

        unset($anchors);
    }

    if (empty($arData['ANCHORS']['ITEMS']))
        $arVisual['ANCHORS']['USE'] = false;
}

/** Результирующий массив */
$arResult['VISUAL'] = $arVisual;
$arResult['DATA'] = $arData;

unset($arVisual, $arData);

$this->__component->SetResultCacheKeys([
    'PREVIEW_PICTURE',
    'DETAIL_PICTURE',
    'DATA',
    'VISUAL'
]);