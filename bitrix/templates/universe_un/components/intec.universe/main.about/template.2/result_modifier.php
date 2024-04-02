<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'ADVANTAGES_IBLOCK_TYPE' => null,
    'ADVANTAGES_IBLOCK_ID' => null,
    'PROPERTY_TITLE' => null,
    'PROPERTY_LINK' => null,
    'PROPERTY_VIDEO' => null,
    'PROPERTY_ADVANTAGES' => null,
    'ADVANTAGES_PROPERTY_SVG_FILE' => null,
    'VIEW' => '1',
    'TITLE_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'BUTTON_SHOW' => 'N',
    'BUTTON_VIEW' => '1',
    'BUTTON_BLANK' => 'N',
    'BUTTON_TEXT' => null,
    'PICTURE_SHOW' => 'N',
    'PICTURE_SIZE' => 'auto',
    'POSITION_HORIZONTAL' => 'center',
    'POSITION_VERTICAL' => 'center',
    'VIDEO_SHOW' => 'N',
    'ADVANTAGES_SHOW' => 'N',
    'SVG_FILE_USE' => 'N',
    'ADVANTAGES_COLUMNS' => '2',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'VIEW' => Type::toInteger(ArrayHelper::fromRange(['1', '2', '3'], $arParams['VIEW'])),
    'TITLE' => [
        'SHOW' => !empty($arParams['PROPERTY_TITLE']) && $arParams['TITLE_SHOW'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => !empty($arResult['ITEM']['PREVIEW_TEXT']) && $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'BUTTON' => [
        'SHOW' => !empty($arParams['PROPERTY_LINK']) && $arParams['BUTTON_SHOW'] === 'Y',
        'VIEW' => Type::toInteger(ArrayHelper::fromRange(['1', '2'], $arParams['BUTTON_VIEW'])),
        'BLANK' => $arParams['BUTTON_BLANK'] === 'Y',
        'TEXT' => $arParams['BUTTON_TEXT']
    ],
    'PICTURE' => [
        'SHOW' => !empty($arResult['PICTURE']) && $arParams['PICTURE_SHOW'] === 'Y',
        'SIZE' => ArrayHelper::fromRange(['auto', 'cover', 'contain'], $arParams['PICTURE_SIZE']),
        'POSITION' => [
            'HORIZONTAL' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['POSITION_HORIZONTAL']),
            'VERTICAL' => ArrayHelper::fromRange(['center', 'top', 'bottom'], $arParams['POSITION_VERTICAL'])
        ]
    ],
    'VIDEO' => [
        'SHOW' => !empty($arParams['PROPERTY_VIDEO']) && $arParams['VIDEO_SHOW'] === 'Y'
    ],
    'ADVANTAGES' => [
        'SHOW' => !empty($arParams['ADVANTAGES_IBLOCK_ID']) && !empty($arParams['PROPERTY_ADVANTAGES']) && $arParams['ADVANTAGES_SHOW'] === 'Y',
        'SVG' => !empty($arParams['ADVANTAGES_PROPERTY_SVG_FILE']) && $arParams['SVG_FILE_USE'] === 'Y',
        'COLUMNS' => Type::toInteger(ArrayHelper::fromRange(['2', '3'], $arParams['ADVANTAGES_COLUMNS']))
    ]
];

if ($arVisual['VIEW'] === 2) {
    $arVisual['PICTURE']['SHOW'] = false;
    $arVisual['VIDEO']['SHOW'] = false;
}

if ($arVisual['VIEW'] === 3)
    $arVisual['ADVANTAGES']['SHOW'] = false;

$arResult['TITLE'] = null;
$arResult['LINK'] = null;
$arResult['VIDEO'] = null;
$arResult['ADVANTAGES'] = [];

if ($arVisual['TITLE']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_TITLE'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['TITLE'] = $property;
    } else {
        $arVisual['TITLE']['SHOW'] = false;
    }

    unset($property);
}

if ($arVisual['BUTTON']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_LINK'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['LINK'] = $property;
    } else {
        $arVisual['BUTTON']['SHOW'] = false;
    }

    unset($property);
}

if ($arVisual['VIDEO']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_VIDEO'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['VIDEO'] = $property;
    } else {
        $arVisual['VIDEO']['SHOW'] = false;
    }

    unset($property);
}

if ($arVisual['ADVANTAGES']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_ADVANTAGES'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (!Type::isArray($property))
            $property = [$property];

        $arAdvantages = [];
        $rsAdvantages = CIBlockElement::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $arParams['ADVANTAGES_IBLOCK_ID'],
            'ID' => $property,
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R'
        ]);

        unset($property);

        while ($element = $rsAdvantages->GetNextElement()) {
            $arElement = $element->GetFields();
            $arElement['PROPERTIES'] = $element->GetProperties();

            $arAdvantages[$arElement['ID']] = $arElement;
        }

        unset($rsAdvantages, $element, $arElement);

        if (!empty($arAdvantages)) {
            $arFiles = [];

            foreach ($arAdvantages as $arAdvantage) {
                if (!empty($arParams['ADVANTAGES_PROPERTY_SVG_FILE'])) {
                    $svg = ArrayHelper::getValue($arAdvantage, [
                        'PROPERTIES',
                        $arParams['ADVANTAGES_PROPERTY_SVG_FILE'],
                        'VALUE'
                    ]);

                    if (!empty($svg)) {
                        if (Type::isArray($svg))
                            $svg = ArrayHelper::getFirstValue($svg);

                        $arFiles[] = $svg;
                    }

                    unset($svg);
                }

                if (!empty($arAdvantage['PREVIEW_PICTURE'])) {
                    $arFiles[] = $arAdvantage['PREVIEW_PICTURE'];
                } else if (!empty($arAdvantage['DETAIL_PICTURE'])) {
                    $arFiles[] = $arAdvantage['DETAIL_PICTURE'];
                }
            }

            if (!empty($arFiles)) {
                $arFiles = Arrays::fromDBResult(CFile::GetList([], [
                    '@ID' => implode(',', $arFiles)
                ]))->indexBy('ID')->each(function ($key, &$file) {
                    $file['SRC'] = CFile::GetFileSRC($file);
                });
            } else {
                $arFiles = Arrays::from([]);
            }

            foreach ($arAdvantages as $arAdvantage) {
                $advantage = [
                    'NAME' => $arAdvantage['NAME'],
                    'PREVIEW' => $arAdvantage['PREVIEW_TEXT'],
                    'PICTURE' => [],
                    'SVG' => []
                ];

                if (!empty($arAdvantage['PREVIEW_PICTURE']) && $arFiles->exists($arAdvantage['PREVIEW_PICTURE'])) {
                    $advantage['PICTURE'] = $arFiles->get($arAdvantage['PREVIEW_PICTURE']);
                } else if (!empty($arAdvantage['DETAIL_PICTURE']) && $arFiles->exists($arAdvantage['DETAIL_PICTURE'])) {
                    $advantage['PICTURE'] = $arFiles->get($arAdvantage['DETAIL_PICTURE']);
                }

                if (!empty($arParams['ADVANTAGES_PROPERTY_SVG_FILE'])) {
                    $svg = ArrayHelper::getValue($arAdvantage, [
                        'PROPERTIES',
                        $arParams['ADVANTAGES_PROPERTY_SVG_FILE'],
                        'VALUE'
                    ]);

                    if (!empty($svg)) {
                        if (Type::isArray($svg))
                            $svg = ArrayHelper::getFirstValue($svg);

                        if ($arFiles->exists($svg))
                            $advantage['SVG'] = $arFiles->get($svg);
                    }

                    unset($svg);
                }

                $arResult['ADVANTAGES'][$arAdvantage['ID']] = $advantage;

                unset($advantage);
            }

            unset($arAdvantage);
        } else {
            $arVisual['ADVANTAGES']['SHOW'] = false;
        }

        unset($arAdvantages);
    } else {
        $arVisual['ADVANTAGES']['SHOW'] = false;
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);