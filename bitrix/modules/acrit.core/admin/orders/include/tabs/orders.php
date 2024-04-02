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
            position: relative;
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
            max-width: 12%;
            min-width: 12%;
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
        .popup_supplies_wrapper {
            display: flex;
            position: absolute;
            justify-content: center;
            align-items: center;
            z-index: +5;
            width: 100%;
        }
        .popup_supplies {
            display: flex;
            padding: 30px;
            flex-direction: column;
            background: white;
            border: 1px solid gray;
            box-shadow: 5px 4px 9px 8px rgba(0,0,0,30%);
            border-radius: 4px;
            position: relative;
        }
        .popup_supplies_tr {
            display: flex;
            flex-direction: row;
            cursor: pointer;
        }
        .popup_supplies_td {
            display: flex;
            padding: 5px;
        }
        .popup_supplies_tr:hover {
            background: lightgray;
        }
        .popup_supplies_hr {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }
        .supplay_id {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
            border: 1px solid;
            border-color: #87919c #959ea9 #9ea7b1 #959ea9;
            border-radius: 4px;
            color: #000;
            box-shadow: 0 1px 0 0 rgba(255,255,255,30%), inset 0 2px 2px -1px rgba(180,188,191,70%);
            outline: none;
            vertical-align: middle;
            -webkit-font-smoothing: antialiased;
            padding: 0 10px;
        }
        .acrit_popup_close {
            position: absolute;
            transform: rotate(45deg);
            font-size: 30px;
            justify-content: center;
            align-items: center;
            display: flex;
            height: 15px;
            width: 15px;
            cursor: pointer;
            right: 10px;
            top: 10px;
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
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/confirm_wb.js'?>" defer ></script>
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/confirm.js'?>" defer ></script>
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/manage.js'?>" defer ></script>
    <script type="text/javascript" src="<?='/bitrix/js/acrit.core/orders/tabs/js/main.js'?>" defer ></script>
    <?
$obTabControl->EndCustomField('PROFILE[ORDERS]');

$obTabControl->AddSection('HEADING_ORDERS_COMMENT', Loc::getMessage('ACRIT_ORDERS_COMMENT'));
$obTabControl->BeginCustomField('PROFILE[OTHER][comment]', Loc::getMessage('ACRIT_ORDERS_COMMENT'));
?>
    <tr>
        <td>
            <div><?=$obPlugin->showOrdersComment();?></div>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[OTHER][comment]');

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


