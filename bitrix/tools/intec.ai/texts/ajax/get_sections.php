<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
use intec\core\helpers\Json;

if (!CModule::IncludeModule("iblock")) die();
if (!Loader::includeModule('intec.core')) die();

$iblockId = $_POST['iblockId'];

function getSectionTree($iblockId, $parentId = 0) {
    $arFilter = array(
        'IBLOCK_ID' => $iblockId,
        'SECTION_ID' => $parentId
    );

    $arSelect = array('ID', 'NAME', 'IBLOCK_SECTION_ID');
    $dbSections = CIBlockSection::GetList(array('SORT' => 'ASC'), $arFilter, false, $arSelect);

    $sectionTree = array();

    while ($section = $dbSections->GetNext()) {
        $section['CHILDREN'] = getSectionTree($iblockId, $section['ID']);
        
        $elementFilter = array(
            'IBLOCK_ID' => $iblockId,
            'SECTION_ID' => $section['ID'],
            'INCLUDE_SUBSECTIONS' => 'N'
        );

        $elementCount = CIBlockElement::GetList(array(), $elementFilter, array(), false);
        $section['ELEMENT_COUNT'] = $elementCount;

        $sectionTree[] = $section;
    }

    return $sectionTree;
}

$sectionTree = getSectionTree($iblockId);

$arResult['SECTIONS'] = $sectionTree;

$elementFilter = array(
    'IBLOCK_ID' => $iblockId,
    'SECTION_ID' => 0,
    'INCLUDE_SUBSECTIONS' => 'N'
);

$elementCount = CIBlockElement::GetList(array(), $elementFilter, array(), false);

$arResult['ROOT_ELEMENT_COUNT'] = $elementCount;

$customProperties = [];

$properties = CIBlock::GetProperties($iblockId);
while ($propFields = $properties->GetNext()) {
    if (($propFields['PROPERTY_TYPE'] == 'S' && ($propFields['USER_TYPE'] == 'HTML' || $propFields['USER_TYPE'] == null)) && $propFields['MULTIPLE'] == 'N') {
        $customProperties[$propFields['CODE']] = $propFields['NAME'];
    }
}

$arResult['CUSTOM_PROPERTIES'] = $customProperties;

$arResult["OK"] = 'Y';

$arResult = Json::encode($arResult, JSON_HEX_APOS, true);
echo $arResult;