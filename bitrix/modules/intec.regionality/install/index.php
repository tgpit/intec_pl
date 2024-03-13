<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_regionality extends CModule
{
    var $MODULE_ID = 'intec.regionality';
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
        $this->MODULE_NAME = Loc::getMessage('intec.regionality.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.regionality.install.index.description');
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

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_regions`(  
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(255) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `description` text,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`code`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_regions_domains`(  
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `regionId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `default` tinyint(1) NOT NULL DEFAULT 0,
          `value` varchar(255) NOT NULL,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`value`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_regions_prices_types`(
          `regionId` int(11) NOT NULL,
          `priceTypeId` int(11) NOT NULL,
          PRIMARY KEY (`regionId`, `priceTypeId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_regions_stores`(
          `regionId` int(11) NOT NULL,
          `storeId` int(11) NOT NULL,
          PRIMARY KEY (`regionId`, `storeId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_regions_sites`(
          `regionId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`regionId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_regions_values`(
          `propertyCode` varchar(255) NOT NULL,
          `regionId` int(11) NOT NULL,
          `siteId` varchar(2),
          `value` text,
          UNIQUE INDEX `UNIQUE` (`propertyCode`, `regionId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_sites_settings`(  
          `siteId` varchar(2) NOT NULL,
          `regionId` int(11) DEFAULT NULL,
          `regionLocationResolve` int(11) NOT NULL DEFAULT 1,
          `regionRememberTime` int(11) NOT NULL DEFAULT 3600,
          `regionResolveOrder` int(11) NOT NULL DEFAULT 0,
          `regionResolveIgnoreUse` tinyint(1) NOT NULL DEFAULT 1,
          `regionResolveIgnoreUserAgents` text NULL,
          `domain` varchar(255) DEFAULT NULL,
          `domainsUse` tinyint(1) NOT NULL DEFAULT 0,
          `domainsLinkingUse` tinyint(1) NOT NULL DEFAULT 1,
          `domainsLinkingReset` tinyint(1) NOT NULL DEFAULT 0,
          `domainsRedirectUse` tinyint(1) NOT NULL DEFAULT 1,
          PRIMARY KEY (`siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `regionality_sites_settings_locator_extensions`(
          `siteId` varchar(2) NOT NULL,
          `extensionCode` varchar(255) NOT NULL,
          PRIMARY KEY (`siteId`, `extensionCode`)
        );');
    }

    function InstallEvents()
    {
        parent::InstallEvents();

        $events = EventManager::getInstance();
        $events->registerEventHandler(
            'catalog',
            'OnCondSaleControlBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\platform\\sale\\discount\\conditions\\RegionsCondition',
            'GetControlDescr'
        );

        $events->registerEventHandler(
            'catalog',
            'OnGetOptimalPrice',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'catalogOnGetOptimalPrice'
        );

        $events->registerEventHandler(
            'main',
            'OnAfterUserTypeDelete',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'mainOnAfterUserTypeDelete'
        );

        $events->registerEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'mainOnEndBufferContent'
        );

        $events->registerEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'mainOnProlog'
        );

        $events->registerEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\platform\\iblock\\properties\\RegionProperty',
            'getDefinition'
        );

        $events->registerEventHandler(
            'sale',
            'OnCondSaleControlBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\platform\\sale\\discount\\conditions\\RegionsCondition',
            'GetControlDescr'
        );

        $events->registerEventHandler(
            'sale',
            'OnSaleComponentOrderProperties',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'saleComponentOrderProperties'
        );

        $events->registerEventHandler(
            'sale',
            'onSaleCompanyRulesClassNamesBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'saleCompanyRestrictions'
        );

        $events->registerEventHandler(
            'sale',
            'onSaleDeliveryRestrictionsClassNamesBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'saleDeliveryRestrictions'
        );

        $events->registerEventHandler(
            'sale',
            'onSalePaySystemRestrictionsClassNamesBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'salePaySystemRestrictions'
        );
    }

    function InstallFiles()
    {
        global $APPLICATION;

        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        CopyDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin', true, true);
        CopyDirFiles($this->GetDirectory().'/install/components', $directory.'/components/'.$this->MODULE_ID, true, true);
        CopyDirFiles($this->GetDirectory().'/install/resources', $directory.'/resources/intec.regionality', true, true);

        $APPLICATION->SetFileAccessPermission(BX_PERSONAL_ROOT.'/admin/regionality_regions_select.php', [2 => 'R']);

        return true;
    }

    function InstallDemo()
    {
        require_once(__DIR__.'/../classes/Loader.php');
        require(__DIR__.'/procedures/demo.install.php');
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();

        global $DB;

        $DB->Query('DROP TABLE IF EXISTS 
          `regionality_regions`,
          `regionality_regions_domains`,
          `regionality_regions_prices_types`,
          `regionality_regions_stores`,
          `regionality_regions_sites`,
          `regionality_regions_values`,
          `regionality_sites_settings`,
          `regionality_sites_settings_locator_extensions`;');
    }

    function UnInstallEvents()
    {
        parent::UnInstallEvents();

        $events = EventManager::getInstance();
        $events->unRegisterEventHandler(
            'catalog',
            'OnCondSaleControlBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\platform\\sale\\discount\\conditions\\RegionsCondition',
            'GetControlDescr'
        );

        $events->unRegisterEventHandler(
            'catalog',
            'OnGetOptimalPrice',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'catalogOnGetOptimalPrice'
        );

        $events->unRegisterEventHandler(
            'main',
            'OnAfterUserTypeDelete',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'mainOnAfterUserTypeDelete'
        );

        $events->unRegisterEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'mainOnEndBufferContent'
        );

        $events->unRegisterEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'mainOnProlog'
        );

        $events->unRegisterEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\platform\\properties\\RegionProperty',
            'getDefinition'
        );

        $events->unRegisterEventHandler(
            'sale',
            'OnCondSaleControlBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\platform\\sale\\discount\\conditions\\RegionsCondition',
            'GetControlDescr'
        );

        $events->unRegisterEventHandler(
            'sale',
            'OnSaleComponentOrderProperties',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'saleComponentOrderProperties'
        );

        $events->unRegisterEventHandler(
            'sale',
            'onSaleCompanyRulesClassNamesBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'saleCompanyRestrictions'
        );

        $events->unRegisterEventHandler(
            'sale',
            'onSaleDeliveryRestrictionsClassNamesBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'saleDeliveryRestrictions'
        );

        $events->unRegisterEventHandler(
            'sale',
            'onSalePaySystemRestrictionsClassNamesBuildList',
            $this->MODULE_ID,
            '\\intec\\regionality\\Callbacks',
            'salePaySystemRestrictions'
        );
    }

    function UnInstallFiles()
    {
        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        DeleteDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/components/intec.regionality');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/resources/intec.regionality');

        return true;
    }

    function DoInstall()
    {
        require_once(__DIR__.'/../classes/Loader.php');

        global $APPLICATION;
        parent::DoInstall();

        if (!Loader::includeModule('intec.core')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.regionality.install.requires.title'),
                __DIR__.'/requires.php'
            );
            exit;
        }

        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallDemo();

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
                Loc::getMessage('intec.regionality.install.uninstall.title'),
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