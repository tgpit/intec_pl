<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

foreach ($arResult['PARTS'] as $arPart) {
    if (!$arPart['USE'] || empty($arPart['AREA'])) continue;

    $this->SetViewTarget($arPart['AREA']);
    echo $arPart['VALUE'];
    $this->EndViewTarget();
}