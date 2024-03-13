<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

Loc::loadMessages(__FILE__);

$arParams = ArrayHelper::merge([
    'ACTION' => null,
    'NAME' => null,
    'LAZYLOAD_USE' => 'N',
    'BASKET_URL' => null
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR
];

$bBase = Loader::includeModule('catalog') && Loader::includeModule('sale');
$bLite = !$bBase && Loader::includeModule('intec.startshop');

$oRequest = Core::$app->request;
$arVisual = [
    'ACTION' => ArrayHelper::fromRange([
        'none',
        'buy',
        'detail'
    ], $arParams['ACTION']),
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PROPERTIES' => [
        'SHOW' => false
    ]
];

$arResult['AJAX'] = false;

if ($oRequest->getIsPost()) {
    $arResult['AJAX'] = $oRequest->post('ajax_action');

    if ($arResult['AJAX'] !== 'Y')
        $arResult['AJAX'] = $oRequest->post('compare_result_reload');

    $arResult['AJAX'] = $arResult['AJAX'] === 'Y';
}

if (!empty($arResult['ITEMS'])) {
    include(__DIR__ . '/modifiers/items.php');

    if ($bBase) {
        include(__DIR__ . '/modifiers/base/catalog.php');
    } else if ($bLite) {
        include(__DIR__ . '/modifiers/lite/catalog.php');
    }

    include(__DIR__ . '/modifiers/pictures.php');
    include(__DIR__ . '/modifiers/properties.php');

    foreach ($arResult['PROPERTIES'] as $arProperty) {
        if (!$arResult['DIFFERENT'] || $arProperty['DIFFERENT']) {
            if (!$arProperty['HIDDEN'])
                $arVisual['PROPERTIES']['SHOW'] = true;
        }

        if ($arVisual['PROPERTIES']['SHOW'])
            break;
    }

    unset($arProperty);
}

$arSections = [];

foreach ($arResult['ITEMS'] as $arItem) {
    $iSectionId = ArrayHelper::getValue($arItem, 'IBLOCK_SECTION_ID');

    if (!empty($iSectionId))
        if (!ArrayHelper::isIn($iSectionId, $arSections))
            $arSections[] = $iSectionId;
}

if (!empty($arSections)) {
    $rsSections = Arrays::fromDBResult(CIBlockSection::GetList(['SORT' => 'ASC'], [
        'ID' => $arSections
    ]))->asArray();

    $arSections = [];

    $arSections[0] = [
        'ID' => 0,
        'NAME' => Loc::getMessage('C_CATALOG_COMPARE_RESULT_DEFAULT_TEMPLATE_TABS_ALL'),
        'ITEMS' => []
    ];

    foreach ($rsSections as $arSection) {
        $arSection['ITEMS'] = [];
        $arSection['PROPERTY_ID'] = [];
        $arSections[$arSection['ID']] = $arSection;
    }
}

$arPropertyIds = [];
$arPropertyIds = array_column($arResult['PROPERTIES'], 'ID');

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['IBLOCK_SECTION'] = null;
    $iSectionId = ArrayHelper::getValue($arItem, 'IBLOCK_SECTION_ID');

    if (!empty($iSectionId)) {
        $arSection = ArrayHelper::getValue($arSections, $iSectionId);

        if (!empty($arSection)) {
            $arItemPropertyIds = [];
            $arItemPropertyIds = array_column($arItem['DISPLAY_PROPERTIES'], 'ID');

            if (isset($arItem['OFFER_DISPLAY_PROPERTIES'])) {
                $arItemOffersPropertyIds = array_column($arItem['OFFER_DISPLAY_PROPERTIES'], 'ID');
            }

            $arItem['IBLOCK_SECTION'] = &$arSection;
            $arSections[$iSectionId]['ITEMS'][] = &$arItem;

            foreach ($arPropertyIds as $propertyId) {
                if (ArrayHelper::isIn($propertyId, $arItemPropertyIds) || ArrayHelper::isIn($propertyId, $arItemOffersPropertyIds))
                    $arSections[$iSectionId]['PROPERTY_ID'][$propertyId] = $propertyId;
            }
        }
    }

    $arSections[0]['ITEMS'][] = &$arItem;
    $arSections[0]['PROPERTY_ID'] = $arPropertyIds;
}

unset($arSection);

if ($arResult['DIFFERENT']) {
    foreach ($arSections as &$arSection) {
        $arPropertyIds = [];

        foreach ($arResult['PROPERTIES'] as &$arProperty) {
            $sValuePrevious = null;

            if (ArrayHelper::isIn($arProperty['ID'], $arSection['PROPERTY_ID'])) {
                foreach ($arSection['ITEMS'] as &$arSectionItem) {
                    $sCode = $arProperty['CODE'];

                    if (empty($sCode))
                        if (isset($arProperty['ID'])) {
                            $sCode = $arProperty['ID'];
                        } else {
                            return;
                        }

                    $sValue = null;

                    if ($arProperty['ENTITY'] === 'product') {
                        $sValue = $arSectionItem['DISPLAY_PROPERTIES'][$sCode]['DISPLAY_VALUE'];
                    } else if ($arProperty['ENTITY'] === 'offer') {
                        $sValue = $arSectionItem['OFFER_DISPLAY_PROPERTIES'][$sCode]['DISPLAY_VALUE'];
                    }

                    if ($sValuePrevious === null) {
                        $sValuePrevious = $sValue;
                    } else if ($sValue !== $sValuePrevious) {
                        $arPropertyIds[$arProperty['ID']] = $arProperty['ID'];
                        break;
                    }
                }
            }
        }

        $arSection['PROPERTY_ID'] = array_intersect($arSection['PROPERTY_ID'], $arPropertyIds);
    }

    unset($arSection, $arPropertyIds, $arProperty, $sValuePrevious, $arSectionItem, $sCode, $sValue);
}

$arResult['SECTIONS'] = $arSections;

unset($arItem, $arSections, $arSection, $iSectionId);

$arResult['URL'] = [
    'BASKET' => $arParams['BASKET_URL']
];

foreach ($arResult['URL'] as $sCode => $sUrl)
    $arResult['URL'][$sCode] = StringHelper::replaceMacros($sUrl, $arMacros);

unset($sCode, $sUrl);

$arResult['VISUAL'] = $arVisual;