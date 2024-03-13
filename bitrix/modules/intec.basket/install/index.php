<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_basket extends CModule
{
    var $MODULE_ID = 'intec.basket';
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
        $this->MODULE_NAME = Loc::getMessage('intec.basket.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.basket.install.index.description');
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
    }

    function InstallFiles()
    {
        $bitrixDirectory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        parent::InstallFiles();

        CopyDirFiles($this->GetDirectory().'/install/templates', $bitrixDirectory.'/templates/.default/components', true, true);
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();
    }

    function UnInstallFiles()
    {
        parent::UnInstallFiles();

        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.basket.basket/intec.basket.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.order.ajax/intec.order.1');
    }

    function DoInstall()
    {
        global $APPLICATION;
        parent::DoInstall();

        if (!Loader::includeModule('intec.core')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.basket.install.requires.title'),
                __DIR__.'/requires.php'
            );
            exit;
        }

        $this->InstallDB();
        $this->InstallFiles();

        ModuleManager::registerModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        global $APPLICATION;
        parent::DoUninstall();

        $continue = $_POST['go'];
        $continue = $continue == 'Y';

        if (!$continue)
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.basket.install.uninstall.title'),
                __DIR__.'/unstep.php'
            );

        $this->UnInstallDB();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
?>