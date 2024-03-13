<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php

global $APPLICATION;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use intec\core\collections\Arrays;

Loc::loadMessages(__FILE__);

Loader::includeModule('intec.core');

$getTasksId = function ()
{
    $arParams = [
        'main' => 'P',
        'catalog' => 'D',
        'clouds' => 'D',
        'fileman' => 'F',
        'landing' => 'R',
        'security' => 'D',
        'seo' => 'D',
    ];

    $arTasks = Arrays::fromDBResult(CTask::GetList([],
        [
            'BINDING' => 'module',
            'SYS' => 'Y'
        ]
    ))->asArray();

    foreach ($arTasks as $arTask) {
        $arTasksGroup[$arTask['MODULE_ID']][$arTask['LETTER']] = $arTask['ID'];
    }

    foreach ($arParams as $keyParam => $valueParam) {
        if (isset($arTasksGroup[$keyParam][$valueParam]))
            $arParams[$keyParam] = $arTasksGroup[$keyParam][$valueParam];
    }

    return $arParams;
};

$arGroups = CGroup::GetList(
    $by = '',
    $order = '',
    ['STRING_ID' => 'site_owners_group']
)->Fetch();

if (!$arGroups) {

    $group = new CGroup;
    $arFields = [
        'ACTIVE'    => 'Y',
        'C_SORT'    => 100,
        'STRING_ID' => 'site_owners_group',
        'NAME'      => Loc::getMessage('wizard.services.main.group.name')
    ];
    $groupId = $group->Add($arFields);

    if (!$groupId)
        return;

    $arModules = [
        'abtest' => 'D',
        'advertising' => 'D',
        'b24connector' => 'W',
        'bitrix_eshop' => 'D',
        'bitrix_sitecommunity' => 'D',
        'bitrix_sitecorporate' => 'D',
        'bitrix_siteinfoportal' => 'D',
        'bitrix_sitepersonal' => 'D',
        'bizproc' => 'D',
        'bizprocdesigner' => 'D',
        'blog' => 'D',
        'calendar' => 'D',
        'conversion' => 'D',
        'currency' => 'W',
        'form' => 'W',
        'forum' => 'D',
        'im' => 'D',
        'learning' => 'D',
        'messageservice' => 'D',
        'mobileapp' => 'R',
        'perfmon' => 'R',
        'pull' => 'D',
        'sale' => 'W',
        'sender' => 'D',
        'socialnetwork' => 'D',
        'statistic' => 'R',
        'storeassist' => 'D',
        'subscribe' => 'D',
        'support' => 'R',
        'translate' => 'D',
        'vote' => 'D',
        'wiki' => 'R',
        'workflow' => 'U'
    ];

    foreach ($arModules as $moduleId=>$moduleRight) {
        $APPLICATION->SetGroupRight($moduleId, $groupId, $moduleRight, false);
    }

    CGroup::SetTasks($groupId, $getTasksId());
}
