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
$arTimerParameters = $arResult['TIMER']['PROPERTIES']['parameters'];

if (isset($arTimerParameters['TIMER_QUANTITY_SHOW']) && $arItem['DATA']['OFFER'])
    $arTimerParameters['TIMER_QUANTITY_SHOW'] = 'N';

?>
<div data-role="timer-holder">
    <?php $APPLICATION->IncludeComponent(
        $arResult['TIMER']['PROPERTIES']['component'],
        $arResult['TIMER']['PROPERTIES']['template'],
        $arTimerParameters,
        $component
    ) ?>
</div>
<?php unset($iElementId, $arTimerParameters) ?>