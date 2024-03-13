<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\GroupLangTable;
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

$sComponent = 'bitrix:news.list';
$sTemplate = 'news.';

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

$sPrefix = 'ADDITIONAL_ARTICLES_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'news.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arNotUsedParameters = [
        'FILTER_NAME',
        'SET_TITLE',
        'SET_BROWSER_TITLE',
        'SET_META_KEYWORDS',
        'SET_META_DESCRIPTION',
        'SET_LAST_MODIFIED',
        'INCLUDE_IBLOCK_INTO_CHAIN',
        'ADD_SECTIONS_CHAIN',
        'PARENT_SECTION',
        'PARENT_SECTION_CODE',
        'INCLUDE_SUBSECTIONS'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
		Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$arNotUsedParameters, $sPrefix) {

                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_ADDITIONAL_ARTICLES').'. '.$arParameter['NAME'];
                $arParameter['PARENT'] = 'LIST_SETTINGS';

                if (!ArrayHelper::isIn($sKey, $arNotUsedParameters))
                    return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
		),
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use ($sPrefix) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_ADDITIONAL_ARTICLES').'. '.$arParameter['NAME'];
                $arParameter['PARENT'] = 'LIST_SETTINGS';

                if (ArrayHelper::isIn($sKey, [
                    'LAZYLOAD_USE',
                ])) return false;

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        )
    );

    unset($arNotUsedParameters);
}

