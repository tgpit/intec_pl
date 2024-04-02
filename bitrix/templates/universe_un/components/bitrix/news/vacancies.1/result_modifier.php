<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LIST_LAZYLOAD_USE' => 'N',
    'DETAIL_LAZYLOAD_USE' => 'N',
    'PROPERTY_CITY' => null,
    'PROPERTY_SKILL' => null,
    'PROPERTY_TYPE_EMPLOYMENT' => null,
    'PROPERTY_SALARY' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');