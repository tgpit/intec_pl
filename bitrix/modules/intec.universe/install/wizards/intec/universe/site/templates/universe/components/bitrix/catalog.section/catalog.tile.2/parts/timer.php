<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arItem
 */

if (!empty($arItem['OFFERS'])) {
    $iElementId = ArrayHelper::getFirstValue($arItem['OFFERS']);
    $iElementId = $iElementId['ID'];
} else {
    $iElementId = $arItem['ID'];
}

$arResult['TIMER']['PROPERTIES']['parameters']['ELEMENT_ID'] = $iElementId;

?>
<?= Html::beginTag('div', [
    'class' => [
        'product-timer-adaptation',
        $arVisual['COLUMNS']['MOBILE'] == 2 ? 'super-small' : null,
        $arVisual['COLUMNS']['MOBILE'] == 2 ? 'small' : null
    ],
    'data-role' => 'timer-holder'
])?>
    <?php $APPLICATION->IncludeComponent(
        $arResult['TIMER']['PROPERTIES']['component'],
        $arResult['TIMER']['PROPERTIES']['template'],
        $arResult['TIMER']['PROPERTIES']['parameters'],
        $component
    ) ?>
<?= Html::endTag('div') ?>
<?php unset($iElementId) ?>