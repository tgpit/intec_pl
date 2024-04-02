<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bSeo = Loader::includeModule('intec.seo');

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'BORDERS' => 'Y',
    'COLUMNS' => 5,
    'COUNT_ELEMENTS' => true,
    '~COUNT_ELEMENTS' => 'Y',
    'WIDE' => 'Y',
    'RECURSION' => 'Y'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y'
    ],
    'BORDERS' => $arParams['BORDERS'] === 'Y',
    'COLUMNS' => ArrayHelper::fromRange([3, 2, 4, 5], $arParams['COLUMNS']),
    'ELEMENTS' => [
        'QUANTITY' => $arParams['~COUNT_ELEMENTS'] === 'Y'
    ],
    'WIDE' => $arParams['WIDE'] === 'Y',
    'SVG' => [
        'USE' => $arParams['SVG_FILE_USE'] === 'Y' && !empty($arParams['PROPERTY_SVG_FILE']),
        'COLOR' => ArrayHelper::fromRange(['original', 'theme'], $arParams['SVG_FILE_COLOR'])
    ]
];

if (!$arVisual['WIDE'] && $arVisual['COLUMNS'] > 4)
    $arVisual['COLUMNS'] = 4;

$arFiles = Collection::from([]);

foreach ($arResult['SECTIONS'] as &$arSection) {
    if ($arVisual['SVG']['USE']) {
        $mValue = ArrayHelper::getValue($arSection, $arParams['PROPERTY_SVG_FILE']);

        if (!empty($mValue))
            if (!$arFiles->has($mValue))
                $arFiles->add($mValue);
    }
}

unset($arSection, $mValue);

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

$arSections = [];

foreach($arResult['SECTIONS'] as $arSection) {
    if ($arVisual['SVG']['USE']) {
        $mValue = ArrayHelper::getValue($arSection, $arParams['PROPERTY_SVG_FILE']);

        if (!empty($mValue) && $arFiles->exists($mValue)) {
            $arSection['PICTURE'] = $arFiles->get($mValue);
        }
    }

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

if ($arParams['RECURSION'] === 'Y') {
    $arResult['SECTIONS'] = $fBuild($arSections);
} else {
    $arResult['SECTIONS'] = $arSections;
}

unset($arVisual, $arSections, $fBuild, $arFiles);