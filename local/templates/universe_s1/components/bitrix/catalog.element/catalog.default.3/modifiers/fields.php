<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use Bitrix\Iblock\Model\PropertyFeature;
use intec\core\helpers\Type;

/**
 * @var array $arCodes
 * @var array $arResult
 * @var array $arParams
 */

$arResult['ARTICLE'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arCodes['ARTICLE'],
    'VALUE'
]);

$arResult['BRAND'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arParams['PROPERTY_BRAND'],
    'VALUE'
]);

if (!empty($arResult['BRAND'])) {
    $arResult['BRAND'] = CIBlockElement::GetByID($arResult['BRAND'])->GetNext();
    $arResult['BRAND']['PICTURE'] = null;

    if (!empty($arResult['BRAND']['PREVIEW_PICTURE'])) {
        $arResult['BRAND']['PREVIEW_PICTURE'] = CFile::GetFileArray($arResult['BRAND']['PREVIEW_PICTURE']);
        $arResult['BRAND']['PICTURE'] = $arResult['BRAND']['PREVIEW_PICTURE'];
    } else if (!empty($arResult['BRAND']['DETAIL_PICTURE'])) {
        $arResult['BRAND']['DETAIL_PICTURE'] = CFile::GetFileArray($arResult['BRAND']['DETAIL_PICTURE']);

        if (empty($arResult['BRAND']['PICTURE']))
            $arResult['BRAND']['PICTURE'] = $arResult['BRAND']['DETAIL_PICTURE'];
    }
}

if (empty($arResult['BRAND']))
    $arResult['BRAND'] = null;

$arResult['ADDITIONAL'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arCodes['ADDITIONAL'],
    'VALUE'
]);

$arResult['ASSOCIATED'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arCodes['ASSOCIATED'],
    'VALUE'
]);

$arResult['RECOMMENDED'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arCodes['RECOMMENDED'],
    'VALUE'
]);

$arResult['ADVANTAGES'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arParams['PROPERTY_ADVANTAGES'],
    'VALUE'
]);

$arResult['ARTICLES'] = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arParams['PROPERTY_ARTICLES'],
    'VALUE'
]);

if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as &$arOffer) {
        $arOffer['ARTICLE'] = ArrayHelper::getValue($arOffer, [
            'PROPERTIES',
            $arCodes['OFFERS']['ARTICLE'],
            'VALUE'
        ]);

        unset($arOffer);
    }

    $arOffersProperties = [];

    if ($arVisual['OFFERS']['PROPERTIES']['SHOW']) {
        $iOfferIblockId = ArrayHelper::getValue(current($arResult['OFFERS']), ['IBLOCK_ID']);
        $arOfferProperties = null;

        if (!empty($iOfferIblockId))
            $arOfferProperties = PropertyFeature::getDetailPageShowProperties($iOfferIblockId);

        if (empty($arOfferProperties) && !empty($arParams['OFFERS_PROPERTY_CODE']))
            $arOfferProperties = $arParams['OFFERS_PROPERTY_CODE'];

        if (!empty($arOfferProperties)) {

            foreach ($arResult['OFFERS'] as $arOffer) {
                foreach ($arOffer['PROPERTIES'] as $sKey => $arProperty) {
                    if (empty($arProperty['VALUE']))
                        continue;

                    $sValue = null;

                    foreach ($arOfferProperties as $arOfferProperty) {
                        if ($arOfferProperty === $arProperty['ID'] || $arOfferProperty === $arProperty['CODE']) {
                            if (Type::isArray($arProperty['VALUE'])) {
                                if ($arProperty['USER_TYPE'] === 'HTML') {

                                    $arValue = CIBlockFormatProperties::GetDisplayValue($arOffer, $arProperty, null);

                                    if (Type::isArray($arValue['DISPLAY_VALUE'])) {
                                        foreach ($arValue['DISPLAY_VALUE'] as $arValueItem) {
                                            if (empty($sValue))
                                                $sValue = $arValueItem;
                                            else
                                                $sValue = $sValue . $arVisual['OFFERS']['PROPERTIES']['DELIMITER'] . ' ' . $arValueItem;
                                        }
                                    } else {
                                        $sValue = $arValue['DISPLAY_VALUE'];
                                    }
                                } else {
                                    foreach ($arProperty['VALUE'] as $sPropertyItem) {
                                        if (empty($sValue))
                                            $sValue = $sPropertyItem;
                                        else
                                            $sValue = $sValue . $arVisual['OFFERS']['PROPERTIES']['DELIMITER'] . ' ' . $sPropertyItem;
                                    }
                                }
                            } else {
                                $sValue = $arProperty['VALUE'];
                            }

                            $arOffersProperties[$arOffer['ID']][$sKey]['NAME'] = $arProperty['NAME'];
                            $arOffersProperties[$arOffer['ID']][$sKey]['VALUE'] = $sValue;

                            break;
                        }
                    }

                    unset($arOfferProperty);
                }

                unset($arProperty, $sValue);
            }

            unset($arOffer);
        }

        unset($iOfferIblockId, $arOfferProperties);
    }

    if (!empty($arOffersProperties))
        $arResult['OFFERS_PROPERTIES'] = $arOffersProperties;

    unset($arOffersProperties);
}