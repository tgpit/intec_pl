<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */

$arResult['LOGOTYPE']['DESKTOP'] = [
    'WIDTH' => Type::toInteger($arParams['LOGOTYPE_WIDTH'])
];

if ($arResult['LOGOTYPE']['DESKTOP']['WIDTH'] <= 0)
    $arResult['LOGOTYPE']['DESKTOP']['WIDTH'] = 180;

$arResult['MENU']['INFO'] = [
    'SHOW' => ArrayHelper::getValue($arParams, 'MENU_INFO_SHOW') == 'Y',
    'ROOT' => ArrayHelper::getValue($arParams, 'MENU_INFO_ROOT'),
    'CHILD' => ArrayHelper::getValue($arParams, 'MENU_INFO_CHILD'),
    'LEVEL' => ArrayHelper::getValue($arParams, 'MENU_INFO_LEVEL')
];

$arResult['BASKET']['POPUP'] = false;