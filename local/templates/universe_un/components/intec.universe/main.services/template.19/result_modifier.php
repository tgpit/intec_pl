<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_SVG_FILE' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'SVG_FILE_USE' => 'N',
    'PICTURE_SHOW' => 'N',
    'PICTURE_SIZE' => 'default',
    'DESCRIPTION_SHOW' => 'N',
    'CHILDREN_SHOW' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'SIZE' => ArrayHelper::fromRange(['default', 'small'], $arParams['PICTURE_SIZE'])
    ],
    'SVG' => [
        'USE' => !empty($arParams['PROPERTY_SVG_FILE']) && $arParams['SVG_FILE_USE'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'CHILDREN' => [
        'SHOW' => $arParams['CHILDREN_SHOW'] === 'Y'
    ]
];

foreach ($arResult['SECTIONS'] as &$arSection)
    $arSectionsId[] = $arSection['ID'];

unset($arSection);

$rsSectionResult = CIBlockSection::GetList([
        'SORT' => 'ASC'
    ], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ID' => $arSectionsId
    ],
    false, [
        $arParams['PROPERTY_SVG_FILE']
    ]
);

while ($sectionProp = $rsSectionResult->Fetch())
    $arResult['SECTIONS'][$sectionProp['ID']][$arParams['PROPERTY_SVG_FILE']] = $sectionProp[$arParams['PROPERTY_SVG_FILE']];

unset($rsSectionResult, $sectionProp);

$arFiles = Collection::from([]);

if ($arVisual['SVG']['USE']) {
    foreach ($arResult['SECTIONS'] as &$arSection) {
        $mValue = ArrayHelper::getValue($arSection, $arParams['PROPERTY_SVG_FILE']);

        if (!empty($mValue) && !$arFiles->has($mValue))
            $arFiles->add($mValue);
    }

    unset($arSection, $mValue);
}

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = Arrays::from([]);
}

$arSections = [];

foreach($arResult['SECTIONS'] as $arSection) {
    if ($arVisual['SVG']['USE']) {
        $mValue = ArrayHelper::getValue($arSection, $arParams['PROPERTY_SVG_FILE']);

        if (!empty($mValue))
            $mValue = $arFiles->get($mValue);

        if (!empty($mValue))
            $arSection['PICTURE'] = $mValue;
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

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

$arResult['SECTIONS'] = $arSections;

unset($arVisual, $arSections);