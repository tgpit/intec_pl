<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\base\Collection;
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
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PROPERTY_PICTURE_BACK' => null,
    'PROPERTY_PICTURE_FRONT' => null,
    'PROPERTY_SCHEME' => null,
    'DESKTOP_BLOCK_JOINTLY' => 'N',
    'SLIDER_NAV' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTOPLAY' => 'N',
    'SLIDER_AUTOPLAY_TIME' => 10000,
    'SLIDER_AUTOPLAY_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'JOINTLY' => $arParams['DESKTOP_BLOCK_JOINTLY'] === 'Y',
    'PICTURE' => false,
    'SLIDER' => [
        'NAV' => [
            'SHOW' => $arParams['SLIDER_NAV'] === 'Y'
        ],
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTOPLAY'] === 'Y',
            'TIME' => !empty($arParams['SLIDER_AUTOPLAY_TIME']) ? Type::toInteger($arParams['SLIDER_AUTOPLAY_TIME']) : 10000,
            'HOVER' => $arParams['SLIDER_AUTOPLAY_HOVER'] === 'Y'
        ]
    ]
];

$arPictures = Collection::from([]);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'SCHEME' => 'black',
        'PICTURE' => [
            'FRONT' => [
                'SHOW' => false,
                'VALUE' => null
            ],
            'BACK' => [
                'SHOW' => false,
                'VALUE' => null
            ]
        ]
    ];

    if (!empty($arParams['PROPERTY_SCHEME'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_SCHEME'],
            'VALUE'
        ]);

        if ($arVisual['JOINTLY'] && !empty($arProperty) && !Type::isArray($arProperty))
            $arItem['DATA']['SCHEME'] = 'white';
    }

    if (!empty($arParams['PROPERTY_PICTURE_FRONT'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PICTURE_FRONT'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (!empty($arProperty) && !$arPictures->has($arProperty)) {
                $arPictures->add($arProperty);

                $arItem['DATA']['PICTURE']['FRONT']['VALUE'] = $arProperty;
                $arItem['DATA']['PICTURE']['FRONT']['SHOW'] = true;
            }
        }
    }

    if (!empty($arParams['PROPERTY_PICTURE_BACK'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PICTURE_BACK'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (!empty($arProperty) && !$arPictures->has($arProperty)) {
                $arPictures->add($arProperty);

                $arItem['DATA']['PICTURE']['BACK']['VALUE'] = $arProperty;
                $arItem['DATA']['PICTURE']['BACK']['SHOW'] = true;
            }
        }
    }

    if (!$arItem['DATA']['PICTURE']['BACK']['SHOW'])
        $arItem['DATA']['SCHEME'] = 'black';
}

unset($arItem);

if (!$arPictures->isEmpty()) {
    $arPictures = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arPictures->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arPictures = Arrays::from([]);
}

if (!$arPictures->isEmpty()) {
    $arVisual['PICTURE'] = true;

    foreach ($arResult['ITEMS'] as &$arItem) {
        $arItemPictureFront = $arItem['DATA']['PICTURE']['FRONT']['VALUE'];

        if (!empty($arItemPictureFront) && $arPictures->exists($arItemPictureFront))
            $arItem['DATA']['PICTURE']['FRONT']['VALUE'] = $arPictures->get($arItemPictureFront);

        unset($arItemPictureFront);

        $arItemPictureBack = $arItem['DATA']['PICTURE']['BACK']['VALUE'];

        if (!empty($arItemPictureBack) && $arPictures->exists($arItemPictureBack))
            $arItem['DATA']['PICTURE']['BACK']['VALUE'] = $arPictures->get($arItemPictureBack);

        unset($arItemPictureBack);
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual, $arPictures, $arItem);
