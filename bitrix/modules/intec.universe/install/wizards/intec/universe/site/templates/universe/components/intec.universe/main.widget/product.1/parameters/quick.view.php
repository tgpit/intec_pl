<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var bool $bBase
 * @var bool $bLite
 * @var bool $bEnabledProperties
 * @var Arrays $arProperties
 */

$arTemplateParameters['QUICK_VIEW_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['QUICK_VIEW_USE'] === 'Y') {
    $sPrefix = 'QUICK_VIEW_';
    $sPrefixTemplate = 'quick.view.';
    $sComponent = 'bitrix:catalog.element';

    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        $sComponent,
        $siteTemplate
    ))->asArray(function ($index, $template) use ($sPrefixTemplate) {
        if (!StringHelper::startsWith($template['NAME'], $sPrefixTemplate))
            return ['skip' => true];

        $sName = StringHelper::cut(
            $template['NAME'],
            StringHelper::length($sPrefixTemplate)
        );

        return [
            'key' => $sName,
            'value' => $sName
        ];
    });

    $arTemplateParameters['QUICK_VIEW_TEMPLATE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (empty($arCurrentValues['QUICK_VIEW_TEMPLATE'])) {
        unset($sPrefixTemplate, $sComponent, $arTemplates);

        return;
    }

    if (!$bEnabledProperties) {
        $arTemplateParameters['QUICK_VIEW_PROPERTY_CODE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW_PROPERTY_CODE'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray(function ($key, $property) {
                if (empty($property['CODE']))
                    return ['skip' => true];

                return [
                    'key' => $property['CODE'],
                    'value' => '['.$property['CODE'].'] '.$property['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'MULTIPLE' => 'Y'
        ];

        if ($bBase) {
            include(__DIR__ . '/base/quick.view.php');
        }
    }

    if (ArrayHelper::isIn($arCurrentValues['QUICK_VIEW_TEMPLATE'], $arTemplates)) {
        $excluded = [
            'LAZYLOAD_USE',
            'PROPERTY_MARKS_HIT',
            'PROPERTY_MARKS_NEW',
            'PROPERTY_MARKS_RECOMMEND',
            'PROPERTY_MARKS_SHARE',
            'PROPERTY_PICTURES',
            'PROPERTY_ARTICLE',
            'MARKS_SHOW',
            'DELAY_USE',
            'ARTICLE_SHOW',
            'COUNTER_SHOW',
            'QUANTITY_SHOW',
            'QUANTITY_MODE',
            'QUANTITY_BOUNDS_FEW',
            'QUANTITY_BOUNDS_MANY',
            'PRICE_RANGE_SHOW',
            'PRICE_DISCOUNT_SHOW',
            'PRICE_DISCOUNT_PERCENT',
            'PRICE_DISCOUNT_ECONOMY',
            'VOTE_USE',
            'VOTE_MODE'
        ];

        $arCurrentValues['QUICK_VIEW_IBLOCK_TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
        $arCurrentValues['QUICK_VIEW_IBLOCK_ID'] = $arCurrentValues['IBLOCK_ID'];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sPrefixTemplate.$arCurrentValues['QUICK_VIEW_TEMPLATE'],
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($key, &$parameter) use (&$excluded) {
                if (StringHelper::startsWith($key, 'TIMER_') && $key !== 'TIMER_SHOW')
                    return false;

                if (ArrayHelper::isIn($key, $excluded))
                    return false;

                $parameter['PARENT'] = 'VISUAL';
                $parameter['NAME'] = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUICK_VIEW').' '.$parameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));

        unset($excluded);
    }

    unset($sPrefix, $sPrefixTemplate, $sComponent, $arTemplates);
}