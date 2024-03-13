<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_measures extends CModule
{
    var $MODULE_ID = 'intec.measures';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    var $uninstallDatabase = false;

    function __construct()
    {
        /** @var array $arModuleVersion */
        require('version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('intec.measures.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.measures.install.index.description');
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

        $DB->Query('CREATE TABLE IF NOT EXISTS `measures_conversions_ratios`(  
          `productId` int(11) NOT NULL,
          `measureId` int(11) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `value` decimal(10,5) NOT NULL DEFAULT 1,
          PRIMARY KEY (`productId`, `measureId`)
        );');
    }

    function InstallEvents()
    {
        parent::InstallEvents();

        $events = EventManager::getInstance();
        $events->registerEventHandler(
            'main',
            'OnAdminTabControlBegin',
            $this->MODULE_ID,
            '\\intec\\measures\\interaction\\iblock\\Element',
            'showConversionTab'
        );

        $events->registerEventHandler(
            'iblock',
            'OnBeforeIBlockElementUpdate',
            $this->MODULE_ID,
            '\\intec\\measures\\interaction\\iblock\\Element',
            'handleConversionTab'
        );

        $events->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementDelete',
            $this->MODULE_ID,
            '\\intec\\measures\\Callbacks',
            'iblockOnAfterIBlockElementDelete'
        );
    }

    function InstallFiles()
    {
        global $APPLICATION;

        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        CopyDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin', true, true);

        return true;
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();

        global $DB;

        $DB->Query('DROP TABLE IF EXISTS `measures_conversions_ratios`;');
    }

    function UnInstallEvents()
    {
        parent::UnInstallEvents();

        $events = EventManager::getInstance();
        $events->unRegisterEventHandler(
            'main',
            'OnAdminTabControlBegin',
            $this->MODULE_ID,
            '\\intec\\measures\\interaction\\iblock\\Element',
            'showConversionTab'
        );

        $events->unRegisterEventHandler(
            'iblock',
            'OnBeforeIBlockElementUpdate',
            $this->MODULE_ID,
            '\\intec\\measures\\interaction\\iblock\\Element',
            'handleConversionTab'
        );

        $events->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementDelete',
            $this->MODULE_ID,
            '\\intec\\measures\\Callbacks',
            'iblockOnAfterIBlockElementDelete'
        );
    }

    function UnInstallFiles()
    {
        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        DeleteDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin');

        return true;
    }

    function DoInstall()
    {
        require_once(__DIR__.'/../classes/Loader.php');

        parent::DoInstall();

        if (!Loader::includeModule('intec.core'))
            return;

        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();

        ModuleManager::registerModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        require_once(__DIR__.'/../classes/Loader.php');

        parent::DoUninstall();

        if ($this->uninstallDatabase)
            $this->UnInstallDB();

        $this->UnInstallEvents();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}