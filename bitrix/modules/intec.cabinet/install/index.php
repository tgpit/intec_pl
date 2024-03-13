<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_cabinet extends CModule
{
    var $MODULE_ID = 'intec.cabinet';
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
        $this->MODULE_NAME = Loc::getMessage('intec.cabinet.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.cabinet.install.index.description');
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

        CopyDirFiles($this->GetDirectory().'/install/intec', $bitrixDirectory.'/components/intec', true, true);
        CopyDirFiles($this->GetDirectory().'/install/templates', $bitrixDirectory.'/templates/.default/components', true, true);
        CopyDirFiles($this->GetDirectory().'/install/css', $bitrixDirectory.'/css', true, true);
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();
    }

    function UnInstallFiles()
    {
        parent::UnInstallFiles();

        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/components/intec/main.widget');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/catalog.product.subscribe.list/intec.cabinet.product.subscribe.list.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/main.pagenavigation/intec.cabinet.main.pagenavigation.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/main.profile/intec.cabinet.main.profile.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.account.pay/intec.cabinet.account.pay.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.order.payment.change/intec.cabinet.order.payment.change.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.account/intec.cabinet.account.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.order/intec.cabinet.order.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.order.cancel/intec.cabinet.order.cancel.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.order.detail/intec.cabinet.order.detail.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.order.list/intec.cabinet.order.list.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.profile.detail/intec.cabinet.profile.detail.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.profile.list/intec.cabinet.profile.list.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/sale.personal.section/intec.cabinet.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/subscribe.edit/intec.cabinet.subscribe.edit.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/support.ticket.edit/intec.cabinet.support.ticket.edit.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/support.ticket.list/intec.cabinet.support.ticket.list.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/bitrix/support.wizard/intec.cabinet.support.wizard.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/templates/.default/components/intec/main.widget/intec.cabinet.personal.extranet.1');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/css/intec');
    }

    function DoInstall()
    {
        global $APPLICATION;
        parent::DoInstall();

        if (!Loader::includeModule('intec.core')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.cabinet.install.requires.title'),
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
                Loc::getMessage('intec.cabinet.install.uninstall.title'),
                __DIR__.'/unstep.php'
            );

        $this->UnInstallDB();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}