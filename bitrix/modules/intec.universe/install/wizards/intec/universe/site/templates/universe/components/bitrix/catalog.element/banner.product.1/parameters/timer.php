<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

$sComponent = 'intec.universe:product.timer';
$sTemplate = 'template.3';
$sPrefix = 'TIMER_';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arTemplate) {
    return [
        'key' => $key,
        'value' => $arTemplate['NAME']
    ];
});

$arParametersExceptions = [
    'TIMER_TITLE_SHOW'
];

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arParametersExceptions) {
            if (ArrayHelper::isIn($key, $arParametersExceptions))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_TIMER').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);
