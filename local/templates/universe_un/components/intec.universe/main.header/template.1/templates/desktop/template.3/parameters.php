<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\UnsetArrayValue;

/**
 * @var string $componentName
 * @var string $templateName
 * @var string $siteTemplate
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 * @var array $arParts
 * @var InnerTemplate $desktopTemplate
 * @var InnerTemplate $fixedTemplate
 * @var InnerTemplate $mobileTemplate
 */

$arReturn = [];
$arReturn['LOGOTYPE_WIDTH'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_DESKTOP_TEMP3_LOGOTYPE_WIDTH'),
    'TYPE' => 'STRING',
    'DEFAULT' => '130'
];
$arReturn['TAGLINE_SHOW'] = new UnsetArrayValue();

return $arReturn;