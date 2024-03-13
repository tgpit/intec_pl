<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$APPLICATION->IncludeComponent(
    'bitrix:catalog.socnets.buttons',
    'template.1',
    ArrayHelper::merge($arResult['SHARES']['PARAMETERS'], [
        'TITLE' => $arResult['NAME'],
        'DESCRIPTION' => $arResult['PREVIEW_TEXT'],
        'IMAGE' => $arResult['DETAIL_PICTURE']['SRC'],
        'GP_USE' => 'N',
    ]),
    $component,
    ['HIDE_ICONS' => 'Y']
);