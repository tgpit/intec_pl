<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var array $arSectionsId
 */

$arResult['SECTIONS'] = Arrays::fromDBResult(CIBlockSection::GetList(['sort' => 'asc'], [
    'ID' => $arSectionsId,
    'ACTIVE' => 'Y',
    'GLOBAL_ACTIVE' => 'Y',
    'CHECK_PERMISSIONS' => 'Y',
    'MIN_PERMISSION' => 'R'
]))->indexBy('ID')->asArray();

foreach ($arResult['SECTIONS'] as &$arSection)
    $arSection['ITEMS'] = [];

unset($arSection);

$arNoSection = [
    'NAME' => $arParams['SECTIONS_ROOT_NAME'],
    'DESCRIPTION' => $arParams['SECTIONS_ROOT_DESCRIPTION'],
    'ITEMS' => []
];

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['IBLOCK_SECTION_ID']))
        $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['ITEMS'][$arItem['ID']] = &$arItem;
    else if ($arVisual['SECTIONS']['ROOT'])
        $arNoSection['ITEMS'][$arItem['ID']] = &$arItem;
}

unset($arItem);

if (!empty($arNoSection['ITEMS'])) {
    if (empty($arNoSection['NAME']))
        $arNoSection['NAME'] = Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_TEMPLATE_SECTIONS_ROOT_NAME_DEFAULT');

    $arResult['SECTIONS']['ROOT'] = $arNoSection;
}

unset($arNoSection);