<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Loader;

/**
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 * @var Arrays $arProperties
 */

$bSeo = Loader::includeModule('intec.seo');
$sPrefix = 'TAGS_';
$arTemplateParameters[$sPrefix.'USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues[$sPrefix.'USE'] === 'Y') {
    $sComponent = 'intec.universe:tags.list';
    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');

    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        $sComponent,
        $siteTemplate
    ))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
        return [
            'key' => $arTemplate['NAME'],
            'value' => $arTemplate['NAME']
        ];
    });

    $arTemplateParameters[$sPrefix.'TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if ($bSeo) {
        $arTemplateParameters[$sPrefix.'POSITION'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'top' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_POSITION_TOP'),
                'bottom' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_POSITION_BOTTOM'),
                'menu' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_POSITION_MENU')
            ],
            'DEFAULT' => 'top'
        ];

        $arTemplateParameters[$sPrefix.'INACTIVE_TARGET_SHOW'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_INACTIVE_TARGET_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        $arTemplateParameters[$sPrefix.'MOBILE_POSITION'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_MOBILE_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'top' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_POSITION_TOP'),
                'bottom' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_POSITION_BOTTOM')
            ],
            'DEFAULT' => 'top'
        ];

        $arTemplateParameters[$sPrefix.'QUANTITY'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_QUANTITY'),
            'TYPE' => 'STRING'
        ];

        $arTemplateParameters[$sPrefix.'SORT'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'name' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_NAME_ASC'),
                'nameDesc' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_NAME_DESC'),
                'count' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_COUNT_ASC'),
                'countDesc' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_COUNT_DESC'),
                'sorting' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_SORTING_ASC'),
                'sortingDesc' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_SORTING_DESC'),
                'none' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_SORT_NONE')
            ],
            'DEFAULT' => 'none'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.seo:filter.tags',
            '.default',
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$arParametersCommon) {
                if (ArrayHelper::isIn($sKey, $arParametersCommon))
                    return false;

                $arParameter['PARENT'] = 'BASE';
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_TAGS').'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }

    $arTemplateParameters[$sPrefix.'PROPERTY'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']) || $arProperty['PROPERTY_TYPE'] != 'L' || $arProperty['USER_TYPE'] !== null)
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters[$sPrefix.'COUNT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_COUNT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];

    $arTemplateParameters[$sPrefix.'VARIABLE_TAGS'] = [
        'PARENT' => 'ACTION_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_TAGS_VARIABLE_TAGS'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'tags'
    ];

    if (!empty($sTemplate)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$arParametersCommon) {
                if (ArrayHelper::isIn($sKey, $arParametersCommon))
                    return false;

                $arParameter['PARENT'] = 'BASE';
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_TAGS').'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }
}
unset($bSeo);