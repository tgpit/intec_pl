<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arResult['GALLERY'] = [
    'PROPERTIES' => [
        'ALT' => $arResult['NAME'],
        'TITLE' => $arResult['NAME']
    ],
    'VALUES' => []
];

if (!empty($arResult['DETAIL_PICTURE'])) {
    if (!empty($arResult['DETAIL_PICTURE']['ALT']))
        $arResult['GALLERY']['PROPERTIES']['ALT'] = $arResult['DETAIL_PICTURE']['ALT'];

    if (!empty($arResult['DETAIL_PICTURE']['TITLE']))
        $arResult['GALLERY']['PROPERTIES']['TITLE'] = $arResult['DETAIL_PICTURE']['TITLE'];

    $arResult['GALLERY']['VALUES'][$arResult['DETAIL_PICTURE']['ID']] = $arResult['DETAIL_PICTURE'];
} else if (!empty($arResult['PREVIEW_PICTURE'])) {
    if (!empty($arResult['PREVIEW_PICTURE']['ALT']))
        $arResult['GALLERY']['PROPERTIES']['ALT'] = $arResult['PREVIEW_PICTURE']['ALT'];

    if (!empty($arResult['PREVIEW_PICTURE']['TITLE']))
        $arResult['GALLERY']['PROPERTIES']['TITLE'] = $arResult['PREVIEW_PICTURE']['TITLE'];

    $arResult['GALLERY']['VALUES'][$arResult['PREVIEW_PICTURE']['ID']] = $arResult['PREVIEW_PICTURE'];
}

if (!empty($arParams['PROPERTY_PICTURES'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_PICTURES'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (!Type::isArray($arProperty))
            $arProperty = [$arProperty];

        $arProperty = Arrays::fromDBResult(CFile::GetList([], [
            '@ID' => implode(',', $arProperty)
        ]))->indexBy('ID');

        if (!$arProperty->isEmpty()) {
            $arResult['GALLERY']['VALUES'] = ArrayHelper::merge(
                $arResult['GALLERY']['VALUES'],
                $arProperty->asArray(function ($key, $file) {
                    $file['SRC'] = CFile::GetFileSRC($file);

                    return [
                        'key' => $file['ID'],
                        'value' => $file
                    ];
                })
            );
        }
    }
}