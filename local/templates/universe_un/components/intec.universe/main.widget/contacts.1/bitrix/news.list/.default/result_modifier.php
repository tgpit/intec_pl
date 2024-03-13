<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\RegExp;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;
use intec\regionality\models\Region;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'STAFF_IBLOCK_TYPE' => null,
    'STAFF_IBLOCK_ID' => null,
    'LAZYLOAD_USE' => 'N',
    'MODE' => 'ID',
    'MAP_VENDOR' => 'google',
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_TITLE' => null,
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => null,
    'MAIN' => null,
    'PROPERTY_MAP' => null,
    'PROPERTY_PHONE' => null,
    'PROPERTY_ADDRESS' => null,
    'STAFF_PERSON' => null,
    'PHONE_SHOW' => 'N',
    'ADDRESS_SHOW' => 'N',
    'FEEDBACK_SHOW' => 'N',
    'FEEDBACK_TEXT' => null,
    'FEEDBACK_BUTTON_TEXT' => null,
    'STAFF_SHOW' => 'N',
    'STAFF_DEFAULT' => null,
    'MAP_GRAY' => 'N'
], $arParams);

$mapId = $arParams['MAP_ID'];
$mapIdLength = StringHelper::length($mapId);
$mapIdExpression = new RegExp('^[A-Za-z_][A-Za-z01-9_]*$');

if ($mapIdLength <= 0 || $mapIdExpression->isMatch($mapId))
    $arParams['MAP_ID'] = 'MAP_'.RandString();

unset($mapId, $mapIdLength, $mapIdExpression);

$arVisual = [
    'MODE' => ArrayHelper::fromRange([
        'ID',
        'CODE'
    ], $arParams['MODE']),
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'MAP' => [
        'ID' => $arParams['MAP_ID'],
        'VENDOR' => ArrayHelper::fromRange([
            'google',
            'yandex'
        ], $arParams['MAP_VENDOR']),
        'GRAY' => $arParams['MAP_GRAY']
    ],
    'CONSENT' => [
        'SHOW' => $arParams['CONSENT_SHOW'] === 'Y' && !empty($arParams['CONSENT_URL']),
        'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], [
            'SITE_DIR' => SITE_DIR
        ])
    ],
    'PHONE' => [
        'SHOW' => $arParams['PHONE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PHONE'])
    ],
    'ADDRESS' => [
        'SHOW' => $arParams['ADDRESS_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ADDRESS'])
    ],
    'FEEDBACK' => [
        'SHOW' => $arParams['FEEDBACK_SHOW'] === 'Y',
        'TEXT' => $arParams['FEEDBACK_TEXT'],
        'BUTTON' => [
            'TEXT' => $arParams['FEEDBACK_BUTTON_TEXT']
        ]
    ],
    'STAFF' => [
        'SHOW' => $arParams['STAFF_SHOW'] === 'Y',
        'DEFAULT' => StringHelper::replaceMacros($arParams['STAFF_DEFAULT'], [
            'SITE_DIR' => SITE_DIR,
            'TEMPLATE_PATH' => $this->GetFolder().'/',
            'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
        ])
    ],
    'REGIONALITY' => [
        'USE' => Loader::includeModule('intec.regionality') && $arParams['REGIONALITY_USE'] === 'Y',
        'FILTER' => $arParams['REGIONALITY_FILTER_PROPERTY'],
        'STRICT' => $arParams['REGIONALITY_FILTER_STRICT']
    ]
];

$arResult['MAIN'] = null;
$arResult['DATA'] = [
    'COUNT' => 0,
    'SLIDER' => [
        'USE' => false
    ],
    'STAFF' => [
        'SHOW' => false,
        'IMAGE' => $arVisual['STAFF']['DEFAULT']
    ],
    'FORM' => [
        'SHOW' => !empty($arParams['FORM_ID']),
        'ID' => $arParams['FORM_ID'],
        'TEMPLATE' => !empty($arParams['FORM_TEMPLATE']) ? $arParams['FORM_TEMPLATE'] : '.default',
        'TITLE' => $arParams['FORM_TITLE']
    ],
    'FEEDBACK' => [
        'SHOW' => false,
        'TEXT' => [
            'SHOW' => !empty($arVisual['FEEDBACK']['TEXT']),
            'VALUE' => $arVisual['FEEDBACK']['TEXT']
        ]
    ]
];

if (!empty($arParams['STAFF_IBLOCK_ID']) && !empty($arParams['STAFF_PERSON'])) {
    $arStaff = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
        'IBLOCK_TYPE' => $arParams['STAFF_IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['STAFF_IBLOCK_ID'],
        $arParams['MODE'] => [$arParams['STAFF_PERSON']]
    ]))->indexBy($arParams['MODE']);

    if ($arStaff->exists($arParams['STAFF_PERSON'])) {
        $arStaff = $arStaff->get($arParams['STAFF_PERSON']);

        $arImage = null;

        if (!empty($arStaff['PREVIEW_PICTURE']))
            $arImage = $arStaff['PREVIEW_PICTURE'];
        else if (!empty($arStaff['DETAIL_PICTURE']))
            $arImage = $arStaff['DETAIL_PICTURE'];

        if (!empty($arImage)) {
            $arImage = Arrays::fromDBResult(CFile::GetByID($arImage))->getFirst();

            if (!empty($arImage)) {
                $arImage = CFile::ResizeImageGet($arImage, [
                    'width' => 112,
                    'height' => 112
                ], BX_RESIZE_IMAGE_EXACT);

                if (!empty($arImage['src']))
                    $arResult['DATA']['STAFF']['IMAGE'] = $arImage['src'];
            }
        }

        unset($arImage);
    }

    unset($arStaff);
}

if (!empty($arResult['DATA']['STAFF']['IMAGE']))
    $arResult['DATA']['STAFF']['SHOW'] = $arVisual['STAFF']['SHOW'];

if ($arResult['DATA']['FEEDBACK']['TEXT']['SHOW'] || $arResult['DATA']['FORM']['SHOW'])
    $arResult['DATA']['FEEDBACK']['SHOW'] = $arVisual['FEEDBACK']['SHOW'];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'MAP' => [
            'SHOW' => false,
            'RAW' => null,
            'VALUES' => [
                'LAT' => null,
                'LON' => null
            ]
        ],
        'PHONE' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'ADDRESS' => [
            'SHOW' => false,
            'VALUE' => null
        ],
    ];

    if (!empty($arParams['PROPERTY_MAP'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_MAP'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            $arItem['DATA']['MAP']['RAW'] = $arProperty;

            $arProperty = explode(',', $arProperty);

            $arItem['DATA']['MAP']['VALUES']['LAT'] = $arProperty[0];
            $arItem['DATA']['MAP']['VALUES']['LON'] = $arProperty[1];

            if (!empty($arItem['DATA']['MAP']['VALUES']['LAT']) && !empty($arItem['DATA']['MAP']['VALUES']['LAT'])) {
                $arItem['DATA']['MAP']['SHOW'] = true;
                $arResult['DATA']['COUNT']++;
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_PHONE'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_PHONE']);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PHONE']['SHOW'] = $arVisual['PHONE']['SHOW'];
                $arItem['DATA']['PHONE']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_ADDRESS'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_ADDRESS']);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['ADDRESS']['SHOW'] = $arVisual['ADDRESS']['SHOW'];
                $arItem['DATA']['ADDRESS']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (empty($arResult['MAIN'])) {
        if (empty($arParams['MAIN'])) {
            $arResult['MAIN'] = &$arItem;
        } else {
            if ($arItem[$arParams['MODE']] === $arParams['MAIN'] && empty($arResult['MAIN']))
                $arResult['MAIN'] = &$arItem;
        }
    }
}

unset($arItem);

if ($arParams['REGIONALITY']['USE'] === 'Y' && !empty($arVisual['REGIONALITY']['FILTER'])) {
    $oRegion = Region::getCurrent();

    foreach ($arResult['ITEMS'] as &$arItem) {
        if (!empty($arItem['PROPERTIES'][$arVisual['REGIONALITY']['FILTER']]['VALUE'])) {
            $arContactRegions = Type::isArray($arItem['PROPERTIES'][$arVisual['REGIONALITY']['FILTER']]['VALUE']) ? $arItem['PROPERTIES'][$arVisual['REGIONALITY']['FILTER']]['VALUE'] : [$arItem['PROPERTIES'][$arVisual['REGIONALITY']['FILTER']]['VALUE']];

            if (ArrayHelper::isIn($oRegion->id, $arContactRegions)) {
                $arResult['MAIN'] = &$arItem;
                break;
            }

            unset($arContactRegions);
        }
    }

    unset($arItem);
}

if ($arResult['DATA']['COUNT'] > 1)
    $arResult['DATA']['SLIDER']['USE'] = true;

if (empty($arResult['MAIN'])) {
    foreach ($arResult['ITEMS'] as &$arItem) {
        $arResult['MAIN'] = &$arItem;

        break;
    }

    unset($arItem);
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);