<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'PROPERTY_ORDER_ID' => null,
    'ORDER_DETAIL_URL' => null
], $arParams);

$arVisual = [
    'SHOW_ORDER_INFO' => !empty($arParams['PROPERTY_ORDER_ID']) && ArrayHelper::keyExists($arParams['PROPERTY_ORDER_ID'], $arResult['TICKET']) && !empty($arResult['TICKET'][$arParams['PROPERTY_ORDER_ID']]),
    'USE_ORDER_LINK' => !empty($arParams['ORDER_DETAIL_URL'])
];

$arResult['TICKET']['ORDER'] = [
    'ID' => null,
    'STATUS' => null,
    'LINK' => null
];

if ($arVisual['SHOW_ORDER_INFO']) {
    $oOrder = Order::load($arResult['TICKET'][$arParams['PROPERTY_ORDER_ID']]);
    $arResult['TICKET']['ORDER']['ID'] = $arResult['TICKET'][$arParams['PROPERTY_ORDER_ID']];

    if ($oOrder->isCanceled()) {
        $arResult['TICKET']['ORDER']['STATUS'] = Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_ORDER_STATUS_CANCELLED');
    } else {
        $arStatus = CSaleStatus::GetByID($oOrder->getField('STATUS_ID'));
        $arResult['TICKET']['ORDER']['STATUS'] = $arStatus['NAME'];
    }

    if ($arVisual['USE_ORDER_LINK'])
        $arResult['TICKET']['ORDER']['LINK'] = StringHelper::replaceMacros($arParams['ORDER_DETAIL_URL'], ['ID' => $arResult['TICKET']['ORDER']['ID']]);

    unset($oOrder, $arStatus);
}

$arResult['VISUAL'] = $arVisual;
unset($arVisual);

if (!empty($arParams['TICKET_LIST_URL']))
    $arResult['REAL_FILE_PATH'] = $arParams['TICKET_LIST_URL'];
