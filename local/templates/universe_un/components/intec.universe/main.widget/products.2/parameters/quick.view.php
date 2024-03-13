<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$sPrefix = 'QUICK_VIEW_';

$arTemplateParameters[$sPrefix.'USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_2_QUICK_VIEW_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N'
];
$arTemplateParameters[$sPrefix.'DETAIL'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_2_QUICK_VIEW_DETAIL'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($arCurrentValues[$sPrefix.'USE'] === 'Y') {
    $arTemplateParameters[$sPrefix.'SLIDE_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_2_QUICK_VIEW_SLIDE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $sComponent = 'bitrix:catalog.element';
    $sTemplate = 'quick.view.';

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

    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
    $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

    if (!empty($sTemplate))
        $sTemplate = 'quick.view.'.$sTemplate;

    $arTemplateParameters[$sPrefix.'TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_2_QUICK_VIEW_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['sort' => 'asc'], [
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]));

        $arTemplateParameters[$sPrefix.'PROPERTY_CODE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_2_QUICK_VIEW_PROPERTY_CODE'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $arProperties->asArray(function ($iIndex, $arProperty) {
                $sCode = $arProperty['CODE'];

                if (empty($sCode))
                    $sCode = $arProperty['ID'];

                return [
                    'key' => $sCode,
                    'value' => '['.$sCode.'] '.$arProperty['NAME']
                ];
            })
        ];

        unset($arProperties);
    }

    if (!empty($sTemplate)) {
        $excluded = [
            'SETTINGS_USE',
            'LAZYLOAD_USE',
            'PROPERTY_MARKS_RECOMMEND',
            'PROPERTY_MARKS_NEW',
            'PROPERTY_MARKS_HIT',
            'PROPERTY_MARKS_SHARE',
            'PROPERTY_PICTURES',
            'OFFERS_PROPERTY_PICTURES',
            'PROPERTY_ORDER_USE',
            'PROPERTY_REQUEST_USE',
            'ACTION',
            'COUNTER_SHOW',
            'DELAY_USE',
            'USE_COMPARE',
            'COMPARE_NAME',
            'MARKS_SHOW',
            'QUANTITY_SHOW',
            'QUANTITY_MODE',
            'BUTTON_REQUEST_TEXT'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$excluded) {
                if (StringHelper::startsWith($sKey, 'TIMER_') && $sKey !== 'TIMER_SHOW')
                    return false;

                if (ArrayHelper::isIn($sKey, $excluded))
                    return false;

                $arParameter['PARENT'] = 'LIST_SETTINGS';
                $arParameter['NAME'] = Loc::getMessage('C_WIDGET_PRODUCTS_2_QUICK_VIEW').'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));

        unset($excluded);
    }

    unset($sComponent, $sTemplate);
}

unset($sPrefix);