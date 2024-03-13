<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
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

$arTemplateParameters['PRODUCTS_SHOW'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_PRODUCTS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRODUCTS_SHOW'] === 'Y') {
    $sComponent = 'bitrix:catalog.section';
    $sTemplate = 'catalog.tile.';

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

    $sPrefix = 'PRODUCTS_';
    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix . 'TEMPLATE');
    $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

    if (!empty($sTemplate))
        $sTemplate = 'catalog.tile.' . $sTemplate;

    $arTemplateParameters[$sPrefix . 'TEMPLATE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_' . $sPrefix . 'TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($sTemplate)) {
        $arUnUsedParameters = [
            'SEF_MODE',
            'SEF_RULE',
            'AJAX_MODE',
            'SECTION_URL',
            'DETAIL_URL',
            'SET_TITLE',
            'SET_BROWSER_TITLE',
            'BROWSER_TITLE',
            'SET_META_KEYWORDS',
            'META_KEYWORDS',
            'SET_META_DESCRIPTION',
            'META_DESCRIPTION',
            'SET_LAST_MODIFIED',
            'USE_MAIN_ELEMENT_SECTION',
            'ADD_SECTIONS_CHAIN',
            'LINE_ELEMENT_COUNT',
            'BACKGROUND_IMAGE',
            'CACHE_TYPE',
            'CACHE_TIME',
            'CACHE_NOTES',
            'CACHE_FILTER',
            'CACHE_GROUPS',
            'COMPATIBLE_MODE',
            'DISABLE_INIT_JS_IN_COMPONENT',
            'PAGER_DESC_NUMBERING_CACHE_TIME',
            'PAGER_SHOW_ALL',
            'PAGER_BASE_LINK_ENABLE',
            'SET_STATUS_404',
            'SHOW_404',
            'FILE_404',
            'SECTION_CODE_PATH',
            'CUSTOM_FILTER',
            'LAZYLOAD_USE',
            'OFFERS_VARIABLE_SELECT',
            'VOTE_MODE',
            'QUICK_VIEW_LAZYLOAD_USE',
            'QUICK_VIEW_TIMER_SHOW',
            'AJAX_OPTION_JUMP',
            'AJAX_OPTION_STYLE',
            'AJAX_OPTION_HISTORY',
            'AJAX_OPTION_ADDITIONAL',
            'SECTION_ID',
            'SECTION_CODE',
            'SECTION_ID_VARIABLE'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
            Component::getParameters(
                $sComponent,
                $sTemplate,
                $siteTemplate,
                $arCurrentValues,
                $sPrefix,
                function ($sKey, &$arParameter) use (&$arUnUsedParameters) {
                    $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_PRODUCTS') . '. ' . $arParameter['NAME'];

                    if (ArrayHelper::isIn($sKey, $arUnUsedParameters))
                        return false;

                    return true;
                },
                Component::PARAMETERS_MODE_BOTH
            )
        );

        unset($arUsedPagerParameters, $arUsedParameters, $arUnusedParameters);
    }
}
