<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['HEADER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'TYPE' => 'CHECKBOX',
    'HIDDEN' => 'Y'
];
$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'TYPE' => 'CHECKBOX',
    'HIDDEN' => 'Y'
];
$arTemplateParameters['PICTURE_SOURCES'] = [
    'PARENT' => 'BASE',
    'TYPE' => 'LIST',
    'HIDDEN' => 'Y'
];
$arTemplateParameters['PROPERTY_LINK'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'LIST',
    'HIDDEN' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyFile = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'F' && $arProperty['LIST_TYPE'] == 'L')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };
    $arPropertyFile = $arProperties->asArray($hPropertyFile);

    $arTemplateParameters['PROPERTY_FILE_MP4'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_PROPERTY_FILE_MP4'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_FILE_WEBM'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_PROPERTY_FILE_WEBM'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_FILE_OGV'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_PROPERTY_FILE_OGV'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_POSITION_LEFT'),
        'right' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_POSITION_RIGHT')
    ],
    'DEFAULT' => 'left'
];
$arTemplateParameters['BUTTON_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_BUTTON_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BUTTON_USE'] === 'Y') {
    $arTemplateParameters['BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_BUTTON_TEXT_DEFAULT')
    ];
    $arTemplateParameters['BUTTON_DELAY_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_BUTTON_DELAY_SHOW'),
        'TYPE' => 'LIST',
        'VALUES' => [
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5'
        ],
        'DEFAULT' => 3
    ];
    $arTemplateParameters['MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'link' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_MODE_LINK'),
            'form' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_MODE_FORM'),
            'product' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_MODE_PRODUCT')
        ],
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if ($arCurrentValues['MODE'] === 'link') {
    $arTemplateParameters['BUTTON_LINK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_BUTTON_LINK'),
        'TYPE' => 'STRING'
    ];
} else if ($arCurrentValues['MODE'] === 'form') {
    if (Loader::includeModule('form'))
        include('parameters/base/forms.php');
    else if (Loader::includeModule('intec.startshop'))
        include('parameters/lite/forms.php');
    else
        return;

    $arTemplateParameters['FORM_TITLE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_TITLE_DEFAULT')
    ];
    $arTemplateParameters['FORM_CONSENT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_CONSENT'),
        'TYPE' => 'STRING'
    ];
} else if ($arCurrentValues['MODE'] === 'product') {
    include('parameters/quick.view.php');
}
