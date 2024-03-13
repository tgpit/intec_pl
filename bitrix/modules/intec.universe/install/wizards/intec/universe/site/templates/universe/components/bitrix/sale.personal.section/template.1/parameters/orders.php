<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$arOrdersStatuses = Arrays::fromDBResult(CSaleStatus::GetList(['SORT' => 'ASC'], [
    'LID' => LANGUAGE_ID
]))->indexBy('ID');

$arColors = [
    'green' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_STATUS_COLOR_GREEN'),
    'yellow' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_STATUS_COLOR_YELLOW'),
    'red' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_STATUS_COLOR_RED'),
    'gray' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_STATUS_COLOR_GRAY'),
    'current' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_STATUS_COLOR_CURRENT')
];

foreach ($arOrdersStatuses as &$arOrdersStatus) {
    switch ($arOrdersStatuses['ID']) {
        case 'N': { $arOrdersStatus['COLOR'] = 'green'; break; }
        case 'P': { $arOrdersStatus['COLOR'] = 'yellow'; break; }
        case 'F': { $arOrdersStatus['COLOR'] = 'gray'; break; }
        default: { $arOrdersStatus['COLOR'] = 'gray'; break; }
    }

    $arTemplateParameters['ORDERS_STATUS_COLOR_'.$arOrdersStatus['ID']] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_STATUS_COLOR', [
            '#NAME#' => $arOrdersStatus['NAME']
        ]),
        'TYPE' => 'LIST',
        'VALUES' => $arColors,
        'DEFAULT' => $arOrdersStatus['COLOR']
    ];
}

unset($arOrdersStatus);

$arTemplateParameters['ORDERS_STATUS_COLOR_PSEUDO_CANCELLED'] = [
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_PSEUDO_CANCELLED_COLOR'),
    'TYPE' => 'LIST',
    'VALUES' => $arColors,
    'DEFAULT' => 'red'
];
