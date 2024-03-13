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

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sComponent = null;

$arComponentsParameters = [];

if (Loader::includeModule('form')) {
    $sComponent = 'bitrix:form.result.new';
    $arComponentsParameters = [
        'WEB_FORM_ID' => $arParams['FORM_ID'],
        'FORM_TITLE_SHOW' => $arParams['FORM_TITLE_SHOW'],
        'FORM_DESCRIPTION_SHOW' => $arParams['FORM_DESCRIPTION_SHOW'],
        'FORM_POSITION' => $arParams['FORM_POSITION'],
        'FORM_ADDITIONAL_PICTURE_SHOW' => $arParams['FORM_ADDITIONAL_PICTURE_SHOW'],
        'FORM_ADDITIONAL_PICTURE_PATH' => $arParams['FORM_ADDITIONAL_PICTURE_PATH'],
        'FORM_ADDITIONAL_PICTURE_VERTICAL' => $arParams['FORM_ADDITIONAL_PICTURE_VERTICAL'],
        'FORM_ADDITIONAL_PICTURE_SIZE' => $arParams['FORM_ADDITIONAL_PICTURE_SIZE'],
        'FORM_BACKGROUND_PATH' => $arParams['FORM_BACKGROUND_PATH'],
        'FORM_BACKGROUND_PARALLAX_USE' => $arParams['FORM_BACKGROUND_PARALLAX_USE'],
        'FORM_BACKGROUND_PARALLAX_RATIO' => $arParams['FORM_BACKGROUND_PARALLAX_RATIO'],
        'LIST_URL' => '',
        'EDIT_URL' => '',
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'CONSENT_SHOW' => $arParams['CONSENT_SHOW'],
        'CONSENT_URL' => $arParams['CONSENT_URL'],
        'AJAX_MODE' => 'Y'

    ];
} else if (Loader::includeModule('intec.startshop')) {
    $sComponent = 'intec:startshop.forms.result.new';
    $arComponentsParameters = [
        'FORM_ID' => $arParams['FORM_ID'],
        'FORM_TITLE_SHOW' => $arParams['FORM_TITLE_SHOW'],
        'FORM_DESCRIPTION_SHOW' => 'N',
        'FORM_POSITION' => $arParams['FORM_POSITION'],
        'FORM_ADDITIONAL_PICTURE_SHOW' => $arParams['FORM_ADDITIONAL_PICTURE_SHOW'],
        'FORM_ADDITIONAL_PICTURE_PATH' => $arParams['FORM_ADDITIONAL_PICTURE_PATH'],
        'FORM_ADDITIONAL_PICTURE_VERTICAL' => $arParams['FORM_ADDITIONAL_PICTURE_VERTICAL'],
        'FORM_ADDITIONAL_PICTURE_SIZE' => $arParams['FORM_ADDITIONAL_PICTURE_SIZE'],
        'FORM_BACKGROUND_PATH' => $arParams['FORM_BACKGROUND_PATH'],
        'FORM_BACKGROUND_PARALLAX_USE' => $arParams['FORM_BACKGROUND_PARALLAX_USE'],
        'FORM_BACKGROUND_PARALLAX_RATIO' => $arParams['FORM_BACKGROUND_PARALLAX_RATIO'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'CONSENT_SHOW' => $arParams['CONSENT_SHOW'],
        'CONSENT_URL' => $arParams['CONSENT_URL'],
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