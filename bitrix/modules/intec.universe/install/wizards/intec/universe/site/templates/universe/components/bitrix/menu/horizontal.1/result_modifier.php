<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'CATALOG_LINKS' => null,
    'PROPERTY_IMAGE' => null,
    'PROPERTY_IMAGE_ELEMENTS' => null,
    'SECTION_VIEW' => null,
    'SECTION_BANNER_IBLOCK_ID' => null,
    'SECTION_BANNER_PROPERTY_SECTION' => null,
    'SECTION_BANNER_PROPERTY_LINK' => null,
    'SECTION_BANNER_SHOW' => 'Y',
    'SECTION_BANNER_SHOW_ICONS_ROOT_ITEMS' => 'N',
    'SUBMENU_VIEW' => 'simple.1'
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arParams['SECTION_VIEW'] = ArrayHelper::fromRange(['default', 'images', 'information', 'banner'], $arParams['SECTION_VIEW']);
$arParams['SUBMENU_VIEW'] = ArrayHelper::fromRange(['simple.1', 'simple.2'], $arParams['SUBMENU_VIEW']);

$sPageUrl = $APPLICATION->GetCurPage();

foreach ($arResult as &$arItem) {
    $arItem['ACTIVE'] = false;

    if ($arItem['LINK'] == $sPageUrl)
        $arItem['ACTIVE'] = true;

    unset($arItem);
}

/**
 * @param array $arResult
 * @return array
 */

$fBuild = function ($arResult) {
    $bFirst = true;

    if (empty($arResult))
        return [];

    $fBuild = function () use (&$fBuild, &$bFirst, &$arResult) {
        $iLevel = null;
        $arItems = array();
        $arItem = null;

        if ($bFirst) {
            $arItem = reset($arResult);
            $bFirst = false;
        }

        while (true) {
            if ($arItem === null) {
                $arItem = next($arResult);

                if (empty($arItem))
                    break;
            }

            if ($iLevel === null)
                $iLevel = $arItem['DEPTH_LEVEL'];

            if ($arItem['DEPTH_LEVEL'] < $iLevel) {
                prev($arResult);
                break;
            }

            if ($arItem['IS_PARENT'] === true)
                $arItem['ITEMS'] = $fBuild();

            $arItems[] = $arItem;
            $arItem = null;
        }

        return $arItems;
    };

    return $fBuild();
};

$arFiles = Collection::from([]);

foreach ($arResult as $sKey => $arItem) {
    $arResult[$sKey]['IMAGE'] = null;

    if (!empty($arItem['PARAMS']['ELEMENT'])) {
        $arElement = &$arItem['PARAMS']['ELEMENT'];

        if (!empty($arParams['PROPERTY_IMAGE_ELEMENTS']) && !empty($arElement['PROPERTIES'][$arParams['PROPERTY_IMAGE_ELEMENTS']]['VALUE'])) {
            $sPicture = ArrayHelper::getValue($arElement, [
                'PROPERTIES',
                $arParams['PROPERTY_IMAGE_ELEMENTS'],
                'VALUE'
            ]);

            if (Type::isArray($sPicture))
                $sPicture = ArrayHelper::getFirstValue($sPicture);

            $arResult[$sKey]['IMAGE'] = $sPicture;
        } else if (!empty($arElement['PREVIEW_PICTURE'])) {
            $arResult[$sKey]['IMAGE'] = $arElement['PREVIEW_PICTURE'];
        } else if (!empty($arElement['DETAIL_PICTURE'])) {
            $arResult[$sKey]['IMAGE'] = $arElement['DETAIL_PICTURE'];
        }
    } else if (!empty($arItem['PARAMS']['SECTION'])) {
        $arSection = &$arItem['PARAMS']['SECTION'];

        if (!empty($arParams['PROPERTY_IMAGE']) && !empty($arSection[$arParams['PROPERTY_IMAGE']])) {
            $arResult[$sKey]['IMAGE'] = $arSection[$arParams['PROPERTY_IMAGE']];
        } elseif (!empty($arSection['PICTURE'])) {
            $arResult[$sKey]['IMAGE'] = $arSection['PICTURE'];
        }
    }

    if (!empty($arResult[$sKey]['IMAGE']))
        if (!$arFiles->has($arResult[$sKey]['IMAGE']))
            $arFiles->add($arResult[$sKey]['IMAGE']);
}

unset($arElement);
unset($arSection);

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

foreach ($arResult as $sKey => $arItem) {
    if (!empty($arResult[$sKey]['IMAGE']))
        $arResult[$sKey]['IMAGE'] = $arFiles->get($arResult[$sKey]['IMAGE']);
}

if (!empty($arParams['CATALOG_LINKS']) && Type::isArrayable($arParams['CATALOG_LINKS'])) {
    foreach ($arParams['CATALOG_LINKS'] as $sKey => $sCatalogLink)
        $arParams['CATALOG_LINKS'][$sKey] = StringHelper::replaceMacros($sCatalogLink, $arMacros);

    foreach ($arResult as $sKey => $arItem)
        if (ArrayHelper::isIn(
            $arItem['LINK'],
            $arParams['CATALOG_LINKS']
        )) $arResult[$sKey]['IS_CATALOG'] = 'Y';
}

$arResult = $fBuild($arResult);
$arImageIdList = array();
$arImageParam = array();
$arSectionBanners = array();

if (
    !empty($arResult) &&
    $arParams['SECTION_BANNER_SHOW'] === 'Y' &&
    !empty($arParams['SECTION_BANNER_IBLOCK_ID']) &&
    !empty($arParams['SECTION_BANNER_PROPERTY_LINK']) &&
    !empty($arParams['SECTION_BANNER_PROPERTY_SECTION'])
) {
    $rsSectionBanner = CIBlockElement::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arParams['SECTION_BANNER_IBLOCK_ID']
    ]);

    while ($arBanners = $rsSectionBanner->GetNextElement()) {
        $arSectionBanner = $arBanners->GetFields();
        $arSectionBannerProperties = $arBanners->GetProperties();

        if (!empty($arSectionBannerProperties[$arParams['SECTION_BANNER_PROPERTY_SECTION']]['VALUE'])) {
            if (!empty($arSectionBanner['PREVIEW_PICTURE']) || !empty($arSectionBanner['DETAIL_PICTURE'])) {

                if (!empty($arSectionBanner['PREVIEW_PICTURE'])) {
                    $arImageIdList[] = $arSectionBanner['PREVIEW_PICTURE'];
                    $arSectionBanner['PROPERTIES']['IMAGE_ID'] = $arSectionBanner['PREVIEW_PICTURE'];
                }
                elseif (!empty($arSectionBanner['DETAIL_PICTURE'])) {
                    $arImageIdList[] = $arSectionBanner['DETAIL_PICTURE'];
                    $arSectionBanner['PROPERTIES']['IMAGE_ID'] = $arSectionBanner['DETAIL_PICTURE'];
                }

                $arSectionBanner['PROPERTIES']['SECTION_ID'] = $arSectionBannerProperties[$arParams['SECTION_BANNER_PROPERTY_SECTION']]['VALUE'];
                $arSectionBanner['PROPERTIES']['LINK'] = $arSectionBannerProperties[$arParams['SECTION_BANNER_PROPERTY_LINK']]['VALUE'];
                $arSectionBanners[] = $arSectionBanner;
            }
        }
    }
}

if (!empty($arImageIdList)) {
    $arDbRes = CFile::GetList(array(), array('@ID' => $arImageIdList));

    while($arImageParam = $arDbRes->GetNext()) {
        foreach ($arSectionBanners as &$arItems) {
            if ($arItems['PROPERTIES']['IMAGE_ID'] == $arImageParam['ID']) {
                $arItems['PROPERTIES']['IMAGE'] = $arImageParam;
                $arItems['PROPERTIES']['IMAGE']['SRC'] = SITE_DIR . 'upload/' . $arImageParam['SUBDIR'] . '/' . $arImageParam['FILE_NAME'];
            }
        }
    }
}

foreach ($arResult as &$arItems) {
    if (!empty($arItems['ITEMS']))
        foreach ($arItems['ITEMS'] as &$arItem) {
            foreach ($arSectionBanners as &$arBannerItems) {
                if (is_array($arBannerItems['PROPERTIES']['SECTION_ID'])) {
                    foreach ($arBannerItems['PROPERTIES']['SECTION_ID'] as $arSectionIdItem) {
                        if ($arItem['PARAMS']['SECTION']['ID'] == $arSectionIdItem) {
                            $arItem['PARAMS']['BANNER'][] = $arBannerItems;
                        }
                    }
                } else {
                    if ($arItem['PARAMS']['SECTION']['ID'] == $arBannerItems['PROPERTIES']['SECTION_ID']) {
                        $arItem['PARAMS']['BANNER'][] = $arBannerItems;
                    }
                }
            }
        }
}