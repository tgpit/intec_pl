<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$sPrefix = 'TIMER_';
$sComponent = 'intec.universe:product.timer';
$sTemplate = 'template.2';

$arExcluded = [
    'IBLOCK_ID',
    'IBLOCK_TYPE',
    'QUANTITY',
    'TIMER_QUANTITY_OVER',
    'ELEMENT_ID_INTRODUCE',
    'TIME_ZERO_HIDE',
    'MODE',
    'TIMER_QUANTITY_SHOW',
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'TIMER_TITLE_SHOW',
    'TIMER_HEADER_SHOW',
    'TIMER_HEADER',
    'SALE_VALUE',
    'SALE_HEADER_SHOW',
    'SALE_HEADER_VALUE',
    'COMPOSITE_FRAME_MODE',
    'COMPOSITE_FRAME_TYPE'
];

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
    $sComponent,
    $sTemplate,
    $siteTemplate,
    $arCurrentValues,
    $sPrefix,
    function ($key, &$value) use (&$arExcluded) {
        if (ArrayHelper::isIn($key, $arExcluded))
            return false;

        $value['PARENT'] = 'TIMER';

        if ($key === 'SALE_SHOW')
            $value['REFRESH'] = 'N';

        return true;
    },
    Component::PARAMETERS_MODE_BOTH
));

unset($sPrefix, $sComponent, $sTemplate, $arExcluded);