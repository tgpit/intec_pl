<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");

/** @global CAdminPage $adminPage */
global $adminPage;
/** @global CAdminSidePanelHelper $adminSidePanelHelper */
global $adminSidePanelHelper;

$publicMode = $adminPage->publicMode;
$selfFolderUrl = $adminPage->getSelfFolderUrl();

$bSearch = false;
$bCurrency = false;
$arCurrencyList = array();


if(!CModule::IncludeModule("iblock"))
	return false;

global $USER, $APPLICATION;
IncludeModuleLangFile(__FILE__);

$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");

$IBLOCK_ID = 58;

$arFilter = array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'); 
$arSelect = array('ID', 'NAME', 'DEPTH_LEVEL');
$rsSection = CIBlockSection::GetTreeList($arFilter, $arSelect); 

echo '<select class="adm-select-multiple sale-report-site-dependent" id="filter_tree[]" name="filter_tree[]" caller="true" tid="Section" multiple="multiple" size="15">';
while($arSection = $rsSection->Fetch()) {
echo "<option value='t".$arSection['ID']."'>";
for ($i=0; $i<$arSection['DEPTH_LEVEL']-1; $i++) 
	echo "-";
   echo $arSection['NAME']."</option>";
}
echo '</select>';

echo '<select class="adm-select-multiple sale-report-site-dependent" id="filter_prop[]" name="filter_prop[]" caller="true" tid="Section" multiple="multiple" size="15">';
$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
while ($prop_fields = $properties->GetNext())
{
echo "<option value='p".$prop_fields["ID"]."'>";
	echo $prop_fields["ID"]." ".$prop_fields["NAME"]."</option>";
}
echo '</select>';


?>