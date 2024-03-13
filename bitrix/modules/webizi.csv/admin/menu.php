<?
IncludeModuleLangFile(__FILE__);
$aMenu = array (
	"parent_menu" 	=> "global_menu_services",
	"sort" 			=> 800,
	"icon" 			=> "wi-csv-ico",
	"text" 			=> GetMessage("WI_CSV"),
	"title" 		=> GetMessage("WI_CSV"),
	"module_id" 	=> "webizi.csv",
	"items_id" 		=> "menu_wi_import_csv",
);

$aMenu["items"][] = array (
	"text" 			=> GetMessage("MCART_EXCEL_IMPORT"),
	"items_id" 		=> "iblock_import",
	"module_id" 	=> "webizi.csv",
	"title" 		=> GetMessage("MCART_EXCEL_IMPORT"),
	"url" 			=> "wi_csv_import.php?lang=".LANGUAGE_ID,
);
$aMenu["items"][] = array (
	"text" 			=> GetMessage("MCART_EXCEL_EXPORT"),
	"items_id" 		=> "iblock_export",
	"module_id" 	=> "webizi.csv",
	"title" 		=> GetMessage("MCART_EXCEL_EXPORT"),
	"url" 			=> "wi_csv_export.php?lang=".LANGUAGE_ID,
);
 
return $aMenu;
?>