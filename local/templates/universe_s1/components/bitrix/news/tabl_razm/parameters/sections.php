<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
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
 */

$sComponent = 'bitrix:catalog.section.list';
$sTemplate = 'catalog.';

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

foreach (['ROOT', 'CHILDREN'] as $sLevel) {
    $sPrefix = 'SECTIONS_'.$sLevel.'_';
    $sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
    $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

    if (!empty($sTemplate))
        $sTemplate = 'catalog.'.$sTemplate;

    $arTemplateParameters[$sPrefix.'SECTION_DESCRIPTION_SHOW'] = [
        'PARENT' => 'SECTIONS_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'SECTION_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues[$sPrefix.'SECTION_DESCRIPTION_SHOW'] === 'Y') {
        $arTemplateParameters[$sPrefix.'SECTION_DESCRIPTION_POSITION'] = [
            'PARENT' => 'SECTIONS_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'SECTION_DESCRIPTION_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'top' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'SECTION_DESCRIPTION_POSITION_TOP'),
                'bottom' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'SECTION_DESCRIPTION_POSITION_BOTTOM')
            ]
        ];
    }

    $arTemplateParameters[$sPrefix.'CANONICAL_URL_USE'] = [
        'PARENT' => 'SECTIONS_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'CANONICAL_URL_USE'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues[$sPrefix.'CANONICAL_URL_USE'] === 'Y') {
        if ($sLevel === 'ROOT') {
            $arTemplateParameters[$sPrefix . 'CANONICAL_URL_TEMPLATE'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_' . $sPrefix . 'CANONICAL_URL_TEMPLATE'),
                'TYPE' => 'STRING'
            ];
        } else {
            $arTemplateParameters[$sPrefix . 'CANONICAL_URL_TEMPLATE'] = CIBlockParameters::GetPathTemplateParam(
                'SECTION',
                $sPrefix . 'CANONICAL_URL_TEMPLATE',
                Loc::getMessage('C_CATALOG_CATALOG_1_' . $sPrefix . 'CANONICAL_URL_TEMPLATE'),
                '',
                'SECTIONS_SETTINGS'
            );
        }
    }

    $arTemplateParameters[$sPrefix.'TEMPLATE'] = [
        'PARENT' => 'SECTIONS_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters[$sPrefix.'MENU_SHOW'] = [
        'PARENT' => 'SECTIONS_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sPrefix.'MENU_SHOW'),
        'TYPE' => 'CHECKBOX'
    ];

    if ($arCurrentValues['USE_GIFTS_SECTION'] === 'Y') {
        $arTemplateParameters['GIFTS_SECTION_LIST_POSITION'] = [
            'PARENT' => 'GIFTS_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'top' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_POSITION_TOP'),
                'middle' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_POSITION_MIDDLE'),
                'bottom' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_POSITION_BOTTOM')
            ],
            'DEFAULT' => 'bottom'
        ];

        $arGiftsView = [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
        ];
        $arGiftsViewDefault = 5;

        $arGiftsColumns = [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
        ];
        $arGiftsColumnsDefault = 3;

        /*$arTemplateParameters['GIFTS_SECTION_LIST_VIEW'] = [
            'PARENT' => 'GIFTS_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_VIEW'),
            'TYPE' => 'LIST',
            'VALUES' => $arGiftsView,
            'DEFAULT' => $arGiftsViewDefault,
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['GIFTS_SECTION_LIST_VIEW'] == 4) {*/
            $arTemplateParameters['GIFTS_SECTION_LIST_NAVIGATION_BUTTON_POSITION'] = [
                'PARENT' => 'GIFTS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_NAVIGATION_BUTTON_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'top' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_NAVIGATION_BUTTON_POSITION_TOP'),
                    'bottom' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_NAVIGATION_BUTTON_POSITION_BOTTOM')
                ],
                'DEFAULT' => 'top'
            ];
        //}

        $arTemplateParameters['GIFTS_SECTION_LIST_COLUMNS'] = [
            'PARENT' => 'GIFTS_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_COLUMNS'),
            'TYPE' => 'LIST',
            'VALUES' => $arGiftsColumns,
            'DEFAULT' => $arGiftsColumnsDefault
        ];

        $arTemplateParameters['GIFTS_SECTION_LIST_QUANTITY'] = [
            'PARENT' => 'GIFTS_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GIFTS_SECTION_LIST_QUANTITY'),
            'TYPE' => 'STRING',
            'DEFAULT' => 20
        ];
    }

    if (!empty($sTemplate)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($sKey, &$arParameter) use (&$sLevel, &$arParametersCommon) {
                if (ArrayHelper::isIn($sKey, $arParametersCommon))
                    return false;

                $arParameter['PARENT'] = 'SECTIONS_SETTINGS';
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_SECTIONS_'.$sLevel).'. '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }

    if ($sLevel === 'CHILDREN') {
        if (Loader::includeModule('intec.seo')) {
            $sExtendingPrefix = $sPrefix.'EXTENDING_';
            $sExtendingComponent = 'bitrix:catalog.section';
            $sExtendingTemplate = 'products.small.';

            $arExtendingTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
                $sExtendingComponent,
                $siteTemplate
            ))->asArray(function ($iIndex, $arTemplate) use (&$sExtendingTemplate) {
                if (!StringHelper::startsWith($arTemplate['NAME'], $sExtendingTemplate))
                    return ['skip' => true];

                $sName = StringHelper::cut(
                    $arTemplate['NAME'],
                    StringHelper::length($sExtendingTemplate)
                );

                return [
                    'key' => $sName,
                    'value' => $sName
                ];
            });

            $sExtendingTemplate = ArrayHelper::getValue($arCurrentValues, $sExtendingPrefix.'TEMPLATE');
            $sExtendingTemplate = ArrayHelper::fromRange($arExtendingTemplates, $sExtendingTemplate, false, false);

            if (!empty($sExtendingTemplate))
                $sExtendingTemplate = 'products.small.'.$sExtendingTemplate;

            $arTemplateParameters[$sExtendingPrefix.'USE'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sExtendingPrefix.'USE'),
                'TYPE' => 'CHECKBOX'
            ];

            $arFields = [];

            if (!empty($arCurrentValues['IBLOCK_ID'])) {
                $rsFields = CUserTypeEntity::GetList(['SORT' => 'ASC'], array(
                    'ENTITY_ID' => 'IBLOCK_'.$arCurrentValues['IBLOCK_ID'].'_SECTION',
                    'USER_TYPE_ID' => 'iblock_section',
                    'MULTIPLE' => 'Y'
                ));

                while ($arField = $rsFields->Fetch())
                    $arFields[$arField['FIELD_NAME']] = $arField['FIELD_NAME'];

                unset($rsFields);
            }

            $arTemplateParameters[$sExtendingPrefix.'PROPERTY'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sExtendingPrefix.'PROPERTY'),
                'TYPE' => 'LIST',
                'VALUES' => $arFields,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            unset($arFields);

            $arTemplateParameters[$sExtendingPrefix.'TITLE'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sExtendingPrefix.'TITLE'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sExtendingPrefix.'TITLE_DEFAULT')
            ];

            $arTemplateParameters[$sExtendingPrefix.'TEMPLATE'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sExtendingPrefix.'TEMPLATE'),
                'TYPE' => 'LIST',
                'VALUES' => $arExtendingTemplates,
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];

            if (!empty($sExtendingTemplate)) {
                $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                    $sExtendingComponent,
                    $sExtendingTemplate,
                    $siteTemplate,
                    $arCurrentValues,
                    $sExtendingPrefix,
                    function ($sKey, &$arParameter) use (&$sLevel, &$arParametersCommon) {
                        if (ArrayHelper::isIn($sKey, $arParametersCommon))
                            return false;

                        $arParameter['PARENT'] = 'SECTIONS_SETTINGS';
                        $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_SECTIONS_'.$sLevel).'. '.Loc::getMessage('C_CATALOG_CATALOG_1_SECTIONS_'.$sLevel.'_EXTENDING').'. '.$arParameter['NAME'];

                        return true;
                    },
                    Component::PARAMETERS_MODE_TEMPLATE
                ));
            }

            $sArticlesExtendingPrefix = 'SECTIONS_ARTICLES_EXTENDING_';
            $sArticlesExtendingComponent = 'bitrix:news.list';
            $sArticlesExtendingTemplate = 'news.';

            $arArticlesExtendingTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
                $sArticlesExtendingComponent,
                $siteTemplate
            ))->asArray(function ($iIndex, $arTemplate) use (&$sArticlesExtendingTemplate) {
                if (!StringHelper::startsWith($arTemplate['NAME'], $sArticlesExtendingTemplate))
                    return ['skip' => true];

                $sName = StringHelper::cut(
                    $arTemplate['NAME'],
                    StringHelper::length($sArticlesExtendingTemplate)
                );

                return [
                    'key' => $sName,
                    'value' => $sName
                ];
            });

            $sArticlesExtendingTemplate = ArrayHelper::getValue($arCurrentValues, $sArticlesExtendingPrefix.'TEMPLATE');
            $sArticlesExtendingTemplate = ArrayHelper::fromRange($arArticlesExtendingTemplates, $sArticlesExtendingTemplate, false, false);

            if (!empty($sArticlesExtendingTemplate))
                $sArticlesExtendingTemplate = 'news.'.$sArticlesExtendingTemplate;

            $arTemplateParameters[$sArticlesExtendingPrefix.'TITLE'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sArticlesExtendingPrefix.'TITLE'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sArticlesExtendingPrefix.'TITLE_DEFAULT')
            ];

            $arTemplateParameters[$sArticlesExtendingPrefix.'QUANTITY'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sArticlesExtendingPrefix.'QUANTITY'),
                'TYPE' => 'STRING',
                'DEFAULT' => 5
            ];

            $arTemplateParameters[$sArticlesExtendingPrefix.'TEMPLATE'] = [
                'PARENT' => 'SECTIONS_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_'.$sArticlesExtendingPrefix.'TEMPLATE'),
                'TYPE' => 'LIST',
                'VALUES' => $arArticlesExtendingTemplates,
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];

            if (!empty($sExtendingTemplate)) {
                $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                    $sArticlesExtendingComponent,
                    $sArticlesExtendingTemplate,
                    $siteTemplate,
                    $arCurrentValues,
                    $sArticlesExtendingPrefix,
                    function ($sKey, &$arParameter) use (&$arParametersCommon) {
                        if (ArrayHelper::isIn($sKey, $arParametersCommon))
                            return false;

                        $arParameter['PARENT'] = 'SECTIONS_SETTINGS';
                        $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_SECTIONS_ARTICLES_EXTENDING').'. '.$arParameter['NAME'];

                        return true;
                    },
                    Component::PARAMETERS_MODE_TEMPLATE
                ));
            }
        }
    }
}