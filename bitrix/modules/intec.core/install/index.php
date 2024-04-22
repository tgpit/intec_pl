<?php
require_once(__DIR__.'/../classes/Core.php');

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use intec\Core;
use intec\core\helpers\FileHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

Loc::loadMessages(__FILE__);

class intec_core extends CModule
{
    var $MODULE_ID = "intec.core";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    protected $directories = [
        '@intec/core/module/install/resources' => '@intec/core/resources'
    ];

    function __construct ()
    {
        $arModuleVersion = array();

        include('version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage('intec.core.installer.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.core.installer.description');
        $this->PARTNER_NAME = "Intec";
        $this->PARTNER_URI = "http://intecweb.ru";
    }

    function InstallDB()
    {
        $date = getdate();
        $newDate = mktime(0, 1, 0, $date["mon"], $date["mday"] + 1, $date["year"]);
        CAgent::AddAgent("\intec\core\AdminNotify::sendNotify();", "intec.core", "N", 86400, "", "Y", ConvertTimeStamp($newDate+CTimeZone::GetOffset(), "FULL"), 0);
    }

    function UnInstallDB()
    {
        CAgent::RemoveModuleAgents('intec.core');
    }

    function InstallEmail()
    {
        $siteId = Arrays::fromDBResult(CSite::GetList())->asArray();
        $siteId = ArrayHelper::getFirstValue($siteId);
        $siteId = $siteId['LID'];
        $mailPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/mail/';

        $mailEvent = new CEventType;
        $mailEventSettings = [
            'LID' => $siteId,
            'EVENT_NAME' => '',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => Loc::getMessage('intec.core.installer.email.event.description'),
        ];

        $mailSetting = [
            'ACTIVE' => 'Y',
            'EVENT_NAME' => '',
            'LID' => $siteId,
            'EMAIL_FROM' => COption::GetOptionString("main", "email_from"),
            'EMAIL_TO' => COption::GetOptionString("main", "email_from"),
            'SUBJECT' => '',
            'BODY_TYPE' => 'html',
            'MESSAGE' => ''
        ];

        $mail = new CEventMessage;
        $content = file_get_contents($mailPath . 'mail_solution_expire.html');

        if (!empty($content)) {
            $mailEventSettings['NAME'] = Loc::getMessage('intec.core.installer.email.event.solution.expire.name');
            $mailEventSettings['EVENT_NAME'] = 'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY';
            $mailEvent::Add($mailEventSettings);
            $mailSetting['EVENT_NAME'] = 'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY';
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.installer.email.subject.solution.expire');
            $mailSetting['MESSAGE'] = $content;
            $mail->Add($mailSetting);
        }

        $content = file_get_contents($mailPath . 'mail_solution_expired.html');

        if (!empty($content)) {
            $mailEventSettings['NAME'] = Loc::getMessage('intec.core.installer.email.event.solution.expired.name');
            $mailEventSettings['EVENT_NAME'] = 'INTEC_LICENCE_SOLUTION_EXPIRED_NOTIFY';
            $mailEvent::Add($mailEventSettings);
            $mailSetting['EVENT_NAME'] = 'INTEC_LICENCE_SOLUTION_EXPIRED_NOTIFY';
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.installer.email.subject.solution.expired');
            $mailSetting['MESSAGE'] = $content;
            $mail->Add($mailSetting);
        }

        $content = file_get_contents($mailPath . 'mail_bitrix_expire.html');

        if (!empty($content)) {
            $mailEventSettings['NAME'] = Loc::getMessage('intec.core.installer.email.event.bitrix.expire.name');
            $mailEventSettings['EVENT_NAME'] = 'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY';
            $mailEvent::Add($mailEventSettings);
            $mailSetting['EVENT_NAME'] = 'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY';
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.installer.email.subject.bitrix.expire');
            $mailSetting['MESSAGE'] = $content;
            $mail->Add($mailSetting);
        }

        $content = file_get_contents($mailPath . 'mail_bitrix_expired.html');

        if (!empty($content)) {
            $mailEventSettings['NAME'] = Loc::getMessage('intec.core.installer.email.event.bitrix.expired.name');
            $mailEventSettings['EVENT_NAME'] = 'INTEC_LICENCE_BITRIX_EXPIRED_NOTIFY';
            $mailEvent::Add($mailEventSettings);
            $mailSetting['EVENT_NAME'] = 'INTEC_LICENCE_BITRIX_EXPIRED_NOTIFY';
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.installer.email.subject.bitrix.expired');
            $mailSetting['MESSAGE'] = $content;
            $mail->Add($mailSetting);
        }
    }

    function UnInstallEmail()
    {
        $mailEvent = new CEventType;
        $mailEvent::Delete([
            'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY',
            'INTEC_LICENCE_SOLUTION_EXPIRED_NOTIFY',
            'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY',
            'INTEC_LICENCE_BITRIX_EXPIRED_NOTIFY'
        ]);
    }

    function GetDirectory()
    {
        return $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/modules/'.$this->MODULE_ID;
    }

    function InstallFiles()
    {
        $bitrixDirectory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        CopyDirFiles($this->GetDirectory().'/install/admin', $bitrixDirectory.'/admin', true, true);
        CopyDirFiles($this->GetDirectory().'/install/gadgets/', $bitrixDirectory.'/gadgets', true, true);

        if (FileHelper::isDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/')) {
            if (!FileHelper::isFile($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/admin_header.php'))
                CopyDirFiles($this->GetDirectory().'/install/php_interface/', $_SERVER['DOCUMENT_ROOT'] .'/local/php_interface', true, true);
        } else {
            if (!FileHelper::isFile($bitrixDirectory . '/php_interface/admin_header.php'))
                CopyDirFiles($this->GetDirectory().'/install/php_interface/', $bitrixDirectory.'/php_interface', true, true);
        }

        return true;
    }

    function UnInstallFiles()
    {
        $bitrixDirectory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        DeleteDirFiles($this->GetDirectory().'/install/admin', $bitrixDirectory.'/admin');
        DeleteDirFiles($this->GetDirectory().'/install/gadgets/', $bitrixDirectory.'/gadgets');
        DeleteDirFiles($this->GetDirectory().'/install/php_interface/', $bitrixDirectory.'/php_interface');

        return true;
    }

    private static function InstallDesktop()
    {
        $arUserOptions = CUserOptions::GetOption("intranet", "~gadgets_admin_index");
        $newUserOptions = [];

        foreach ($arUserOptions as $key => $values) {
            if (array_key_exists('GADGETS', $arUserOptions[$key])) {
                $newUserOptions[$key]['GADGETS']['INTEC_NOTIFY@'.rand(1000000000, 9999999999)] = [
                    'COLUMN' => 0,
                    'ROW' => 0,
                    'HIDE' => 'N'
                ];

                foreach ($values['GADGETS'] as $gadgetKey => $gadget) {
                    $newUserOptions[$key]['GADGETS'][$gadgetKey] = $gadget;
                }
            }
        }

        CUserOptions::SetOption('intranet', "~gadgets_admin_index", $newUserOptions);
    }

    private static function UnInstallDesktop()
    {
        $arUserOptions = CUserOptions::GetOption("intranet", "~gadgets_admin_index");

        $newUserOptions = [];

        foreach ($arUserOptions as $key => $values) {
            foreach ($values['GADGETS'] as $gadgetKey => $gadget) {
                if (!StringHelper::startsWith($gadgetKey, 'INTEC_NOTIFY'))
                    $newUserOptions[$key]['GADGETS'][$gadgetKey] = $gadget;
            }
        }

        CUserOptions::SetOption('intranet', "~gadgets_admin_index", $newUserOptions);
    }

    function DoInstall()
    {
        parent::DoInstall();

        global $APPLICATION;

        if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.core.installer.requires.title'),
                __DIR__.'/requires.php'
            );
            exit;
        }

        require(__DIR__.'/procedures/database.php');

        $this->InstallFiles();
        $this->InstallDesktop();
        $this->InstallDB();
        $this->InstallEmail();

        foreach ($this->directories as $directoryFrom => $directoryTo) {
            FileHelper::copyDirectory(
                Core::getAlias($directoryFrom),
                Core::getAlias($directoryTo)
            );
        }

        ModuleManager::registerModule($this->MODULE_ID);

        $events = EventManager::getInstance();
        $events->registerEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            '\\intec\\core\\Callbacks',
            'mainOnBuildGlobalMenu'
        );

        $events->registerEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            '\\intec\\core\\Callbacks',
            'mainOnEndBufferContent'
        );

        $events->registerEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\\intec\\core\\bitrix\\main\\properties\\HtmlProperty',
            'getDefinition'
        );

        $events->registerEventHandler(
            'iblock',
            'OnTemplateGetFunctionClass',
            $this->MODULE_ID,
            '\\intec\\core\\bitrix\\iblock\\Tags',
            'resolve'
        );
    }

    function DoUninstall()
    {
        parent::DoUninstall();
        $this->UnInstallFiles();
        $this->UnInstallDesktop();
        $this->UnInstallDB();
        $this->UnInstallEmail();

        foreach ($this->directories as $directory) {
            $directory = Core::getAlias($directory);
            FileHelper::removeDirectory($directory);
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $events = EventManager::getInstance();
        $events->unRegisterEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            '\\intec\\core\\Callbacks',
            'mainOnBuildGlobalMenu'
        );

        $events->unRegisterEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            '\\intec\\core\\Callbacks',
            'mainOnEndBufferContent'
        );

        $events->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\\intec\\core\\bitrix\\main\\properties\\HtmlProperty',
            'getDefinition'
        );

        $events->unRegisterEventHandler(
            'iblock',
            'OnTemplateGetFunctionClass',
            $this->MODULE_ID,
            '\\intec\\core\\bitrix\\iblock\\Tags',
            'resolve'
        );
    }
}