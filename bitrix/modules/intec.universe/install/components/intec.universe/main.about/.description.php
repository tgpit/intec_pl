<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('C_MAIN_DESCRIPTION_ABOUT_NAME'),
    'DESCRIPTION' => Loc::getMessage('C_MAIN_DESCRIPTION_ABOUT_DESCRIPTION'),
    'ICON' => null,
    'CACHE_PATH' => 'Y',
    'SORT' => 1,
    'PATH' => [
        'ID' => 'Universe'
    ],
];