<?
IncludeModuleLangFile(__FILE__);

include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/arturgolubev.smartsearch/lib/installation.php';

Class arturgolubev_smartsearch extends CModule
{
	const MODULE_ID = 'arturgolubev.smartsearch';
	var $MODULE_ID = 'arturgolubev.smartsearch'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("arturgolubev.smartsearch_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("arturgolubev.smartsearch_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("arturgolubev.smartsearch_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("arturgolubev.smartsearch_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler('catalog', '\Bitrix\Catalog\Product::onAfterUpdate', self::MODULE_ID, 'CArturgolubevSmartsearch', 'onProductChange', 500);
		
		RegisterModuleDependences('search', 'BeforeIndex', self::MODULE_ID, 'CArturgolubevSmartsearch', 'onIndexHandler');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler('catalog', '\Bitrix\Catalog\Product::onAfterUpdate', self::MODULE_ID, 'CArturgolubevSmartsearch', 'onProductChange');
		
		UnRegisterModuleDependences('search', 'BeforeIndex', self::MODULE_ID, 'CArturgolubevSmartsearch', 'onIndexHandler');
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		
		
		$mPath = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID;
		CopyDirFiles($mPath."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js",true,true);
		CopyDirFiles($mPath."/install/gadgets", $_SERVER["DOCUMENT_ROOT"]."/bitrix/gadgets",true,true);
		CopyDirFiles($mPath."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		
		if(class_exists('agInstaHelperSmartsearch')){
			agInstaHelperSmartsearch::addGadgetToDesctop("WATCHER");
		}
		
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		
		DeleteDirFilesEx("/bitrix/js/".self::MODULE_ID);
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFilesEx("/bitrix/themes/.default/icons/".self::MODULE_ID."/");
		
		return true;
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		
		if (class_exists('agInstaHelperSmartsearch'))
		{
			agInstaHelperSmartsearch::IncludeAdminFile(GetMessage("MOD_INST_OK"), "/bitrix/modules/".self::MODULE_ID."/install/success_install.php");
		}
	}

	function DoUninstall()
	{
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
