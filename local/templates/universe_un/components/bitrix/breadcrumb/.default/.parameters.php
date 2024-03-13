<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [
    'BREADCRUMB_MOBILE_COMPACT' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('BREADCRUMB_MOBILE_COMPACT'),
        'TYPE' => 'CHECKBOX'
    ],
    'BREADCRUMB_DROPDOWN_USE' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('BREADCRUMB_DROPDOWN_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ]
];