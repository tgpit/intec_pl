<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var bool $bOffer
 * @return array $arData
 */


$dData = function (&$arItem) use (&$arResult) {
    $fHandle = function (&$arItem) use (&$arResult) {

        $arData = [];

        $arData['quickView'] = [
            'show' => false,
            'template' => null,
            'parameters' => [],
        ];

        if ($arResult['QUICK_VIEW']['USE'] && !empty($arResult['QUICK_VIEW']['TEMPLATE'])) {
            $arData['quickView']['template'] = $arResult['QUICK_VIEW']['TEMPLATE'];
            $arData['quickView']['show'] = true;
        }

        if ($arData['quickView']['show']) {
            $arParameters = $arResult['QUICK_VIEW']['PARAMETERS'];

            if (!empty($arParameters['PROPERTY_CODE']) && Type::isArray($arParameters['PROPERTY_CODE'])) {
                $iCount = 0;
                $arProperties = [];

                foreach ($arParameters['PROPERTY_CODE'] as $sPropertyCode) {
                    $sPropertyValue = ArrayHelper::getValue($arItem, ['PROPERTIES', $sPropertyCode, 'VALUE']);

                    if (empty($sPropertyValue) && !Type::isNumeric($sPropertyValue))
                        continue;

                    $arProperties[] = $sPropertyCode;
                    $iCount++;

                    if ($iCount >= 10)
                        break;
                }

                $arParameters['PROPERTY_CODE'] = $arProperties;
            }

            $arParameters = ArrayHelper::merge($arParameters, [
                'IBLOCK_ID' => $arParameters['IBLOCK_ID'],
                'ELEMENT_ID' => $arItem['ID'],
                'ELEMENT_CODE' => $arItem['CODE'],
                'SECTION_ID' => $arItem['IBLOCK_SECTION_ID'],
                'SECTION_CODE' => null,
            ]);

            $arData['quickView']['parameters'] = $arParameters;
        }

        return $arData;
    };

    $arData = $fHandle($arItem);

    return $arData;
};