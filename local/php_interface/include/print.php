<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/pheader.php");
//$APPLICATION->SetTitle("Ценники");

global $USER, $APPLICATION;
IncludeModuleLangFile(__FILE__);
$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");
if ($SALE_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$ORDER_ID = (isset($_REQUEST['ORDER_ID']) ? (int)$_REQUEST['ORDER_ID'] : 0);

function GetRealPath2Report($rep_name)
{
	$rep_name = str_replace("\0", "", $rep_name);
	$rep_name = preg_replace("#[\\\\\\/]+#", "/", $rep_name);
	$rep_name = preg_replace("#\\.+[\\\\\\/]#", "", $rep_name);

	$rep_file_name = $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/reports/".$rep_name;
	if (!file_exists($rep_file_name))
	{
		$rep_file_name = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/reports/".$rep_name;
		if (!file_exists($rep_file_name))
		{
			return "";
		}
	}

	return $rep_file_name;
}


function GetUniformDestribution($arBasket, $discount, $priceTotal)
{
	foreach ($arBasket as $key => $val)
	{
		$val["PRICE_DEFAULT"] = $val["PRICE"];
		$val["DISCOUNT_RATION_PERCENT"] = round(($val["PRICE"] * 100) / $priceTotal, 5);
		$val["DISCOUNT_RATION_VALUE"] = round(($discount * $val["DISCOUNT_RATION_PERCENT"] / 100), 5);
		$val["PRICE"] -= $val["DISCOUNT_RATION_VALUE"];
		$arBasket[$key] = $val;
	}
	return $arBasket;
}


if (CModule::IncludeModule("sale"))
{
$srv = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER['SERVER_NAME'];
$st  = "style=\"position: relative; 
				background-image: url('/local/images/nFon.jpg'); 
				width: 147mm;  
				height: 207mm;\"";
$stf = "style=\"position: relative; 
				background-image: url('/local/images/fFon.jpg'); 
				width: 147mm;  
				height: 207mm;\"";

$stz = "style=\"position: absolute;
				left: 20px;
				top: 106px;
				width: 380px;
				font-style: bold; 
				font-size: 21.2px; 
				color: black; 
				font-family: 'mFutura';
				\"";
$stq = "style=\"position: absolute;
				top: 647px;
				left: 418px;
				\"";
$stqb= "style=\"position: relative;
			    height: 118px;
			    width: 118px;
			    background: white; 
                \"";
$stqi = "style=\"position: absolute;
				 top: 50%;
				 left: 50%;
				 transform: translate(-50%, -50%);
				\"";

$stp = "style=\"position: absolute;
				font-style: bold; 
				top: 250px;
				left: 16px;
				font-size: 27px; 
				color: black; 
				font-family: 'mFutura'; 
				\"";

$str = "style=\"position: absolute;
				font-style: bold; 
				top: 326px;
				left: 16px;
				font-size: 27px; 
				color: red; font-family: 'mFutura'; 
				\"";

$stc = "style=\"font-style: bold; 
				font-size: 20px; 
				color: #39c1f1; 
				font-family: 'mFutura'; 
				margin-left: 17px;\"";

$sto = "style=\"position: absolute;
				font-style: bold; 
				top: 446px;
				left: 60px;
				width: 200px;
				text-align: center;
				font-size: 46px; 
				font-family: 'mFutura'; 
				\"";

$stl = "style=\"position: absolute;
				font-style: bold; 
				top: 446px;
				left: 350px;
				width: 120px;
				text-align: center;
				font-size: 46px; 
				font-family: 'mFutura'; 
				\"";


$arr = array();

$arr = $_POST["filter_tree"];
$arprop = $_POST["filter_prop"];
$arel = $_POST["filter_element"];


$arFilter = Array(
	"IBLOCK_ID"=> "58",
	"ACTIVE"=>"Y", 
	"SECTION_ID" => $arr,
	"ID" => $arel,
);

$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter);
$n = 0;
$s = 0;
echo "<table style=\"border-spacing: 1px; border: 1px solid white;\">";

while($ar_fields = $res->GetNext())
{
	$n++;


$PRICE_TYPE_ID = 1;
$rsPrices = CPrice::GetList(array(), array('PRODUCT_ID' => $ar_fields['ID'], 'CATALOG_GROUP_ID' => $PRICE_TYPE_ID));
if ($arPrice = $rsPrices->Fetch())
{
$base_pr = $arPrice["PRICE"];
$curren = $arPrice["CURRENCY"];
}
$PRICE_TYPE_ID = 2;
$rsPrices = CPrice::GetList(array(), array('PRODUCT_ID' => $ar_fields['ID'], 'CATALOG_GROUP_ID' => $PRICE_TYPE_ID));
if ($arPrice = $rsPrices->Fetch())
{
$acc_pr = $arPrice["PRICE"];
}


// -------------------==============================------------------------
if ($acc_pr > 0) {
	if ($n == 1){
		echo "<tr><td $st>";
	}else{
		echo "</td><td $st>";
	}
}else{
	if ($n == 1){
		echo "<tr><td $stf>";
	}else{
		echo "</td><td $stf>";
	}
}



	echo "<div $stz><b>".$ar_fields["NAME"]."</b></div>";
// -------------------==============================------------------------


if ($acc_pr > 0) {
	$pr =  "<div $stp>&nbsp;".number_format($base_pr, 0, '.', ' ')." руб.</div>
			<div $str>&nbsp;<b>".number_format($acc_pr, 0, '.', ' ')." руб.</b></div>";
echo $pr;
	$acc_pr = 0;
    }else{
	$pr = "<div $stp>&nbsp;<b>".number_format($base_pr, 0, '.', ' ')." руб.</b></div>";
echo $pr;
    }

// ------------------================================-----------------------
		$prop_fields = CIBlockProperty::GetByID(639,'58');
		$fields = $prop_fields->GetNext();
		$prod_id = $ar_fields['ID'];
		$mmpr = CIBlockElement::GetProperty(
			58,
			$prod_id,
			Array(),
			Array("ID"=>639)
		);
		$mpr = $mmpr->GetNext();

		$vll = CIBlockPropertyEnum::GetByID($mpr["VALUE"]);

		$vlt = $mpr["PROPERTY_TYPE"];
		$mobj1 = 0;
		if (isset($mpr["VALUE"])){
			if ($vlt == "N") { 
				$mobj1 = $mpr["VALUE"];
			};
			if ($vlt == "L") { 
				$mobj1 = $vll["VALUE"];
			};
		};

		$prop_fields = CIBlockProperty::GetByID(742,'58');
		$fields = $prop_fields->GetNext();
		$prod_id = $ar_fields['ID'];
		$mmpr = CIBlockElement::GetProperty(
			58,
			$prod_id,
			Array(),
			Array("ID"=>742)
		);
		$mpr = $mmpr->GetNext();

		$vll = CIBlockPropertyEnum::GetByID($mpr["VALUE"]);

		$vlt = $mpr["PROPERTY_TYPE"];
		$mobj2 = 0;
		if (isset($mpr["VALUE"])){
			if ($vlt == "N") { 
				$mobj2 = $mpr["VALUE"];
			};
			if ($vlt == "L") { 
				$mobj2 = $vll["VALUE"];
			};
		};
$mobj = '';
if ($mobj1 != 0) { $mobj = $mobj1;};
if ($mobj2 != 0) { $mobj = $mobj2;};

echo "<div $sto>$mobj</div>";
// -----------------============================---------------------------
		$prop_fields = CIBlockProperty::GetByID(571,'58');
		$fields = $prop_fields->GetNext();
		$prod_id = $ar_fields['ID'];
		$mmpr = CIBlockElement::GetProperty(
			58,
			$prod_id,
			Array(),
			Array("ID"=>571)
		);
		$mpr = $mmpr->GetNext();

		$vll = CIBlockPropertyEnum::GetByID($mpr["VALUE"]);

		$vlt = $mpr["PROPERTY_TYPE"];
		$mobj1 = 0;
		if (isset($mpr["VALUE"])){
			if ($vlt == "N") { 
				$mobj1 = $mpr["VALUE"];
			};
			if ($vlt == "L") { 
				$mobj1 = $vll["VALUE"];
			};
		};

		$prop_fields = CIBlockProperty::GetByID(743,'58');
		$fields = $prop_fields->GetNext();
		$prod_id = $ar_fields['ID'];
		$mmpr = CIBlockElement::GetProperty(
			58,
			$prod_id,
			Array(),
			Array("ID"=>743)
		);
		$mpr = $mmpr->GetNext();

		$vll = CIBlockPropertyEnum::GetByID($mpr["VALUE"]);

		$vlt = $mpr["PROPERTY_TYPE"];
		$mobj2 = 0;
		if (isset($mpr["VALUE"])){
			if ($vlt == "N") { 
				$mobj2 = $mpr["VALUE"];
			};
			if ($vlt == "L") { 
				$mobj2 = $vll["VALUE"];
			};
		};

		$prop_fields = CIBlockProperty::GetByID(746,'58');
		$fields = $prop_fields->GetNext();
		$prod_id = $ar_fields['ID'];
		$mmpr = CIBlockElement::GetProperty(
			58,
			$prod_id,
			Array(),
			Array("ID"=>746)
		);
		$mpr = $mmpr->GetNext();

		$vll = CIBlockPropertyEnum::GetByID($mpr["VALUE"]);

		$vlt = $mpr["PROPERTY_TYPE"];
		$mobj3 = 0;
		if (isset($mpr["VALUE"])){
			if ($vlt == "N") { 
				$mobj3 = $mpr["VALUE"];
			};
			if ($vlt == "L") { 
				$mobj3 = $vll["VALUE"];
			};
		};

$mobj = '';
if ($mobj1 > 0) { $mobj = $mobj1;};
if ($mobj2 > 0) { $mobj = $mobj2;};
if ($mobj3 > 0) { $mobj = $mobj3;};


echo "<div $stl>$mobj</div>";





echo "<div $stq><div $stqb><img $stqi src='/local/php_interface/include/qrcode.php?s=qr&sx=2&sy=2&d=". $srv.$ar_fields["DETAIL_PAGE_URL"]."'></div></div>";
if ($n == 2){
echo "</td></tr>";
$n = 0;
$s++;
if ($s == 1) {
echo "</table><div style=\"page-break-before:always;\"></div><table style=\"border-spacing: 1px; border: 1px solid white;\">";
$s=0;
}
}

}

echo "</table>";
}
else
	ShowError("SALE MODULE IS NOT INSTALLED");
?>