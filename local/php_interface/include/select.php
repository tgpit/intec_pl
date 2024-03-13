<?
$arr = $_POST["filter_tree"];
$fnd = $_POST["srch"];
$stor= $_POST["filter_store"];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $USER, $APPLICATION;
IncludeModuleLangFile(__FILE__);
$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");
if ($SALE_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
CModule::IncludeModule("sale");


$arFilter = Array(
	"IBLOCK_ID"=> "58",
	"ACTIVE"=>"Y", 
);
if (count($arr)>0) {
	$arFilter["SECTION_ID"] = $arr;
 }else{
	$arFilter["SECTION_ID"] = array();
}
if ($fnd != '') {
	$arFilter["%NAME"] = $fnd;
}

$arr = array();
$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter);

while($ar_fields = $res->GetNext())
{
$items = array($ar_fields['ID']);
$dbStores = CCatalogStore::GetList(
   array(),
   array('ACTIVE' => 'Y','PRODUCT_ID'=>$items, 'ID'=>$stor),
   false,
   false,
   array("ID","PRODUCT_AMOUNT")
);

$kk = 0;
$na = $ar_fields["NAME"];
$nm = $ar_fields["NAME"];
    while ($arStore = $dbStores->Fetch()) {
	    $nm .= ": ".$arStore["ID"]."-".$arStore["PRODUCT_AMOUNT"];
        if ($arStore["PRODUCT_AMOUNT"]>0) { $kk = 1;};
    }

if ($kk == 1) {
	$arr[$ar_fields['ID']] = $na; //." -=:$kk";
	}
}
echo json_encode($arr);

