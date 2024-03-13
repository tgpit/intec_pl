<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class intec_seo extends CModule
{
    var $MODULE_ID = 'intec.seo';
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
        $this->MODULE_NAME = Loc::getMessage('intec.seo.install.index.name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('intec.seo.install.index.description');
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

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_autofill_templates` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(255) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `iBlockId` int(11) DEFAULT NULL,
          `self` tinyint(1) NOT NULL DEFAULT 0,
          `random` tinyint(1) NOT NULL DEFAULT 0,
          `quantity` int(11) DEFAULT NULL,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`code`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_autofill_templates_sections`(
          `templateId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_autofill_templates_filling_sections`(
          `templateId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_autofill_templates_elements`(
          `templateId` int(11) NOT NULL,
          `iBlockElementId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockElementId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_autofill_templates_sites`(
          `templateId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`templateId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_articles_templates` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(255) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `iBlockId` int(11) DEFAULT NULL,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`code`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_articles_templates_sections`(
          `templateId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_articles_templates_sections_for_elements`(
          `templateId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_articles_templates_elements`(
          `templateId` int(11) NOT NULL,
          `iBlockElementId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockElementId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_articles_templates_articles`(
          `templateId` int(11) NOT NULL,
          `iBlockElementId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockElementId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_articles_templates_sites`(
          `templateId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`templateId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_texts_patterns` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(255) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `value` longtext,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`code`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `searchable` tinyint(1) NOT NULL DEFAULT 1,
          `indexing` tinyint(1) NOT NULL DEFAULT 1,
          `strict` tinyint(1) NOT NULL DEFAULT 0,
          `recursive` tinyint(1) NOT NULL DEFAULT 1,
          `priority` double NOT NULL DEFAULT 0,
          `frequency` varchar(255) NOT NULL DEFAULT \'always\',
          `iBlockId` int(11) DEFAULT NULL,
          `rules` longtext,
          `metaTitle` text,
          `metaKeywords` text,
          `metaDescription` text,
          `metaSearchTitle` text,
          `metaPageTitle` text,
          `metaBreadcrumbName` text,
          `metaDescriptionTop` text,
          `metaDescriptionBottom` text,
          `metaDescriptionAdditional` text,
          `tagName` text,
          `tagMode` varchar(255) NOT NULL DEFAULT \'self\',
          `tagRelinkingStrict` tinyint(1) NOT NULL DEFAULT 0,
          `urlActive` tinyint(1) NOT NULL DEFAULT 1,
          `urlName` varchar(255),
          `urlSource` text,
          `urlTarget` text,
          `urlGenerator` text,
          `autofillIBlockId` int(11) DEFAULT NULL,
          `autofillSelf` tinyint(1) NOT NULL DEFAULT 0,
          `autofillQuantity` int(11) DEFAULT NULL,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_sections` (
          `conditionId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`conditionId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_sites` (
          `conditionId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`conditionId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_tags_relinking_conditions` (
          `conditionId` int(11) NOT NULL,
          `relinkingConditionId` int(11) NOT NULL,
          PRIMARY KEY (`conditionId`, `relinkingConditionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_articles`(
          `conditionId` int(11) NOT NULL,
          `iBlockElementId` int(11) NOT NULL,
          PRIMARY KEY (`conditionId`,`iBlockElementId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_autofill_sections`(
          `conditionId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`conditionId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_generators` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `iBlockId` int(11) DEFAULT NULL,
          `operator` varchar(255) NOT NULL DEFAULT \'and\',
          `blocks` longtext,
          `conditionName` varchar(255) NOT NULL,
          `conditionActive` tinyint(1) NOT NULL DEFAULT 1,
          `conditionSearchable` tinyint(1) NOT NULL DEFAULT 1,
          `conditionIndexing` tinyint(1) NOT NULL DEFAULT 1,
          `conditionStrict` tinyint(1) NOT NULL DEFAULT 0,
          `conditionRecursive` tinyint(1) NOT NULL DEFAULT 1,
          `conditionPriority` float NOT NULL DEFAULT 0,
          `conditionFrequency` varchar(255) NOT NULL DEFAULT \'always\',
          `conditionMetaTitle` text,
          `conditionMetaKeywords` text,
          `conditionMetaDescription` text,
          `conditionMetaSearchTitle` text,
          `conditionMetaPageTitle` text,
          `conditionMetaBreadcrumbName` text,
          `conditionMetaDescriptionTop` text,
          `conditionMetaDescriptionBottom` text,
          `conditionMetaDescriptionAdditional` text,
          `conditionTagName` text,
          `conditionTagMode` varchar(255) NOT NULL DEFAULT \'self\',
          `conditionTagRelinkingStrict` tinyint(1) NOT NULL DEFAULT 0,
          `conditionUrlActive` tinyint(1) NOT NULL DEFAULT 0,
          `conditionUrlName` varchar(255),
          `conditionUrlSource` text,
          `conditionUrlTarget` text,
          `conditionUrlGenerator` text,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_generators_sections` (
          `generatorId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`generatorId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_conditions_generators_sites` (
          `generatorId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`generatorId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_url` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `conditionId` int(11),
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `source` text NOT NULL,
          `target` text NOT NULL,
          `iBlockId` int(11) DEFAULT NULL,
          `iBlockSectionId` int(11) DEFAULT NULL,
          `iBlockElementsCount` int(11) DEFAULT NULL,
          `dateCreate` datetime NOT NULL,
          `dateChange` datetime NOT NULL,
          `mapping` tinyint(1) NOT NULL DEFAULT 1,
          `metaTitle` text,
          `metaKeywords` text,
          `metaDescription` text,
          `metaPageTitle` text,
          `metaBreadcrumbName` text,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_url_scans` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `urlId` int(11) NOT NULL,
          `date` datetime NOT NULL,
          `status` int(11),
          `metaTitle` text,
          `metaKeywords` text,
          `metaDescription` text,
          `metaPageTitle` text,
          PRIMARY KEY (`id`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_visits` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `sessionId` varchar(255) NOT NULL,
          `referrerUrl` text NOT NULL,
          `pageUrl` text NOT NULL,
          `pageCount` int(11) NOT NULL,
          `dateCreate` datetime NOT NULL,
          `dateVisit` datetime NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`sessionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_filter_sitemaps` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `siteId` varchar(2),
          `name` varchar(255) NOT NULL,
          `scheme` varchar(255) NOT NULL,
          `domain` varchar(255) NOT NULL,
          `sourceFile` varchar(255) NOT NULL DEFAULT \'sitemap.xml\',
          `targetFile` varchar(255) NOT NULL DEFAULT \'sitemap_seo.xml\',
          `configured` tinyint(1) NOT NULL DEFAULT 1,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_iblocks_metadata_templates`(  
          `id` INT NOT NULL AUTO_INCREMENT,
          `code` varchar(255) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `iBlockId` int(11) DEFAULT NULL,
          `rules` text NOT NULL,
          `sectionMetaTitle` text,
          `sectionMetaKeywords` text,
          `sectionMetaDescription` text,
          `sectionMetaPageTitle` text,
          `sectionMetaPicturePreviewAlt` text,
          `sectionMetaPicturePreviewTitle` text,
          `sectionMetaPictureDetailAlt` text,
          `sectionMetaPictureDetailTitle` text,
          `elementMetaTitle` text,
          `elementMetaKeywords` text,
          `elementMetaDescription` text,
          `elementMetaPageTitle` text,
          `elementMetaPicturePreviewAlt` text,
          `elementMetaPicturePreviewTitle` text,
          `elementMetaPictureDetailAlt` text,
          `elementMetaPictureDetailTitle` text,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`code`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_iblocks_metadata_templates_sections`(  
          `templateId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_iblocks_metadata_templates_sites`(  
          `templateId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`templateId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_iblocks_elements_names_templates`(
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(255) NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `name` varchar(255) NOT NULL,
          `iBlockId` int(11) DEFAULT NULL,
          `value` text NOT NULL,
          `quantity` int(11) DEFAULT NULL,
          `sort` int(11) NOT NULL DEFAULT 500,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `UNIQUE` (`code`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_iblocks_elements_names_templates_sections`(
          `templateId` int(11) NOT NULL,
          `iBlockSectionId` int(11) NOT NULL,
          PRIMARY KEY (`templateId`, `iBlockSectionId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_iblocks_elements_names_templates_sites`(
          `templateId` int(11) NOT NULL,
          `siteId` varchar(2) NOT NULL,
          PRIMARY KEY (`templateId`, `siteId`)
        );');

        $DB->Query('CREATE TABLE IF NOT EXISTS `seo_sites_settings` (
          `siteId` varchar(2),
          `filterIndexingDisabled` tinyint(1) NOT NULL DEFAULT 0,
          `filterPaginationPart` varchar(255),
          `filterPaginationText` varchar(255),
          `filterCanonicalUse` tinyint(1) NOT NULL DEFAULT 1,
          `filterUrlQueryClean` tinyint(1) NOT NULL DEFAULT 1,
          `filterVisitsEnabled` tinyint(1) NOT NULL DEFAULT 1,
          `filterVisitsReferrers` text,
          `filterPages` text,
          `filterClearRedirectUse` tinyint(1) NOT NULL DEFAULT 1,
          PRIMARY KEY (`siteId`)
        );');
    }

    function InstallEvents()
    {
        parent::InstallEvents();

        $events = EventManager::getInstance();
        $events->registerEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            '\\intec\\seo\\Callbacks',
            'mainOnPageStart'
        );

        $events->registerEventHandler(
            'iblock',
            'OnTemplateGetFunctionClass',
            $this->MODULE_ID,
            '\\intec\\seo\\Tags',
            'resolve'
        );

        $events->registerEventHandler(
            'search',
            'OnReindex',
            $this->MODULE_ID,
            '\\intec\\seo\\Callbacks',
            'searchOnReindex'
        );
    }

    function InstallFiles()
    {
        global $APPLICATION;

        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        CopyDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin', true, true);
        CopyDirFiles($this->GetDirectory().'/install/components', $directory.'/components/intec.seo', true, true);
        CopyDirFiles($this->GetDirectory().'/install/resources', $directory.'/resources/intec.seo', true, true);

        return true;
    }

    function UnInstallDB()
    {
        parent::UnInstallDB();

        global $DB;

        $DB->Query('DROP TABLE IF EXISTS
          `seo_autofill_templates`,
          `seo_autofill_templates_sections`,
          `seo_autofill_templates_filling_sections`,
          `seo_autofill_templates_elements`,
          `seo_autofill_templates_sites`,
          `seo_articles_templates`,
          `seo_articles_templates_sections`,
          `seo_articles_templates_sections_for_elements`,
          `seo_articles_templates_elements`,
          `seo_articles_templates_articles`,
          `seo_articles_templates_sites`,
          `seo_texts_patterns`,
          `seo_filter_conditions`,
          `seo_filter_conditions_sections`,
          `seo_filter_conditions_sites`,
          `seo_filter_conditions_articles`,
          `seo_filter_conditions_autofill_sections`,
          `seo_filter_conditions_tags_relinking_conditions`,
          `seo_filter_conditions_generators`,
          `seo_filter_conditions_generators_sections`,
          `seo_filter_conditions_generators_sites`,
          `seo_filter_url`,
          `seo_filter_url_scans`,
          `seo_filter_visits`,
          `seo_filter_sitemaps`,
          `seo_iblocks_metadata_templates`,
          `seo_iblocks_metadata_templates_sections`,
          `seo_iblocks_metadata_templates_sites`,
          `seo_iblocks_elements_names_templates`,
          `seo_iblocks_elements_names_templates_sections`,
          `seo_iblocks_elements_names_templates_sites`,
          `seo_sites_settings`;');
    }

    function UnInstallEvents()
    {
        parent::UnInstallEvents();

        $events = EventManager::getInstance();
        $events->unRegisterEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            '\\intec\\seo\\Callbacks',
            'mainOnPageStart'
        );

        $events->unRegisterEventHandler(
            'iblock',
            'OnTemplateGetFunctionClass',
            $this->MODULE_ID,
            '\\intec\\seo\\Tags',
            'resolve'
        );

        $events->unRegisterEventHandler(
            'search',
            'OnReindex',
            $this->MODULE_ID,
            '\\intec\\seo\\Callbacks',
            'searchOnReindex'
        );
    }

    function UnInstallFiles()
    {
        $directory = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;

        DeleteDirFiles($this->GetDirectory().'/install/admin', $directory.'/admin');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/components/intec.seo');
        DeleteDirFilesEx(BX_PERSONAL_ROOT.'/resources/intec.seo');

        return true;
    }

    function DoInstall()
    {
        require_once(__DIR__.'/../classes/Loader.php');

        global $APPLICATION;
        parent::DoInstall();

        if (!Loader::includeModule('intec.core')) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('intec.seo.install.requires.title'),
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
                Loc::getMessage('intec.seo.install.uninstall.title'),
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