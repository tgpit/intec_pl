<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

if(!CModule::IncludeModule("iblock"))
    return;

$arParams = ArrayHelper::merge([
    'IBLOCK_TYPE' => null,
    'IBLOCK_ID' => null,
    'ELEMENT_ID' => null,
    'ELEMENT_ID_ENTER' => 'N',
    'REQUEST_NAME' => 'PRODUCT_ID',
    'PROPERTY_PRODUCTS_ACCESSORIES' => null,
    'SECTIONS_LIST_SHOW' => 'Y',
    'FILTER_SHOW' => 'Y',
    'SECTION_COMPARE_USE' => 'N',
    'SECTION_COMPARE_PATH' => null,
    'SECTION_COMPARE_NAME' => null,
    'ERROR_SHOW' => 'Y'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LIST' => [
        'SHOW' => $arParams['SECTIONS_LIST_SHOW'] === 'Y',
        'OPEN' => true,
        'ACTIVE' => 'none'
    ],
    'FILTER' => [
        'SHOW' => $arParams['FILTER_SHOW'] === 'Y' && !empty($arParams['FILTER_TEMPLATE'])
    ],
    'ERROR' => [
        'SHOW' => $arParams['ERROR_SHOW'] === 'Y'
    ]
];

$arItem = null;

if ($arParams['ELEMENT_ID_ENTER'] === 'Y' && !empty($arParams['ELEMENT_ID']))
    $iElementId = $arParams['ELEMENT_ID'];
elseif (!empty($arParams['REQUEST_NAME']) && !empty($_REQUEST[$arParams['REQUEST_NAME']]))
    $iElementId = $_REQUEST[$arParams['REQUEST_NAME']];

if (empty($iElementId) && !empty($arParams['ELEMENT_ID']))
    $iElementId = $arParams['ELEMENT_ID'];

if (!empty($iElementId)) {
    $arFilter = [
        'ID' => $iElementId,
        'ACTIVE' => 'Y'
    ];

    $arSelect = [
        'ID',
        'IBLOCK_ID',
        'NAME',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PAGE_URL',
        'IBLOCK_SECTION_ID'
    ];

    $dbResult = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, $arSelect);

    while($obResult = $dbResult->GetNextElement()){
        $arItem = $obResult->GetFields();
        $arItem['PROPERTIES'] = $obResult->GetProperties();
    }

    unset($arFilter, $arSelect, $dbResult, $iElementId);
}

if (!empty($arItem)) {
    $arItem['DATA'] = [];
    $arItemsIdsFilter = null;
    $sProductImage = null;

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        $arItem['DATA']['PREVIEW_PICTURE_PATH'] = CFile::GetPath($arItem['PREVIEW_PICTURE']);
        $sProductImage = $arItem['DATA']['PREVIEW_PICTURE_PATH'];
    }

    if (!empty($arItem['DETAIL_PICTURE'])) {
        $arItem['DATA']['DETAIL_PICTURE_PATH'] = CFile::GetPath($arItem['DETAIL_PICTURE']);

        if (empty($sProductImage))
            $sProductImage = $arItem['DATA']['DETAIL_PICTURE_PATH'];
    }

    if (empty($sProductImage))
        $sProductImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

    $arItem['DATA']['PICTURE'] = $sProductImage;

    if (!empty($arParams['PROPERTY_PRODUCTS_ACCESSORIES']))
        if (!empty($arItem['PROPERTIES'][$arParams['PROPERTY_PRODUCTS_ACCESSORIES']]['VALUE']))
            $arItemsIdsFilter['ID'] = $arItem['PROPERTIES'][$arParams['PROPERTY_PRODUCTS_ACCESSORIES']]['VALUE'];

    if (!empty($arItemsIdsFilter)) {
        $arItemsIdsFilter['ACTIVE'] = 'Y';
        $arSectionsItems = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], $arItemsIdsFilter))->indexBy('ID')->asArray();
    }

    if (!empty($arSectionsItems)) {
        foreach ($arSectionsItems as $arSectionsItem) {
            $sSectionId = $arSectionsItem['IBLOCK_SECTION_ID'];
            $arSectionsIds[] = $sSectionId;
            $arResult['DATA']['ITEMS'][$sSectionId][] = $arSectionsItem;
        }

        $arSectionsFilter = [
            'ID' => array_unique($arSectionsIds),
            'ACTIVE' => 'Y',
            'CNT_ACTIVE' => 'Y'
        ];

        $arSections = Arrays::fromDBResult(CIBlockSection::GetList([], $arSectionsFilter))->indexBy('ID')->asArray();
        $bFirst = true;

        foreach ($arSections as &$arSection) {
            $arSection['LINK_ACTIVE'] = false;

            if (empty($_REQUEST['SECTION_ID']) && $bFirst)
                $arSection['LINK_ACTIVE'] = true;

            if (!empty($_REQUEST['SECTION_ID']) && $_REQUEST['SECTION_ID'] === $arSection['ID'])
                $arSection['LINK_ACTIVE'] = true;

            $arSection['LINK'] = $APPLICATION->GetCurPage() . '?' .
                $arParams['REQUEST_NAME'] . '=' .
                $_REQUEST[$arParams['REQUEST_NAME']] . '&SECTION_ID=' .
                $arSection['ID'];

            $bFirst = false;
        }
        $arResult['DATA']['LIST'] = $arSections;

        unset($sProductImage, $arItemsIdsFilter, $arSectionsItems, $arSectionsFilter, $arSections);
    }
}
if (!empty($arResult['DATA']['LIST'])) {
    if (count($arResult['DATA']['LIST']) > 7)
        $arVisual['LIST']['ACTIVE'] = 'all';
    elseif (count($arResult['DATA']['LIST']) > 1)
        $arVisual['LIST']['ACTIVE'] = 'mobile';
}

if (!empty($arResult['DATA']['ITEMS'])) {
    $iActiveSectionId = null;
    $iCounter = 0;

    foreach ($arResult['DATA']['LIST'] as $arListItem) {
        $iCounter++;

        if ($arListItem['LINK_ACTIVE']) {
            $iActiveSectionId = $arListItem['ID'];
            break;
        }
    }

    if ($iCounter < 7)
        $arVisual['LIST']['OPEN'] = false;

    if (!empty($iActiveSectionId)) {
        if ($arVisual['FILTER']['SHOW']) {
            $arItemsId = [];

            foreach ($arResult['DATA']['ITEMS'][$iActiveSectionId] as $arSectionItem) {
                $arItemsId[] = $arSectionItem['ID'];
            }
        }

        $GLOBALS['arAccessoriesPreFilterItems'] = [
            'ID' => $arItemsId
        ];

    }

    unset($iActiveSectionId, $arItemsId);
}

$arResult['ITEM'] = $arItem;
$arResult['VISUAL'] = $arVisual;

unset($arVisual, $arData);