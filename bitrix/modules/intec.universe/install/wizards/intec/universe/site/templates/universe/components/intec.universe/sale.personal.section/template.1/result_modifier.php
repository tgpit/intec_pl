<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

$arParams = ArrayHelper::merge([
    'BASKET_PAGE_NAME' => null,
    'BASKET_URL' => null,
    'CONTACTS_PAGE_NAME' => null,
    'CONTACTS_URL' => null
], $arParams);

if (!empty($arParams['BASKET_URL'])) {
    $arResult['ITEMS'][] = [
        'CODE' => 'basket',
        'NAME' => !empty($arParams['BASKET_PAGE_NAME']) ? $arParams['BASKET_PAGE_NAME'] : Loc::getMessage('C_SALE_PERSONAL_SECTION_ITEMS_BASKET_NAME'),
        'LINK' => StringHelper::replaceMacros($arParams['BASKET_URL'], [
            'SITE_DIR' => SITE_DIR
        ])
    ];
}

if (!empty($arParams['CONTACTS_URL'])) {
    $arResult['ITEMS'][] = [
        'CODE' => 'contacts',
        'NAME' => !empty($arParams['CONTACTS_PAGE_NAME']) ? $arParams['CONTACTS_PAGE_NAME'] : Loc::getMessage('C_SALE_PERSONAL_SECTION_ITEMS_CONTACTS_NAME'),
        'LINK' => StringHelper::replaceMacros($arParams['CONTACTS_URL'], [
            'SITE_DIR' => SITE_DIR
        ])
    ];
}

$arData = [
    'basket' => [
        'ICON' => 'shopping-cart'
    ],
    'contacts' => [
        'ICON' => 'info-circle'
    ],
    'orders' => [
        'ICON' => 'calculator'
    ],
    'private' => [
        'ICON' => 'user-secret'
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItemData = ArrayHelper::getValue($arData, $arItem['CODE']);

    if (empty($arItemData))
        continue;

    $arItem = ArrayHelper::merge($arItem, $arItemData);
}

unset($arItem, $arData);
