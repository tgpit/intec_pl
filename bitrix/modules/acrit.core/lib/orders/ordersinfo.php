<?
/**
 * Info for a profile fields
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class OrdersInfo {

	/**
	 * Get properties for external order ID
	 */
	public static function getOrderExtIDFields(array $profile=[]) {
		$result = [];
		// Base field
		$result[] = [
			'id' => 'XML_ID',
			'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_ORDERID_FIELD_XML_ID'),
		];
		// Properties
		$db = \Bitrix\Sale\Property::getList([
			'order' => ['ID' => 'asc'],
			'select' => ['ID', 'NAME', 'PERSON_TYPE_ID', 'TYPE', 'MULTIPLE'],
		]);
		while ($prop = $db->Fetch()) {
			// Check props sync availibility
			if (!in_array($prop['TYPE'], ['STRING', 'NUMBER'])) {
				continue;
			}
			// Add to the result
			if ($profile['FIELDS']['table_compare'][$prop['ID']]['value']) {
				$result[] = [
					'id'   => $prop['ID'],
					'name' => $prop['NAME'],
				];
			}
		}
		return $result;
	}

    public static function getNameSchema($id) {
        $name = '';
        $I = Loc::getMessage('ORDERS_NUMBER_SCHEMA_NUMBER');
        $E = Loc::getMessage('ORDERS_NUMBER_SCHEMA_EXNUMBER');
        $P = Loc::getMessage('ORDERS_NUMBER_SCHEMA_PREFIX');
        $arr_id = str_split($id);
        $i = 1;
        foreach ($arr_id as $item) {
            $name .= $$item;
            if ($i < count($arr_id) ) {
                $name .= ' / ';
            }
            $i++;
        }
        return $name;
    }

    public static function getSeparator() {
        $result[''] = [
            'ID' => '',
            'NAME' => Loc::getMessage('ORDERS_NUMBER_SCHEMA_EMPTY'),
        ];
        $result[] = [
            'ID' => '.',
            'NAME' =>  Loc::getMessage('ORDERS_NUMBER_SCHEMA_POINT'),
        ];
        $result[] = [
            'ID' => '/',
            'NAME' =>  Loc::getMessage('ORDERS_NUMBER_SCHEMA_SLASH'),
        ];
        $result[] = [
            'ID' => '-',
            'NAME' =>  Loc::getMessage('ORDERS_NUMBER_SCHEMA_DASH'),
        ];
        return $result;
    }
    /**
     * Schemas number list
     */

    public static function getNumberScheme() {
        $result[''] = [
            'ID' => '',
            'NAME' => Loc::getMessage('ORDERS_NUMBER_SCHEMA_EMPTY'),
        ];
        $result[] = [
              'ID' => 'I',
              'NAME' =>  self::getNameSchema('I')
        ];
        $result[] = [
            'ID' => 'E',
            'NAME' =>  self::getNameSchema('E')
        ];
        $result[] = [
            'ID' => 'PI',
            'NAME' =>  self::getNameSchema('PI')
        ];
        $result[] = [
            'ID' => 'PE',
            'NAME' =>  self::getNameSchema('PE')
        ];
        $result[] = [
            'ID' => 'IE',
            'NAME' =>  self::getNameSchema('IE')
        ];
        $result[] = [
            'ID' => 'EI',
            'NAME' =>  self::getNameSchema('EI')
        ];
        $result[] = [
            'ID' => 'PIE',
            'NAME' =>  self::getNameSchema('PIE')
        ];
        $result[] = [
            'ID' => 'PEI',
            'NAME' =>  self::getNameSchema('PEI')
        ];
        return $result;
    }
    /**
     * Sites list
     */
    public static function getSites() {
        $result = [];
        $rsSites = \Bitrix\Main\SiteTable::getList();
        while($arSite = $rsSites->fetch()) {
            $result[] = [
                'ID' => $arSite['LID'],
                'NAME' => $arSite['NAME']
            ];
        }
        $result[''] = [
            'ID' => '',
            'NAME' => Loc::getMessage('ORDERS_DELIV_METHOD_EMPTY'),
        ];
        return $result;
    }

	/**
	 * Fields for the order user
	 */
	public static function getUserFields() {
		$result = [
			'LOGIN' =>[
				'name' => Loc::getMessage("ORDERS_PORTAL_LOGIN"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'LOGIN',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_LOGIN_HINT"),
			],
			'LAST_NAME' => [
				'name' => Loc::getMessage("ORDERS_PORTAL_LAST_NAME"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'LAST_NAME',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_LAST_NAME_HINT"),
			],
			'NAME' => [
				'name' => Loc::getMessage("ORDERS_PORTAL_NAME"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'NAME',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_NAME_HINT"),
			],
			'SECOND_NAME' => [
				'name' => Loc::getMessage("ORDERS_PORTAL_SECOND_NAME"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'SECOND_NAME',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_SECOND_NAME_HINT"),
			],
			'EMAIL' =>[
				'name' => Loc::getMessage("ORDERS_PORTAL_EMAIL"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'EMAIL',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_EMAIL_HINT"),
			],
		];
		return $result;
	}

	/**
	 * Site users
	 */
	public static function getUsers($search='') {
		$result = [];
		$fields = [
			'select' => ['ID', 'SHORT_NAME', 'EMAIL'],
			'order' => ['ID' => 'DESC'],
		];
		if ($search) {
			$fields['filter'][] = [
				'LOGIC' => 'OR',
				'SHORT_NAME' => $search . '%',
				'EMAIL' => $search . '%',
			];
//			$fields['filter']['SHORT_NAME'] = $search . '%';
//			$fields['filter']['EMAIL'] = $search . '%';
		}
		$db = \Bitrix\Main\UserTable::getList($fields);
		while ($item = $db->fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['SHORT_NAME'],
				'code' => $item['EMAIL'],
			];
		}
		return $result;
	}

	/**
	 * Get site user
	 */
	public static function getUser($id) {
		$result = false;
		$fields = [
			'filter' => ['ID' => $id],
			'select' => ['ID', 'SHORT_NAME', 'EMAIL'],
			'order' => ['ID' => 'DESC'],
		];
		$db = \Bitrix\Main\UserTable::getList($fields);
		if ($item = $db->fetch()) {
			$result = [
				'id' => $item['ID'],
				'name' => $item['SHORT_NAME'],
				'code' => $item['EMAIL'],
			];
		}
		return $result;
	}

    /**
     * Get site companies
     */
    public static function getCompanies($search='')
    {
        if ( !Main\Loader::IncludeModule('crm') )
        {
            return false;
        }
        $result = [];
        $fields = [
            'select' => ['ID', 'TITLE'],
            'order' => ['ID' => 'DESC'],
        ];
        if ($search) {
            $fields['filter'][] = [
//                'LOGIC' => 'OR',
                'TITLE' => '%'. $search . '%',
            ];
        }
        $db = \Bitrix\Crm\CompanyTable::getList($fields);
        while ($item = $db->fetch()) {
            $result[] = [
                'id' => $item['ID'],
                'name' => $item['TITLE'],
//                'code' => $item['EMAIL'],
            ];
        }
        return $result;
    }

    /**
     * Get site company
     */
    public static function getCompany($id) {
        if ( !Main\Loader::IncludeModule('crm') )
        {
            return false;
        }
        $result = false;
        $fields = [
            'filter' => ['ID' => $id],
            'select' => ['ID', 'TITLE' ],
            'order' => ['ID' => 'DESC'],
        ];
        $db = \Bitrix\Crm\CompanyTable::getList($fields);
        if ($item = $db->fetch()) {
            $result = [
                'id' => $item['ID'],
                'name' => $item['TITLE'],
//                'code' => $item['EMAIL'],
            ];
        }
        return $result;
    }

    /**
     * Get site contacts
     */
    public static function getContacts($search='')
    {
        if ( !Main\Loader::IncludeModule('crm') )
        {
            return false;
        }
        $result = [];
        $fields = [
            'select' => ['ID', 'NAME', 'FULL_NAME'],
            'order' => ['ID' => 'DESC'],
        ];
        if ($search) {
            $fields['filter'][] = [
                'LOGIC' => 'OR',
                'FULL_NAME' => '%'. $search . '%',
                'NAME' => '%'. $search . '%',
            ];
        }
        $db = \Bitrix\Crm\ContactTable::getList($fields);
        while ($item = $db->fetch()) {
            $result[] = [
                'id' => $item['ID'],
                'name' => $item['NAME'],
                'code' => $item['FULL_NAME'],
            ];
        }
        return $result;
    }

    /**
     * Get site company
     */
    public static function getContact($id) {
        if ( !Main\Loader::IncludeModule('crm') )
        {
            return false;
        }
        $result = false;
        $fields = [
            'filter' => ['ID' => $id],
            'select' => ['ID', 'NAME', 'FULL_NAME' ],
            'order' => ['ID' => 'DESC'],
        ];
        $db = \Bitrix\Crm\ContactTable::getList($fields);
        if ($item = $db->fetch()) {
            $result = [
                'id' => $item['ID'],
                'name' => $item['NAME'],
                'code' => $item['FULL_NAME'],
            ];
        }
        return $result;
    }


    public static function getConstants($arr){
        $arConstants = $arr['LIST'];
        if(empty($arConstants)){
            $arConstants['constant_1'] = [
                'NAME' => '',
                'VALUE' => '',
            ];
        }
        return $arConstants;
    }

    public static function getConstantsList($arr){
        $arConstants = $arr['LIST'];
        $list = [];
        foreach ( $arConstants as $key => $item) {
            $list[] = [
                'id' => $key,
                'name' => $item['NAME'],
                'direction' => Plugin::SYNC_STOC,
            ];
        }
        return $list;
    }

	/**
	 * Site users group
	 */
	public static function getUserGroups() {
		$result = [];
		$filter = [];
		$db = \CGroup::GetList(($by="c_sort"), ($order="asc"), $filter);
		while ($item = $db->Fetch()) {
			$result[$item['ID']] = $item['NAME'];
		}
		return $result;
	}

	/**
	 * Order statuses
	 */
	public static function getStatuses() {
		$result = [];
		$filter = [
			'LID' => LANGUAGE_ID,
			'TYPE' => 'O',
		];
		$select = ['ID', 'NAME'];
		$db = \CSaleStatus::GetList(['SORT' => 'ASC'], $filter, false, false, $select);
		while ($item = $db->Fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['NAME'],
			];
		}
		return $result;
	}

	/**
	 * List of person types
	 */
	public static function getPersonTypes() {
		$result = [];
		$filter = [];
		$select = ['ID', 'NAME'];
		$db = \CSalePersonType::GetList(['SORT' => 'ASC'], $filter, false, false, $select);
		while ($item = $db->Fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['NAME'],
			];
		}
		return $result;
	}

	/**
	 * Order properties
	 */
	public static function getProps() {
		$result = [];
		$db = \Bitrix\Sale\Property::getList([
			'order' => ['ID' => 'asc'],
			'select' => ['ID', 'NAME', 'PERSON_TYPE_ID', 'TYPE', 'MULTIPLE'],
		]);
		while ($prop = $db->Fetch()) {
			// Check props sync availibility
			if (!in_array($prop['TYPE'], Plugin::PROPS_AVAILABLE)) {
				continue;
			}
			$prop['SYNC_DIR'] = Plugin::SYNC_ALL;
			switch ($prop['TYPE']) {
				case 'FILE':
					$prop['SYNC_DIR'] = Plugin::SYNC_STOC;
					if ($prop['MULTIPLE'] == 'Y') {
						continue 2;
					}
					break;
				case 'CHECKBOX':
				case 'RADIO':
					if ($prop['MULTIPLE'] == 'Y') {
						continue 2;
					}
					break;
				case 'LOCATION':
					$prop['SYNC_DIR'] = Plugin::SYNC_STOC;
					break;
				default:
			}
			// Hints
			$prop['HINT'] = Loc::getMessage("SP_CI_PROP_".$prop['TYPE']."_HINT");
			// Add to the result
			$result[$prop['PERSON_TYPE_ID']][] = [
				'ID' => $prop['ID'],
				'NAME' => $prop['NAME'],
				'SYNC_DIR' => $prop['SYNC_DIR'],
				'HINT' => $prop['HINT'],
			];
		}
		return $result;
	}

	/**
	 * List of delivery methods
	 */
	public static function getDeliveryMethods() {
		$result = [];
		$filter = ['ACTIVE' => 'Y'];
		$select = ['ID', 'NAME'];
		$db_res = \Bitrix\Sale\Delivery\Services\Table::getList([
			'filter'  => $filter,
			'select'  => $select,
		]);
		$result[] = [
			'id' => '',
			'name' => Loc::getMessage('ORDERS_DELIV_METHOD_EMPTY'),
		];
		while ($item = $db_res->fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['NAME'],
			];
		}
		return $result;
	}

	/**
	 * List of pay methods
	 */
	public static function getPayMethods() {
		$result = [];
		$filter = ['ACTIVE' => 'Y'];
		$select = ['ID', 'NAME'];
		$db_res = \Bitrix\Sale\PaySystem\Manager::getList([
			'filter'  => $filter,
			'select'  => $select,
		]);
		$result[] = [
			'id' => '',
			'name' => Loc::getMessage('ORDERS_PAY_METHOD_EMPTY'),
		];
		while ($item = $db_res->fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['NAME'],
			];
		}
		return $result;
	}

}
