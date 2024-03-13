<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_ai extends CModule
{
    var $MODULE_ID = 'intec.ai';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    var $uninstallDatabase = true;

    function __construct()
    {
        /** @var array $arModuleVersion */
        require('version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('intec.ai.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.ai.install.index.description');
        $this->PARTNER_NAME = 'Intec';
        $this->PARTNER_URI = 'http://intecweb.ru';
    }

    function GetDirectory()
    {
        return $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/modules/'.$this->MODULE_ID;
    }

    function InstallDB()
    {
        parent::InstallDB();

        global $DB;

        $DB->Query("CREATE TABLE ai_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            elementId INT,
            iblockProperty VARCHAR(255),
            prompt LONGTEXT,
            generationResult LONGTEXT,
            done CHAR(1),
            error VARCHAR(255),
            dateCreate DATETIME DEFAULT CURRENT_TIMESTAMP,
            taskUpdated CHAR(1) DEFAULT 'N'
        );");
    }

    function InstallFiles()
    {
        global $APPLICATION;

        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        CopyDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin', true, true);
        CopyDirFiles($this->GetDirectory().'/install/tools/', $directory.'/tools/'.$this->MODULE_ID, true, true);
        CopyDirFiles($this->GetDirectory().'/install/resources', $directory.'/resources/'.$this->MODULE_ID, true, true);

        return true;
    }

    function InstallAgents()
    {
        CAgent::AddAgent(
            "\\intec\\ai\\Module::generateFromQuene();",
            $this->MODULE_ID,
            "N",
            20
        );
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();

        global $DB;

        $DB->Query("DROP TABLE IF EXISTS ai_tasks;");
    }

    function UnInstallFiles()
    {
        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        DeleteDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/tools/'.$this->MODULE_ID);
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/resources/'.$this->MODULE_ID);

        return true;
    }

    function UnInstallAgents()
    {
        CAgent::RemoveAgent(
            "\\intec\\ai\\Module::generateFromQuene();",
            $this->MODULE_ID
        );
    }

    function DoInstall()
    {
		require_once(__DIR__.'/../classes/Loader.php');
		
		global $APPLICATION;
        parent::DoInstall();
		
		if (!Loader::includeModule('intec.core')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.ai.install.requires.title'),
                __DIR__.'/requires.php'
            );
            exit;
        }

        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallAgents();

        ModuleManager::registerModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        parent::DoUninstall();

        if ($this->uninstallDatabase)
            $this->UnInstallDB();

        $this->UnInstallFiles();
        $this->UnInstallAgents();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}