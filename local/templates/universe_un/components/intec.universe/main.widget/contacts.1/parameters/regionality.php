<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\regionality\platform\iblock\properties\RegionProperty;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var Arrays $arProperties
 */

if (!Loader::includeModule('intec.regionality'))
    return;

$arTemplateParameters['REGIONALITY_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_REGIONALITY_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['REGIONALITY_USE'] === 'Y') {
    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arTemplateParameters['REGIONALITY_FILTER_USE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_REGIONALITY_FILTER_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['REGIONALITY_FILTER_USE'] === 'Y') {
            $arTemplateParameters['REGIONALITY_FILTER_STRICT'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_REGIONALITY_FILTER_STRICT'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
            $arTemplateParameters['REGIONALITY_FILTER_PROPERTY'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_REGIONALITY_FILTER_PROPERTY'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray(function ($key, $value) {
                    if ($value['PROPERTY_TYPE'] === RegionProperty::PROPERTY_TYPE && $value['USER_TYPE'] === RegionProperty::USER_TYPE)
                        return [
                            'key' => $key,
                            'value' => '['.$key.'] '.$value['NAME']
                        ];

                    return ['skip' => true];
                }),
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }
    }
}