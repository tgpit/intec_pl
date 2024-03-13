<?
IncludeModuleLangFile( __FILE__);

if(class_exists("webizi_csv")) 
	return;

Class webizi_csv extends CModule
{
	var $MODULE_ID = "webizi.csv";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	function webizi_csv() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=TASKFROMEMAIL_MODULE_VERSION;
            $this->MODULE_VERSION_DATE=TASKFROMEMAIL_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("WI_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("WI_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("WI_PARTNER_NAME");
        $this->PARTNER_URI  = "http://webizi.ru/";
	}
	
	function DoInstall()
	{
		if (!IsModuleInstalled("webizi.csv"))
		{
			$this->InstallFiles();
		}
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallFiles();
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webizi.csv/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webizi.csv/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default", true);

		RegisterModule("webizi.csv");
		return true;
	}
	
	function UnInstallFiles()
	{	
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webizi.csv/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webizi.csv/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");

		UnRegisterModule("webizi.csv");
		return true;
	}
}
?>