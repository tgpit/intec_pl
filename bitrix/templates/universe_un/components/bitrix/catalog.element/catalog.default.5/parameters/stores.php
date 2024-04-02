<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Text\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

if ($arCurrentValues['SKU_VIEW'] === 'dynamic') {
    if ($arCurrentValues['STORE_POSITION'] === 'popup')
        $sTemplate = 'template.1';
    else
        $sTemplate = ArrayHelper::fromRange(['template.2', 'template.4'], $arCurrentValues['STORE_TEMPLATE']);
} else
    $sTemplate = 'template.3';

$sComponent = 'bitrix:catalog.store.amount';
$sPrefix = 'STORE_';

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
    $arStoreCommonParameters = [
        'SETTINGS_USE',
        'LAZYLOAD_USE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use ($arStoreCommonParameters) {
            if (ArrayHelper::isIn($key, $arStoreCommonParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset($arStoreCommonParameters);
}

unset($sTemplate, $sComponent, $sPrefix, $arTemplates);