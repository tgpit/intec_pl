<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'STORE_';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];
$arExcluded = [
    'POSITION',
    'NAME',
    'PATH',
    'COLUMNS',
    'PICTURE_SHOW',
    'SCHEDULE_SHOW',
    'PHONE_SHOW',
    'EMAIL_SHOW',
    'DESCRIPTION_SHOW'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    if (ArrayHelper::isIn($sKey, $arExcluded))
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $arExcluded, $sKey, $sValue);

$arProperties = ArrayHelper::merge([
    'STORES' => $arParams['STORES'],
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'ELEMENT_ID' => $arResult['ID'],
    'OFFER_ID' => '',
    'STORE_PATH' => $arParams['STORE_PATH'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'MAIN_TITLE' => '',
    'USER_FIELDS' => $arParams['USER_FIELDS'],
    'FIELDS' => $arParams['FIELDS'],
    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
    'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
    'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
    'SHOW_GENERAL_STORE_INFORMATION' => 'N',
], $arProperties);

?>
<div class="catalog-element-quantity-stores">
    <?php $APPLICATION->IncludeComponent(
        'bitrix:catalog.store.amount',
        'template.1', [
            'ELEMENT_ID' => $arResult['ID'],
            'STORE_PATH' => $arParams['STORE_PATH'],
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '36000',
            'MAIN_TITLE' => '',
            'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
            'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
            'STORES' => $arParams['STORES'],
            'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
            'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
            'USER_FIELDS' => $arParams['USER_FIELDS'],
            'FIELDS' => $arParams['FIELDS']
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    ) ?>
</div>
<?php unset($arProperties) ?>