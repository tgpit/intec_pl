<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Loader;
use \intec\ai\Module;
use intec\core\helpers\Json;

if (!Loader::includeModule('iblock')) die();
if (!Loader::includeModule('intec.core')) die();

$arResult['OK'] = 'N';

$elementId = $_POST['id'];
$elementName = $_POST['name'];
$promptMask = $_POST['promptMask'];
$iblockProperty = $_POST['iblockProperty'];

$prompt = $promptMask;

if (strpos($prompt, '#NAME#') !== false) {
    $prompt = trim(str_replace('#NAME#', strip_tags($elementName), $prompt));
} 

if (strpos($prompt, '#PREVIEW_TEXT#') !== false) {
    $res = CIBlockElement::GetByID($elementId);
    if($ar_res = $res->GetNext()) {
        $prompt = trim(str_replace('#PREVIEW_TEXT#', strip_tags($ar_res['PREVIEW_TEXT']), $prompt));
    }
}

if (strpos($prompt, '#DETAIL_TEXT#') !== false) {
    $res = CIBlockElement::GetByID($elementId);
    if($ar_res = $res->GetNext()) {
        $prompt = trim(str_replace('#DETAIL_TEXT#', strip_tags($ar_res['DETAIL_TEXT']), $prompt));
    }
}

if (preg_match_all('/(#PROPERTY_.*?_VALUE#)/', $prompt, $matches)) {
	$res = CIBlockElement::GetByID($elementId);
    if($ar_res = $res->GetNext()) {
		$IBLOCK_ID = $ar_res['IBLOCK_ID'];

		foreach ($matches[0] as $match) {
			$propertyCode = str_replace(['#PROPERTY_', '_VALUE#'], '', $match);
			$db_props = CIBlockElement::GetProperty($IBLOCK_ID, $elementId, array("sort" => "asc"), Array("CODE" => $propertyCode));
			if ($ar_props = $db_props->Fetch()) {
                if ($ar_props['PROPERTY_TYPE'] == 'S' && $ar_props['MULTIPLE'] == 'N') {
                    if (is_array($ar_props["VALUE"])) {
                        $prompt = preg_replace('/' . preg_quote($match, '/') . '/', strip_tags($ar_props["VALUE"]['TEXT']), $prompt);
                    } else {
                        $prompt = preg_replace('/' . preg_quote($match, '/') . '/', strip_tags($ar_props["VALUE"]), $prompt);
                    }
                }
			}

		}
	}
}

$data = array(
    'elementId' => $elementId,
    'iblockProperty' => $iblockProperty,
    'prompt' => $prompt,
    'done' => 'N'
);

$addResult = $DB->Add('ai_tasks', $data);

if ($addResult) {
    $arResult['OK'] = 'Y';
} else {
    $arResult['OK'] = 'N';
    $arResult['ERROR'] = $DB->db_Error;
}

$arResult = Json::encode($arResult, JSON_HEX_APOS, true);
echo $arResult;