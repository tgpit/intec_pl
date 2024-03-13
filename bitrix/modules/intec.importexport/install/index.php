<?php


use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_importexport extends CModule
{
    var $MODULE_ID = 'intec.importexport';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function __construct()
    {
        /** @var array $arModuleVersion */
        require('version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('intec.importexport.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.importexport.install.index.description');
        $this->PARTNER_NAME = 'Intec';
        $this->PARTNER_URI = 'https://intecweb.ru';
    }

    function GetDirectory()
    {
        return $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/modules/'.$this->MODULE_ID;
    }

    function InstallDB()
    {
        parent::InstallDB();

        global $DB;

        $DB->Query('CREATE TABLE IF NOT EXISTS `intec_importexport_excel_export_profile` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `params` mediumtext,
          `tableParams` mediumtext,
          `columnSettings` mediumtext,
          `createDate` datetime,
          `editDate` datetime,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`id`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `intec_importexport_excel_import_profile` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `params` mediumtext,
          `tableParams` mediumtext,
          `columnSettings` mediumtext,
          `rowSettings` mediumtext,
          `createDate` datetime,
          `editDate` datetime,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`id`)
        );');
    }

    function InstallEvents()
    {
        parent::InstallEvents();


    }

    function InstallFiles()
    {
        global $APPLICATION;

        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        CopyDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin', true, true);
        CopyDirFiles($this->GetDirectory().'/install/resources', $directory.'/resources/intec.importexport', true, true);
        CopyDirFiles($this->GetDirectory().'/install/js', $directory.'/js/intec.importexport', true, true);
        CopyDirFiles($this->GetDirectory().'/install/php_interface', $directory.'/php_interface/include/intec.importexport', true, true);

        return true;
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();

        global $DB;

        $DB->Query('DROP TABLE IF EXISTS
          `intec_importexport_excel_export_profile`,
          `intec_importexport_excel_import_profile`;');
    }

    function UnInstallEvents()
    {
        parent::UnInstallEvents();
    }

    function UnInstallFiles()
    {
        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        DeleteDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/resources/intec.importexport');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/js/intec.importexport');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/php_interface/include/intec.importexport');

        return true;
    }

    function DoInstall()
    {
        require_once(__DIR__.'/../classes/Loader.php');

        global $APPLICATION;
        parent::DoInstall();

        if (!Loader::includeModule('intec.core')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.importexport.install.requires.title'),
                __DIR__.'/requires.php'
            );
            exit;
        }

        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();

        ModuleManager::registerModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        require_once(__DIR__.'/../classes/Loader.php');

        global $APPLICATION;
        parent::DoUninstall();

        $continue = $_POST['go'];
        $continue = $continue == 'Y';
        $remove = $_POST['remove'];

        if (!$continue)
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.importexport.install.uninstall.title'),
                __DIR__.'/unstep.php'
            );


        if ($remove['database'] == 'Y')
            $this->UnInstallDB();

        $this->UnInstallEvents();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    function GetInstallDirectory()
    {
        return __DIR__;
    }
}