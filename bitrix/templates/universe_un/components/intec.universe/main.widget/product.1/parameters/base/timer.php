<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$sComponent = 'intec.universe:product.timer';
$sPrefix = 'SECTION_TIMER_';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $value) {
    return [
        'key' => $value['NAME'],
        'value' => $value['NAME']
    ];
});

$arTemplateParameters['TIMER_TEMPLATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TIMER_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'REFRESH' => 'Y',
    'ADDITIONAL_VALUES' => 'Y',
    'DEFAULT' => '.default'
];

if (ArrayHelper::isIn($arCurrentValues['TIMER_TEMPLATE'], $arTemplates)) {
    $excluded = [
        'SHOW',
        'TEMPLATE',
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'ELEMENT_ID_INTRODUCE',
        'ELEMENT_ID'
    ];

    $arCurrentValues['TIMER_IBLOCK_TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
    $arCurrentValues['TIMER_IBLOCK_ID'] = $arCurrentValues['IBLOCK_ID'];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $arCurrentValues['TIMER_TEMPLATE'],
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$parameter) use (&$excluded) {
            if (ArrayHelper::isIn($key, $excluded))
                return false;

            $parameter['PARENT'] = 'VISUAL';
            $parameter['NAME'] = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TIMER').' '.$parameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($excluded);
}

unset($sComponent, $sPrefix, $arTemplates);