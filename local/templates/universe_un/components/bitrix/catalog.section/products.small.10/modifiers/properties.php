<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;

$arParams = ArrayHelper::merge([
    'PROPERTY_ARTICLE' => 'ARTICLE',
    'OFFERS_PROPERTY_ARTICLE' => 'ARTICLE'
], $arParams);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'ARTICLE' => null
    ];

    if (!empty($arParams['PROPERTY_ARTICLE']))
        $arItem['DATA']['ARTICLE'] = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_ARTICLE'],
            'VALUE'
        ]);

    if (!empty($arItem['OFFERS'])) {
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $arOffer['DATA'] = [
                'ARTICLE' => null
            ];

            if (!empty($arParams['OFFERS_PROPERTY_ARTICLE']))
                $arOffer['DATA']['ARTICLE'] = ArrayHelper::getValue($arOffer, [
                    'PROPERTIES',
                    $arParams['OFFERS_PROPERTY_ARTICLE'],
                    'VALUE'
                ]);
        }

        unset($arOffer);
    }
}

unset($arItem);
