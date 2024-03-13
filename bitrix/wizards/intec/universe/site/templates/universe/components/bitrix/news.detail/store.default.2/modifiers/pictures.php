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

$arResult['GALLERY'] = [];

if ($arVisual['PICTURE']['SOURCE'] === 'property') {

    $hGetPictures($arResult, [$arParams['PROPERTY_PICTURES']]);

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
                                $arItem['GALLERY'][] = $arPicture;
                            }
                        }
                    } else {
                        if ($arGallery->exists($arProperty)) {
                            $arPicture = $arGallery->get($arProperty);
                            $arPicture['SRC'] = CFile::GetFileSRC($arPicture);
                            $arItem['GALLERY'][] = $arPicture;
                        }
                    }
                }
            }
        };

        $hSetPictures($arResult, [$arParams['PROPERTY_PICTURES']]);

        unset($hSetPictures);
    }

    unset($arGallery);

} else if ($arVisual['PICTURE']['SOURCE'] === 'detail') {
    if (!empty($arResult['DETAIL_PICTURE']))
        $arResult['GALLERY'][] = $arResult['DETAIL_PICTURE'];
} else {
    if (!empty($arResult['PREVIEW_PICTURE']))
        $arResult['GALLERY'][] = $arResult['PREVIEW_PICTURE'];
}

if (empty($arResult['GALLERY']))
    $arVisual['PICTURE']['SHOW'] = false;