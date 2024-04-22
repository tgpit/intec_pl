<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");


//$time = -microtime(true);
//sleep(5);
//$end = sprintf('%f', $time += microtime(true));
//echo $end;
if(!CModule::IncludeModule("iblock"))
return; 




$db_props = CIBlockElement::GetProperty(81,	24060, Array(),	Array() );
if ($ar_props = $db_props->Fetch()) {
	echo "<pre>".$ar_props['VALUE']."</pre>";
}


?>