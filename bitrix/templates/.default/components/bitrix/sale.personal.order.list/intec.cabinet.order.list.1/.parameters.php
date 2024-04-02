<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('sale'))
    return;


$arOrdersStatuses = Arrays::fromDBResult(CSaleStatus::GetList(['SORT' => 'ASC'], [
    'LID' => LANGUAGE_ID
]))->indexBy('ID');

$arColors = [
    'green' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_STATUS_COLOR_GREEN'),
    'yellow' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_STATUS_COLOR_YELLOW'),
    'red' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_STATUS_COLOR_RED'),
    'gray' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_STATUS_COLOR_GRAY'),
    'current' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_STATUS_COLOR_CURRENT')
];

foreach ($arOrdersStatuses as &$arOrdersStatus) {
    switch ($arOrdersStatuses['ID']) {
        case 'N': { $arOrdersStatus['COLOR'] = 'green'; break; }
        case 'P': { $arOrdersStatus['COLOR'] = 'yellow'; break; }
        case 'F': { $arOrdersStatus['COLOR'] = 'gray'; break; }
        default: { $arOrdersStatus['COLOR'] = 'gray'; break; }
    }

    $arTemplateParameters['STATUS_COLOR_'.$arOrdersStatus['ID']] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_STATUS_COLOR', [
            '#NAME#' => $arOrdersStatus['NAME']
        ]),
        'TYPE' => 'LIST',
        'VALUES' => $arColors,
        'DEFAULT' => $arOrdersStatus['COLOR']
    ];
}

unset($arOrdersStatus);

$arTemplateParameters['STATUS_COLOR_PSEUDO_CANCELLED'] = [
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_PSEUDO_CANCELLED_COLOR'),
    'TYPE' => 'LIST',
    'VALUES' => $arColors,
    'DEFAULT' => 'red'
];

$arTemplateParameters['USE_SEARCH'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_USE_SEARCH'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

$arTemplateParameters['USE_FILTER'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_USE_FILTER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['USE_SEARCH'] != 'Y' && $arCurrentValues['USE_FILTER'] != 'Y') {
    $arTemplateParameters['SHOW_ONLY_CURRENT_ORDERS'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_SHOW_ONLY_CURRENT_ORDERS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['CURRENT_ORDERS_LINK'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_CURRENT_ORDERS_LINK'),
        'TYPE' => 'STRING'
    ];
}
