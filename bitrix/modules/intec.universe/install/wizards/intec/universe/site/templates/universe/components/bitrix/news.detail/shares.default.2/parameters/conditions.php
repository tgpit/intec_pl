<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;

$arTemplateParameters['CONDITIONS_PROPERTY_ELEMENTS'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS_PROPERTY_ELEMENTS'),
    'TYPE' => 'LIST',
    'VALUES' => $arProperties->asArray($hPropertiesElements),
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['CONDITIONS_HEADER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS_HEADER'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS_HEADER_DEFAULT')
];

$arTemplateParameters['CONDITIONS_HEADER_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS_HEADER_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_LEFT'),
        'center' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_CENTER'),
        'right' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_RIGHT')
    ],
    'DEFAULT' => 'left'
];

$arTemplateParameters['CONDITIONS_NUMBER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS_NUMBER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

$sComponent = 'intec.universe:main.advantages';
$sTemplate = 'template.';

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

$sPrefix = 'CONDITIONS_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'template.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y',
    'DEFAULT' => 39
];

$arUnusedParams = [
    'SECTIONS_MODE',
    'SECTIONS',
    'CACHE_TIME',
    'CACHE_TYPE'
];

if (!empty($sTemplate)) {
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            'CONDITIONS_',
            function ($sKey, &$arParameter) use (&$arUnusedParams) {

                if (ArrayHelper::isIn($sKey, $arUnusedParams))
                    return false;

                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS').' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_BOTH
        )
    );

    unset($arUnusedParams);
}