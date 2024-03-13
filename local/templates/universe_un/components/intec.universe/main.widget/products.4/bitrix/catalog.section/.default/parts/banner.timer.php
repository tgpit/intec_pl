<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 */

$sPrefix = 'SECTION_TIMER_';

$iElementId = $arBanner['ID'];

if (!empty($arBanner['OFFERS'])) {
    $iElementId = ArrayHelper::getFirstValue($arBanner['OFFERS']);
    $iElementId = $iElementId['ID'];
}

?>
<div data-role="timer-holder">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:product.timer',
        'template.1', [
            'COMPONENT_TEMPLATE' => 'template.1',
            'ELEMENT_ID' => $iElementId,
            'ELEMENT_ID_INTRODUCE' => $arParams[$sPrefix.'ELEMENT_ID_INTRODUCE'],
            'ELEMENT_ID_INTRODUCE_VALUE' => $arParams[$sPrefix.'ELEMENT_ID_INTRODUCE_VALUE'],
            'IBLOCK_ID' => $arParams[$sPrefix.'IBLOCK_ID'],
            'IBLOCK_TYPE' => $arParams[$sPrefix.'IBLOCK_TYPE'],
            'LAZYLOAD_USE' => $arParams[$sPrefix.'LAZYLOAD_USE'],
            'MODE' => $arParams[$sPrefix.'MODE'],
            'PROPERTY_TIMER' => $arParams[$sPrefix.'PROPERTY_TIMER'],
            'QUANTITY' => $arBanner['CATALOG_QUANTITY'],
            'SETTINGS_USE' => $arParams[$sPrefix.'SETTINGS_USE'],
            'SET_UNTIL_DATE' => $arParams[$sPrefix.'SET_UNTIL_DATE'],
            'TIMER' => $arParams[$sPrefix.'TIMER'],
            'TIMER_FORMAT' => $arParams[$sPrefix.'TIMER_FORMAT'],
            'TIMER_TITLE_SHOW' => $arParams[$sPrefix.'TIMER_TITLE_SHOW'],
            'TIMER_TITLE_ENTER' => $arParams[$sPrefix.'TIMER_TITLE_ENTER'],
            'TIMER_TITLE_VALUE' => $arParams[$sPrefix.'TIMER_TITLE_VALUE'],
            'TIMER_HEADER' => $arParams[$sPrefix.'TIMER_HEADER'],
            'TIMER_HEADER_SHOW' => $arParams[$sPrefix.'TIMER_HEADER_SHOW'],
            'TIMER_QUANTITY_ENTER_VALUE' => $arParams[$sPrefix.'TIMER_QUANTITY_ENTER_VALUE'],
            'TIMER_QUANTITY_HEADER' => $arParams[$sPrefix.'TIMER_QUANTITY_HEADER'],
            'TIMER_QUANTITY_HEADER_SHOW' => $arParams[$sPrefix.'TIMER_QUANTITY_HEADER_SHOW'],
            'TIMER_QUANTITY_SHOW' => $arParams[$sPrefix.'TIMER_QUANTITY_SHOW'],
            'TIMER_SECONDS_SHOW' => $arParams[$sPrefix.'TIMER_SECONDS_SHOW'],
            'TIME_OUT_SHOW' => $arParams[$sPrefix.'TIME_OUT_SHOW'],
            'TIME_ZERO_HIDE' => $arParams[$sPrefix.'TIME_ZERO_HIDE'],
            'UNTIL_DATE' => $arParams[$sPrefix.'UNTIL_DATE'],
            'IS_SECTION' => true
        ]
    ) ?>
</div>
<?php unset($sPrefix, $iElementId) ?>