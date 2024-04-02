<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

if(!CModule::IncludeModule("iblock"))
    return;

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arParams = ArrayHelper::merge([
    'IBLOCK_TYPE' => null,
    'IBLOCK_ID' => null,
    'ELEMENT_ID' => 'N',
    'DISCOUNT_SHOW' => 'Y',
    'PROPERTY_DISCOUNT' => null,
    'DISCOUNT_MINUS_USE' => 'Y',
    'DATE_SHOW_FROM' => 'property',
    'PROPERTY_DATE' => null,
    'DATE_ONLY_ONE_SHOW' => 'N',
    'TIMER_SHOW' => 'Y',
    'TIMER_SECONDS_SHOW' => 'N',
    'TIMER_END_HIDE' => 'N',
    'TEXT_USE' => 'preview',
    'ALL_TEXT_SHOW' => 'N',
    'BUTTON_SHOW' => 'Y',
    'BUTTON_TEXT' => null
], $arParams);

if (!empty($arParams['ELEMENT_ID'])) {
    $arFilter = [
        'ID' => $arParams['ELEMENT_ID']
    ];

    $dbResult = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter);

    while($obResult = $dbResult->GetNextElement()){
        $arItem = $obResult->GetFields();
        $arItem['PROPERTIES'] = $obResult->GetProperties();
    }
}

$arVisual = [
    'DISCOUNT' => [
        'SHOW' => $arParams['DISCOUNT_SHOW'] === 'Y',
        'VALUE' => null
    ],
    'DATE' => [
        'SHOW' => false,
        'VALUE' => null
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y',
        'SECONDS' => [
            'SHOW' => $arParams['TIMER_SECONDS_SHOW'] === 'Y'
        ],
        'HIDE' => $arParams['TIMER_END_HIDE'] === 'Y',
        'DATE' => null
    ],
    'TEXT' => [
        'ALL' => $arParams['ALL_TEXT_SHOW'] === 'Y',
        'VALUE' => $arParams['TEXT_USE'] === 'preview' ? $arItem['PREVIEW_TEXT'] : $arItem['DETAIL_TEXT']
    ],
    'BUTTON' => [
        'SHOW' => $arParams['BUTTON_SHOW'] === 'Y' && !empty($arParams['BUTTON_TEXT']),
        'VALUE' => $arParams['BUTTON_TEXT']
    ],
    'STATUS' => [
        'SHOW' => false
    ]
];

if ($arVisual['DISCOUNT']['SHOW']) {
    $sDiscount = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $arParams['PROPERTY_DISCOUNT'],
        'VALUE'
    ]);

    if (!empty($sDiscount)) {
        if ($arParams['DISCOUNT_MINUS_USE'] === 'Y') {
            $arVisual['DISCOUNT']['VALUE'] = Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DISCOUNT_FORMAT_TEXT', [
                '#DISCOUNT#' => $sDiscount
            ]);
        } else {
            $arVisual['DISCOUNT']['VALUE'] = $sDiscount;
        }
    } else {
        $arVisual['DISCOUNT']['SHOW'] = false;
    }
}


if ($arParams['DATE_SHOW_FROM'] === 'property') {
    $arVisual['DATE']['VALUE'] = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $arParams['PROPERTY_DATE'],
        'VALUE'
    ]);
} else {
    $sDateFormat = $arParams['DATE_FORMAT'];

    $oDateFrom = null;
    $oDateTo = null;

    if (!empty($arItem['ACTIVE_FROM'])) {
        $oDateFrom = new DateTime($arItem['ACTIVE_FROM']);
    } elseif (!empty($arItem['DATE_CREATE'])) {
        $oDateFrom = new DateTime($arItem['DATE_CREATE']);
    }

    if (!empty($oDateFrom))
        $sDateFrom = $oDateFrom->format('d-m-Y H:i:s');

    if (!empty($arItem['ACTIVE_TO'])) {
        $oDateTo= new DateTime($arItem['ACTIVE_TO']);
        $sDateTo = $oDateTo->format('d-m-Y H:i:s');
    }

    if (!empty($sDateFormat)){
        if (!empty($oDateFrom)) {
            $sDateFrom = CIBlockFormatProperties::DateFormat(
                $sDateFormat,
                MakeTimeStamp(
                    $sDateFrom,
                    CSite::GetDateFormat()
                )
            );
        }

        if (!empty($oDateTo)) {
            $sDateTo = CIBlockFormatProperties::DateFormat(
                $sDateFormat,
                MakeTimeStamp(
                    $sDateTo,
                    CSite::GetDateFormat()
                )
            );
        }
    }

    if ($arParams['DATE_ONLY_ONE_SHOW'] === 'N' && !empty($sDateTo) || $arParams['DATE_ONLY_ONE_SHOW'] === 'Y')
        $arVisual['DATE']['VALUE'] = Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DATE_FORMAT_TEXT', [
            '#FROM#' => $sDateFrom,
            '#TO#' => $sDateTo
        ]);

    unset($sDateFrom, $sDateTo, $oDateTo, $oDateFrom);
}

if (!empty($arVisual['DATE']['VALUE'])) {
    $arVisual['DATE']['SHOW'] = true;
}

$arResult['DATA']['TIMER']['DATE'] = null;

if (empty(trim($arItem['DATE_ACTIVE_TO'])))
    if ($arVisual['TIMER']['SHOW'])
        $arVisual['TIMER']['SHOW'] = false;

if ($arVisual['TIMER']['SHOW']) {
    if (!empty($arItem['DATE_ACTIVE_TO'])) {
        $oDateEnd = New DateTime($arItem['DATE_ACTIVE_TO']);
        $oDateCurrent = New DateTime();

        $arDate = null;

        $arVisual['TIMER']['DATE'] = $oDateEnd;

        if ($oDateEnd <= $oDateCurrent && $arVisual['TIMER']['HIDE']) {
            $arVisual['TIMER']['SHOW'] = false;
        } else {
            $arDate = explode(',', $oDateEnd->format('Y,m,d,H,i,s'));

            foreach ($arDate as &$value) {
                $value = Type::toInteger($value);
            }

            $arResult['DATA']['TIMER']['DATE'] = $arDate;
        }
    } else {
        $arVisual['TIMER']['SHOW'] = false;
    }
}

if (!$arVisual['TEXT']['ALL'])
    $arVisual['TEXT']['VALUE'] = StringHelper::truncate($arVisual['TEXT']['VALUE'],1000);

if (($arVisual['DISCOUNT']['SHOW'] && !empty($arVisual['DISCOUNT']['VALUE'])) ||
    ($arVisual['TIMER']['SHOW'] && !empty($arVisual['TIMER']['DATE'])) ) {
    $arVisual['STATUS']['SHOW'] = true;
}

$arResult['ITEM'] = $arItem;
$arResult['VISUAL'] = $arVisual;

unset($arVisual, $arData);