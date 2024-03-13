<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;

$arResult['PROPERTIES'] = [];

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['DISPLAY_PROPERTIES'])) {
        foreach ($arItem['DISPLAY_PROPERTIES'] as $sKey => &$arProperty) {
            if (isset($arResult['PROPERTIES'][$sKey]))
                continue;

            if (empty($arProperty['VALUE']))
                if (!Type::isNumeric($arProperty['VALUE']))
                    continue;

            if (empty($arProperty['DISPLAY_VALUE']))
                if (!Type::isNumeric($arProperty['DISPLAY_VALUE']))
                    continue;

            $arResult['PROPERTIES'][$sKey] = [
                'ID' => $arProperty['ID'],
                'NAME' => $arProperty['NAME'],
                'CODE' => $arProperty['CODE'],
                'SORT' => Type::toInteger($arProperty['SORT'])
            ];
        }

        unset($arProperty);

        uasort($arResult['PROPERTIES'], function ($arProperty1, $arProperty2) {
            return $arProperty1['SORT'] - $arProperty2['SORT'];
        });
    }
}

unset($arItem);

foreach ($arResult['CATEGORIES'] as &$arCategory) {
    foreach ($arCategory['ITEMS'] as &$arItem) {
        if (!empty($arItem['DISPLAY_PROPERTIES'])) {
            foreach ($arItem['DISPLAY_PROPERTIES'] as $sKey => &$arProperty) {
                if (isset($arCategory['PROPERTIES'][$sKey]))
                    continue;

                if (empty($arProperty['VALUE']))
                    if (!Type::isNumeric($arProperty['VALUE']))
                        continue;

                if (empty($arProperty['DISPLAY_VALUE']))
                    if (!Type::isNumeric($arProperty['DISPLAY_VALUE']))
                        continue;

                $arCategory['PROPERTIES'][$sKey] = [
                    'ID' => $arProperty['ID'],
                    'NAME' => $arProperty['NAME'],
                    'CODE' => $arProperty['CODE'],
                    'SORT' => Type::toInteger($arProperty['SORT'])
                ];
            }

            unset($arProperty);

            uasort($arCategory['PROPERTIES'], function ($arProperty1, $arProperty2) {
                return $arProperty1['SORT'] - $arProperty2['SORT'];
            });
        }
    }
}

unset($arItem);
unset($arCategory);
unset($arProperties);
