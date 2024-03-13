<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arOffer
 * @var array $arParams
 * @var array $arStoreProperties
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$arOffer) use (&$arResult, &$arParams, &$APPLICATION, &$component, $arStoreProperties) {
    $arStoreProperties['OFFER_ID'] = $arOffer['ID'];

    $APPLICATION->IncludeComponent(
        'bitrix:catalog.store.amount',
        'template.3',
        $arStoreProperties,
        $component
    );
} ?>