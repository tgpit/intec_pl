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

$sTemplate = 'products.small.7';

if ($arCurrentValues['PRODUCTS_ACCESSORIES_VIEW'] === 'list')
    $sTemplate = 'products.small.8';

if (empty($sTemplate))
    return;

$sComponent = 'bitrix:catalog.section';
$sPrefix = 'PRODUCTS_ACCESSORIES_';
$iLength = StringHelper::length($sTemplate);

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arTemplate) {
    return [
        'key' => $key,
        'value' => $arTemplate['NAME']
    ];
});

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arAssociatedCommonParameters = [
        'SETTINGS_USE',
        'LAZYLOAD_USE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arAssociatedCommonParameters) {
            if (ArrayHelper::isIn($key, $arAssociatedCommonParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ACCESSORIES').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset($arAssociatedCommonParameters);
}

unset($sTemplate, $sComponent, $sPrefix, $iLength, $arTemplates);