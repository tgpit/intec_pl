<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_NAME'),
    'DESCRIPTION' => Loc::getMessage('C_SALE_PERSONAL_SECTION_DESCRIPTION'),
    'CACHE_PATH' => 'Y',
    'COMPLEX' => 'Y',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'Universe'
    ]
];
