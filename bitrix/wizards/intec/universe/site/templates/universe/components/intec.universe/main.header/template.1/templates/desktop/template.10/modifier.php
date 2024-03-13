<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arResult['LOGOTYPE']['DESKTOP'] = [
    'WIDTH' => Type::toInteger($arParams['LOGOTYPE_WIDTH'])
];

if ($arResult['LOGOTYPE']['DESKTOP']['WIDTH'] <= 0)
    $arResult['LOGOTYPE']['DESKTOP']['WIDTH'] = 130;