<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['PROPERTY_TEXT_SOURCE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_PROPERTY_TEXT_SOURCE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'preview' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_PROPERTY_TEXT_SOURCE_PREVIEW'),
            'detail' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_PROPERTY_TEXT_SOURCE_DETAIL')
        ],
        'DEFAULT' => 'preview'
    ];
}

$arTemplateParameters['VIEW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_VIEW_1'),
        '2' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_VIEW_2')
    ],
    'DEFAULT' => '1',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VIEW'] === '1') {
    $arTemplateParameters['COLUMNS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_STAGES_TEMPLATE_5_COLUMNS'),
        'TYPE' => 'LIST',
        'VALUES' => [
            '2' => '2',
            '3' => '3',
            '4' => '4'
        ],
        'DEFAULT' => '4'
    ];
}