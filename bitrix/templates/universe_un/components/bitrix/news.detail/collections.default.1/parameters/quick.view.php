<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$bQuickViewUse = false;

foreach (['TEXT', 'LIST', 'TILE'] as $sView) {
    $sPrefix = 'LIST_'.$sView.'_QUICK_VIEW_';

    if ($arCurrentValues[$sPrefix.'USE'] === 'Y') {
        $bQuickViewUse = true;
        break;
    }
}

if ($bQuickViewUse) {
    $sPrefix = 'QUICK_VIEW_';

    $arTemplateParameters[$sPrefix.'SLIDE_USE'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_QUICK_VIEW_SLIDE_USE'),
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
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_QUICK_VIEW_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PRODUCTS_IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['sort' => 'asc'], [
            'IBLOCK_ID' => $arCurrentValues['PRODUCTS_IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]));

        $arTemplateParameters[$sPrefix . 'PROPERTY_CODE'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_QUICK_VIEW_PROPERTY_CODE'),
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
    }

    $arCurrentValuesQuick = $arCurrentValues;
    $arCurrentValuesQuick['IBLOCK_ID'] = $arCurrentValues['PRODUCTS_IBLOCK_ID'];
    $arCurrentValuesQuick['IBLOCK_TYPE'] = $arCurrentValues['PRODUCTS_IBLOCK_TYPE'];

    if (!empty($sTemplate)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValuesQuick,
            $sPrefix,
            function ($sKey, &$arParameter) {

                $arParameter['PARENT'] = 'LIST_SETTINGS';
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_QUICK_VIEW').'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }
}