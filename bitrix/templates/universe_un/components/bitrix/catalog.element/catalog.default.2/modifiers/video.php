<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

$arVideoProperties = [
    $arParams['GALLERY_VIDEO_PROPERTY_URL'],
    $arParams['GALLERY_VIDEO_PROPERTY_FILE_MP4'],
    $arParams['GALLERY_VIDEO_PROPERTY_FILE_WEBM'],
    $arParams['GALLERY_VIDEO_PROPERTY_FILE_OGV']
];

$arVideoElements = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arParams['GALLERY_VIDEO_PROPERTY_LINK'],
    'VALUE'
]);

if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        $arVideoElements = ArrayHelper::merge(ArrayHelper::getValue($arOffer, [
            'PROPERTIES',
            $arParams['GALLERY_VIDEO_OFFER_PROPERTY_LINK'],
            'VALUE'
        ]), $arVideoElements);
    }

    $arVideoElements = ArrayHelper::unique($arVideoElements);
}

if (!empty($arVideoElements)) {
    $arVideoElementProperties = Arrays::fromDBResult(CIBlockElement::GetPropertyValues(
        $arParams['GALLERY_VIDEO_IBLOCK_ID'],
        ['ID' => $arVideoElements],
        false,
        $arVideoProperties
    ))
        ->indexBy('IBLOCK_ELEMENT_ID')
        ->asArray();

    $arFiles = [];

    $arFiles = array_filter(array_merge(
        array_column($arVideoElementProperties, $arParams['GALLERY_VIDEO_PROPERTY_FILE_MP4']),
        array_column($arVideoElementProperties, $arParams['GALLERY_VIDEO_PROPERTY_FILE_WEBM']),
        array_column($arVideoElementProperties, $arParams['GALLERY_VIDEO_PROPERTY_FILE_OGV'])
    ));

    $arFiles = Arrays::fromDBResult(CFile::GetList(
        [],
        ['@ID' => $arFiles])
    )
        ->indexBy('ID')
        ->asArray();

    foreach ($arFiles as &$arFile) {
        $arFile['SRC'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
    }

    unset($arFile);

    $arElementVideos = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['GALLERY_VIDEO_PROPERTY_LINK'],
        'VALUE'
    ]);

    if (!empty($arElementVideos)) {
        foreach ($arElementVideos as $sVideoId) {
            $arResult['GALLERY_VIDEO']['PRODUCT'][] = [
                'LINK' => $arVideoElementProperties[$sVideoId][$arParams['GALLERY_VIDEO_PROPERTY_URL']],
                'FILE_MP4' => $arFiles[$arVideoElementProperties[$sVideoId][$arParams['GALLERY_VIDEO_PROPERTY_FILE_MP4']]],
                'FILE_WEBM' => $arFiles[$arVideoElementProperties[$sVideoId][$arParams['GALLERY_VIDEO_PROPERTY_FILE_WEBM']]],
                'FILE_OGV' => $arFiles[$arVideoElementProperties[$sVideoId][$arParams['GALLERY_VIDEO_PROPERTY_FILE_OGV']]]
            ];
        }
    }

    if (!empty($arResult['OFFERS'])) {
        foreach ($arResult['OFFERS'] as $arOffer) {
            $arResult['GALLERY_VIDEO']['OFFERS'][$arOffer['ID']] = [];
            $arOfferVideos = ArrayHelper::getValue($arOffer, [
                'PROPERTIES',
                $arParams['GALLERY_VIDEO_OFFER_PROPERTY_LINK'],
                'VALUE'
            ]);

            if (!empty($arOfferVideos)) {
                foreach ($arOfferVideos as $sOfferVideoId) {
                    $arResult['GALLERY_VIDEO']['OFFERS'][$arOffer['ID']][] = [
                        'LINK' => $arVideoElementProperties[$sOfferVideoId][$arParams['GALLERY_VIDEO_PROPERTY_URL']],
                        'FILE_MP4' => $arFiles[$arVideoElementProperties[$sOfferVideoId][$arParams['GALLERY_VIDEO_PROPERTY_FILE_MP4']]],
                        'FILE_WEBM' => $arFiles[$arVideoElementProperties[$sOfferVideoId][$arParams['GALLERY_VIDEO_PROPERTY_FILE_WEBM']]],
                        'FILE_OGV' => $arFiles[$arVideoElementProperties[$sOfferVideoId][$arParams['GALLERY_VIDEO_PROPERTY_FILE_OGV']]]
                    ];
                }
            }
        }

        unset($arOfferVideos);
    }

    unset($arVideoElementProperties, $arFiles, $arElementVideos);
}

unset($arVideoProperties, $arVideoElements);
