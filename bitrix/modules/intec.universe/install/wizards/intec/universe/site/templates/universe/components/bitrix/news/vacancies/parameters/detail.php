<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\bitrix\Component;
use intec\core\helpers\StringHelper;

$sPrefix = 'DETAIL_';
$sComponent = 'bitrix:news.detail';
$sTemplate = 'vacancies.';

//получаем все шаблоны в переменную $arTemplates
$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) { //формируем массив из обьекта с помощью asArray
    //если шаблон не начинается на $sTemplate, пропускаем его
    if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
        return ['skip' => true];

    $sName = $arTemplate['NAME'];

    return [
        'key' => $sName,
        'value' => $sName
    ];
});

//создаем параметр, в значения пишем переменную с шаблонами news.detail
$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_VACANCIES_DETAIL_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'REFRESH' => 'Y'
];
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');

$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);


if(!empty($sTemplate)){
    //получаем параметры из компонента news.detail
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) use (&$sLevel, &$arParametersCommon) {
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';
            $arParameter['NAME'] = Loc::getMessage('C_NEWS_BRANDS_LIST').$arParameter['NAME'];
            //исключаем параметры, которые дублируются
            if (ArrayHelper::isIn($sKey, [
                'PROPERTY_CITY',
                'PROPERTY_SKILL',
                'PROPERTY_TYPE_EMPLOYMENT',
                'PROPERTY_SALARY'
            ]))
                return false;
            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

}
unset($sComponent, $sPrefix, $sTemplate);