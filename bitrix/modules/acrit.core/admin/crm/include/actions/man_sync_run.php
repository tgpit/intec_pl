<?
namespace Acrit\Core\Crm;

use Acrit\Core\Log;

// Prepare
//$next_item = $_REQUEST['next_item']?$_REQUEST['next_item']:0;
//$limit = $_REQUEST['limit']?$_REQUEST['limit']:10;
//$imported_count = (int)$_REQUEST['imported_count'];
//$next_item_new = 0;

use Acrit\Core\Crm\DealsSync;

$next_item = $_REQUEST['next_item'] ? : 0;
$cnt = $_REQUEST['count'] ? : 0;
//Helper::Log('(sync) next_item '.$next_item);
$step_time = 20;
$start_time = time();

$start_sync_ts = false;
$sync_period_opt = $arProfile['SYNC']['man']['period'];
if ($sync_period_opt == '1d') {
	$sync_period = 3600 * 24;
}
elseif ($sync_period_opt == '1w') {
	$sync_period = 3600 * 24 * 7;
}
elseif ($sync_period_opt == '1m') {
	$sync_period = 3600 * 24 * 31;
}
elseif ($sync_period_opt == '3m') {
	$sync_period = 3600 * 24 * 31 * 3;
}
if ($sync_period) {
	$start_sync_ts = time() - $sync_period;
}
$start_date_ts = Controller::getStartDateTs();
if ($start_date_ts) {
	if ($start_date_ts > $start_sync_ts) {
		$start_sync_ts = $start_date_ts;
	}
}

// Process
//Helper::Log('(sync) cnt '.$cnt);
if (!$cnt || $next_item < $cnt) {
	$orders_ids = $obPlugin->getOrdersIDsList($start_sync_ts);
	$i = 0;
	foreach($orders_ids as $order_id) {
		// Skip processed items
		if ($i < $next_item) {
			$i++;
			continue;
		}
		// Process the order
		$order_data = (array)$obPlugin->getOrder($order_id);
		if ($arProfile['SYNC']['man']['only_new']) {
			$deal_id = DealsSync::findDeal($order_data);
			if (!$deal_id) {
				try {
					if (Controller::syncOrderToDeal($order_data)) {
						$i++;
					}
				}
				catch (\Exception $e) {
//					\Helper::Log('(sync) can\'t sync of order ' . $order_data['ID']);
				}
			}
		}
		else {
			try {
				if (Controller::syncOrderToDeal($order_data)) {
					$i++;
				}
			}
			catch (\Exception $e) {
//				\Helper::Log('(sync) can\'t sync of order ' . $order_data['ID']);
			}
		}
		// Check time limit
		$exec_time = time() - $start_time;
		if ($exec_time >= $step_time) {
//			Helper::Log('(sync) break on '.$i);
			break;
		}
	}
}
$next_item   = $i;

// Result
$arJsonResult['result'] = 'ok';
$arJsonResult['next_item'] = (int)$next_item;
$arJsonResult['errors'] = [];
$arJsonResult['report'] = [
	'done' => (int)$next_item,
];
$arJsonResult['sync_period_opt'] = $sync_period_opt;
$arJsonResult['orders'] = $orders_ids;
