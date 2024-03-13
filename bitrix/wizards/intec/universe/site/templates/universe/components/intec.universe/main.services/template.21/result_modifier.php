<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\base\Collection;
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
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3,
    'DESCRIPTION_USE' => 'N',
    'LINK_USE' => 'N',
    'HEADER_BUTTON_SHOW' => 'N',
    'HEADER_BUTTON_TEXT' => null,
    'SVG_FILE_USE' => 'N',
    'PROPERTY_SVG_FILE' => null,
    'SVG_FILE_COLOR' => 'original',
    'PICTURE_SHOW' => 'Y',
    'CHILDREN_SHOW' => 'Y',
    'CHILDREN_DISPLAY' => 'line',
    'PICTURE_SIZE' => 'middle',
    'DESCRIPTION_SHOW' => 'Y',
    'PICTURE_POSITION_VERTICAL' => 'center'
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 2], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'PICTURE' => [
        'SIZE' => ArrayHelper::fromRange(['small', 'middle'], $arParams['PICTURE_SIZE']),
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'POSITION' => [
            'VERTICAL' => ArrayHelper::fromRange(['top', 'center'], $arParams['PICTURE_POSITION_VERTICAL'])
        ]
    ],
    'SVG' => [
        'USE' => $arParams['SVG_FILE_USE'] === 'Y',
        'COLOR' => ArrayHelper::fromRange(['original', 'theme'], $arParams['SVG_FILE_COLOR'])
    ],
    'CHILDREN' => [
        'DISPLAY' => ArrayHelper::fromRange(['line', 'column'], $arParams['CHILDREN_DISPLAY']),
        'SHOW' => $arParams['CHILDREN_SHOW'] === 'Y'
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if (empty($arParams['PROPERTY_SVG_FILE']))
    $arVisual['SVG']['USE'] = false;

foreach ($arResult['SECTIONS'] as &$arSection) {
    $arSectionsId[] = $arSection['ID'];
}

$arSectionResult = CIBlockSection::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $arSectionsId), false, $arSelect = array($arParams['PROPERTY_SVG_FILE']));
while ($sectionProp = $arSectionResult -> Fetch())
{
    $arResult['SECTIONS'][$sectionProp['ID']][$arParams['PROPERTY_SVG_FILE']] = $sectionProp[$arParams['PROPERTY_SVG_FILE']];
}

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

$arResult['BLOCKS']['BUTTON'] = [
    'SHOW' => $arParams['HEADER_BUTTON_SHOW'] === 'Y',
    'TEXT' => $arParams['HEADER_BUTTON_TEXT'],
    'LINK' => null
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arResult['BLOCKS']['BUTTON']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

if (empty($arResult['BLOCKS']['BUTTON']['TEXT']) || empty($arResult['BLOCKS']['BUTTON']['LINK']))
    $arResult['BLOCKS']['BUTTON']['SHOW'] = false;

unset($arMacros);