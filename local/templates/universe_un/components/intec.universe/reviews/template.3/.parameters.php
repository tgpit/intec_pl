<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyListSingle = function($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] == 'L' && $arValue['LIST_TYPE'] == 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyListSingle = $arProperties->asArray($hPropertyListSingle);

    $arTemplateParameters['SETTINGS_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_SETTINGS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
        $arTemplateParameters['LAZYLOAD_USE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_LAZYLOAD_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTIES_DISPLAY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_PROPERTIES_DISPLAY'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function($key, $arValue) {
            if (!empty($arValue['CODE']))
                return [
                    'key' => $arValue['CODE'],
                    'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'MULTIPLE' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (Type::isArray($arCurrentValues['PROPERTIES_DISPLAY']))
        $arCurrentValues['PROPERTIES_DISPLAY'] = array_filter($arCurrentValues['PROPERTIES_DISPLAY']);

    if ($arCurrentValues['FORM_USE'] === 'Y') {
        if (Type::isArray($arCurrentValues['PROPERTIES_DISPLAY']))
            $arFormFields = array_filter($arCurrentValues['PROPERTY_FORM_FIELDS']);

        $arTemplateParameters['FORM_SUBMIT_TEXT'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_FORM_SUBMIT_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_FORM_SUBMIT_TEXT_DEFAULT')
        ];

        if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
            $arTemplateParameters['CONSENT_SHOW'] = [
                'PARENT' => 'FORM',
                'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_CONSENT_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];
        }

        if ($arCurrentValues['CONSENT_SHOW'] === 'Y' || $arCurrentValues['SETTINGS_USE'] === 'Y') {
            $arTemplateParameters['CONSENT_URL'] = [
                'PARENT' => 'FORM',
                'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_CONSENT_URL'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_CONSENT_URL_DEFAULT')
            ];
        }
    }

    if ($arCurrentValues['ITEMS_HIDE'] !== 'Y') {
        $arTemplateParameters['PICTURE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_PICTURE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
            $arTemplateParameters['PICTURE_VIEW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_PICTURE_VIEW'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'rounded' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_PICTURE_VIEW_ROUNDED'),
                    'squared' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_PICTURE_VIEW_SQUARED')
                ],
                'DEFAULT' => 'rounded'
            ];
        }

        $arTemplateParameters['DATE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_DATE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['DATE_SHOW'] === 'Y') {
            $arTemplateParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
                Loc::getMessage('C_REVIEWS_TEMPLATE_3_DATE_FORMAT'),
                'VISUAL'
            );
        }

        if (Type::isArray($arCurrentValues['PROPERTIES_DISPLAY']) && !empty(array_filter($arCurrentValues['PROPERTIES_DISPLAY']))) {
            $arTemplateParameters['PROPERTIES_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_3_PROPERTIES_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];
        }
    }
}