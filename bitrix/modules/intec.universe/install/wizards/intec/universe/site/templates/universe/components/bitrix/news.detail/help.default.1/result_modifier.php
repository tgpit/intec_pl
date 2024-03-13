<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'DESCRIPTION' => [
        'SHOW' => false,
        'VALUE' => null
    ],
    'BANNER' => [
        'SHOW' => false,
        'SRC' => null
    ]
];

$sText = !empty($arResult['DETAIL_TEXT']) ? $arResult['DETAIL_TEXT'] : $arResult['PREVIEW_TEXT'];
$arVisual['DESCRIPTION']['SHOW'] = !empty($sText);
$arVisual['DESCRIPTION']['VALUE'] = $sText;

unset($sText);

$sPicture = !empty($arResult['DETAIL_PICTURE']['SRC']) ? $arResult['DETAIL_PICTURE']['SRC'] : $arResult['PREVIEW_PICTURE']['SRC'];
$arVisual['BANNER']['SHOW'] = !empty($sPicture);
$arVisual['BANNER']['SRC'] = $sPicture;

unset($sPicture);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$this->__component->SetResultCacheKeys(['PREVIEW_PICTURE', 'DETAIL_PICTURE']);