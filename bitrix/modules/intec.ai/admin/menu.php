<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;

global $USER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.ai'))
    return;

Core::$app->web->css->addFile(Core::getAlias('@intec/ai/resources/css/icons.css'));

$bIsMenu = true;
if (!isset($bIsMenu))
    Intecai::Initialize();

$sUrlRoot = '/bitrix/admin';

$arMenu = [
    'parent_menu' => 'global_intec',
    'text' => Loc::getMessage('intec.ai.admin.menu.text'),
    'icon' => 'ai-menu-icon',
    'page_icon' => 'ai-menu-icon',
    'items_id' => 'intec_ai',
    'items' => [
        [
            'text' => Loc::getMessage('intec.ai.admin.menu.general_settings.text'),
            'icon' => 'sys_menu_icon',
            'page_icon' => 'sys_menu_icon',
            'url' => $sUrlRoot.'/ai_general_settings.php?lang='.LANGUAGE_ID,
            'items_id' => 'intec_ai_general_settings'
        ],
        [
            'text' => Loc::getMessage('intec.ai.admin.menu.texts.text'),
            'icon' => 'workflow_menu_icon',
            'page_icon' => 'workflow_menu_icon',
            'url' => $sUrlRoot.'/ai_texts.php?lang='.LANGUAGE_ID,
            'items_id' => 'intec_ai_texts'
        ],
        [
            'text' => Loc::getMessage('intec.ai.admin.menu.tasks.text'),
            'icon' => 'rating_menu_icon',
            'page_icon' => 'rating_menu_icon',
            'url' => $sUrlRoot.'/ai_tasks.php?lang='.LANGUAGE_ID.'&desc=dateCreate&ai_tasks_order=desc',
            'items_id' => 'intec_ai_tasks'
        ],
    ]
];

return $arMenu;