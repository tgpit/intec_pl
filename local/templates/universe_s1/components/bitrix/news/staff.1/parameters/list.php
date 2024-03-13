<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$sComponent = 'bitrix:news.list';
$sPrefix = 'LIST_';
$sTemplate = 'staff.';
$sLength = StringHelper::length($sTemplate);

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arValue) use (&$sTemplate, &$sLength) {
    if (!StringHelper::startsWith($arValue['NAME'], $sTemplate))
        return ['skip' => true];

    $sName = StringHelper::cut($arValue['NAME'], $sLength);

    return [
        'key' => $sName,
        'value' => $sName
    ];
});

$arTemplateParameters['LIST_TEMPLATE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_STAFF_1_LIST_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$sTemplateSelected = ArrayHelper::getValue($arCurrentValues, 'LIST_TEMPLATE');
$sTemplateSelected = ArrayHelper::fromRange($arTemplates, $sTemplateSelected, false, false);

if (!empty($sTemplateSelected)) {
    $arCommonParameters = [
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'FORM_ASK_TEMPLATE',
        'FORM_ASK_ID',
        'FORM_ASK_FIELD',
        'FORM_ASK_CONSENT_URL',
        'PROPERTY_POSITION',
        'PROPERTY_PHONE',
        'PROPERTY_EMAIL',
        'PROPERTY_SOCIAL_VK',
        'PROPERTY_SOCIAL_FB',
        'PROPERTY_SOCIAL_INST',
        'PROPERTY_SOCIAL_TW',
        'PROPERTY_SOCIAL_SKYPE'
    ];

    if ($arCurrentValues['FORM_ASK_USE'] !== 'Y' || empty($arCurrentValues['FORM_ASK_TEMPLATE']) || empty($arCurrentValues['FORM_ASK_ID'])) {
        $arCommonParameters[] = 'FORM_ASK_USE';
        $arCommonParameters[] = 'FORM_ASK_TITLE';
        $arCommonParameters[] = 'FORM_ASK_BUTTON_TEXT';
    } else {
        $arCurrentValues['LIST_FORM_ASK_TEMPLATE'] = $arCurrentValues['FORM_ASK_TEMPLATE'];
        $arCurrentValues['LIST_FORM_ASK_ID'] = $arCurrentValues['FORM_ASK_ID'];
    }

    $arCurrentValues['LIST_PROPERTY_POSITION'] = $arCurrentValues['PROPERTY_POSITION'];
    $arCurrentValues['LIST_PROPERTY_PHONE'] = $arCurrentValues['PROPERTY_PHONE'];
    $arCurrentValues['LIST_PROPERTY_EMAIL'] = $arCurrentValues['PROPERTY_EMAIL'];
    $arCurrentValues['LIST_PROPERTY_SOCIAL_VK'] = $arCurrentValues['PROPERTY_SOCIAL_VK'];
    $arCurrentValues['LIST_PROPERTY_SOCIAL_FB'] = $arCurrentValues['PROPERTY_SOCIAL_FB'];
    $arCurrentValues['LIST_PROPERTY_SOCIAL_INST'] = $arCurrentValues['PROPERTY_SOCIAL_INST'];
    $arCurrentValues['LIST_PROPERTY_SOCIAL_TW'] = $arCurrentValues['PROPERTY_SOCIAL_TW'];
    $arCurrentValues['LIST_PROPERTY_SOCIAL_SKYPE'] = $arCurrentValues['PROPERTY_SOCIAL_SKYPE'];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate.$sTemplateSelected,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$parameter) use ($arCommonParameters) {
            if (ArrayHelper::isIn($key, $arCommonParameters))
                return false;

            $parameter['PARENT'] = 'LIST_SETTINGS';
            $parameter['NAME'] = Loc::getMessage('C_NEWS_STAFF_1_LIST').' '.$parameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset(
        $arCurrentValues['LIST_PROPERTY_POSITION'],
        $arCurrentValues['LIST_PROPERTY_PHONE'],
        $arCurrentValues['LIST_PROPERTY_EMAIL'],
        $arCurrentValues['LIST_PROPERTY_SOCIAL_VK'],
        $arCurrentValues['LIST_PROPERTY_SOCIAL_FB'],
        $arCurrentValues['LIST_PROPERTY_SOCIAL_INST'],
        $arCurrentValues['LIST_PROPERTY_SOCIAL_TW'],
        $arCurrentValues['LIST_PROPERTY_SOCIAL_SKYPE'],
        $arCurrentValues['LIST_FORM_ASK_TEMPLATE'],
        $arCurrentValues['LIST_FORM_ASK_ID']
    );
}