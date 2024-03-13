<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arProperties = [];
    $arItem['DATA'] = [
        'ARTICLE' => [],
        'STORES' => [
            'SHOW' => null
        ]
    ];

    if (!empty($arItem['DISPLAY_PROPERTIES']))
        foreach ($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
            if (empty($arProperty['NAME']))
                if (!Type::isNumeric($arProperty['NAME']))
                    continue;

            if (empty($arProperty['VALUE']))
                if (!Type::isNumeric($arProperty['VALUE']))
                    continue;

            if (empty($arProperty['DISPLAY_VALUE']))
                if (!Type::isNumeric($arProperty['DISPLAY_VALUE']))
                    continue;

            $arProperties[] = $arProperty;
        }

    $arItem['DISPLAY_PROPERTIES'] = $arProperties;

    unset($arProperty);
    unset($arProperties);

    if (!empty($arParams['PROPERTY_ARTICLE']))
        $arItem['DATA']['ARTICLE'] = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_ARTICLE']
        ]);

    $arItem['DATA']['STORES']['SHOW'] = !empty(ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $arParams['PROPERTY_STORES_SHOW'],
        'VALUE'
    ]));

    if (!empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $arOffer['DATA'] = [
                'ARTICLE' => [],
                'STORES' => [
                    'SHOW' => null
                ]
            ];

            if (!empty($arParams['OFFERS_PROPERTY_ARTICLE']))
                $arOffer['DATA']['ARTICLE'] = ArrayHelper::getValue($arOffer, [
                    'PROPERTIES',
                    $arParams['OFFERS_PROPERTY_ARTICLE']
                ]);

            $arOffer['DATA']['STORES']['SHOW'] = !empty(ArrayHelper::getValue($arOffer, [
                'PROPERTIES',
                $arParams['OFFERS_PROPERTY_STORES_SHOW'],
                'VALUE'
            ]));
        }
}

unset($arItem);