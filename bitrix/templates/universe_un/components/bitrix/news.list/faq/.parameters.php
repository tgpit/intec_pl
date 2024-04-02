<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters['COMMON_LIST_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_FAQ_COMMON_LIST_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];