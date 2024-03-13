<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['INTEREST_PRODUCTS_SHOW'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['INTEREST_PRODUCTS_SHOW'] === 'Y') {
        $arTemplateParameters['INTEREST_PRODUCTS_TITLE'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_TITLE'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_TITLE_DEFAULT')
        ];

        if ($arCurrentValues['INTEREST_PRODUCTS_ADDITION_COUNT_USE'] !== 'Y') {
            $arTemplateParameters['INTEREST_PRODUCTS_COUNT'] = [
                'PARENT' => 'LIST_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_COUNT'),
                'TYPE' => 'STRING',
                'DEFAULT' => 6
            ];
        }

        $arTemplateParameters['INTEREST_PRODUCTS_POSITION'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'content' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_POSITION_CONTENT'),
                'footer' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_POSITION_BOTTOM')
            ]
        ];

        $arFields = Arrays::fromDBResult(CUserTypeEntity::GetList([
            'SORT' => 'ASC'
        ], [
            'ENTITY_ID' => 'IBLOCK_'.$arCurrentValues['IBLOCK_ID'].'_SECTION',
            'USER_TYPE_ID' => 'iblock_element'
        ]));

        $arTemplateParameters['INTEREST_PRODUCTS_PROPERTY'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_PROPERTY'),
            'TYPE' => 'LIST',
            'VALUES' => $arFields->asArray(function ($iIndex, $arField) {
                return [
                    'key' => $arField['FIELD_NAME'],
                    'value' => $arField['FIELD_NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['INTEREST_PRODUCTS_RANDOM_FILLING'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_RANDOM_FILLING'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['INTEREST_PRODUCTS_PARENT_ELEMENTS_USE'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_PARENT_ELEMENTS_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['INTEREST_PRODUCTS_ADDITION_COUNT_USE'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_ADDITION_COUNT_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['INTEREST_PRODUCTS_ADDITION_COUNT_USE'] === 'Y') {
            $arTemplateParameters['INTEREST_PRODUCTS_ADDITION_COUNT'] = [
                'PARENT' => 'LIST_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCTS_ADDITION_COUNT'),
                'TYPE' => 'STRING'
            ];
        }

        $sComponent = 'bitrix:catalog.section';
        $sTemplate = 'products.small.6';
        $sPrefix = 'INTEREST_PRODUCTS_';

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
            $arRecommendedCommonParameters = [
                'SETTINGS_USE',
                'LAZYLOAD_USE'
            ];

            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                $sComponent,
                $sTemplate,
                $siteTemplate,
                $arCurrentValues,
                $sPrefix,
                function ($key, &$arParameter) use (&$arRecommendedCommonParameters) {
                    if (ArrayHelper::isIn($key, $arRecommendedCommonParameters))
                        return false;

                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_INTEREST_PRODUCT').' '.$arParameter['NAME'];
                    $arParameter['PARENT'] = 'LIST_SETTINGS';

                    return true;
                },
                Component::PARAMETERS_MODE_TEMPLATE
            ));

            unset($arRecommendedCommonParameters);
        }

        unset($sTemplate, $sComponent, $sPrefix, $arTemplates);
    }
}
