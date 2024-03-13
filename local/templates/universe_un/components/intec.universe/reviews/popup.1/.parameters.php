<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_REVIEWS_POPUP_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID']) && $arCurrentValues['FORM_USE'] === 'Y') {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyListSingle = function($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'L' && $value['LIST_TYPE'] == 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyListSingle = $arProperties->asArray($hPropertyListSingle);

    if (Type::isArray($arCurrentValues['PROPERTY_FORM_FIELDS']))
        $arFields = array_filter($arCurrentValues['PROPERTY_FORM_FIELDS']);

    $arFieldsRating = [];

    if (!empty($arFields)) {
        foreach ($arFields as $sFormField) {
            if (ArrayHelper::keyExists($sFormField, $arPropertyListSingle))
                $arFieldsRating[$sFormField] = $arPropertyListSingle[$sFormField];
        }
    }

    $arTemplateParameters['PROPERTY_RATING'] = [
        'PARENT' => 'FORM',
        'NAME' => Loc::getMessage('C_REVIEWS_POPUP_1_PROPERTY_RATING'),
        'TYPE' => 'LIST',
        'VALUES' => $arFieldsRating,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_RATING'])) {
        $arTemplateParameters['RATING_USE'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_POPUP_1_RATING_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
        $arTemplateParameters['CONSENT_SHOW'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_POPUP_1_CONSENT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
    }

    if ($arCurrentValues['CONSENT_SHOW'] === 'Y' || $arCurrentValues['SETTINGS_USE'] === 'Y') {
        $arTemplateParameters['CONSENT_URL'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_POPUP_1_CONSENT_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#SITE_DIR#company/consent/'
        ];
    }

    $arTemplateParameters['FORM_SUBMIT_TEXT'] = [
        'PARENT' => 'FORM',
        'NAME' => Loc::getMessage('C_REVIEWS_POPUP_1_FORM_SUBMIT_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_REVIEWS_POPUP_1_FORM_SUBMIT_TEXT_DEFAULT')
    ];
}