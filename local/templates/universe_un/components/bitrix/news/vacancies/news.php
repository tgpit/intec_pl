<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */
if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arList = $arResult['LIST'];

//подключаем компонент news.list
$APPLICATION->IncludeComponent (
    'bitrix:news.list',
    $arList['TEMPLATE'],
    $arList['PARAMETERS'],
    $component
);