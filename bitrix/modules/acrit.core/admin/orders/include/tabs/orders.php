<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Page\Asset,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
\Bitrix\Main\UI\Extension::load("ui.vue.vuex");
//$obTabControl->AddSection('HEADING_ORDERS_FEEDBACK', Loc::getMessage('ACRIT_ORDERS_FEEDBACK_HEADING'));
$obTabControl->BeginCustomField('PROFILE[ORDERS]', Loc::getMessage('ACRIT_ORDERS'));
?>
    <style>
        .acrit_orders_menu {
            display: flex;
        }
        .acrit_orders_wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .acrit_orders_header {
            display: flex;
            flex-direction: row;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px solid black;
        }
        .acrit_orders_header div {
            margin-right: 10px;
        }
        .acrit_table_header {
            display: flex;
            flex-direction: row;
            width: 100%;
            padding-bottom: 5px;
            padding-top: 5px;
            border-bottom: 1px solid black;
            justify-content: space-around;
        }
        .acrit_orders_items {
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 5px 0px;
            /*justify-content: space-around;*/
        }
        .acrit_orders_items_top {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            width: 100%;
            padding: 5px 0px;
            justify-content: space-around;
        }
        .show_down {
            display: flex;
            transform: rotate(90deg);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            width: 10px;
            justify-content: center;
            align-items: center;
            padding: 5px;
        }
        .show_up {
            display: flex;
            transform: rotate(270deg);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            width: 10px;
            justify-content: center;
            align-items: center;
            padding: 5px;
        }
        .acrit_orders_items_bottom {
            display: flex;
            flex-direction: row;
            width: 100%;
            padding: 5px 0px;
            justify-content: space-around;
            /*border: 1px solid black;*/
        }
        .acrit_orders_items_bottom_left {
            display: flex;
            width: 49%;
            flex-direction: column;
        }
        .acrit_orders_items_bottom_centr {
            display: flex;
            border: 1px solid gray;
        }
        .acrit_orders_items_in {
            display: flex;
            justify-content: space-around;
            border-bottom: 1px solid black;
            padding: 5px 5px;
        }
        .acrit_orders_items_in:last-child {
         border: none;
        }
        .acrit_orders_items_name {
            display: flex;
            width: 100%;
            justify-content: center;
            font-weight: 500;
            padding: 5px 0;
            border-bottom: 1px solid black;
        }
        .items_border {
            border: 1px solid black;
        }
        .acrit_table_item_in {
            display: flex;
            width: 25%;
            padding: 0 7px;
            overflow-wrap: anywhere;
        }
        .acrit_orders_items:nth-child(odd) {
            background: #FFF;
        }
        .acrit_table_item {
            display: flex;
            justify-content: flex-end;
            max-width: 15%;
            min-width: 10%;
            /*flex-grow: 1;*/
            padding-left: 5px;
            padding-right: 5px;
        }
        .spinner {
            height: 50px;
            width: 50px;
            border-left: 3px solid black;
            border-bottom: 3px solid black;
            border-right: 3px solid black;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spinner 1s linear infinite;
        }
        @keyframes spinner {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <tr>
        <td>
            <div data-role="main-notice"><?=Helper::showNote(Loc::getMessage('ACRIT_ORDER_MAIN_NOTICE_FOR_HINTS'), true);?></div>
        </td>
    </tr>
    <tr>
        <td class="manage_wrapper" id="app">
            <manage ref="manage" :module="'<?=$strModuleId?>'" :profile_id="'<?=$arProfile['ID']?>'"></manage>
        </td>
    </tr>
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/confirm.js'?>" defer ></script>
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/manage.js'?>" defer ></script>
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/main.js'?>" defer ></script>
    <?
$obTabControl->EndCustomField('PROFILE[ORDERS]');

$obTabControl->AddSection('HEADING_ORDERS_HELP', Loc::getMessage('ACRIT_ORDERS_HELP'));
$obTabControl->BeginCustomField('PROFILE[ORDERS][help]', Loc::getMessage('ACRIT_ORDERS_HELP_HINT'), true);
?>
    <tr>
        <td>
            <div><?=Loc::getMessage('ACRIT_ORDER_DATA_MARKET_MANUAL');?></div>
        </td>
    </tr>
<?php
$obTabControl->EndCustomField('PROFILE[ORDERS][help]');


//
//$obTabControl->BeginCustomField('PROFILE[SPECIAL][stocks]', Loc::getMessage('ACRIT_CRM_TAB_SPECIAL_STOKS'), true);
//    if(is_object($obPlugin) && method_exists($obPlugin,'showSpecial' )) {
//        $obPlugin->showSpecial($arProfile);
//    }
//
////if(is_object($obPlugin) && method_exists($obPlugin,'showSpecial' )) {
////    $obPlugin->showSpecial($arProfile);
////}
//$obTabControl->EndCustomField('PROFILE[SPECIAL][stocks]');


