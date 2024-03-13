<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

$sComponent = 'intec.universe:main.services';
$sTemplate = 'template.26';
$sPrefix = 'SERVICES_';

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
    $arServicesCommonParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'SECTIONS',
        'ELEMENTS_COUNT',
        'HEADER_BLOCK_SHOW',
        'HEADER_BLOCK_POSITION',
        'HEADER_BLOCK_TEXT',
        'DESCRIPTION_BLOCK_SHOW',
        'DESCRIPTION_BLOCK_POSITION',
        'DESCRIPTION_BLOCK_TEXT',
        'LIST_PAGE_URL',
        'SECTION_URL',
        'DETAIL_URL',
        'CACHE_TYPE',
        'CACHE_TIME',
        'CACHE_NOTES',
        'SORT_BY',
        'ORDER_BY',
        'SETTINGS_USE',
        'LAZYLOAD_USE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arServicesCommonParameters) {
            if (ArrayHelper::isIn($key, $arServicesCommonParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SERVICES').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($arServicesCommonParameters);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);