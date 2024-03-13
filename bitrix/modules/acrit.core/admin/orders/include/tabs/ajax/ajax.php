<?
//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//$APPLICATION->restartBuffer();
CModule::IncludeModule("acrit.core");
CModule::IncludeModule("sale");

use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper,
    \Acrit\Core\Export\PluginManager,
    \Acrit\Core\Orders\Plugin,
    \Acrit\Core\Export\Field\Field,
    \Acrit\Core\Export\Field\ValueBase,
    \Acrit\Core\Export\Filter,
    \Acrit\Core\Cli,
    \Acrit\Core\Log,
    \Acrit\Core\Json,
    \Acrit\Core\DiscountRecalculation,
    \Acrit\Core\Orders\Exporter,
    \Acrit\Core\Export\Debug;
$action = $_POST['action'];
$plugin = $_POST['plugin'];
$conf_arr = $_POST['conf_arr'];
$data = $_POST['data'];
$arr_request = [];
$order_id = $_POST['order_id'];

switch ($action) {
    case 'get_props':
        $obPlugin = get_plugin($plugin);
        $props = $obPlugin->feedBack();
        $menu = [];
        foreach ($props['ACTION'] as $key=>$item ) {
            $menu[$item['id']] =  [
              'name'=> $item['name'],
              'active' => $key == 0 ? true : false ,
            ];
        }
        $mess = $obPlugin->getMess();
        $arr_request = [
            'profile' => get_profile($plugin),
//            'props' => $props,
            'menu' => $menu,
            'mess' => $mess,
        ];
        break;
    case 'get_props_confirm':
        $obPlugin = get_plugin($plugin);
        $mess = $obPlugin->getMess();
        $arr_request = [
            'profile' => get_profile($plugin),
            'date_end' => date('d.m.Y', time()),
            'date_beg' => date('d.m.Y', time() - 7 *24 *60 * 60 ),
//            'mess' => $mess,
        ];
        break;
    case 'get_orders_for_confirm':
        $sale_orders_list = [];
        $ext_orders_ids = [];
        $date_from = strtotime($data['date_beg']);
        $date_to = strtotime($data['date_end']) + 24 * 60 * 60 - 1;
        $obPlugin = get_plugin($plugin);
        $ext_orders_ids = $obPlugin->getOrdersListNew($date_from, $date_to);
        try {
            $sale_orders_list = get_orders_sale($ext_orders_ids);
        } catch (\Throwable  $e) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
        }
        foreach ($ext_orders_ids as $key=>$item) {
            $ext_orders_ids[$key]['ID_SALE'] = $sale_orders_list[$item['ID_MARKET']]['ID'];
            $ext_orders_ids[$key]['NUMBER_SALE'] = $sale_orders_list[$item['ID_MARKET']]['ACCOUNT_NUMBER'];
            $ext_orders_ids[$key]['PRICE_SALE'] = $sale_orders_list[$item['ID_MARKET']]['PRICE'];
            $ext_orders_ids[$key]['SHOW'] = false;
            $ext_orders_ids[$key]['CONF'] = false;
        }
        $arr_request = ['orders' => $ext_orders_ids,
                        'dates' => [$date_from, $date_to],
                        'sale' => $sale_orders_list,
        ];
        break;
    case 'confirm_orders':
        $arr_confirm = [];
        $obPlugin = get_plugin($plugin);
        $list = $obPlugin->operateOrder($conf_arr);
        $arr_request = $list;
        break;
    case 'get_basket':
        $arr_basket = get_order_basket($order_id);
        $arr_request = $arr_basket;
        break;
}
function get_order_basket($order_id) {
    $dbRes = \Bitrix\Sale\Basket::getList([
        'select' => ['ID', 'NAME', 'QUANTITY', '*'],
        'filter' => [
            '=ORDER_ID' => $order_id,
            'SET_PARENT_ID' => NULL,
        ]
    ]);
    $product_arr = [];
    while ($item = $dbRes->fetch()) {
        $product_arr[] = [
         'PRODUCT_ID' =>  $item['PRODUCT_ID'],
         'PRICE' =>  $item['PRICE'],
         'QUANTITY' =>  $item['QUANTITY'],
         'NAME' =>  $item['NAME'],
        ];
    }
    return [
        'order_id' => $order_id,
        'basket'=> $product_arr
    ];
}

function get_plugin($data) {
    $arProfilePlugin = false;
    $strPluginClass = null;
    $obPlugin = null;
    $arProfile = array();
    $strModuleId = $data['ModuleId'];
    $intProfileID = $data['ID'];
    if ($intProfileID) {
        // Get from db
        $arProfile = Helper::call($strModuleId, 'OrdersProfiles', 'getProfiles', [$intProfileID]);
        // Get plugin info
        if (strlen($arProfile['PLUGIN'])) {
            $arProfilePlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arProfile['PLUGIN']);
            if(is_array($arProfilePlugin)){
                $strPluginClass = $arProfilePlugin['CLASS'];
            }
            else {
                print Helper::showError(Loc::getMessage('ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_TITLE'),
                    Loc::getMessage('ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_DETAILS', array(
                        '#FORMAT#' => $arProfile['FORMAT'],
                    )));
            }
        }
    }
    if (strlen($strPluginClass) && class_exists($strPluginClass)) {
        $obPlugin = new $strPluginClass($strModuleId);
        $obPlugin->setProfileArray($arProfile);
    }
    return $obPlugin;
}
function get_orders_sale($arr_ext) {
    if( empty($arr_ext) ) {
        return [];
    }
    $list = [];
    foreach ($arr_ext as $item) {
        $list[] = $item['ID_MARKET'];
    }
    $orders_list = [];
        $filter = Array(
            'XML_ID' => $list,
        );
        $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $filter, false, false);
        while ($ar_sales = $db_sales->Fetch()) {
            $orders_list[$ar_sales['XML_ID']] = [
                'ID' => $ar_sales['ID'],
                'ACCOUNT_NUMBER' => $ar_sales['ACCOUNT_NUMBER'],
                'PRICE' => $ar_sales['PRICE'],
            ];
    }
//    file_put_contents(__DIR__.'/list.txt', var_export( $list, true ));
//    file_put_contents(__DIR__.'/while_i.txt', var_export( $i, true ));
//    file_put_contents(__DIR__.'/orders_list.txt', var_export( $orders_list, true ));
    return $orders_list;
}

function get_profile($data) {
    return Helper::call($data['ModuleId'], 'OrdersProfiles', 'getProfiles', [$data['ID']]);
}

echo json_encode($arr_request,JSON_UNESCAPED_UNICODE);