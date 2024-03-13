<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::IncludeModule('intec.core'))
    return;

$sPropertyImage = ArrayHelper::getValue($arParams, 'PROPERTY_IMAGE');

/**
 * @param array $arResult
 * @return array
 */
$fBuild = function ($arResult) {
    $bFirst = true;

    if (empty($arResult))
        return [];

    $fBuild = function () use (&$fBuild, &$bFirst, &$arResult) {
        $iLevel = null;
        $arItems = array();
        $arItem = null;

        if ($bFirst) {
            $arItem = reset($arResult);
            $bFirst = false;
        }

        while (true) {
            if ($arItem === null) {
                $arItem = next($arResult);

                if (empty($arItem))
                    break;
            }

            if ($iLevel === null)
                $iLevel = $arItem['DEPTH_LEVEL'];

            if ($arItem['DEPTH_LEVEL'] < $iLevel) {
                prev($arResult);
                break;
            }

            if ($arItem['IS_PARENT'] === true)
                $arItem['ITEMS'] = $fBuild();

            $arItems[] = $arItem;
            $arItem = null;
        }

        return $arItems;
    };

    return $fBuild();
};

$arFiles = Collection::from([]);

foreach ($arResult as $sKey => $arItem) {
    $arResult[$sKey]['IMAGE'] = null;

    if (!empty($arItem['PARAMS']['SECTION'])) {
        $arSection = &$arItem['PARAMS']['SECTION'];

        if (!empty($arParams['PROPERTY_PICTURE']) && !empty($arSection[$arParams['PROPERTY_PICTURE']])) {
            $arResult[$sKey]['IMAGE'] = $arSection[$arParams['PROPERTY_PICTURE']];
        } elseif (!empty($arSection['PICTURE'])) {
            $arResult[$sKey]['IMAGE'] = $arSection['PICTURE'];
        }
    }

    if (!empty($arResult[$sKey]['IMAGE']))
        if (!$arFiles->has($arResult[$sKey]['IMAGE']))
            $arFiles->add($arResult[$sKey]['IMAGE']);
}

unset($arSection);

if (!$arFiles->isEmpty()) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles->asArray())
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

foreach ($arResult as $sKey => $arItem) {
    if (!empty($arResult[$sKey]['IMAGE']))
        $arResult[$sKey]['IMAGE'] = $arFiles->get($arResult[$sKey]['IMAGE']);
}

foreach ($arResult as &$arItem) {
    $arItem['ACTIVE'] = false;

    if ($arItem['LINK'] == $APPLICATION->GetCurPage())
        $arItem['ACTIVE'] = true;

    if ($arItem['SELECTED'])
        $bExpandedMenu = true;
}

unset($arItem);

$arResult = [
    'ELEMENTS' => $fBuild($arResult),
    'MENU' => [
        'SHOW' => $bExpandedMenu
    ],
    'VISUAL' => [
        'MAIN_MENU' => [
            'SHOW' => $arParams['MAIN_LINK_SHOW'] === 'Y',
            'TEXT' => $arParams['MAIN_LINK_TEXT'],
            'LINK' => $arParams['MAIN_LINK']
        ]
    ]
];

if (empty($arResult['VISUAL']['MAIN_MENU']['TEXT']))
    $arResult['VISUAL']['MAIN_MENU']['TEXT'] = Loc::getMessage('C_MENU_VERTICAL_4_CATALOG');

if (!empty($arParams['LOGOUT_URL'])) {
    $arResult['ELEMENTS'][] = [
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_4_LOGOUT'),
        'LINK' => $arParams['LOGOUT_URL'],
        'SELECTED' => '',
        'PERMISSION' => 'X',
        'ADDITIONAL_LINKS' => [],
        'ITEM_TYPE' => 'U',
        'ITEM_INDEX' => max(ArrayHelper::getKeys($arResult['ELEMENTS'])) + 1,
        'PARAMS' => [],
        'DEPTH_LEVEL' => 1,
        'IS_PARENT' => '',
        'IMAGE' => [
            'CONTENT_TYPE' => 'image/svg+xml',
            'SRC' => FileHelper::getFileData(__DIR__.'/svg/catalog.svg')
        ],
        'ACTIVE' => ''
    ];
}
