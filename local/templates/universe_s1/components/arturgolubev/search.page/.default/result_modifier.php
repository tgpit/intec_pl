<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'BLOCK_ON_EMPTY_RESULTS' => [
        'SHOW' => false,
        'TITLE' => null,
        'IS_CATALOG' => false,
        'ITEMS' => null
    ]
]);

$arResult['TAGS_CHAIN'] = [];

if ($arResult['REQUEST']['~TAGS']) {
	$res = array_unique(explode(',', $arResult['REQUEST']['~TAGS']));
	$url = [];

	foreach ($res as $key => $tags)	{
		$tags = trim($tags);

		if (!empty($tags)) {
			$url_without = $res;
			unset($url_without[$key]);
			$url[$tags] = $tags;
			$result = [
				'TAG_NAME' => htmlspecialcharsex($tags),
				'TAG_PATH' => $APPLICATION->GetCurPageParam('tags='.urlencode(implode(',', $url)), ['tags']),
				'TAG_WITHOUT' => $APPLICATION->GetCurPageParam((count($url_without) > 0 ? 'tags='.urlencode(implode(',', $url_without)) : ''), ['tags']),
			];
			$arResult['TAGS_CHAIN'][] = $result;
		}
	}
}

if (!empty($arResult['SEARCH'])) {
    $arItemsIDFilter = [];
    $arItemsID = [];

    foreach($arResult['SEARCH'] as $keyItem => $arItem) {
        $arItemsIDFilter[] = $arItem['ITEM_ID'];
        $arItemsID[$arItem['ITEM_ID']]['ID'] = $arItem['ITEM_ID'];
        $arItemsID[$arItem['ITEM_ID']]['KEY'] = $keyItem;
    }

    CModule::IncludeModule('iblock');
    $dbItems = CIBlockElement::GetList([], ['ID'=> $arItemsIDFilter], false, false, ['ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE']);

    while ($arElement = $dbItems->Fetch()) {
        if (!empty($arElement['PREVIEW_PICTURE'])) {
            $picture = CFile::ResizeImageGet($arElement['PREVIEW_PICTURE'], ['width' => 270, 'height' => 270, BX_RESIZE_IMAGE_PROPORTIONAL_ALT]);
            $arResult['SEARCH'][$arItemsID[$arElement['ID']]['KEY']]['PICTURE'] = $picture['src'];
        } else if (!empty($arElement['DETAIL_PICTURE'])) {
            $picture = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], ['width' => 270, 'height' => 270, BX_RESIZE_IMAGE_PROPORTIONAL_ALT]);
            $arResult['SEARCH'][$arItemsID[$arElement['ID']]['KEY']]['PICTURE'] = $picture['src'];
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
