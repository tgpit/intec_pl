<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;

/**
 * @var $arUrlTemplates
 */

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule('intec.core'))
    return;

Core::$app->web->css->addFile(Core::getAlias('@resources/intec.universe/css/icons.css'));

$arMenu = [
    'parent_menu' => 'global_intec',
    'text' => Loc::getMessage('intec.core.menu'),
    'icon' => "intec-notify-menu-icon",
    'page_icon' => 'intec-universe-menu-icon',
    'url' => '/bitrix/admin/intec_notifyer.php',
    'items_id' => 'intec_notifyer',
    'items' => []
];

return $arMenu;