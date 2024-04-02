<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$sComponent = 'bitrix:news.detail';
$sPrefix = 'DETAIL_';
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

$sTemplateSelected = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplateSelected = ArrayHelper::fromRange($arTemplates, $sTemplateSelected, false, false);

$arTemplateParameters['DETAIL_TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_STAFF_1_DETAIL_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

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
        $arCurrentValues['DETAIL_FORM_ASK_TEMPLATE'] = $arCurrentValues['FORM_ASK_TEMPLATE'];
        $arCurrentValues['DETAIL_FORM_ASK_ID'] = $arCurrentValues['FORM_ASK_ID'];
    }

    $arCurrentValues['DETAIL_PROPERTY_POSITION'] = $arCurrentValues['PROPERTY_POSITION'];
    $arCurrentValues['DETAIL_PROPERTY_PHONE'] = $arCurrentValues['PROPERTY_PHONE'];
    $arCurrentValues['DETAIL_PROPERTY_EMAIL'] = $arCurrentValues['PROPERTY_EMAIL'];
    $arCurrentValues['DETAIL_PROPERTY_SOCIAL_VK'] = $arCurrentValues['PROPERTY_SOCIAL_VK'];
    $arCurrentValues['DETAIL_PROPERTY_SOCIAL_FB'] = $arCurrentValues['PROPERTY_SOCIAL_FB'];
    $arCurrentValues['DETAIL_PROPERTY_SOCIAL_INST'] = $arCurrentValues['PROPERTY_SOCIAL_INST'];
    $arCurrentValues['DETAIL_PROPERTY_SOCIAL_TW'] = $arCurrentValues['PROPERTY_SOCIAL_TW'];
    $arCurrentValues['DETAIL_PROPERTY_SOCIAL_SKYPE'] = $arCurrentValues['PROPERTY_SOCIAL_SKYPE'];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate.$sTemplateSelected,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$parameter) use (&$arCommonParameters) {
            if (ArrayHelper::isIn($key, $arCommonParameters))
                return false;

            $parameter['PARENT'] = 'DETAIL_SETTINGS';
            $parameter['NAME'] = Loc::getMessage('C_NEWS_STAFF_1_DETAIL').' '.$parameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset(
        $arCommonParameters,
        $arCurrentValues['DETAIL_PROPERTY_POSITION'],
        $arCurrentValues['DETAIL_PROPERTY_PHONE'],
        $arCurrentValues['DETAIL_PROPERTY_EMAIL'],
        $arCurrentValues['DETAIL_PROPERTY_SOCIAL_VK'],
        $arCurrentValues['DETAIL_PROPERTY_SOCIAL_FB'],
        $arCurrentValues['DETAIL_PROPERTY_SOCIAL_INST'],
        $arCurrentValues['DETAIL_PROPERTY_SOCIAL_TW'],
        $arCurrentValues['DETAIL_PROPERTY_SOCIAL_SKYPE'],
        $arCurrentValues['DETAIL_FORM_ASK_TEMPLATE'],
        $arCurrentValues['DETAIL_FORM_ASK_ID']
    );
}

unset($sComponent, $sPrefix, $sTemplate, $sLength, $arTemplates, $sTemplateSelected);