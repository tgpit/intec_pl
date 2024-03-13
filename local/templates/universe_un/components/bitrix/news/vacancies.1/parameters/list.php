<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\bitrix\Component;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$sPrefix = 'LIST_';
$sComponent = 'bitrix:news.list';
$sTemplate = 'vacancies.';

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
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => GetMessage('C_NEWS_VACANCIES_1_LIST_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'REFRESH' => 'Y'
];

$arTemplateParameters[$sPrefix.'MENU_SHOW'] = [
    'PARENT' => 'SECTIONS_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_'.$sPrefix.'MENU_SHOW'),
    'TYPE' => 'CHECKBOX'
];

$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'vacancies.'.$sTemplate;

if(!empty($sTemplate)){
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) use (&$arParametersCommon) {
            if (ArrayHelper::isIn($sKey, $arParametersCommon))
                return false;

            $arParameter['PARENT'] = 'LIST_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));
}

unset($sComponent, $sPrefix, $sTemplate);