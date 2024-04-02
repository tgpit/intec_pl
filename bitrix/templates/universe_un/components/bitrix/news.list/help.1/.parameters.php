<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], ['ACTIVE' => 'Y']))->indexBy('ID');
$arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = [];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['IBLOCK_DESCRIPTION_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_IBLOCK_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['BANNER_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BANNER_SHOW'] === 'Y') {
    $arTemplateParameters['BANNER_THEME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_THEME'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'dark' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_THEME_DARK'),
            'light' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_THEME_LIGHT')
        ],
        'DEFAULT' => 'dark'
    ];

    $arTemplateParameters['BANNER_TITLE_SHOW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_TITLE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BANNER_TITLE_SHOW'] === 'Y') {
        $arTemplateParameters['BANNER_TITLE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_TITLE'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];
    }

    $arTemplateParameters['BANNER_SUBTITLE_SHOW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_SUBTITLE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BANNER_SUBTITLE_SHOW'] === 'Y') {
        $arTemplateParameters['BANNER_SUBTITLE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_BANNER_SUBTITLE'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];
    }
}

if (!empty($arIBlock)) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arIBlock['ID']
    ]))->indexBy('ID');

    $hPropertyString = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['MULTIPLE'] === 'N' && !$arProperty['USER_TYPE'])
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['ELEMENT_LINK_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_ELEMENT_LINK_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['ELEMENT_LINK_USE'] === 'Y') {
        $arTemplateParameters['ELEMENT_LINK_PROPERTY'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_LIST_HELP_1_ELEMENT_LINK_PROPERTY'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertyString),
            'REFRESH' => 'Y'
        ];
    }
}
