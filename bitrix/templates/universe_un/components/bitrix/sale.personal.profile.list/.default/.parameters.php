<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

$arTemplateParameters['PATH_TO_ADD'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_PATH_TO_ADD'),
    'TYPE' => 'STRING'
];
