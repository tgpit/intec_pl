<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\FileHelper;


/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'MOBILE_HIDE' => 'N',
    'SLIDER_USE' => 'N',
    'SLIDER_ARROW' => 'N',
    'COUNT_SHOW' => 'N',
    'SHOW_ALL_COUNT' => null,
], $arParams);

$arVisual = [
    'MOBILE' => [
        'HIDE' => $arParams['MOBILE_HIDE'] === 'Y'
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'ARROW' => $arParams['SLIDER_ARROW'] === 'Y'
    ],
    'COUNT' => [
        'SHOW' => $arParams['COUNT_SHOW'] === 'Y'
    ],
    'BUTTON' => [
        'AUTO' => empty($arParams['SHOW_ALL_COUNT']) || $arParams['SHOW_ALL_COUNT'] <= 0,
        'NUMBER' => 0,
        'MOD' => $arParams['POSITION'] === 'menu' ? 'menu' : 'list'
    ]
];

if (!$arVisual['BUTTON']['AUTO'])
    $arVisual['BUTTON']['NUMBER'] = $arParams['SHOW_ALL_COUNT'];

$arNavigation = [
    'container' => '[data-role="navigation"]',
    'class' => [
        'navigation-left intec-cl-background-hover intec-cl-border-hover',
        'navigation-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'text' => [
        FileHelper::getFileData(__DIR__.'/svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/svg/navigation.right.svg')
    ]
];

$arSectionPath = CIBlockSection::GetNavChain($arResult['IBLOCK']['ID'],$arResult['SECTION']['ID'], [], true);
$sSectionPath = '';

foreach ($arSectionPath as $section) {
    if (empty($sSectionPath))
        $sSectionPath = $section['CODE'];
    else
        $sSectionPath = $sSectionPath . '/' . $section['CODE'];
}

$sSectionUrl = $arResult['SECTION']['SECTION_PAGE_URL'];
$sSectionUrl = StringHelper::replaceMacros($sSectionUrl,[
    'SITE_DIR' => '',
    'SECTION_CODE' => $arResult['SECTION']['CODE'],
    'SECTION_ID' => $arResult['SECTION']['ID'],
    'SECTION_CODE_PATH' => $sSectionPath
]);

unset($arSectionPath, $sSectionPath);

$arResult['DATA'] = [
    'VISUAL' => $arVisual,
    'NAVIGATION' => $arNavigation,
    'SECTION' => [
        'URL' => $sSectionUrl
    ]
];

unset($arVisual, $arNavigation, $sSectionUrl);