<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters['STORE_BLOCK_DESCRIPTION_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_STORE_BLOCK_DESCRIPTION_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['STORE_BLOCK_DESCRIPTION_USE'] === 'Y') {
    $arTemplateParameters['STORE_BLOCK_DESCRIPTION_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_STORE_BLOCK_DESCRIPTION_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_STORE_BLOCK_DESCRIPTION_TEXT_DEFAULT')
    ];
}