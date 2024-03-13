<?
/**
 * Products data synchronization
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Acrit\Core\Log,
	Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class Products {

	/**
	 * Iblocks catalogs list
	 */
	public static function getIblockList($offers=false) {
		$list = [];
		$catalog_iblocks_ids = [];
		$filter = [];
		if (!$offers) {
			$filter['PRODUCT_IBLOCK_ID'] = 0;
		}
		$catalog_iblocks = \Bitrix\Catalog\CatalogIblockTable::getList([
			'filter' => $filter,
		])->fetchAll();
		foreach ($catalog_iblocks as $catalog_iblock) {
			$catalog_iblocks_ids[] = $catalog_iblock['IBLOCK_ID'];
		}
		$res = \Bitrix\Iblock\IblockTable::getList([
			'select' => ['ID', 'NAME'],
		]);
		while ($item = $res->fetch()) {
			if (in_array($item['ID'], $catalog_iblocks_ids)) {
				$list[] = [
					'id' => $item['ID'],
					'name' => $item['NAME'],
				];
			}
		}
		return $list;
	}

	/**
	 * Store products fields
	 */
	public static function getFieldsForID($iblock_id) {
		$list = [];
		if (!$iblock_id) {
			return;
		}
		// IBlock fields
		$list['main'] = [
			'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_OSNOVNYE_PARAMETRY"),
		];
		$list['main']['items'] = [
			[
				'id' => 'ID',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_ID")
			],
			[
				'id' => 'NAME',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_IMA_ELEMENTA")
			],
			[
				'id' => 'CODE',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_KOD_ELEMENTA")
			],
			[
				'id' => 'XML_ID',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_XML_ID")
			],
		];
        $list['catalog'] = [
            'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_CATALOG_PARAMETRY"),
        ];
        $list['catalog']['items'] = [
            [
                'id' => 'BARCODE_CATALOG',
                'name' => GetMessage("ORDERS_PORTAL_CATALOG_BARCODE")
            ],
        ];
        // IBlock properties
		$list['props'] = [
			'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_SVOYSTVA"),
		];
		if ($iblock_id) {
			$ob = \CIBlockProperty::GetList(["sort" => "asc", "name" => "asc"], ["ACTIVE" => "Y", "IBLOCK_ID" => $iblock_id]);
			while ($prop = $ob->GetNext()) {
				if ($prop['MULTIPLE'] != 'Y' && !in_array($prop['PROPERTY_TYPE'], ['F'])) {
					$list['props']['items'][] = [
						'id'   => 'PROPERTY_' . $prop['CODE'],
						'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_SVOYSTVO", ['#NAME#' => $prop['NAME']]),
					];
				}
			}
		}
		return $list;
	}

	/**
	 * Search for a store product by its identification code
	 */
	public static function findIblockProduct($find_code, array $profile) {
		$product = false;
		if ($find_code) {
			$iblock_list = self::getIblockList(true);
			$comp_table = $profile['PRODUCTS']['search_fields'];
			$price_table = $profile['PRODUCTS']['search_prices'];
			foreach ($iblock_list as $iblock) {
				if ($comp_table[$iblock['id']]) {
                    if ( $comp_table[$iblock['id']] == 'BARCODE_CATALOG') {
                        $rsProducts = \CCatalogStoreBarCode::getList(array(), array(
                            "BARCODE" => $find_code,
                        ));
                        $ITEM_ID =  $rsProducts->Fetch();
                        if ($ITEM_ID['PRODUCT_ID']) {
                            $comp_table[$iblock['id']] = 'ID';
                            $find_code = $ITEM_ID['PRODUCT_ID'];
                        } else {
                            return false;
                        }
                    }
					$filter = [
						'IBLOCK_ID' => $iblock['id'],
//						'ACTIVE' => 'Y',
						$comp_table[$iblock['id']] => $find_code,
					];
                    Log::getInstance(Settings::getModuleId(), 'orders')->add('(findIblockProduct) search filter ' . print_r($filter, true), $profile['ID'], true);
					$res = \CIBlockElement::GetList(['SORT' => 'ASC'], $filter, false, ['nTopCount' => 1], ['ID', 'NAME', $price_table[$iblock['id']] ]);
                    while ($ob = $res->GetNextElement()) {
//                        file_put_contents(__DIR__.'/ob.txt', var_export($ob, true));
                        $fields = $ob->GetFields();
						$fields['catalog'] = \Bitrix\Catalog\ProductTable::getById($fields['ID'])->fetch();
						Log::getInstance(Settings::getModuleId(), 'orders')->add('(findIblockProduct) found variant ' . print_r($fields, true), $profile['ID'], true);

                        if ( preg_match( '/CATALOG_GROUP_/' , $price_table[$iblock['id']]) === 1 ) {
                            $price = $fields['CATALOG_PRICE_'. str_replace('CATALOG_GROUP_', '', $price_table[$iblock['id']] ) ];
                        }
                        if ( preg_match( '/PROPERTY_/' , $price_table[$iblock['id']]) === 1 ) {
                            $price = $fields[ $price_table[$iblock['id']].'_VALUE' ];
                        }
                        if ($profile['OTHER']['DISCOUNT']['ON'] == 'Y' && $profile['OTHER']['DISCOUNT']['PERCENT'] > 0 ) {
                            $price = floor($price * $profile['OTHER']['DISCOUNT']['PERCENT'] / 100 );
                        }
                        if ($price > 0 ) {
                            $fields['CHANGE_PRICE'] = $price;
                        } else {
                            $fields['CHANGE_PRICE'] = false;
                        }
//					    $fields['PROPERTIES'] = $ob->GetProperties();
						$product = $fields;
						break 2;
					}
				}
			}
		}
		return $product;
	}

    public static function getPriceForID($iblock_id) {
        $list = [];
        $list['props'] = [
            'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_SVOYSTVA"),
        ];
        if ($iblock_id) {
            $ob = \CIBlockProperty::GetList(["sort" => "asc", "name" => "asc"], ["ACTIVE" => "Y", "IBLOCK_ID" => $iblock_id]);
            while ($prop = $ob->GetNext()) {
                if ($prop['MULTIPLE'] != 'Y' && in_array($prop['PROPERTY_TYPE'], ['N'])) {
                    $list['props']['items'][] = [
                        'id'   => 'PROPERTY_' . $prop['CODE'],
                        'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_SVOYSTVO", ['#NAME#' => $prop['NAME']]),
                    ];
                }
            }
        }
        // Price fields
        $list['price'] = [
            'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_TORGOVY_CATALOG"),
        ];

        $ob = \CCatalogGroup::GetList(["ID" => "asc", "NAME" => "asc"], []);
        while ($price = $ob->GetNext()) {
            $list['price']['items'][] = [
                'id'   => 'CATALOG_GROUP_' . $price['ID'],
                'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_CATALOG_PRICE", ['#NAME#' => $price['NAME']]),
            ];
        }
        return $list;
    }
}
