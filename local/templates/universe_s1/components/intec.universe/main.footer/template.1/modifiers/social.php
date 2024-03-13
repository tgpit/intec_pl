<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global $APPLICATION
 */

$arParams = ArrayHelper::merge([
    'SOCIAL_SHOW' => 'N',
    'SOCIAL_SQUARE' => 'N',
    'SOCIAL_GREY' => 'N'
], $arParams);

$arResult['SOCIAL'] = [
    'SHOW' => false,
    'SQUARE' => $arParams['SOCIAL_SQUARE'] === 'Y' ? 'true' : 'false',
    'GREY' => $arParams['SOCIAL_GREY'] === 'Y' ? 'true' : 'false',
    'ITEMS' => [
        'VK' => null,
        'FACEBOOK' => null,
        'INSTAGRAM' => null,
        'TWITTER' => null,
        'YOUTUBE' => null,
        'ODNOKLASSNIKI' => null,
        'VIBER' => null,
        'WHATSAPP' => null,
        'YANDEX_DZEN' => null,
        'MAIL_RU' => null,
        'TELEGRAM' => null,
        'PINTEREST' => null,
        'TIKTOK' => null,
        'SNAPCHAT' => null,
        'LINKEDIN' => null
    ]
];

foreach ($arResult['SOCIAL']['ITEMS'] as $arKey => $arItem) {
    $sCodeSocial = StringHelper::toLowerCase($arKey);
    $arItem = [
        'CODE' => $arKey,
        'SHOW' => false,
        'LINK' => ArrayHelper::getValue($arParams, 'SOCIAL_'.$arKey.'_LINK')
    ];

    if (!empty($arItem['LINK'])) {
        $arItem['SHOW'] = true;
        $arResult['SOCIAL']['SHOW'] = true;
    }

    $arResult['SOCIAL']['ITEMS'][$arKey] = $arItem;
}

$arResult['SOCIAL']['SHOW'] =
    $arResult['SOCIAL']['SHOW'] &&
    $arParams['SOCIAL_SHOW'] === 'Y';

unset($arKey);
unset($arItem);