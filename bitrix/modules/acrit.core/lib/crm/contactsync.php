<?
/**
 * CRM contact data synchronization
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

class ContactSync {

	/**
	 * Get contact data by profile
	 */

	public static function getDataByProfile(array $order_data, $contact, $profile) {
		$cont_fields = [];
		$comp_table = (array)$profile['CONTACTS']['table_compare'];
		$user_fields = $order_data['USER'];
		foreach ($comp_table as $deal_f_id => $order_f_id) {
			// User fields
			if ($order_f_id) {
				$value = $user_fields[$order_f_id];
				if ($value) {
					if (in_array($deal_f_id, ['EMAIL', 'PHONE'])) {
						$phonemail_mode = Settings::get('contacts_phonemail_mode');
						if ($phonemail_mode == 'replace' && ! empty($contact[$deal_f_id])) {
							foreach ($contact[$deal_f_id] as $i => $item) {
								if ($i == 0) {
									$cont_fields[$deal_f_id][] = ['ID'         => $item['ID'],
									                              'VALUE'      => $value,
									                              'VALUE_TYPE' => 'WORK'
									];
								} else {
									$cont_fields[$deal_f_id][] = ['ID' => $item['ID'], 'DELETE' => 'Y'];
								}
							}
						} else {
							$cont_fields[$deal_f_id][] = ['VALUE' => $value, 'VALUE_TYPE' => 'WORK'];
						}
					} else {
						$cont_fields[$deal_f_id] = $value;
					}
				} else {
					$cont_fields[$deal_f_id] = '';
				}
			}
		}
		return $cont_fields;
	}


	/**
	 * Sync the deal contact data
	 */

	public static function runSync(array $order_data, $deal_info, $profile) {
		$result = false;
		if (Utilities::checkConnection()) {
			$sync_new_type = (int) $profile['CONTACTS']['sync_new_type'];
			$deal = $deal_info['deal'];
			$contact = $deal_info['contact'];
			// Find contact
			if (!$contact['ID']) {
				$contact = self::find($order_data, $deal_info, $profile);
			}
			// Get contacts data
			$cont_fields = self::getDataByProfile($order_data, $contact, $profile);
			$cont_fields = Utilities::convEncForDeal($cont_fields);
			//\Helper::Log('(syncOrderToDealContact) cont_fields '.print_r($cont_fields, true));
			// Add contact
			if (!$contact['ID']) {
				$responsible_id = (int)$profile['CONNECT_DATA']['responsible'];
				if ($responsible_id) {
					$cont_fields['ASSIGNED_BY_ID'] = $responsible_id;
				}
				$contact_id = Rest::execute('crm.contact.add', [
					'fields' => $cont_fields,
				]);
				if (!$contact_id) {
					$res = Rest::execute('crm.contact.add', [
						'fields' => $cont_fields,
					], false, true, false);
					//\Helper::Log('(syncOrderToDealContact) add contact error '.print_r($res, true));
				}
			}
			// Update contact
			else {
				$contact_id = $contact['ID'];
				// TODO: Checking for changes
				if ((!$deal['ID'] && $sync_new_type == 1) || $sync_new_type == 2) {
					Rest::execute('crm.contact.update', [
						'id'     => $contact_id,
						'fields' => $cont_fields,
					]);
				}
			}
			if ($contact_id) {
				$result = $contact_id;
			}
		}
		return $result;
	}


	/**
	 * Contacts search
	 */

	public static function find(array $order_data, $deal_info, $profile) {
		$contact = false;
		if (Utilities::checkConnection()) {
			$cont_fields = self::getDataByProfile($order_data, [], $profile);
			$cont_s_field = $profile['CONTACTS']['contact_search_fields'];
			if ($cont_s_field) {
				if ($cont_fields[$cont_s_field]) {
					$filter = [
						$cont_s_field => $cont_fields[$cont_s_field],
					];
					$request = [
						'list' => [
							'method' => 'crm.contact.list',
							'params' => [
								'filter' => $filter,
							]
						],
						'get' => [
							'method' => 'crm.contact.get',
							'params' => [
								'id' => '$result[list][0][ID]',
							]
						]
					];
					$res = Rest::batch($request);
					if ($res['get']) {
						$contact = $res['get'];
					}
				}
			}
			else {
				if ($cont_fields['PHONE'] && $cont_fields['PHONE'][0]['VALUE']) {
					$search_phone = $cont_fields['PHONE'][0]['VALUE'];
				}
				if ($cont_fields['EMAIL'] && $cont_fields['EMAIL'][0]['VALUE']) {
					$search_email = $cont_fields['EMAIL'][0]['VALUE'];
				}
				// Find by phone
				if ($search_phone) {
					$phones = Utilities::getPhonesFormats($search_phone);
					foreach ($phones as $phone) {
						$filter = [
							'PHONE' => $phone,
						];
						$request = [
							'list' => [
								'method' => 'crm.contact.list',
								'params' => [
									'filter' => $filter,
								]
							],
							'get' => [
								'method' => 'crm.contact.get',
								'params' => [
									'id' => '$result[list][0][ID]',
								]
							]
						];
						$res = Rest::batch($request);
						if ($res['get']) {
							$contact = $res['get'];
						}
					}
				}
				// Find by email
				if ( ! $contact && $search_email) {
					$filter = [
						'EMAIL' => $search_email,
					];
					$request = [
						'list' => [
							'method' => 'crm.contact.list',
							'params' => [
								'filter' => $filter,
							]
						],
						'get' => [
							'method' => 'crm.contact.get',
							'params' => [
								'id' => '$result[list][0][ID]',
							]
						]
					];
					$res = Rest::batch($request);
					if ($res['get']) {
						$contact = $res['get'];
					}
				}
			}
			//\Helper::Log('(findContact) finded contact "' . $contact['ID'] . '" by ' . print_r($filter, true));
		}
		return $contact;
	}

}
