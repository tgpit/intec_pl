<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$sComponent = 'intec.universe:main.news';
$sTemplate = 'template.8';
$sPrefix = 'ARTICLES_';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arTemplate) {
    return [
        'key' => $key,
        'value' => $arTemplate['NAME']
    ];
});

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arArticlesCommonParameters = [
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'ELEMENTS_COUNT',
        'HEADER_BLOCK_SHOW',
        'DESCRIPTION_BLOCK_SHOW',
        'FOOTER_SHOW',
        'LIST_PAGE_URL',
        'SECTION_URL',
        'DETAIL_URL',
        'SORT_BY',
        'ORDER_BY',
        'CACHE_TYPE',
        'CACHE_TIME',
        'CACHE_NOTES'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arArticlesCommonParameters) {
            if (ArrayHelper::isIn($key, $arArticlesCommonParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_ARTICLES').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($arArticlesCommonParameters);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);