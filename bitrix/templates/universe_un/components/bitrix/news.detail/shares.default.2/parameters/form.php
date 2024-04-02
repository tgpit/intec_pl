<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;

$sComponent = 'intec.universe:main.widget';
$sTemplate = 'form.';

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

$sPrefix = 'FORM_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'form.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y',
    'DEFAULT' => 7
];

if (!empty($sTemplate)) {
    $arUnusedParameters = [
        'CACHE_TIME',
        'CACHE_TYPE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            'FORM_',
            function ($sKey, &$arParameter) use (&$arUnusedParameters) {
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_FORM').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, $arUnusedParameters))
                    return false;

                return true;
            },
            Component::PARAMETERS_MODE_BOTH
        )
    );

    unset ($arUsedParameters);
}