<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N'
], $arParams);


if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'BLOCK_ON_EMPTY_RESULTS' => [
        'SHOW' => false,
        'TITLE' => null,
        'IS_CATALOG' => false,
        'ITEMS' => null
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if (!empty($arResult['SEARCH'])) {
    $arItemsId = [];

    foreach ($arResult['SEARCH'] as $arItem) {
        if (Type::isNumeric($arItem['ITEM_ID'])) {
            $arItemsId[] = $arItem['ITEM_ID'];
        }
    }

    if (!empty($arItemsId)) {
        $arPicturesId = Arrays::fromDBResult(CIBlockElement::GetList([], ['ID' => $arItemsId]))->indexBy('ID');
        $arPicturesId = $arPicturesId->asArray(function ($sKey, $arElement) {
            $iPicture = $arElement['PREVIEW_PICTURE'];

            if (empty($iPicture))
                $iPicture = $arElement['DETAIL_PICTURE'];

            if (empty($iPicture))
                return ['skip' => true];

            return [
                'key' => $arElement['ID'],
                'value' => $iPicture
            ];
        });

        if (!empty($arPicturesId)) {
            $arPictures = Arrays::fromDBResult(CFile::GetList([], ['@ID' => implode(',', $arPicturesId)]))->indexBy('ID');
            $arPictures = $arPictures->asArray(function ($sKey, $arPicture) {
                return [
                    'key' => $arPicture['ID'],
                    'value' => $arPicture
                ];
            });
        }
    }

    foreach ($arResult['SEARCH'] as &$arItem) {
        if (Type::isNumeric($arItem['ITEM_ID'])) {
            $arItem['PICTURE'] = $arPictures[$arPicturesId[$arItem['ITEM_ID']]];
        }
    }
}

if (!empty($arParams['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'])) {
    $arFilter = [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arParams['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID']
    ];

    if ($arParams['BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'] === 'Y' && !empty($arParams['BLOCK_ON_EMPTY_RESULTS_PROPERTY_FILTER'])) {
        $arFilter['!PROPERTY_'.$arParams['BLOCK_ON_EMPTY_RESULTS_PROPERTY_FILTER'].'_VALUE'] = false;
    }

    $arEmptyResultElements = array_column(Arrays::fromDBResult(CIBlockElement::GetList(
        [],
        $arFilter,
        false,
        ['nTopCount' => !empty($arParams['BLOCK_ON_EMPTY_RESULTS_COUNT_ELEMENTS']) ? $arParams['BLOCK_ON_EMPTY_RESULTS_COUNT_ELEMENTS'] : '6'],
        ['ID'])
    )->asArray(), 'ID');

    if (!empty($arEmptyResultElements) && !empty($arParams['BLOCK_ON_EMPTY_RESULTS_TEMPLATE'])) {
        $arVisual['BLOCK_ON_EMPTY_RESULTS']['SHOW'] = true;
        $arVisual['BLOCK_ON_EMPTY_RESULTS']['ITEMS'] = $arEmptyResultElements;
        $arVisual['BLOCK_ON_EMPTY_RESULTS']['IS_CATALOG'] = Loader::includeModule('catalog') && Loader::includeModule('sale') && $arParams['BLOCK_ON_EMPTY_RESULTS_IS_CATALOG'] === 'Y';
        $arVisual['BLOCK_ON_EMPTY_RESULTS']['TITLE'] = $arParams['BLOCK_ON_EMPTY_RESULTS_TITLE'];
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);