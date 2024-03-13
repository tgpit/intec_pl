<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$arTemplateParameters['ORDER_FAST_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ORDER_FAST_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ORDER_FAST_USE'] == 'Y') {
    $sComponent = 'intec.universe:sale.order.fast';
    $sPrefix = 'ORDER_FAST_';

    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        $sComponent,
        $siteTemplate
    ))->asArray(function ($index, $template) {
        return [
            'key' => $template['NAME'],
            'value' => $template['NAME']
        ];
    });

    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
    $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

    $arTemplateParameters['ORDER_FAST_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ORDER_FAST_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($sTemplate)) {
        $arCommonParameters = [
            'SETTINGS_USE',
            'LAZYLOAD_USE',
            'CONSENT_URL',
            'PROPERTY_ARTICLE',
            'OFFERS_PROPERTY_ARTICLE'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($key, &$parameter) use (&$arCommonParameters) {
                if (ArrayHelper::isIn($key, $arCommonParameters))
                    return false;

                if (StringHelper::startsWith($key, 'AJAX_'))
                    return false;

                $parameter['NAME'] = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ORDER_FAST').' '.$parameter['NAME'];
                $parameter['PARENT'] = 'BASE';

                return true;
            },
            Component::PARAMETERS_MODE_BOTH
        ));
    }
}