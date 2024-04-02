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

$sComponent = 'intec.universe:main.sections';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($iIndex, $arTemplate) {

    $sName = $arTemplate['NAME'];

    return [
        'key' => $sName,
        'value' => $sName
    ];
});

$sPrefix = 'ADDITIONAL_PRODUCTS_CATEGORIES_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = $sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_DEFAULT_1_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arUsedParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'ELEMENTS_COUNT',
        'LIST_PAGE_URL',
        'SECTION_URL',
        'SORT_BY',
        'ORDER_BY'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$arUsedParameters, $sPrefix) {

                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_DEFAULT_1_ADDITIONAL_PRODUCTS_CATEGORIES').'. '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, $arUsedParameters))
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
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_DEFAULT_1_ADDITIONAL_PRODUCTS_CATEGORIES').'. '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'LAZYLOAD_USE'
                ])) return false;

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        )
    );

    unset($arUsedParameters);
}

