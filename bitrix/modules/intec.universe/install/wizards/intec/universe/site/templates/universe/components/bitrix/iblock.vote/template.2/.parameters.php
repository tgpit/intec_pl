<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters['DISPLAY_AS_RATING'] = [
    'NAME' => Loc::getMessage('C_IBLOCK_VOTE_TEMPLATE_2_DISPLAY_AS_RATING'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'rating' => Loc::getMessage('C_IBLOCK_VOTE_TEMPLATE_2_DISPLAY_AS_RATING_RATING'),
        'vote_avg' => Loc::getMessage('C_IBLOCK_VOTE_TEMPLATE_2_DISPLAY_AS_RATING_AVG')
    ],
    'DEFAULT' => 'rating'
];