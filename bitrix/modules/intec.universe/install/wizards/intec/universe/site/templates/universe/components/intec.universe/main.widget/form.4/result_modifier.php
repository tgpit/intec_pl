<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return false;

$arParams = ArrayHelper::merge([
    'FORM_ID' => null,
    'FORM_TEMPLATE' => '.default',
    'FORM_TITLE' => null,
    'SETTINGS_USE' => 'N',
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => null,
    'WIDE' => 'N',
    'BORDER_STYLE' => 'squared',
    'TITLE' => null,
    'DESCRIPTION' => null,
    'BUTTON_TEXT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult['VISUAL'] = [
    'WIDE' => $arParams['WIDE'] === 'Y',
    'BORDERS' => $arParams['WIDE'] !== 'Y' ? ArrayHelper::fromRange(['squared', 'rounded'], $arParams['BORDER_STYLE']) : 'squared'
];

if (Loader::includeModule('form'))
    $arResult['FORM'] = Arrays::fromDBResult(CForm::GetByID($arParams['FORM_ID']))->getFirst();
else if (Loader::includeModule('intec.startshop'))
    $arResult['FORM'] = Arrays::fromDBResult(CStartShopForm::GetByID($arParams['FORM_ID']))->getFirst();
else
    $arResult['FORM'] = [];

$arResult['DATA'] = [
    'TITLE' => null,
    'DESCRIPTION' => null,
    'BUTTON' => [
        'TEXT' => null
    ],
    'FORM' => [
        'TEMPLATE' => !empty($arParams['FORM_TEMPLATE']) ? $arParams['FORM_TEMPLATE'] : '.default',
        'TITLE' => null,
        'CONSENT' => [
            'SHOW' => false,
            'URL' => $arParams['CONSENT_URL']
        ]
    ]
];

if (!empty($arParams['TITLE']))
    $arResult['DATA']['TITLE'] = Html::decode($arParams['TITLE']);
else if (!empty($arResult['FORM']['NAME']))
    $arResult['DATA']['TITLE'] = $arResult['FORM']['NAME'];

if (!empty($arParams['DESCRIPTION']))
    $arResult['DATA']['DESCRIPTION'] = Html::decode($arParams['DESCRIPTION']);
else if (!empty($arResult['FORM']['DESCRIPTION']))
    $arResult['DATA']['DESCRIPTION'] = $arResult['FORM']['DESCRIPTION'];

if (!empty($arParams['BUTTON_TEXT']))
    $arResult['DATA']['BUTTON']['TEXT'] = $arParams['BUTTON_TEXT'];
else if (!empty($arResult['FORM']['NAME']))
    $arResult['DATA']['BUTTON']['TEXT'] = $arResult['FORM']['NAME'];

if (!empty($arParams['FORM_TITLE']))
    $arResult['DATA']['FORM']['TITLE'] = Html::decode($arParams['FORM_TITLE']);
else if (!empty($arResult['FORM']['NAME']))
    $arResult['DATA']['BUTTON']['TEXT'] = $arResult['FORM']['NAME'];

if (!empty($arResult['DATA']['FORM']['CONSENT']['URL'])) {
    $arResult['DATA']['FORM']['CONSENT']['URL'] = StringHelper::replaceMacros($arResult['DATA']['FORM']['CONSENT']['URL'], [
        'SITE_DIR' => SITE_DIR
    ]);
    $arResult['DATA']['FORM']['CONSENT']['SHOW'] = $arParams['CONSENT_SHOW'] === 'Y';
}