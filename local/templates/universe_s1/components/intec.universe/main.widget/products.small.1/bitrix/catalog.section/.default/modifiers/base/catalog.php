<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arCodes
 * @var array $arVisual
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$component->applyTemplateModifications();

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['OFFERS']))
        uasort($arItem['OFFERS'], function ($arOffer1, $arOffer2) {
            return Type::toInteger($arOffer1['SORT']) - Type::toInteger($arOffer2['SORT']);
        });
}

unset($arItem);