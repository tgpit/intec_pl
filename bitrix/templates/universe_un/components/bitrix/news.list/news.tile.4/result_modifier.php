<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3,
    'TITLE' => null
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 4], $arParams['COLUMNS']),
    'TITLE' => [
        'SHOW' => !empty($arParams['TITLE']),
        'VALUE' => Html::encode($arParams['TITLE'])
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);