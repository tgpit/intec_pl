<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'CONSENT_SHOW' => 'Y',
    'CONSENT_URL' => '#SITE_DIR#company/consent/',
    'PROPERTY_ARTICLE' => '',
    'OFFERS_PROPERTY_ARTICLE' => ''
], $arParams);

if ($arParams['SETTINGS_USE'])
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'ARTICLE' => [
        'SHOW' => false,
        'VALUE' => null
    ]
]);

$sArticle = ArrayHelper::getValue($arResult['PRODUCT']['PROPERTIES'][$arParams['PROPERTY_ARTICLE']], 'VALUE');

if (empty($sArticle))
    $sArticle = ArrayHelper::getValue($arResult['PRODUCT']['PROPERTIES'][$arParams['OFFERS_PROPERTY_ARTICLE']], 'VALUE');

if (!empty($sArticle)) {
    $arVisual['ARTICLE']['SHOW'] = true;
    $arVisual['ARTICLE']['VALUE'] = $sArticle;
}

unset($sArticle);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$arResult['CONSENT'] = [
    'SHOW' => $arParams['CONSENT_SHOW'] === 'Y',
    'URL' => $arParams['CONSENT_URL']
];

if (!empty($arResult['CONSENT']['URL'])) {
    $arResult['CONSENT']['URL'] = StringHelper::replaceMacros(
        $arResult['CONSENT']['URL'],
        $arMacros
    );
} else {
    $arResult['CONSENT']['SHOW'] = false;
}

$arResult['MESSAGES'] = [
    'TITLE' => $arParams['MESSAGES_TITLE'],
    'ORDER' => $arParams['MESSAGES_ORDER'],
    'BUTTON' => $arParams['MESSAGES_BUTTON']
];

if (empty($arResult['MESSAGES']['TITLE']))
    $arResult['MESSAGES']['TITLE'] = Loc::getMessage('C_SALE_ORDER_FAST_DEFAULT_MESSAGES_TITLE');

if (empty($arResult['MESSAGES']['ORDER']))
    $arResult['MESSAGES']['ORDER'] = Loc::getMessage('C_SALE_ORDER_FAST_DEFAULT_MESSAGES_ORDER');

if (empty($arResult['MESSAGES']['BUTTON']))
    $arResult['MESSAGES']['BUTTON'] = Loc::getMessage('C_SALE_ORDER_FAST_DEFAULT_MESSAGES_BUTTON');