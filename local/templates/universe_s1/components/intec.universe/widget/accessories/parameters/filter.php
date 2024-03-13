<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$sTemplate = null;

$sTemplate = 'vertical.';

if (empty($sTemplate))
    return;

$sComponent = 'bitrix:catalog.smart.filter';
$sPrefix = 'ACCESSORIES_FILTER_';
$iLength = StringHelper::length($sTemplate);

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


$arTemplateParameters['FILTER_TEMPLATE'] = [
    'PARENT' => 'FILTER_SETTINGS',
    'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_FILTER_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FILTER_TEMPLATE']))
    $sTemplate = $arCurrentValues['FILTER_TEMPLATE'];

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arFilterUseParameters = [
        'TEMPLATE',
        'PRICE_CODE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arFilterUseParameters) {
            if (ArrayHelper::isIn($key, $arFilterUseParameters)) {
                $arParameter['NAME'] = Loc::getMessage('C_WIDGET_ACCESSORIES_FILTER').' '.$arParameter['NAME'];
                $arParameter['PARENT'] = 'FILTER_SETTINGS';

                return true;
            }
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($arFilterCommonParameters);
}

unset($sTemplate, $sComponent, $sPrefix, $iLength, $arTemplates);