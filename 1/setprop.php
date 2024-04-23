<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Каталог товаров");

$IBLOCK_ID    = 58;

function getnod($sID,$sNam) {
$rsParentSection = CIBlockSection::GetByID($sID);
if ($arParentSection = $rsParentSection->GetNext())
	{
	$narFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
	$nrsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$narFilter);
	while (
$narSect = $nrsSect->GetNext())	{
	    	echo  "---> ".$narSect['ID']." ".$narSect['NAME']."<br>";
// -==============================================================================================--
			$arSelect = Array("ID","IBLOCK_ID","IBLOCK_SECTION_ID", "NAME", "TIMESTAMP_X", "DATE_CREATE", "TIMESTAMP_X_UNIX", "DATE_CREATE_X_UNIX");
			$artFilter = Array("IBLOCK_ID"=>58,"IBLOCK_SECTION_ID"=> $narSect['ID'], "ACTIVE"=>"Y");
			$res = CIBlockElement::GetList(Array(), $artFilter, false, false, $arSelect);
			while($ob = $res->GetNext()){
// ----------==================----------------
				$ELEMENT_ID = $ob['ID'];  // код элемента				
				$dt2 = $ob['TIMESTAMP_X_UNIX'];
				$dt1 = $ob['DATE_CREATE_X_UNIX'];
				$cdt = time();
				$rdt = $cdt - $dt2;
				$rddt = floor($rdt / (60 * 60 * 24));
				$vprop = "";
// -----==================--------------------------
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"g_cat"));
				while($enum_fields = $property_enums->GetNext())
				{
					if ($enum_fields["VALUE"] == $sNam){ $vprop = $enum_fields["ID"];};
				}
// -----==================--------------------------
		    	echo  "$sNam--->---> "." ".$ob['NAME']." ||| ".$vprop."||| >=".$rddt."=< ";
				$nnew = "N";
				if ($rddt < 56) {
					$nnew = "Y";
				};
				$pr1 = 0;
				$pr2 = 0;
				$db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'],"CATALOG_GROUP_ID" => 1));
				$raspr = 'N';
				while($ar_res = $db_res->GetNext()) { $pr1 = $ar_res["PRICE"]; }
				$db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'],"CATALOG_GROUP_ID" => 2));
				while($ar_res = $db_res->GetNext()) { $pr2 = $ar_res["PRICE"]; }
				if ($pr1 > $pr2) {
					if ($pr2 >0) {
						$raspr = 'Y';
						echo "$pr1 -=- $pr2";
					}
				}
// -----==================--------------------------
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"g_new"));
                $fnf = "qw";
				while($enum_fields = $property_enums->GetNext())	{
					if ($enum_fields["VALUE"] == 'N') {
						$vnew = $enum_fields["ID"];
					};
					if ($enum_fields["VALUE"] == 'Y') {
						$vras = $enum_fields["ID"];
					};
					$fnf = $enum_fields["VALUE"];
					if ($enum_fields["VALUE"] == $nnew){ $vanew = $enum_fields["ID"];};
				}
// -----==================--------------------------
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"g_raspr"));
                $fnf = "wq";
				while($enum_fields = $property_enums->GetNext())	{
					if ($enum_fields["VALUE"] == 'N') {
						$rnew = $enum_fields["ID"];
					};
					if ($enum_fields["VALUE"] == 'Y') {
						$rras = $enum_fields["ID"];
					};
					$rnf = $enum_fields["VALUE"];
					if ($enum_fields["VALUE"] == $raspr){ $varasp = $enum_fields["ID"];};
				}
// -----==================--------------------------
				$PROPERTY_CODE = "g_cat";  // код свойства
				$r = CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, array($PROPERTY_CODE => $vprop,
																						"g_new" => $vanew,
																						"g_raspr" => $varasp));

				echo "-=:$vnew -=:$vras /\ -=:$rnew -=:$rras  N-$nnew R-$raspr<br>";
				
				// -----------================-----------------
			}
// -==============================================================================================--
		}


// -==============================================================================================--
			$arSelect = Array("ID","IBLOCK_ID","IBLOCK_SECTION_ID", "NAME", "TIMESTAMP_X", "DATE_CREATE", "TIMESTAMP_X_UNIX", "DATE_CREATE_X_UNIX");
			$artFilter = Array("IBLOCK_ID"=>58,"IBLOCK_SECTION_ID"=> $sID, "ACTIVE"=>"Y");
			$res = CIBlockElement::GetList(Array(), $artFilter, false, false, $arSelect);
			while($ob = $res->GetNext()){
// ----------==================----------------
				$ELEMENT_ID = $ob['ID'];  // код элемента				
				$dt2 = $ob['TIMESTAMP_X_UNIX'];
				$dt1 = $ob['DATE_CREATE_X_UNIX'];
				$cdt = time();
				$rdt = $cdt - $dt2;
				$rddt = floor($rdt / (60 * 60 * 24));
				$vprop = "";
// -----==================--------------------------
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"g_cat"));
				while($enum_fields = $property_enums->GetNext())
				{
					if ($enum_fields["VALUE"] == $sNam){ $vprop = $enum_fields["ID"];};
				}
				$PROPERTY_VALUE = $vprop;  // значение свойства
// -----==================--------------------------
		    	echo  "$sNam--->---> "." ".$ob['NAME']." ".$vprop." >=".$rddt."=< ";
				$nnew = "N";
				if ($rddt < 28) {
					$nnew = "Y";
				};
				$pr1 = 0;
				$pr2 = 0;
				$db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'],"CATALOG_GROUP_ID" => 1));
				$raspr = 'N';
				while($ar_res = $db_res->GetNext()) { $pr1 = $ar_res["PRICE"]; }
				$db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'],"CATALOG_GROUP_ID" => 2));
				while($ar_res = $db_res->GetNext()) { $pr2 = $ar_res["PRICE"]; }
				if ($pr1 > $pr2) {
					if ($pr2 >0) {
						$raspr = 'Y';
						echo "$pr1 -=- $pr2";
					}
				}
// -----==================--------------------------
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"g_new"));
                $fnf = "qw";
				while($enum_fields = $property_enums->GetNext())	{
					if ($enum_fields["VALUE"] == 'N') {
						$vnew = $enum_fields["ID"];
					};
					if ($enum_fields["VALUE"] == 'Y') {
						$vras = $enum_fields["ID"];
					};
					$fnf = $enum_fields["VALUE"];
					if ($enum_fields["VALUE"] == $nnew){ $vanew = $enum_fields["ID"];};
				}
// -----==================--------------------------
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"g_raspr"));
                $fnf = "wq";
				while($enum_fields = $property_enums->GetNext())	{
					if ($enum_fields["VALUE"] == 'N') {
						$rnew = $enum_fields["ID"];
					};
					if ($enum_fields["VALUE"] == 'Y') {
						$rras = $enum_fields["ID"];
					};
					$rnf = $enum_fields["VALUE"];
					if ($enum_fields["VALUE"] == $raspr){ $varasp = $enum_fields["ID"];};
				}
// -----==================--------------------------
				$PROPERTY_CODE = "g_cat";  // код свойства
				$r = CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, array($PROPERTY_CODE => $PROPERTY_VALUE,
																						"g_new" => $vanew,
																						"g_raspr" => $varasp));

				echo "-=:$vnew -=:$vras /\ -=:$rnew -=:$rras  N-$nnew R-$raspr<br>";			
				// -----------================-----------------
			}
// -==============================================================================================--
	}
}

$arFilter = array('IBLOCK_ID' => IntVal($IBLOCK_ID),
				  'ACTIVE' => 'Y',
				  'DEPTH_LEVEL' => "1",
    			); 
$rsSect = CIBlockSection::GetTreeList(
     $arFilter, //фильтр (выше объявили)
     false, 	//выводить количество элементов - нет
	);

while ($arSect = $rsSect->GetNext()) {
    echo  "<b>".$arSect['ID']." ".$arSect['NAME']."<b><br>";
	getnod($arSect['ID'],$arSect['NAME']);
}

?>  