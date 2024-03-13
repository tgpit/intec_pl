<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arItem
 */

if (!empty($arItem['OFFERS'])) {
    $iElementId = ArrayHelper::getFirstValue($arItem['OFFERS']);
    $iElementId = $iElementId['ID'];
} else {
    $iElementId = $arItem['ID'];
}

$arResult['TIMER']['PROPERTIES']['parameters']['ELEMENT_ID'] = $iElementId;

unset($iElementId);

?>
<div data-role="timer.holder">
    <?php $APPLICATION->IncludeComponent(
        $arResult['TIMER']['PROPERTIES']['component'],
        $arResult['TIMER']['PROPERTIES']['template'],
        $arResult['TIMER']['PROPERTIES']['parameters'],
        $component
    ) ?>
</div>