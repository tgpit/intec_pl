<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;

$arTemplateParameters['ICONS_LINK'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_ICONS_LINK'),
    'TYPE' => 'LIST',
    'VALUES' => $arProperties->asArray($hPropertiesElements),
    'ADDITIONAL_VALUES' => 'Y',
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

$sPrefix = 'ICONS_';
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
    'DEFAULT' => 30
];

$arUnusedParams = [
    'SECTIONS_MODE',
    'SECTIONS',
    'LAZYLOAD_USE',
    'CACHE_TIME',
    'CACHE_TYPE',
    'SETTINGS_USE'
];

if (!empty($sTemplate)) {
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            'ICONS_',
            function ($sKey, &$arParameter) use (&$arUnusedParams) {

                if (ArrayHelper::isIn($sKey, $arUnusedParams))
                    return false;

                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_ICONS').' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_BOTH
        )
    );

    unset($arUnusedParams);
}