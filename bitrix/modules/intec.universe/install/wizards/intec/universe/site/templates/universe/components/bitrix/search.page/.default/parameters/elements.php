<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$bIsCatalog = false;

if ($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IS_CATALOG'] === 'Y' && $bBase) {
    $bIsCatalog = !empty(CCatalogSku::GetInfoByIBlock($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID']));
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

$sTemplateId = ArrayHelper::getValue($arCurrentValues, 'BLOCK_ON_EMPTY_RESULTS_TEMPLATE');
$sTemplateId = ArrayHelper::fromRange($arTemplates, $sTemplateId, false, false);

if (!empty($sTemplateId))
    $sTemplate = $sTemplate.$sTemplateId;

$arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_TEMPLATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_TEMPLATE'),
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
                $arParameter['NAME'] = Loc::getMessage('C_SEARCH_PAGE_DEFAULT_'.$sPrefix).' '.$arParameter['NAME'];

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

                $arParameter['NAME'] = Loc::getMessage('C_SEARCH_PAGE_DEFAULT_'.$sPrefix).' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        )
    );

    unset($arAssociatedCommonParameters, $arUsedParameters);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates, $sTemplateId);