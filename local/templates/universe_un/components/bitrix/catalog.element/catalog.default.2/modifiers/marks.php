<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 */

$arResult['MARKS'] = [
    "NEW" => false,
    "HIT" => false,
    "SHARE" => false,
    "RECOMMEND" => false
];
$arVisual['MARKS']['SHOW'] = false;

foreach ($arResult['MARKS']  as $sCode => $bMark) {
    $arResult['MARKS'][$sCode] = !empty(ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_'.$sCode],
        'VALUE'
    ]));

    if ($arResult['MARKS'][$sCode]) {
        $arVisual['MARKS']['SHOW'] = true;
    }
}
