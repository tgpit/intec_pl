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
 * @var Arrays $arProperties
 */

$arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_TITLE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_TITLE'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_TITLE_DEFAULT')
];
$arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_COUNT_ELEMENTS'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_COUNT_ELEMENTS'),
    'TYPE' => 'STRING',
    'DEFAULT' => '6'
];
$arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_IS_CATALOG'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_IS_CATALOG'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
$arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_USE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_USE'] === 'Y') {
    $arProperties = null;
    $rsProperties = CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID']]);

    while ($arProperty = $rsProperties->GetNext()) {
        if ($arProperty['PROPERTY_TYPE'] === 'L')
            $arProperties[$arProperty['CODE']] = '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME'];
    }

    $arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_FILTER'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_PROPERTY_FILTER'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$bIsCatalog = false;

if ($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_IS_CATALOG'] === 'Y' && $bBase) {
    $bIsCatalog = !empty(CCatalogSku::GetInfoByIBlock($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID']));
}

$sComponent = $bIsCatalog ? 'bitrix:catalog.section' : 'bitrix:news.list';
$sTemplate = $bIsCatalog ? 'products.small.' : 'news.tile.';
$sPrefix = $bIsCatalog ? 'PRODUCTS_BLOCK_ON_EMPTY_RESULTS' : 'NEWS_BLOCK_ON_EMPTY_RESULTS';

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

$sTemplateId = ArrayHelper::getValue($arCurrentValues, 'BLOCK_ON_EMPTY_SEARCH_RESULTS_TEMPLATE');
$sTemplateId = ArrayHelper::fromRange($arTemplates, $sTemplateId, false, false);

if (!empty($sTemplateId))
    $sTemplate = $sTemplate.$sTemplateId;

$arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_TEMPLATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arUsedParameters = [];

    if ($bIsCatalog) {
        $arUsedParameters = [
            'PRICE_CODE',
            'CONVERT_CURRENCY',
            'CURRENCY_ID',
            'BASKET_URL',
            'CONSENT_URL',
            'COMPARE_NAME',
            'USE_COMPARE',
            'PRODUCT_DISPLAY_MODE',
            'OFFERS_PROPERTY_CODE'
        ];
    }

    $arAssociatedCommonParameters = [
        'SETTINGS_USE',
        'LAZYLOAD_USE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$arUsedParameters, &$sPrefix) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix).' '.$arParameter['NAME'];

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
            function ($sKey, &$arParameter) use (&$arAssociatedCommonParameters, &$sPrefix) {
                if (ArrayHelper::isIn($sKey, $arAssociatedCommonParameters))
                    return false;

                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix).' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        )
    );

    unset($arAssociatedCommonParameters, $arUsedParameters);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates, $sTemplateId);