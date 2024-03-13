<?
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */
/** @global CUser $USER */
use Bitrix\Main\Loader,
  Bitrix\Main,
  Bitrix\Iblock,
  Bitrix\Currency,
  Bitrix\Catalog;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
Loader::includeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");
CJSCore::Init(["jquery"]);
$APPLICATION->SetTitle('Ценники');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?
if(!CModule::IncludeModule("iblock"))
	return false;

global $USER, $APPLICATION;
IncludeModuleLangFile(__FILE__);

$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");

$IBLOCK_ID = 58;

$arFilter = array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'); 
$arSelect = array('ID', 'NAME', 'DEPTH_LEVEL');
$rsSection = CIBlockSection::GetTreeList($arFilter, $arSelect); 
?>
<div>-=-</div>
<form id="gto_form" class="gto_form" action = "print.php" target="_blank" method="POST" >



<div style="display: flex; flex-direction: row;"><div style="display: inline-block;">Разделы:<br><select class="adm-select-multiple sale-report-site-dependent" style="width: 270px;" id="filter_tree[]" name="filter_tree[]" caller="true" tid="Section" multiple="multiple" size="15">
<?
while($arSection = $rsSection->Fetch()) {
	echo "<option value='".$arSection['ID']."'>";
	for ($i=0; $i<$arSection['DEPTH_LEVEL']-1; $i++) 
		echo "--";
	echo $arSection['NAME']."</option>";
}
?>
</select></div>
<!--
<div>Свойства:<br><select class="adm-select-multiple sale-report-site-dependent" style="width: 270px;" id="filter_prop[]" name="filter_prop[]" caller="true" tid="Section" multiple="multiple" size="15">
<?
$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
while ($prop_fields = $properties->GetNext())
{
	echo "<option value='".$prop_fields["ID"]."'>";
	echo $prop_fields["ID"]." ".$prop_fields["NAME"]."</option>";
}?>
</select></div>
-->
<div>Склады:<br><select class="adm-select-multiple sale-report-site-dependent" style="width: 270px;" id="filter_store[]" name="filter_store[]" caller="true" tid="Section" multiple="multiple" size="15">

<?
	$storeList = \CCatalogStore::getList(array('TITLE'=>'DESC'), array("ACTIVE" => "Y"), false, false, array("ID", "TITLE", "ACTIVE"));
	$result = array();
	while ($ar = $storeList->fetch()) {
	echo "<option value='".$ar["ID"]."'>".$ar["TITLE"]."</option>";
     }
?>

</select></div>
</div>

<div style="display: block;">
Товары:<br>
<select class="adm-select-multiple sale-report-site-dependent" id="filter_element[]" name="filter_element[]" caller="true" tid="Section" multiple="multiple" size="15">
</div>

<div>
<p>
<input id='srch' name='srch' placeholder='фильтр'>
<input id='sel' name='sel' value='Выборка' type='button'>
<input name='submit' type='submit' value='Сформировать ценники' ></form>
</p>
</div>

<script type="text/javascript">

function loadOptions(selectEl, data) {
        if (Object.keys(data).length) {
            for (let key in data) {
                let opt = document.createElement('option');
                opt.appendChild(document.createTextNode(data[key]));
                opt.value = key;
                selectEl.appendChild(opt);
            }
        }
    }

	$('#sel').click(function(){
	var $data = {};
	$('#gto_form').find ('input, select').each(function() {
	  $data[this.name] = $(this).val();
	});

	$.post({
	  url: '/bitrix/admin/select.php',
	  type: 'post',
	  data: $data,
	  success: function(result) {
	  let selectEl = document.getElementById('filter_element[]');
	  $(selectEl).find('option').remove(); 
	  loadOptions(selectEl, JSON.parse(result));
     }
	});

});
</script>


<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>

