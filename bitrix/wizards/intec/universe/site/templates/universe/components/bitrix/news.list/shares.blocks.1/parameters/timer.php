<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$arTemplateParameters['TIMER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_1_TIMER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TIMER_SHOW'] !== 'Y')
    return;

$excluded = [
    'TIME_ZERO_HIDE',
    'MODE',
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'TIMER_QUANTITY_SHOW',
    'TIMER_QUANTITY_OVER',
    'TIMER_TITLE_SHOW',
    'TIMER_TITLE_ENTER',
    'TIMER_PRODUCT_UNITS_USE',
    'TIMER_QUANTITY_HEADER_SHOW',
    'TIMER_QUANTITY_HEADER',
    'TIMER_HEADER_SHOW',
    'TIMER_HEADER',
    'SALE_HEADER_SHOW',
    'SALE_HEADER_VALUE',
    'UNTIL_DATE',
    'SALE_VALUE'
];

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
    'intec.universe:product.timer',
    'template.2',
    $siteTemplate,
    $arCurrentValues,
    'TIMER_',
    function ($key, &$value) use (&$excluded) {
        if (ArrayHelper::isIn($key, $excluded))
            return false;

        $value['PARENT'] = 'VISUAL';
        $value['NAME'] = Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_1_TIMER').' '.$value['NAME'];

        if ($key === 'SALE_SHOW')
            $value['REFRESH'] = 'N';

        return true;
    },
    Component::PARAMETERS_MODE_BOTH
));

unset($excluded);