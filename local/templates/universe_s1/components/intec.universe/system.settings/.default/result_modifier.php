<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\constructor\models\Build;
use intec\core\helpers\FileHelper;
use intec\template\Properties;

/**
 * @var CBitrixComponentTemplate $this
 */

global $USER;

/** @var Build $oBuild */
$oBuild = $arResult['BUILD'];

$arResult['LAZYLOAD'] = [
    'USE' => $arResult['PROPERTIES']['template-images-lazyload-use']['value'],
    'STUB' => Properties::get('template-images-lazyload-stub')
];

$arResult['SECTIONS'] = [
    'variants' => [
        'name' => Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_SECTIONS_VARIANTS_NAME'),
        'description' => Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_SECTIONS_VARIANTS_DESCRIPTION'),
        'form' => false,
        'icon' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M26.6667 28H5.33341C3.86008 28 2.66675 26.8067 2.66675 25.3333V6.66667C2.66675 5.19333 3.86008 4 5.33341 4H26.6667C28.1401 4 29.3334 5.19333 29.3334 6.66667V25.3333C29.3334 26.8067 28.1401 28 26.6667 28Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2.66675 10.6667H29.3334" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.37214 7.32001C6.36814 7.32001 6.36548 7.32267 6.36548 7.32667C6.36548 7.33067 6.36814 7.33334 6.37214 7.33334C6.37614 7.33334 6.37881 7.33067 6.37881 7.32667C6.37881 7.32267 6.37614 7.32001 6.37214 7.32001Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.76277 7.32001C9.75877 7.32001 9.7561 7.32267 9.7561 7.32667C9.7561 7.33067 9.7601 7.33334 9.76277 7.33334C9.76677 7.33334 9.76944 7.33067 9.76944 7.32667C9.76944 7.32267 9.76677 7.32001 9.76277 7.32001Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.148 7.32001C13.144 7.32001 13.1414 7.32267 13.1414 7.32667C13.1414 7.33067 13.144 7.33334 13.148 7.33334C13.152 7.33334 13.1547 7.33067 13.1547 7.32667C13.1547 7.32267 13.152 7.32001 13.148 7.32001Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.3333 18.6667H9.33333C8.59733 18.6667 8 18.0693 8 17.3333V16C8 15.264 8.59733 14.6667 9.33333 14.6667H13.3333C14.0693 14.6667 14.6667 15.264 14.6667 16V17.3333C14.6667 18.0693 14.0693 18.6667 13.3333 18.6667Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18.6667 14.6667H24.0001" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18.6667 18.6667H22.6667" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M24 28V24C24 23.264 23.4027 22.6667 22.6667 22.6667H9.33333C8.59733 22.6667 8 23.264 8 24V28" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>'
    ],
    'properties' => [
        'name' => Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_SECTIONS_PROPERTIES_NAME'),
        'description' => Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_SECTIONS_PROPERTIES_DESCRIPTION'),
        'form' => true,
        'icon' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.5455 13.4544C19.9514 14.8603 19.9514 17.1397 18.5455 18.5456C17.1396 19.9514 14.8602 19.9514 13.4543 18.5456C12.0484 17.1397 12.0484 14.8603 13.4543 13.4544C14.8602 12.0485 17.1396 12.0485 18.5455 13.4544" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.99999 16C6.99999 16.396 7.03599 16.792 7.08399 17.176L4.96666 18.832C4.49732 19.2 4.36932 19.8573 4.66799 20.3733L6.55066 23.6307C6.84799 24.1467 7.47999 24.364 8.03332 24.1427L9.92932 23.3813C10.304 23.2307 10.7213 23.2907 11.0573 23.5133C11.3507 23.708 11.6547 23.8867 11.9693 24.0467C12.3293 24.2293 12.5907 24.556 12.648 24.956L12.9373 26.9733C13.0213 27.5627 13.5267 28 14.1213 28H17.8773C18.472 28 18.9773 27.5627 19.0613 26.9733L19.3507 24.9573C19.408 24.5573 19.672 24.228 20.0333 24.0467C20.3467 23.8893 20.6493 23.712 20.9413 23.5187C21.28 23.2947 21.6987 23.2307 22.0747 23.3827L23.9667 24.1427C24.5187 24.364 25.1507 24.1467 25.4493 23.6307L27.332 20.3733C27.6307 19.8573 27.5027 19.1987 27.0333 18.832L24.916 17.176C24.964 16.792 25 16.396 25 16C25 15.604 24.964 15.208 24.916 14.824L27.0333 13.168C27.5027 12.8 27.6307 12.1427 27.332 11.6267L25.4493 8.36933C25.152 7.85333 24.52 7.636 23.9667 7.85733L22.0747 8.61733C21.6987 8.768 21.28 8.70533 20.9413 8.48133C20.6493 8.288 20.3467 8.11067 20.0333 7.95333C19.672 7.772 19.408 7.44267 19.3507 7.04267L19.0627 5.02667C18.9787 4.43733 18.4733 4 17.8787 4H14.1227C13.528 4 13.0227 4.43733 12.9387 5.02667L12.648 7.04533C12.5907 7.444 12.328 7.772 11.9693 7.95467C11.6547 8.11467 11.3507 8.29467 11.0573 8.488C10.72 8.70933 10.3027 8.76933 9.92799 8.61867L8.03332 7.85733C7.47999 7.636 6.84799 7.85333 6.55066 8.36933L4.66799 11.6267C4.36932 12.1427 4.49732 12.8013 4.96666 13.168L7.08399 14.824C7.03599 15.208 6.99999 15.604 6.99999 16V16Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
        'categories' => $oBuild->getMetaValue('properties-categories')
    ]
];

$arResult['BANNERS'] = [];

if ($USER->IsAdmin() && !empty($arResult['TEMPLATES']))
    $arResult['SECTIONS']['properties']['categories']['templates'] = [
        'name' => Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_SECTIONS_PROPERTIES_CATEGORIES_TEMPLATES')
    ];

$oSession = Core::$app->getSession();
$sSessionSectionKey = 'IntecUniverseSystemSettingsSection';

$arResult['SECTION'] = null;

if (!empty($arResult['ACTION'])) {
    $arResult['SECTION'] = Core::$app->request->post('section');
    $oSession->set($sSessionSectionKey, $arResult['SECTION']);
} else {
    $arResult['SECTION'] = $oSession->get($sSessionSectionKey);
    $oSession->remove($sSessionSectionKey);
}

include(__DIR__.'/modifiers/properties.php');

if (FileHelper::isFile(__DIR__.'/style.custom.css'))
    $this->addExternalCss($this->GetFolder().'/style.custom.css');

if (FileHelper::isFile(__DIR__.'/modifiers/custom/start.php'))
    include(__DIR__.'/modifiers/custom/start.php');

if (!empty($arResult['SECTIONS']['variants']))
    if (empty($arResult['VARIANTS']))
        unset($arResult['SECTIONS']['variants']);

if (!empty($arResult['SECTIONS']['properties'])) {
    $arSection = &$arResult['SECTIONS']['properties'];

    foreach ($arSection['categories'] as $sKey => &$arCategory) {
        $arCategory['code'] = $sKey;
        $arCategory['properties'] = [];
    }

    unset($arCategory);

    foreach ($arResult['PROPERTIES'] as $sKey => &$arProperty) {
        if (isset($arProperty['visible']) && !$arProperty['visible'])
            continue;

        $arCategory = ArrayHelper::getValue($arProperty, 'category');

        if (
            empty($arCategory) ||
            empty($arSection['categories'][$arCategory])
        ) continue;

        $arCategory = &$arSection['categories'][$arCategory];
        $arCategory['properties'][$sKey] = &$arProperty;

        unset($arCategory);
    }

    $arCategories = [];

    foreach ($arSection['categories'] as $sKey => $arCategory)
        if (!empty($arCategory['properties']) || $sKey === 'templates')
            $arCategories[$sKey] = $arCategory;

    $arSection['categories'] = $arCategories;

    unset($arCategories);

    if (empty($arSection['categories']))
        unset($arResult['SECTIONS']['properties']);

    unset($arProperty);
    unset($arSection);
}

foreach ($arResult['SECTIONS'] as $sKey => &$arSection)
    $arSection['code'] = $sKey;

unset($arSection);

if (FileHelper::isFile(__DIR__.'/modifiers/custom/end.php'))
    include(__DIR__.'/modifiers/custom/end.php');

foreach ($arResult['SECTIONS'] as &$arSection) {
    if (!isset($arSection['form']))
        $arSection['form'] = true;
}

unset($arSection);