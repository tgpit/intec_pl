<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arGallery = [];

$hGetPictures = function (&$arItem, $arProperties = []) use (&$arGallery) {
    if (empty($arItem) || empty($arProperties) || !Type::isArray($arProperties))
        return;

    foreach ($arProperties as $sProperty) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $sProperty,
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                foreach ($arProperty as $sValue)
                    $arGallery[] = $sValue;
            else
                $arGallery[] = $arProperty;
        }
    }
};

$arResult['GALLERY'] = [
    'PROPERTIES' => [
        'ALT' => $arResult['NAME'],
        'TITLE' => $arResult['NAME'],
    ],
    'VALUES' => []
];

if (!empty($arResult['DETAIL_PICTURE'])) {
    $arResult['GALLERY']['VALUES'][] = $arResult['DETAIL_PICTURE'];

    if (!empty($arResult['DETAIL_PICTURE']['ALT']))
        $arResult['GALLERY']['PROPERTIES']['ALT'] = $arResult['DETAIL_PICTURE']['ALT'];

    if (!empty($arResult['DETAIL_PICTURE']['TITLE']))
        $arResult['GALLERY']['PROPERTIES']['TITLE'] = $arResult['DETAIL_PICTURE']['TITLE'];

} else if (!empty($arResult['PREVIEW_PICTURE'])) {
    $arResult['GALLERY']['VALUES'][] = $arResult['PREVIEW_PICTURE'];

    if (!empty($arResult['PREVIEW_PICTURE']['ALT']))
        $arResult['GALLERY']['PROPERTIES']['ALT'] = $arResult['PREVIEW_PICTURE']['ALT'];

    if (!empty($arResult['PREVIEW_PICTURE']['TITLE']))
        $arResult['GALLERY']['PROPERTIES']['TITLE'] = $arResult['PREVIEW_PICTURE']['TITLE'];

}

$hGetPictures($arResult, [$arParams['PROPERTY_PICTURES']]);

if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as &$arOffer) {
        $arOffer['GALLERY'] = [
            'PROPERTIES' => [
                'ALT' => $arResult['GALLERY']['PROPERTIES']['ALT'],
                'TITLE' => $arResult['GALLERY']['PROPERTIES']['TITLE'],
            ],
            'VALUES' => []
        ];

        if (!empty($arOffer['DETAIL_PICTURE']))
            $arOffer['GALLERY']['VALUES'][] = $arOffer['DETAIL_PICTURE'];
        else if (!empty($arOffer['PREVIEW_PICTURE']))
            $arOffer['GALLERY']['VALUES'][] = $arOffer['PREVIEW_PICTURE'];

        $hGetPictures($arOffer, [$arParams['OFFERS_PROPERTY_PICTURES']]);
    }

    unset($arOffer);
}

unset($hGetPictures);

if (!empty($arGallery))
    $arGallery = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arGallery)
    ]))->indexBy('ID');
else
    $arGallery = Arrays::from([]);

if (!$arGallery->isEmpty()) {
    $hSetPictures = function (&$arItem, $arProperties = []) use (&$arGallery) {
        if (empty($arItem) || empty($arProperties) || !Type::isArray($arProperties))
            return;

        foreach ($arProperties as $sProperty) {
            $arProperty = ArrayHelper::getValue($arItem, [
                'PROPERTIES',
                $sProperty,
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty)) {
                    foreach ($arProperty as $sValue) {
                        if ($arGallery->exists($sValue)) {
                            $arPicture = $arGallery->get($sValue);
                            $arPicture['SRC'] = CFile::GetFileSRC($arPicture);
                            $arItem['GALLERY']['VALUES'][] = $arPicture;
                        }
                    }
                } else {
                    if ($arGallery->exists($arProperty)) {
                        $arPicture = $arGallery->get($arProperty);
                        $arPicture['SRC'] = CFile::GetFileSRC($arPicture);
                        $arItem['GALLERY']['VALUES'][] = $arPicture;
                    }
                }
            }
        }
    };

    $hSetPictures($arResult, [$arParams['PROPERTY_PICTURES']]);

    if (!empty($arResult['OFFERS'])) {
        foreach ($arResult['OFFERS'] as &$arOffer)
            $hSetPictures($arOffer, [$arParams['OFFERS_PROPERTY_PICTURES']]);

        unset($arOffer);
    }

    unset($hSetPictures);
}

unset($arGallery);