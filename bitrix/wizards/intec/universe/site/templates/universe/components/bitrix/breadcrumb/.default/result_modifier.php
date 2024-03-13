<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 */

if (!Loader::includeModule('iblock'))
    return;
if (!defined('EDITOR')) {
    $arParams['BREADCRUMB_MOBILE_COMPACT'] = (!Properties::get('mobile-panel-breadcrumbs-compact')) ? 'N' : 'Y';
    $arParams['BREADCRUMB_DROPDOWN_USE'] = (!Properties::get('breadcrumb-dropdown-use')) ? 'N' : 'Y';
}

$sIBlockType = ArrayHelper::getValue($GLOBALS, 'BreadCrumbIBlockType');
$iIBlockId = ArrayHelper::getValue($GLOBALS, 'BreadCrumbIBlockId');
$sMobileCompact = ArrayHelper::getValue($arParams, 'BREADCRUMB_MOBILE_COMPACT');

if (!defined('EDITOR')) {
    $sMobileSlider = Properties::get('mobile-panel-breadcrumbs-compact-slider') ? 'Y' : 'N';
} else {
    $sMobileSlider = null;
}
//$sMobileSlider = ArrayHelper::getValue($GLOBALS, 'BreadcrumbsMobileSlider');

$arSections = new Arrays();

if (!empty($sIBlockType) && !empty($iIBlockId)) {
    $oCache = Cache::createInstance();
    $arFilter = [
        'ACTIVE' => 'Y',
        'IBLOCK_TYPE' => $sIBlockType,
        'IBLOCK_ID' => $iIBlockId
    ];

    if ($oCache->initCache(36000, 'SECTIONS'.serialize($arFilter), '/iblock/breadcrumb')) {
        $arVariables = $oCache->getVars();
        $arSections->setRange($arVariables['SECTIONS']);

        unset($arVariables);
    } else if ($oCache->startDataCache()) {
        if (Loader::includeModule('iblock')) {
            if (!empty($sIBlockType) && !empty($iIBlockId)) {
                $arSections = Arrays::fromDBResult(CIBlockSection::GetList(['SORT' => 'ASC'], [
                    'ACTIVE' => 'Y',
                    'IBLOCK_TYPE' => $sIBlockType,
                    'IBLOCK_ID' => $iIBlockId
                ], false, [
                    'ID',
                    'CODE',
                    'IBLOCK_TYPE',
                    'IBLOCK_ID',
                    'IBLOCK_SECTION_ID',
                    'NAME',
                    'SECTION_PAGE_URL'
                ]), true);
            }

            $oCache->endDataCache([
                'SECTIONS' => $arSections->asArray()
            ]);
        } else {
            $oCache->abortDataCache();
        }
    }

    unset($oCache);
}

$arVisual = [
    'MOBILE' =>[
        'USE' => $sMobileCompact === 'Y',
        'SLIDER' => [
            'USE' => $sMobileSlider === 'Y',
        ]
    ]
];