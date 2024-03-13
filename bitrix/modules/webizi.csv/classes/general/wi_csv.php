<?
class WICSV
{
	public static function GetEnumList($IBLOCK_ID)
	{
		$arrEnum = Array();
		$db_enum_list = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID" => intval($IBLOCK_ID)));
		while($ar_enum_list = $db_enum_list->GetNext(true, false)) {
			$arrEnum[$ar_enum_list["PROPERTY_ID"]][$ar_enum_list["ID"]] = $ar_enum_list["VALUE"];
		}
		return $arrEnum;
	}
}
?>