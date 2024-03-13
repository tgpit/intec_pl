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

$sPrefix = 'SEND_';
$sPrefixLength = StringHelper::length($sPrefix);
$sComponent = 'intec.universe:reviews';

$arTemplateParameters['SEND_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_16_SEND_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SEND_USE'] !== 'Y')
    return;

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    'intec.universe:reviews',
    $siteTemplate
))->asArray(function ($key, $template) {
    if (StringHelper::startsWith($template['NAME'], 'popup.'))
        return [
            'key' => $template['NAME'],
            'value' => $template['NAME']
        ];

    return ['skip' => true];
});

$arTemplateParameters['SEND_TEMPLATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_16_SEND_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!ArrayHelper::keyExists($arCurrentValues['SEND_TEMPLATE'], $arTemplates))
    return;

$arTemplateParameters['SEND_TITLE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_16_SEND_TITLE'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_16_SEND_TITLE_DEFAULT')
];

$arSendExcluded = [
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'IBLOCK_TYPE',
    'IBLOCK_ID',
    'ELEMENTS_COUNT',
    'FORM_USE',
    'MODE',
    'ITEMS_HIDE',
    'PROPERTIES_DISPLAY',
    'NAVIGATION_USE',
    'SORT_BY',
    'ORDER_BY',
    'CACHE_TYPE',
    'CACHE_TIME',
    'CACHE_NOTES'
];

$arCurrentValues['SEND_FORM_USE'] = 'Y';
$arCurrentValues['SEND_ITEMS_HIDE'] = 'Y';

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
    $sComponent,
    $arCurrentValues['SEND_TEMPLATE'],
    $siteTemplate,
    $arCurrentValues,
    $sPrefix,
    function ($key, &$parameter) use (&$arSendExcluded) {
        if (ArrayHelper::isIn($key, $arSendExcluded))
            return false;

        $parameter['NAME'] = Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_16_SEND').' '.$parameter['NAME'];
        $parameter['PARENT'] = 'VISUAL';

        return true;
    },
    Component::PARAMETERS_MODE_BOTH
));