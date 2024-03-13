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
    'COLUMNS' => 2,
    'LINK_BLANK' => 'N',
    'PICTURE_SHOW' => 'N',
    'PICTURE_SIZE' => 'small',
    'DESCRIPTION_SHOW' => 'N',
    'CHILDREN_SHOW' => 'N',
    'CHILDREN_VIEW' => 1,
    'CHILDREN_ELEMENTS' => 'N',
    'CHILDREN_COUNT' => 5,
    'SVG_FILE_USE' => 'Y',
    'PROPERTY_SVG_FILE' => null,
    'SVG_FILE_COLOR' => 'original',
    'RECURSION' => 'Y'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 3], $arParams['COLUMNS']),
    'LINK' => [
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'SIZE' => ArrayHelper::fromRange(['big', 'medium', 'small'], $arParams['PICTURE_SIZE'])
    ],
    'CHILDREN' => [
        'SHOW' => $arParams['CHILDREN_SHOW'] === 'Y',
        'VIEW' => ArrayHelper::fromRange([1, 2], $arParams['CHILDREN_VIEW']),
        'ELEMENTS' => $arParams['CHILDREN_ELEMENTS'] === 'Y',
        'COUNT' => [
            'USE' => false,
            'VALUE' => Type::toInteger($arParams['CHILDREN_COUNT'])
        ]
    ],
    'SVG' => [
        'USE' => $arParams['SVG_FILE_USE'] === 'Y' && !empty($arParams['PROPERTY_SVG_FILE']),
        'COLOR' => ArrayHelper::fromRange(['original', 'theme'], $arParams['SVG_FILE_COLOR'])
    ]
];

if ($arVisual['CHILDREN']['COUNT']['VALUE'] > 0)
    $arVisual['CHILDREN']['COUNT']['USE'] = true;

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
    if (!empty($arSection['PICTURE'])) {
        $arSection['PICTURE']['TITLE'] = ArrayHelper::getValue($arSection, ['IPROPERTY_VALUES', 'SECTION_PICTURE_FILE_TITLE']);
        $arSection['PICTURE']['ALT'] = ArrayHelper::getValue($arSection, ['IPROPERTY_VALUES', 'SECTION_PICTURE_FILE_ALT']);

        if (empty($arSection['PICTURE']['TITLE']))
            $arSection['PICTURE']['TITLE'] = $arSection['NAME'];

        if (empty($arSection['PICTURE']['ALT']))
            $arSection['PICTURE']['ALT'] = $arSection['NAME'];
    }

    if ($arVisual['SVG']['USE']) {
        $mValue = ArrayHelper::getValue($arSection, $arParams['PROPERTY_SVG_FILE']);

        if (!empty($mValue) && $arFiles->exists($mValue)) {
            $arSection['PICTURE'] = $arFiles->get($mValue);
        }
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
    if (empty($arSections))
        return [];

    $bFirst = true;

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

unset($arVisual, $arSections, $fBuild);