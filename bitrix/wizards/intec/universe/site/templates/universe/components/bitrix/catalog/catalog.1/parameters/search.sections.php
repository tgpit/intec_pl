<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 * @var array $arParametersCommon
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 * @var Arrays $arProperties
 */

$sPrefix = 'SEARCH_SECTIONS_';
$arTemplateParameters[$sPrefix.'USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_SEARCH_SECTIONS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues[$sPrefix.'USE'] === 'Y') {
    $sComponent = 'intec.universe:search.sections';
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
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_SEARCH_SECTIONS_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($sTemplate)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) {
                if (ArrayHelper::isIn($sKey, [
                    'IBLOCK_TYPE',
                    'IBLOCK_ID',
                    'CACHE_TYPE',
                    'CACHE_TIME',
                ]))
                    return false;

                $arParameter['PARENT'] = 'BASE';
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_SEARCH_SECTIONS').'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ));
    }
}