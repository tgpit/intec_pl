<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use intec\core\helpers\Json;

if (!CModule::IncludeModule("iblock")) die();
if (!Loader::includeModule('intec.core')) die();

$arResult["OK"] = 'N';

$sectionsId = json_decode($_POST['sectionsId']);
$iblockId = $_POST['iblockId'];

if (!empty($iblockId) && !empty($sectionsId)) {
    $arFilter = array(
        "IBLOCK_ID" => $iblockId,
        "SECTION_ID" => $sectionsId
    );

    $arSelect = array(
        "ID",
        "NAME"
    );

    $elements = [];

    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $elements[] = $arFields;
    }

    $arResult['ELEMENTS'] = $elements;

    $arResult["OK"] = 'Y';
}

$arResult = Json::encode($arResult, JSON_HEX_APOS, true);
echo $arResult;