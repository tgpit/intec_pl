<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php if (!empty($arResult['OFFERS'])) { ?>
    <?php if ($arResult['OFFER_GROUP']) { ?>
        <?php foreach ($arResult['OFFER_GROUP_VALUES'] as $offerId) { ?>
            <div class="catalog-element-additional-block catalog-element-sets-container" data-offer="<?= $offerId ?>">
                <div class="catalog-element-additional-block-name">
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SETS_NAME') ?>
                </div>
                <div class="catalog-element-additional-block-content">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.set.constructor',
                        'template.1', [
                        'IBLOCK_ID' => $arResult['OFFERS_IBLOCK'],
                        'ELEMENT_ID' => $offerId,
                        'PRICE_CODE' => $arParams['PRICE_CODE'],
                        'BASKET_URL' => $arResult['URL']['BASKET'],
                        'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                        'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
                        'BUNDLE_ITEMS_COUNT' => 1
                    ],
                        $component
                    ); ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <?php if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP']) { ?>
        <div class="catalog-element-additional-block catalog-element-sets-container" data-offer="false">
            <div class="catalog-element-additional-block-name">
                <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SETS_NAME') ?>
            </div>
            <div class="catalog-element-additional-block-content">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.set.constructor',
                    'template.1', [
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ELEMENT_ID' => $arResult['ID'],
                    'PRICE_CODE' => $arParams['PRICE_CODE'],
                    'BASKET_URL' => $arResult['URL']['BASKET'],
                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                    'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
                    'BUNDLE_ITEMS_COUNT' => 1
                ],
                    $component
                ); ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>