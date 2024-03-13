<?
/**
 * Deals data synchronization
 */

namespace Acrit\Core\Crm;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Main\Context,
	Bitrix\Currency\CurrencyManager,
	Bitrix\Sale\Order,
	Bitrix\Sale\Basket,
	Bitrix\Sale\Delivery,
	Bitrix\Sale\PaySystem,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;
use PhpOffice\PhpSpreadsheet\Exception;

Loc::loadMessages(__FILE__);

class DealsSync {
	static $profile = false;

	public static function setProfile($profile) {
		self::$profile = $profile;
	}

	/**
	 * CRM info for sync process
	 */

	public static function getDealInfo($deal_id=0) {
		$info = [
			'deal' => [],
			'fields' => [],
			'stages' => [],
			'contact' => [],
			'company' => [],
			'products' => [],
			'product_fields' => [],
			'assigned_user' => [],
		];
		$request = [];
		if ($deal_id) {
			$request['deal'] = [
				'method' => 'crm.deal.get',
				'params' => ['id' => $deal_id]
			];
			$request['contact'] = [
				'method' => 'crm.contact.get',
				'params' => [
					'id' => '$result[deal][CONTACT_ID]',
				]
			];
			$request['assigned_user'] = [
				'method' => 'user.get',
				'params' => [
					'id' => '$result[deal][ASSIGNED_BY_ID]',
				]
			];
			$request['products'] = [
				'method' => 'crm.deal.productrows.get',
				'params' => [
					'id' => $deal_id,
				]
			];
		}
		$request['fields'] = [
			'method' => 'crm.deal.fields',
		];
		$dealcateg_id = (int)self::$profile['CONNECT_DATA']['category'];
		if (!$dealcateg_id) {
			$request['stages'] = [
				'method' => 'crm.status.list',
				'params' => [
					'order' => ['SORT' => 'ASC'],
					'filter' => [
						'ENTITY_ID' => 'DEAL_STAGE',
					]
				]
			];
		}
		else {
			$request['stages'] = [
				'method' => 'crm.dealcategory.stage.list',
				'params' => [
					'id' => $dealcateg_id,
				]
			];
		}
		$request['product_fields'] = [
			'method' => 'crm.product.fields',
		];
		$info = array_merge($info, Rest::batch($request));
		if (!empty($info['assigned_user'])) {
			$info['assigned_user'] = $info['assigned_user'][0];
		}
		return $info;
	}


	/**
	 * Search of deal
	 */

	public static function findDeal(array $order_data, $wo_categ=false) {
		$deal_id = false;
		$filter = [
			Controller::getOrderIDField() => $order_data['ID'],
		];
		if (!$wo_categ) {
			$category_id = (int) self::$profile['CONNECT_DATA']['category'];
			$filter['CATEGORY_ID'] = $category_id;
		}
		$source_id = self::$profile['CONNECT_DATA']['source_id'];
		if ($source_id) {
			$filter['ORIGINATOR_ID'] = $source_id;
		}
		$i = 0;
		while (!$deal_id && $i < 3) {
			if ($i > 0) {
				usleep(500000);
			}
			$res = Rest::execute('crm.deal.list', [
				'filter' => $filter,
			]);
			if ($res) {
				$deal_id = (int) $res[0]['ID'];
			}
			$i++;
		}
		Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(findDeal) order ' . $order_data['ID'] . ' find deal ' . $deal_id, self::$profile['ID'], true);
		return $deal_id;
	}


	/**
	 * Deal data
	 */

	public static function getDeal($deals_ids) {
		$deals = [];
		if (is_array($deals_ids) && !empty($deals_ids)) {
			$req_list = [];
			foreach ($deals_ids as $i => $deals_id) {
				$req_list[$i] = 'crm.deal.get' . '?' . http_build_query([
						'id' => $deals_id,
					]);
			}
			$resp = Rest::execute('batch', [
				"halt"  => false,
				"cmd" => $req_list,
			]);
			if ($resp['result']) {
				foreach ($resp['result'] as $deal) {
					$deal['LINK'] = Settings::get("crm_conn_portal") . '/crm/deal/details/' . $deal['ID'] . '/';
					$deals[] = $deal;
				}
			}
		}
		return $deals;
	}

	/**
	 * Get changed fields of the deal
	 */

	public static function getDealChangedFields(array $order_data, array $deal_info) {
		$d_new_fields = [];
		// Changes of status
		$d_new_fields = array_merge($d_new_fields, self::getDealChangedStatus($order_data, $deal_info));
		// Changes of props
		$d_new_fields = array_merge($d_new_fields, self::getDealChangedProps($order_data, $deal_info));

		return $d_new_fields;
	}


	/**
	 * Information of status changes
	 */

	public static function getDealChangedStatus(array $order_data, array $deal_info) {
		$changed_fields = [];
		$status_table = (array)self::$profile['STAGES']['table_compare'];
		$cancel_table = (array)self::$profile['STAGES']['cancel_stages'];
		$reverse_disable = self::$profile['STAGES']['reverse_disable'] ? true : false;
		$deal = $deal_info['deal'];
		// Stage of canceled order
		$new_stage = false;
		if ($order_data['IS_CANCELED']) {
			if ( ! in_array($deal['STAGE_ID'], $cancel_table)) {
				$new_stage = $cancel_table[0];
			}
		}
		// Change stage if set conformity of status and statuses is different
		else {
			$sync_params = $status_table[$order_data['STATUS_ID']];
			$deal_stages = (array) $sync_params;
			$deal_stages = array_diff($deal_stages, ['']);
			if ( !empty($deal_stages) && !in_array($deal['STAGE_ID'], $deal_stages)) {
				$new_stage = $deal_stages[0];
			}
		}
		// Check if is reverse stage
		if ($new_stage && $reverse_disable) {
			$stages_list = [];
			foreach ($deal_info['stages'] as $item) {
				$stages_list[$item['STATUS_ID']] = count($stages_list);
			}
			if ($stages_list[$new_stage] <= $stages_list[$deal['STAGE_ID']]) {
				$new_stage = false;
			}
		}
		if ($new_stage) {
			$changed_fields['STAGE_ID'] = $new_stage;
		}
		return $changed_fields;
	}


	/**
	 * Information of properties changes
	 */

	public static function getDealChangedProps(array $order_data, array $deal_info) {
		$changed_fields = [];
		$deal = $deal_info['deal'];
		$deal_fields = $deal_info['fields'];
		$comp_table = (array)self::$profile['FIELDS']['table_compare'];
		foreach ($comp_table as $cp_o_f_code => $sync_params) {
			$d_f_code = $sync_params['value'];
			if ($deal_fields[$d_f_code]) {
				$new_value = false;
				$deal_value = $deal[$d_f_code];
				// Properties
				foreach ($order_data['FIELDS'] as $o_f_code => $o_field) {
					$value = false;
					if ($o_f_code == $cp_o_f_code) {
						//\Helper::Log('(syncOrderToDeal) prop: ' . print_r($prop, true));
						switch ($o_field['TYPE']) {
							case 'LIST':
								foreach ($o_field['VALUE'] as $value) {
									foreach ($deal_fields[$d_f_code]['items'] as $deal_f_value) {
										if ($deal_f_value['VALUE'] == Utilities::convEncForDeal($value)) {
											$new_value[] = $deal_f_value['ID'];
										}
									}
								}
								break;
							case 'FILE':
								foreach ($o_field['VALUE'] as $file) {
									$data = file_get_contents($file['PATH']);
									$new_value[] = array(
										"fileData" => array(
											$file['NAME'],
											base64_encode($data)
										)
									);
								}
								break;
							case 'BOOLEAN':
								$new_value[] = $o_field['VALUE'][0];
								break;
							case 'DATE':
								if ($deal_fields[$d_f_code]['type'] == 'date') {
									$value[] = date(ProfileInfo::DATE_FORMAT_PORTAL_SHORT, strtotime($o_field['VALUE'][0]));
									$deal_value = date(ProfileInfo::DATE_FORMAT_PORTAL_SHORT, strtotime($deal_value));
								}
								else {
									$value[] = date(ProfileInfo::DATE_FORMAT_PORTAL, strtotime($o_field['VALUE'][0]));
									$deal_value = date(ProfileInfo::DATE_FORMAT_PORTAL, strtotime($deal_value));
								}
								$new_value = Utilities::convEncForDeal($value);
								break;
							default:
								if (is_array($o_field['VALUE']) && count($o_field['VALUE']) === 1 && !$o_field['VALUE'][0]) {
									$o_field['VALUE'] = [];
								}
								$new_value = Utilities::convEncForDeal($o_field['VALUE']);
						}
						break;
					}
				}

				if ($new_value !== false) {
					Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(syncOrderToDeal) new_value: ' . print_r($new_value, true), self::$profile['ID'], true);
					Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(syncOrderToDeal) deal_value: ' . print_r($deal_value, true), self::$profile['ID'], true);
					$deal_value = is_array($deal_value) ? $deal_value : (! $deal_value ? [] : [$deal_value]);
					if ( ! Utilities::isEqual($new_value, $deal_value)) {
						if ($deal_fields[$d_f_code]['isMultiple']) {
							$changed_fields[$d_f_code] = $new_value;
						} else {
							$changed_fields[$d_f_code] = $new_value[0];
						}
					}
				}
			}
		}

		return $changed_fields;
	}


	/**
	 * Updating of deal data
	 */

	public static function updateDealFields($deal_id, $order_id, $d_new_fields) {
		// Send changes
		if (!empty($d_new_fields)) {
			foreach ($d_new_fields as $k => $value) {
				if ($value === null) {
					$d_new_fields[$k] = '';
				}
			}
			Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(updateDealFields) deal '.$deal_id.' update fields ' . print_r($d_new_fields, true), self::$profile['ID'], true);
			Rest::execute('crm.deal.update', [
				'id'     => $deal_id,
				'fields' => $d_new_fields,
			]);
		}
		return true;
	}


	/**
	 * Create new deal from order
	 */

	private static function createDealFromOrder($order_data, $deal_info, $deal_fields) {
		$deal = [];
		if (Utilities::checkConnection()) {
			$order_id = $order_data['ID'];
			// Add deal
			$category_id = (int)self::$profile['CONNECT_DATA']['category'];
			$deal_title = Controller::getOrdTitleWithPrefix($order_data);
			$fields = [
				'TITLE'     => $deal_title,
				Controller::getOrderIDField() => $order_id,
				'CATEGORY_ID' => $category_id,
			];
			$fields = array_merge($deal_fields, $fields);
			// Source of deal
			$source_id = self::$profile['CONNECT_DATA']['source_id'];
			if ($source_id) {
				$fields['ORIGINATOR_ID'] = $source_id;
			}
			// Responsible user
			if (!$fields['ASSIGNED_BY_ID']) {
				$responsible_id = (int) self::$profile['CONNECT_DATA']['responsible'];
			}
			if ($responsible_id) {
				$fields['ASSIGNED_BY_ID'] = $responsible_id;
			}
			// Create deal
			Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(createDealFromOrder) crm.deal.add for order ' . $order_id . ' ' . print_r($fields, 1), self::$profile['ID'], true);
			$resp = Rest::execute('crm.deal.add', ['fields' => $fields]);
			if ($resp) {
				$deal_id = $resp;
				// Return deal details
				$deals = self::getDeal([$deal_id]);
				$deal = $deals[0];
				Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(createDealFromOrder) order ' . $order_id . ' deal ' . $deal_id . ' created', self::$profile['ID'], true);
			}
		}
		return $deal;
	}


	/**
	 * Sync goods and delivery
	 */

	public static function updateDealProducts($deal_id, $order_data, $deal_info) {
		$result = false;
		if (Utilities::checkConnection()) {
			$old_prod_rows = Rest::execute('crm.deal.productrows.get', [
				'id' => $deal_id
			]);
			$new_rows = [];
			// Products list of deal
			foreach ($order_data['PRODUCTS'] as $k => $item) {
				// Discount
				$price = $item['PRICE'];
				// Product fields
				$deal_prod = [
					'PRODUCT_NAME' => $item['PRODUCT_NAME'],
					'QUANTITY' => $item['QUANTITY'],
					'DISCOUNT_TYPE_ID' => 1,
					'DISCOUNT_SUM' => $item['DISCOUNT_SUM'],
					'MEASURE_CODE' => $item['MEASURE_CODE'],
					'TAX_RATE' => $item['TAX_RATE'],
					'TAX_INCLUDED' => $item['TAX_INCLUDED'],
				];
				if ($item['TAX_INCLUDED']) {
					$deal_prod['PRICE_EXCLUSIVE'] = $price;
					$deal_prod['PRICE'] = $price + $price * 0.01 * (int)$item['TAX_RATE'];
				}
				else {
					$deal_prod['PRICE'] = $price;
				}
				$new_rows[] = $deal_prod;
			}
//			// Delivery
//			$delivery_sync_type = Settings::get('products_delivery');
//			if (!$delivery_sync_type || ($delivery_sync_type == 'notnull' && $order_data['DELIVERY_PRICE'])) {
//				$new_rows[] = [
//					'PRODUCT_ID'   => 'delivery',
//					'PRODUCT_NAME' => Loc::getMessage("SP_CI_PRODUCTS_DELIVERY"),
//					'PRICE'        => $order_data['DELIVERY_PRICE'],
//					'QUANTITY'     => 1,
//				];
//			}
			// Check changes
			$new_rows = Utilities::convEncForDeal($new_rows);
			$has_changes = false;
			if (count($new_rows) != count($old_prod_rows)) {
				$has_changes = true;
			}
			else {
				foreach ($new_rows as $j => $row) {
					foreach ($row as $k => $value) {
						if ($value != $old_prod_rows[$j][$k]) {
							$has_changes = true;
							continue 2;
						}
					}
				}
			}
			// Send request
			if ($has_changes) {
				Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(updateDealProducts) deal ' . $deal_id . ' changed products ' . print_r($new_rows, true), self::$profile['ID'], true);
				$resp = Rest::execute('crm.deal.productrows.set', [
					'id'   => $deal_id,
					'rows' => $new_rows
				]);
				if ($resp) {
					$result = true;
				}
			}
		}
		return $result;
	}


	/**
	 * Sync order with deal
	 */

	public static function runSync(array $order_data, $profile) {
		self::setProfile($profile);
		// Has synchronization active
		$sync_active = (self::$profile['ACTIVE'] == 'Y');
		if (!$sync_active) {
			return;
		}
		$result = true;
		// Get deal
		$deal_id = self::findDeal($order_data);
		$order_id = $order_data['ID'];
		// Update fields of the deal
		if ($deal_id) {
			$deal_info = self::getDealInfo($deal_id);
			$deal = $deal_info['deal'];
			$deal_new_fields = self::getDealChangedFields($order_data, $deal_info);
			// Update contact
			try {
				$contact_id = ContactSync::runSync($order_data, $deal_info, self::$profile);
				if ($deal_info['deal']['CONTACT_ID'] != $contact_id) {
					$deal_new_fields['CONTACT_ID'] = $contact_id;
				}
			}
			catch (\Exception $e) {
				Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(syncOrderToDeal) for deal ' . $deal_id . ' can\'t sync of contact', self::$profile['ID'], true);
			}
			// Update deal
			if (!self::updateDealFields($deal_id, $order_id, $deal_new_fields)) {
				$result = false;
			}
		}
		// Create a new deal
		else {
			// Check if deal of order doesn't exist on other categs
			if (!self::findDeal($order_data, true)) {
				$deal_info   = self::getDealInfo();
				$deal_fields = self::getDealChangedFields($order_data, $deal_info);
				// Add contact
				try {
					$contact_id = ContactSync::runSync($order_data, $deal_info, self::$profile);
					if ($contact_id) {
						$deal_fields['CONTACT_ID'] = $contact_id;
					}
				}
				catch (\Exception $e) {
					Log::getInstance(Controller::$MODULE_ID, 'crm')->add('(syncOrderToDeal) for new deal can\'t sync of contact', self::$profile['ID'], true);
				}
				// Add deal
				$deal = self::createDealFromOrder($order_data, $deal_info, $deal_fields);
				$deal_id     = $deal['ID'];
				$deal_info   = self::getDealInfo($deal_id);
				if (!$deal_id) {
					$result = false;
				}
			}
		}
		if ($deal) {
			// Update products
			self::updateDealProducts($deal_id, $order_data, $deal_info);
		}
		return $result;
	}

}
