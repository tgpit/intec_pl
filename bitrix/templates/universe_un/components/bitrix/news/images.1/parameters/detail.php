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
$sTemplate = 'images.';

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
    'NAME' => Loc::getMessage('C_NEWS_IMAGES_1_DETAIL_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'REFRESH' => 'Y'
];

$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'images.'.$sTemplate;

if(!empty($sTemplate)) {
    $arCommonParameters = [
        'PROPERTY_PRODUCTS'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arCommonParameters) {
            if (ArrayHelper::isIn($key, $arCommonParameters))
                return false;

            $arParameter['PARENT'] = 'DETAIL_SETTINGS';
            $arParameter['NAME'] = Loc::getMessage('C_NEWS_IMAGES_1_DETAIL').' '.$arParameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset($arCommonParameters, $arCurrentValues['PROPERTY_PRODUCTS']);
}
unset($sComponent, $sPrefix, $sTemplate);