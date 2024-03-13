<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
    <?php

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arFiles = [];

$hCollect = function (&$arItem) use (&$arFiles) {
    if (!empty($arItem['PREVIEW_PICTURE']) && !Type::isArray($arItem['PREVIEW_PICTURE']))
        $arFiles[] = $arItem['PREVIEW_PICTURE'];

    if (!empty($arItem['DETAIL_PICTURE']) && !Type::isArray($arItem['DETAIL_PICTURE']))
        $arFiles[] = $arItem['DETAIL_PICTURE'];
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $hCollect($arItem);

    if (!empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer)
            $hCollect($arOffer);

    unset($arOffer);
}

unset($arItem);

if (!empty($arFiles)) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles)
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = Arrays::from([]);
}

$hSet = function (&$arItem) use (&$arFiles, &$arVisual) {
    $arItem['PICTURE'] = [
        'SHOW' => false,
        'VALUE' => []
    ];

    if (!empty($arItem['PREVIEW_PICTURE']) && !Type::isArray($arItem['PREVIEW_PICTURE']))
        $arItem['PREVIEW_PICTURE'] = $arFiles->get($arItem['PREVIEW_PICTURE']);

    if (!empty($arItem['DETAIL_PICTURE']) && !Type::isArray($arItem['DETAIL_PICTURE']))
        $arItem['DETAIL_PICTURE'] = $arFiles->get($arItem['DETAIL_PICTURE']);

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        $arItem['PICTURE']['VALUE'] = $arItem['PREVIEW_PICTURE'];
        $arItem['PICTURE']['SHOW'] = $arVisual['PICTURE']['SHOW'];
    } else if (!empty($arItem['DETAIL_PICTURE'])) {
        $arItem['PICTURE']['VALUE'] = $arItem['DETAIL_PICTURE'];
        $arItem['PICTURE']['SHOW'] = $arVisual['PICTURE']['SHOW'];
    }
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $hSet($arItem);

    if (!empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer)
            $hSet($arOffer);

    unset($arOffer);
}

unset($arItem);

unset($arFiles, $hCollect, $hSet);