<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

$sComponent = null;
$arComponentsParameters = [];
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (Loader::includeModule('form')) {
    $sComponent = 'bitrix:form.result.new';
    $arComponentsParameters = [
        'WEB_FORM_ID' => $arParams['WEB_FORM_ID'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'WEB_FORM_CONSENT_SHOW' => $arParams['WEB_FORM_CONSENT_SHOW'],
        'WEB_FORM_CONSENT_LINK' => $arParams['WEB_FORM_CONSENT_LINK'],
        'WEB_FORM_TITLE_SHOW' => $arParams['WEB_FORM_TITLE_SHOW'],
        'WEB_FORM_TITLE_POSITION' => $arParams['WEB_FORM_TITLE_POSITION'],
        'WEB_FORM_DESCRIPTION_SHOW' => $arParams['WEB_FORM_DESCRIPTION_SHOW'],
        'WEB_FORM_DESCRIPTION_POSITION' => $arParams['WEB_FORM_DESCRIPTION_POSITION'],
        'WEB_FORM_THEME' => $arParams['WEB_FORM_THEME'],
        'WEB_FORM_BUTTON_POSITION' => $arParams['WEB_FORM_BUTTON_POSITION'],
        'WEB_FORM_BACKGROUND_USE' => $arParams['WEB_FORM_BACKGROUND_USE'],
        'WEB_FORM_BACKGROUND_COLOR' => $arParams['WEB_FORM_BACKGROUND_COLOR'],
        'WEB_FORM_BACKGROUND_COLOR_CUSTOM' => $arParams['WEB_FORM_BACKGROUND_COLOR_CUSTOM'],
        'WEB_FORM_BACKGROUND_OPACITY' => $arParams['WEB_FORM_BACKGROUND_OPACITY'],
        'LIST_URL' => '',
        'EDIT_URL' => '',
        'AJAX_MODE' => 'Y'
    ];
} else if (Loader::includeModule('intec.startshop')) {
    $sComponent = 'intec:startshop.forms.result.new';
    $arComponentsParameters = [
        'FORM_ID' => $arParams['WEB_FORM_ID'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'WEB_FORM_CONSENT_SHOW' => $arParams['WEB_FORM_CONSENT_SHOW'],
        'WEB_FORM_CONSENT_LINK' => $arParams['WEB_FORM_CONSENT_LINK'],
        'WEB_FORM_TITLE_SHOW' => $arParams['WEB_FORM_TITLE_SHOW'],
        'WEB_FORM_TITLE_POSITION' => $arParams['WEB_FORM_TITLE_POSITION'],
        'WEB_FORM_DESCRIPTION_SHOW' => 'N',
        'WEB_FORM_DESCRIPTION_POSITION' => $arParams['WEB_FORM_DESCRIPTION_POSITION'],
        'WEB_FORM_THEME' => $arParams['WEB_FORM_THEME'],
        'WEB_FORM_BUTTON_POSITION' => $arParams['WEB_FORM_BUTTON_POSITION'],
        'WEB_FORM_BACKGROUND_USE' => $arParams['WEB_FORM_BACKGROUND_USE'],
        'WEB_FORM_BACKGROUND_COLOR' => $arParams['WEB_FORM_BACKGROUND_COLOR'],
        'WEB_FORM_BACKGROUND_COLOR_CUSTOM' => $arParams['WEB_FORM_BACKGROUND_COLOR_CUSTOM'],
        'WEB_FORM_BACKGROUND_OPACITY' => $arParams['WEB_FORM_BACKGROUND_OPACITY'],
        'AJAX_MODE' => 'Y'
    ];
} else
    return;

$APPLICATION->IncludeComponent(
    $sComponent,
    '.default',
    $arComponentsParameters,
    $component
);