<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock\Component\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\Json;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $iBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite,
        'TYPE' => $arCurrentValues['IBLOCK_TYPE']
    ]));
} else {
    $iBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite
    ]));
}

$arParameters = [];

$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $iBlocks->asArray(function ($key, $value) {
        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

unset($iBlocks);

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    if ($arCurrentValues['MODE'] === 'code') {
        $mode = 'CODE';
    } else {
        $mode = 'ID';
    }

    $arElements = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy($mode);

    $arParameters['ELEMENTS_COUNT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_ELEMENTS_COUNT'),
        'TYPE' => 'STRING'
    ];
    $arParameters['MODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'id' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_MODE_ID'),
            'code' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_MODE_CODE')
        ],
        'DEFAULT' => 'id',
        'REFRESH' => 'Y'
    ];

    $arParameters['ELEMENTS'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_ELEMENTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arElements->asArray(function ($key, $value) use ($mode) {
            return [
                'key' => $value[$mode],
                'value' => '['.$value[$mode].'] '.$value['NAME']
            ];
        }),
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $orderItems = [];

    if (!empty($arCurrentValues['ELEMENTS']))
        $arCurrentValues['ELEMENTS'] = array_filter($arCurrentValues['ELEMENTS']);

    if (!empty($arCurrentValues['ELEMENTS'])) {
        foreach ($arCurrentValues['ELEMENTS'] as $element) {
            if (empty($element))
                continue;

            if ($arElements->exists($element)) {
                $currentElement = $arElements->get($element);
                $orderItems[$currentElement['ID']] = $currentElement['NAME'];
            }
        }

        unset($element);
    }

    if (!empty($orderItems)) {
        $arParameters['ELEMENTS_ORDER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_ELEMENTS_ORDER'),
            'TYPE' => 'CUSTOM',
            'JS_FILE' => Base::getSettingsScript(
                '/bitrix/components/intec.universe/main.panel',
                'dragdrop_order'
            ),
            'JS_EVENT' => 'initDraggableOrderControl',
            'JS_DATA' => Json::encode($orderItems, 320, true),
            'DEFAULT' => implode(',', $orderItems)
        ];
    }
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetElementSortFields(),
    'DEFAULT' => 'SORT'
];
$arParameters['ORDER_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_MAIN_PANEL_PARAMETERS_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];
$arParameters['CACHE_TIME'] = [];

$arComponentParameters = [
    'GROUPS' => [
        'SORT' => [
            'NAME' => Loc::getMessage('C_MAIN_PANEL_GROUPS_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];