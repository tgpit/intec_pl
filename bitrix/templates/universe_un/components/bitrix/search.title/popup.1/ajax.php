<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

if (empty($arResult['CATEGORIES']))
    return;

$arVisual = &$arResult['VISUAL'];

if (!$arVisual['SHOW'])
    return;

Loc::loadMessages(__FILE__);

$APPLICATION->ShowAjaxHead(true, true, false, false);

$arAllItem = null;

if (!empty($arResult['CATEGORIES']['all']) && !empty($arResult['CATEGORIES']['all']['ITEMS'][0]))
    $arAllItem = $arResult['CATEGORIES']['all']['ITEMS'][0];

include(__DIR__.'/parts/results/'.$arVisual['TIPS']['VIEW'].'.php');

?>