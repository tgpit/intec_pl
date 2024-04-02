<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'LINE_COUNT' => 4,
    'LINK_USE' => 'N',
    'POSITION_SHOW' => 'Y',
    'POSITION_PROPERTIES' => null,
    'BUTTON_SHOW' => 'N',
    'BUTTON_LINK' => null,
    'BUTTON_TEXT' => null,
    'SLIDER_USE' => 'Y',
    'SLIDER_NAV' => 'Y',
    'SLIDER_DOTS' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTO' => 'N',
    'SLIDER_AUTO_TIME' => 5000,
    'SLIDER_AUTO_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult['VISUAL'] = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 4, 5], $arParams['LINE_COUNT']),
    'LINK' => [
        'USE' => ArrayHelper::getValue($arParams, 'LINK_USE') == 'Y'
    ],
    'BUTTON' => [
        'SHOW' => $arParams['BUTTON_SHOW'] === 'Y',
        'TEXT' => !empty($arParams['BUTTON_TEXT']) ? $arParams['BUTTON_TEXT'] : Loc::getMessage('C_MAIN_STAFF_TEMPLATE_2_BUTTON_TEXT_DEFAULT'),
        'LINK' => null
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'NAV' => $arParams['SLIDER_NAV'] === 'Y',
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO'] === 'Y',
            'TIME' => !empty($arParams['SLIDER_AUTO_TIME']) ? $arParams['SLIDER_AUTO_TIME'] : 5000,
            'HOVER' => $arParams['SLIDER_AUTO_HOVER'] === 'Y'
        ]
    ],
    'ELEMENT' => [
        'POSITION' => [
            'SHOW' => $arParams['POSITION_SHOW'] === 'Y' && !empty($arParams['POSITION_PROPERTIES'])
        ]
    ]
];

$sListPage = ArrayHelper::getValue($arParams, 'LIST_PAGE_URL');

if (!empty($sListPage)) {
    $sListPage = trim($sListPage);
    $sListPage = StringHelper::replaceMacros($sListPage, [
        'SITE_DIR' => SITE_DIR,
        'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
        'TEMPLATE_PATH' => $this->GetFolder().'/'
    ]);
} else {
    $sListPage = ArrayHelper::getFirstValue($arResult['ITEMS']);
    $sListPage = $sListPage['LIST_PAGE_URL'];
}

$arResult['VISUAL']['BUTTON']['LINK'] = $sListPage;

if ($arResult['VISUAL']['ELEMENT']['POSITION']['SHOW']) {
    foreach ($arResult['ITEMS'] as &$arItem) {
        $arItem['DATA']['POSITION'] = ArrayHelper::getValue($arItem['PROPERTIES'], [$arParams['POSITION_PROPERTIES'], 'VALUE']);
    }
}