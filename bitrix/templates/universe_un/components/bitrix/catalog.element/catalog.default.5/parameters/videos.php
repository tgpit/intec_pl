<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 */

$sComponent = 'intec.universe:main.videos';
$sTemplate = 'template.3';
$sPrefix = 'VIDEO_';

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
    $arVideosCommonParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'SECTIONS_MODE',
        'SECTIONS',
        'ELEMENTS_COUNT',
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'PICTURE_SOURCES',
        'PROPERTY_URL',
        'HEADER_SHOW',
        'HEADER_POSITION',
        'HEADER',
        'DESCRIPTION_SHOW',
        'DESCRIPTION_POSITION',
        'DESCRIPTION',
        'FOOTER_SHOW',
        'FOOTER_POSITION',
        'FOOTER_BUTTON_SHOW',
        'FOOTER_BUTTON_TEXT',
        'LIST_PAGE_URL',
        'CACHE_TYPE',
        'CACHE_TIME',
        'CACHE_NOTES',
        'SORT_BY',
        'ORDER_BY'
    ];

    $arCurrentValues['PICTURE_SOURCES'] = [
        0 => 'service',
        1 => 'preview',
        2 => 'detail'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use (&$arVideosCommonParameters) {
            if (ArrayHelper::isIn($key, $arVideosCommonParameters))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($arVideosCommonParameters, $arCurrentValues['PICTURE_SOURCES']);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);