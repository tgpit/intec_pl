<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$arReturn = [];
$arReturn['BUTTON_SHAPE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_VIEW_6_BUTTON_SHAPE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'round' => Loc::getMessage('C_FOOTER_TEMPLATE_1_VIEW_6_BUTTON_SHAPE_ROUND'),
        'square' => Loc::getMessage('C_FOOTER_TEMPLATE_1_VIEW_6_BUTTON_SHAPE_SQUARE')
    ],
    'DEFAULT' => 'square'
];

return $arReturn;