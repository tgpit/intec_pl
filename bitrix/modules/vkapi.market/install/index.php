<?php

use Bitrix\Main\Localization\Loc as Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
class vkapi_market extends CModule
{
    var $MODULE_ID = "vkapi.market";
    var $PARTNER_NAME = "VK";
    var $PARTNER_URI = "https://vk.com/";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_ID = "vkapi";
    private $arModuleDependences = array(
        //глобальное меню
        array('main', 'OnBuildGlobalMenu', '\\VKapi\\Market\\Handler', 'main_onBuildGlobalMenu'),
    );
    private $arModuleDependencesV2 = array(array('sale', 'OnSaleOrderSaved', '\\VKapi\\Market\\Handler', 'saleOnSaleOrderSaved'), array('sale', 'OnSaleOrderCanceled', '\\VKapi\\Market\\Handler', 'onSaleOrderChanged'), array('sale', 'OnSaleStatusOrderChange', '\\VKapi\\Market\\Handler', 'onSaleOrderChanged'), array('sale', 'OnSaleOrderPaid', '\\VKapi\\Market\\Handler', 'onSaleOrderChanged'));
    public function __construct()
    {
        include __DIR__ . '/version.php';
        $this->MODULE_DIR = \Bitrix\Main\Loader::getLocal('modules/vkapi.market');
        $this->isLocal = !!\strpos($this->MODULE_DIR, '/local/modules/');
        $this->MODULE_NAME = \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.MODULE_NAME');
        $this->MODULE_DESCRIPTION = \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.MODULE_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('VKAPI.MARKET.PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('VKAPI.MARKET.PARTNER_URI');
        $this->MODULE_VERSION = empty($arModuleVersion['VERSION']) ? '' : $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = empty($arModuleVersion['VERSION_DATE']) ? '' : $arModuleVersion['VERSION_DATE'];
    }
    function GetModuleRightList()
    {
        $arr = array("reference_id" => array("D", "R", "W"), "reference" => array("[D] " . \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.PERM_D'), "[R] " . \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.PERM_R'), "[W] " . \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.PERM_W')));
        return $arr;
    }
    function DoInstall()
    {
        global $APPLICATION;
        \RegisterModule($this->MODULE_ID);
        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallAgents();
        $this->InstallDependences();
        $APPLICATION->IncludeAdminFile(\Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.MODULE_INSTALL_SUCCESS'), $this->MODULE_DIR . "/install/step_final.php");
        return \true;
    }
    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallAgents();
        $this->UnInstallDependences();
        \COption::RemoveOption($this->MODULE_ID);
        \UnRegisterModule($this->MODULE_ID);
        return \true;
    }
    /**
         * 
         * @return bool
         */
    function InstallDB()
    {
        global $DB, $DBType, $APPLICATION;
        // Database tables creation
        $DB->RunSQLBatch(\dirname(__FILE__) . "/db/mysql/install.sql");
        return \true;
    }
    /**
         * 
         * @return bool|void
         */
    function UnInstallDB()
    {
        global $DB, $DBType, $APPLICATION;
        $DB->RunSQLBatch(\dirname(__FILE__) . "/db/mysql/uninstall.sql");
        return \true;
    }
    function InstallFiles($arParams = array())
    {
        if ($this->isLocal) {
            \CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/local/components/", \true, \true);
        } else {
            \CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", \true, \true);
        }
        if (\file_exists($path = $this->MODULE_DIR . '/admin')) {
            if ($dir = \opendir($path)) {
                while (\false !== ($item = \readdir($dir))) {
                    if (\in_array($item, array('.', '..', 'menu.php')) || \is_dir($this->MODULE_DIR . '/admin/' . $item)) {
                        continue;
                    }
                    if (\preg_match('/\\.css$|\\.js$/', $item)) {
                        continue;
                    }
                    if (!\file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item)) {
                        \file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                    }
                }
            }
        }
        if (\file_exists($path = $this->MODULE_DIR . '/tools')) {
            if ($dir = \opendir($path)) {
                \CheckDirPath($_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools/" . $this->MODULE_ID . '/');
                while (\false !== ($item = \readdir($dir))) {
                    if (\in_array($item, array('.', '..'))) {
                        continue;
                    }
                    $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '/' . $item;
                    if (!\file_exists($file)) {
                        \file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/tools/' . $item . '");?' . '>');
                    }
                }
            }
        }
        // js + css -------
        \CheckDirPath($_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID . '/');
        \CopyDirFiles($this->MODULE_DIR . "/dist/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID . '/', \true, \true);
        return \true;
    }
    function UnInstallFiles()
    {
        if (\is_dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/")) {
            $d = \dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/");
            while ($entry = $d->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                \DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                \DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
            }
            $d->close();
        }
        if (\file_exists($path = $this->MODULE_DIR . '/admin')) {
            if ($dir = \opendir($path)) {
                while (\false !== ($item = \readdir($dir))) {
                    if (\in_array($item, array('.', '..', 'menu.php'))) {
                        continue;
                    }
                    if (\file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item)) {
                        \unlink($file);
                    }
                }
            }
        }
        if (\file_exists($path = $this->MODULE_DIR . '/tools')) {
            if ($dir = \opendir($path)) {
                while (\false !== ($item = \readdir($dir))) {
                    if (\in_array($item, array('.', '..'))) {
                        continue;
                    }
                    if (\file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '/' . $item)) {
                        \unlink($file);
                    }
                }
            }
        }
        // файлы стилей -------
        // удаляет рекурсивно файлы
        \DeleteDirFilesEx("/bitrix/js/" . $this->MODULE_ID . '/');
        return \true;
    }
    public function InstallAgents()
    {
        $oAgent = new \CAgent();
        $oAgent->AddAgent('\\VKapi\\Market\\Agent::clearAntiCaptchaResults();', $this->MODULE_ID, 'N', 3600);
        $oAgent->AddAgent('\\VKapi\\Market\\Agent::exportData();', $this->MODULE_ID, 'N', 50);
        $oAgent->AddAgent('\\VKapi\\Market\\Agent::clearLimit();', $this->MODULE_ID, 'N', 3600);
    }
    public function UnInstallAgents()
    {
        $oAgent = new \CAgent();
        $oAgent->RemoveModuleAgents($this->MODULE_ID);
    }
    public function InstallDependences()
    {
        foreach ($this->arModuleDependences as $item) {
            if (\count($item) < 4) {
                continue;
            }
            \RegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3], isset($item[4]) ? \intval($item[4]) : 100);
        }
        foreach ($this->arModuleDependencesV2 as $item) {
            if (\count($item) < 4) {
                continue;
            }
            \Bitrix\Main\EventManager::getInstance()->registerEventHandler($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3]);
        }
    }
    public function UnInstallDependences()
    {
        foreach ($this->arModuleDependences as $item) {
            if (\count($item) < 4) {
                continue;
            }
            \UnRegisterModuleDependences($item[0], $item[1], $this->MODULE_ID);
        }
        foreach ($this->arModuleDependencesV2 as $item) {
            if (\count($item) < 4) {
                continue;
            }
            \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($item[0], $item[1], $this->MODULE_ID);
        }
    }
    /**
         * 
         */
    public function InstallTemplates()
    {
    }
    /**
         * 
         */
    public function UnInstallTemplates()
    {
    }
}
?>