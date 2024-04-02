<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Currency;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters['PATH_TO_CRM'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_PERSONAL_EXTRANET_TEMPLATE_1_PATH_TO_CRM'),
    'TYPE' => 'STRING'
];