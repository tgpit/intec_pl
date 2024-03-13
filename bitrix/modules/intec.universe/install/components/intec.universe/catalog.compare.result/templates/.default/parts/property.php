<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $sTemplateId
 */

use intec\core\helpers\Html;
use intec\core\helpers\Type;

return function ($arProperty, &$arItem) use (&$arResult) {

if ($arResult['DIFFERENT'] && !$arProperty['DIFFERENT'])
    return;

if ($arProperty['HIDDEN'])
    return;

$sCode = $arProperty['CODE'];

if (empty($sCode))
    if (isset($arProperty['ID'])) {
        $sCode = $arProperty['ID'];
    } else {
        return;
    }

$sValue = null;

if ($arProperty['ENTITY'] === 'product') {
    if ($arProperty['TYPE'] === 'field') {
        $sValue = $arItem['FIELDS'][$sCode];
    } else if ($arProperty['TYPE'] === 'property') {
        $sValue = $arItem['DISPLAY_PROPERTIES'][$sCode]['DISPLAY_VALUE'];
    }
} else if ($arProperty['ENTITY'] === 'offer') {
    if ($arProperty['TYPE'] === 'field') {
        $sValue = $arItem['OFFER_FIELDS'][$sCode];
    } else if ($arProperty['TYPE'] === 'property') {
        $sValue = $arItem['OFFER_DISPLAY_PROPERTIES'][$sCode]['DISPLAY_VALUE'];
    }
}

if (Type::isArray($sValue))
    $sValue = implode(', ', $sValue);

?>
    <div class="catalog-compare-result-property-value">
        <?= !empty($sValue) ? $sValue : '-' ?>
    </div>
<?php };