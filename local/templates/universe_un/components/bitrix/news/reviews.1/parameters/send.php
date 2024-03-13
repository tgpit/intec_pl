<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$sComponent = 'intec.universe:reviews';
$sPrefix = 'SEND_';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arTemplate) {
    return [
        'key' => $arTemplate['NAME'],
        'value' => $arTemplate['NAME']
    ];
});

$arTemplateParameters['SEND_TEMPLATE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_SEND_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!ArrayHelper::keyExists($arCurrentValues['SEND_TEMPLATE'], $arTemplates))
    return;

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
    function ($key, &$arParameter) use (&$arSendExcluded) {
        if (ArrayHelper::isIn($key, $arSendExcluded))
            return false;

        $arParameter['NAME'] = Loc::getMessage('C_NEWS_REVIEWS_1_SEND').' '.$arParameter['NAME'];
        $arParameter['PARENT'] = 'BASE';

        return true;
    },
    Component::PARAMETERS_MODE_BOTH
));

unset(
    $sComponent,
    $sPrefix,
    $arTemplates,
    $arSendExcluded,
    $arCurrentValues['SEND_FORM_USE'],
    $arCurrentValues['SEND_ITEMS_HIDE']
);