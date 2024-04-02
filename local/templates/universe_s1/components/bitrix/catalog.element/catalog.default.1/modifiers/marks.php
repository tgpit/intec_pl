<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 */

$arResult['MARKS'] = [
    'NEW' => false,
    'HIT' => false,
    'RECOMMEND' => false,
    'SHARE' => false
];

$arVisual['MARKS']['SHOW'] = false;

foreach ($arResult['MARKS'] as $sKey => $bMark) {
    $arResult['MARKS'][$sKey] = !empty(ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_' . $sKey],
        'VALUE'
    ]));

    if ($arResult['MARKS'][$sKey])
        $arVisual['MARKS']['SHOW'] = true;
}
