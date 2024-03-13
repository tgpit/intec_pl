<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_DESCRIPTION_NAME'),
    'DESCRIPTION' => Loc::getMessage('C_REVIEWS_COMPONENT_DESCRIPTION_DESCRIPTION'),
    'COMPLEX' => 'N',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'Universe'
    ]
];