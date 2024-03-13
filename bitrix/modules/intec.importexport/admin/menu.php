<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;

/**
 * @var $arUrlTemplates
 * @global CUser $USER
 */

global $USER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.importexport'))
    return;

Core::$app->web->css->addFile(Core::getAlias('@intec/importexport/resources/css/icons.css'));

$bIsMenu = true;
include(__DIR__.'/url.php');

$arMenu = [
    'parent_menu' => 'global_intec',
    'text' => Loc::getMessage('intec.importexport.menu'),
    'icon' => 'importexport-menu-icon',
    'page_icon' => 'importexport-menu-icon',
    'items_id' => 'intec_importexport',
    'items' => [[
        'text' => Loc::getMessage('intec.importexport.menu.excel'),
        'icon' => 'importexport-menu-icon-excel-templates',
        'page_icon' => 'importexport-menu-icon-excel-templates',
        'items_id' => 'intec_importexport_excel_patterns',
        'items' => [[
            'text' => Loc::getMessage('intec.importexport.menu.excel.export'),
            'icon' => 'importexport-menu-icon-excel-export-templates',
            'page_icon' => 'importexport-menu-icon-excel-export-templates',
            'url' => $arUrlTemplates['excel.export.templates'],
            'more_url' => [
                'intec_importexport_export_templates_edit.php'
            ],
            'items_id' => 'intec_importexport_excel_export_templates'
        ], [
            'text' => Loc::getMessage('intec.importexport.menu.excel.import'),
            'icon' => 'importexport-menu-icon-excel-import-templates',
            'page_icon' => 'importexport-menu-icon-excel-import-templates',
            'url' => $arUrlTemplates['excel.import.templates'],
            'more_url' => [
                'intec_importexport_import_templates_edit.php'
            ],
            'items_id' => 'intec_importexport_excel_import_templates'
        ]]
    ]]
];

return $arMenu;