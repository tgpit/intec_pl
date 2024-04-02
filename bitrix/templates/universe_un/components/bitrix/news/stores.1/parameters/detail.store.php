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


$sComponent = 'bitrix:catalog.store.detail';
$sPrefix = 'DETAIL_STORE_';
$sTemplate = 'store.';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
    if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
        return ['skip' => true];

    $sName = StringHelper::cut(
        $arTemplate['NAME'],
        StringHelper::length($sTemplate)
    );

    return [
        'key' => $sName,
        'value' => $sName
    ];
});

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_DETAIL_STORE_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'REFRESH' => 'Y'
];

$arParametersCommon = [
    'MAP_VENDOR',
    'MAP_SHOW',
    'SOCIAL_SERVICES_VK',
    'SOCIAL_SERVICES_FACEBOOK',
    'SOCIAL_SERVICES_INSTAGRAM',
    'SOCIAL_SERVICES_TWITTER',
    'SOCIAL_SERVICES_SKYPE',
    'SOCIAL_SERVICES_YOUTUBE',
    'SOCIAL_SERVICES_OK',
    'FORM_ID',
    'FORM_TEMPLATE',
    'FORM_TITLE',
    'CONSENT',
];

$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'store.'.$sTemplate;

if(!empty($sTemplate)) {
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) use (&$arParametersCommon) {
            if (ArrayHelper::isIn($sKey, $arParametersCommon))
                return false;

            $arParameter['PARENT'] = 'DETAIL_SETTINGS';
            $arParameter['NAME'] = Loc::getMessage('C_NEWS_STORES_1_DETAIL_STORE') . '. ' . $arParameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));
}
unset($sComponent, $sPrefix, $sTemplate, $arParametersCommon, $arTemplates);