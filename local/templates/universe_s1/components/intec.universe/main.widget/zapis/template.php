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
        'WEB_FORM_TITLE_SHOW' => $arParams['WEB_FORM_TITLE_SHOW'],
        'WEB_FORM_DESCRIPTION_SHOW' => $arParams['WEB_FORM_DESCRIPTION_SHOW'],
        'WEB_FORM_BACKGROUND' => $arParams['WEB_FORM_BACKGROUND'],
        'WEB_FORM_BACKGROUND_CUSTOM' => $arParams['WEB_FORM_BACKGROUND_CUSTOM'],
        'WEB_FORM_BACKGROUND_OPACITY' => $arParams['WEB_FORM_BACKGROUND_OPACITY'],
        'WEB_FORM_TEXT_COLOR' => $arParams['WEB_FORM_TEXT_COLOR'],
        'WEB_FORM_POSITION' => $arParams['WEB_FORM_POSITION'],
        'WEB_FORM_ADDITIONAL_PICTURE_SHOW' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_SHOW'],
        'WEB_FORM_ADDITIONAL_PICTURE' => $arParams['WEB_FORM_ADDITIONAL_PICTURE'],
        'WEB_FORM_ADDITIONAL_PICTURE_HORIZONTAL' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_HORIZONTAL'],
        'WEB_FORM_ADDITIONAL_PICTURE_VERTICAL' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_VERTICAL'],
        'WEB_FORM_ADDITIONAL_PICTURE_SIZE' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_SIZE'],
        'BLOCK_BACKGROUND' => $arParams['BLOCK_BACKGROUND'],
        'BLOCK_BACKGROUND_PARALLAX_USE' => $arParams['BLOCK_BACKGROUND_PARALLAX_USE'],
        'BLOCK_BACKGROUND_PARALLAX_RATIO' => $arParams['BLOCK_BACKGROUND_PARALLAX_RATIO'],
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
        'FORM_ID' => $arParams['WEB_FORM_ID'],
        'WEB_FORM_TITLE_SHOW' => $arParams['WEB_FORM_TITLE_SHOW'],
        'WEB_FORM_DESCRIPTION_SHOW' => 'N',
        'WEB_FORM_BACKGROUND' => $arParams['WEB_FORM_BACKGROUND'],
        'WEB_FORM_BACKGROUND_CUSTOM' => $arParams['WEB_FORM_BACKGROUND_CUSTOM'],
        'WEB_FORM_BACKGROUND_OPACITY' => $arParams['WEB_FORM_BACKGROUND_OPACITY'],
        'WEB_FORM_TEXT_COLOR' => $arParams['WEB_FORM_TEXT_COLOR'],
        'WEB_FORM_POSITION' => $arParams['WEB_FORM_POSITION'],
        'WEB_FORM_ADDITIONAL_PICTURE_SHOW' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_SHOW'],
        'WEB_FORM_ADDITIONAL_PICTURE' => $arParams['WEB_FORM_ADDITIONAL_PICTURE'],
        'WEB_FORM_ADDITIONAL_PICTURE_HORIZONTAL' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_HORIZONTAL'],
        'WEB_FORM_ADDITIONAL_PICTURE_VERTICAL' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_VERTICAL'],
        'WEB_FORM_ADDITIONAL_PICTURE_SIZE' => $arParams['WEB_FORM_ADDITIONAL_PICTURE_SIZE'],
        'BLOCK_BACKGROUND' => $arParams['BLOCK_BACKGROUND'],
        'BLOCK_BACKGROUND_PARALLAX_USE' => $arParams['BLOCK_BACKGROUND_PARALLAX_USE'],
        'BLOCK_BACKGROUND_PARALLAX_RATIO' => $arParams['BLOCK_BACKGROUND_PARALLAX_RATIO'],
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