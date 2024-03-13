<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bSeo = Loader::includeModule('intec.seo');

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'Y',
    'DESCRIPTION_SHOW' => 'N',
    'DESCRIPTION_LINK_USE' => 'N',
    'DESCRIPTION' => null,
    'SECTION_DESCRIPTION_SHOW' => 'Y',
    'ELEMENTS_SHOW' => 'Y',
    'COUNT_ELEMENTS' => false,
    '~COUNT_ELEMENTS' => 'N',
    'WIDE' => 'Y'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y'
    ],
    'ELEMENTS' => [
        'SHOW' => $arParams['ELEMENTS_SHOW'] ==='Y',
        'QUANTITY' => $arParams['~COUNT_ELEMENTS'] === 'Y'
    ],
    'WIDE' => $arParams['WIDE'] === 'Y',
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
        'LINK' => $arParams['DESCRIPTION_LINK_USE'] === 'Y',
        'VALUE' => $arParams['DESCRIPTION']
    ],
    'SECTION' => [
        'DESCRIPTION' => [
            'SHOW' => $arParams['SECTION_DESCRIPTION_SHOW'] === 'Y'
        ]
    ]
];

$arSections = [];

foreach($arResult['SECTIONS'] as $arSection) {
    if (!empty($arSection['PICTURE'])) {
        $arSection['PICTURE']['TITLE'] = ArrayHelper::getValue($arSection, ['IPROPERTY_VALUES', 'SECTION_PICTURE_FILE_TITLE']);
        $arSection['PICTURE']['ALT'] = ArrayHelper::getValue($arSection, ['IPROPERTY_VALUES', 'SECTION_PICTURE_FILE_ALT']);

        if (empty($arSection['PICTURE']['TITLE']))
            $arSection['PICTURE']['TITLE'] = $arSection['NAME'];

        if (empty($arSection['PICTURE']['ALT']))
            $arSection['PICTURE']['ALT'] = $arSection['NAME'];
    }

    $arSection['SECTIONS'] = [];
    $arSections[$arSection['ID']] = $arSection;
}

unset($arSection);

if ($bSeo) {
    $arMeta = $APPLICATION->IncludeComponent('intec.seo:iblocks.metadata.loader', '', [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_ID' => ArrayHelper::getKeys($arSections),
        'TYPE' => 'section',
        'MODE' => 'multiple',
        'CACHE_TYPE' => 'N'
    ], $component);

    foreach ($arSections as &$arSection) {
        $arMetaSection = ArrayHelper::getValue($arMeta['SECTIONS'], $arSection['ID']);

        if (empty($arMetaSection))
            continue;

        if (!empty($arSection['PICTURE'])) {
            if (!empty($arMetaSection['META']['picturePreviewTitle']) || Type::isNumeric($arMetaSection['META']['picturePreviewTitle']))
                $arSection['PICTURE']['TITLE'] = $arMetaSection['META']['picturePreviewTitle'];

            if (!empty($arMetaSection['META']['picturePreviewAlt']) || Type::isNumeric($arMetaSection['META']['picturePreviewAlt']))
                $arSection['PICTURE']['ALT'] = $arMetaSection['META']['picturePreviewAlt'];
        }
    }

    unset($arMeta, $arMetaSection, $arSection);
}


$fBuild = function ($arSections) {
    $bFirst = true;

    if (empty($arSections))
        return [];

    $fBuild = function () use (&$fBuild, &$bFirst, &$arSections) {
        $iLevel = null;
        $arItems = array();
        $arItem = null;
        $arList = null;

        if ($bFirst) {
            $arItem = reset($arSections);
            $bFirst = false;
        }

        while (true) {
            if ($arItem === null) {
                $arItem = next($arSections);

                if (empty($arItem))
                    break;
            }

            if ($iLevel === null)
                $iLevel = $arItem['DEPTH_LEVEL'];

            if ($arItem['DEPTH_LEVEL'] < $iLevel) {
                prev($arSections);
                break;
            }

            if ($arItem['DEPTH_LEVEL'] > $iLevel) {
                $arItem = prev($arSections);
                $arItem['SECTIONS'] = $fBuild();
                $arItems[count($arItems) - 1] = $arItem;
            } else {
                $arItems[] = $arItem;
            }

            $arItem = null;
        }

        return $arItems;
    };

    return $fBuild();
};

$arResult['VISUAL'] = $arVisual;
$arResult['SECTIONS'] = $fBuild($arSections);

function ListId($arSection) {
    $arListId = null;

    foreach ($arSection as $arSectionItem) {
        if (empty($arSectionItem['SECTIONS'])) {
            $arListId = $arListId . "," . $arSectionItem['ID'];
        } else {
            $arListId = $arListId . ListId($arSectionItem['SECTIONS']);
            $arListId = $arListId . ",". $arSectionItem['ID'];
        }
    }

    return $arListId;
}

foreach ($arResult['SECTIONS'] as &$arSectionItem) {
    $arListId = null;

    if (!empty($arSectionItem['SECTIONS'])) {
        $arListId = ListId($arSectionItem['SECTIONS']);
        $arListId = substr($arListId,1);
    }

    if (!empty($arListId))
        $arSectionItem['LIST_ID'] = explode(",",$arListId);
}

$arItems = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arParams['IBLOCK_ID']));

while ($arElement = $arItems->GetNextElement()) {
    $arResult['ITEMS'][] = $arElement->GetFields();
}

unset($arVisual, $arItems, $arSections, $fBuild);
