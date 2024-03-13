<?php
/**
 * Acrit Core: Aliexpress plugin API Local (Russian)
 * @documentation https://business.aliexpress.ru/docs/category/open-api
 */

namespace Acrit\Core\Export\Plugins\AliHelpers;

use Acrit\Core\Export\Plugins\AliexpressComApiLocal;
use Acrit\Core\Json;
use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Products extends Request {

	public function __construct($instance) {
		parent::__construct($instance);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($api_key, &$message) {
		$result = false;
		$res = $this->request('scroll-short-product-by-filter', [
			'limit' => 1,
		], $api_key);
		if ($res['error']) {
			$message = Loc::getMessage('ACRIT_EXPORT_PLUGIN_ALIAPILOC_API_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_EXPORT_PLUGIN_ALIAPILOC_API_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	/**
	 * Get categories list
	 */
	public function getCategList(int $parent_categ=0) {
		$list = [];
		if ($parent_categ == 0) {
			$res = $this->request('categories/top', [
				'a' => 'b',
			]);
			if (isset($res['categories'])) {
				$list = $res['categories'];
			}
		}
		else {
			$res = $this->request('categories/get', [
				'ids' => [$parent_categ],
			]);
			if (!empty($res['categories'])) {
				$subcateg_ids = array_chunk($res['categories'][0]['children_ids'], 20);
				foreach ($subcateg_ids as $ids_list) {
					$res = $this->request('categories/get', [
						'ids' => $ids_list,
					]);
					if (isset($res['categories'])) {
						$list = array_merge($list, $res['categories']);
					}
				}
			}
		}
		return $list;
	}

	/**
	 * Get category info
	 */
	public function getCateg($category_id) {
		$resp = $this->request('categories/get', ['ids' => [$category_id]]);
		$result = $resp['categories'][0] ?? false;
		return $result;
	}

	/**
	 * Get products list
	 */
	public function getProductList($filter = [], $limit = 50) {
		$req_params = [
			'limit' => $limit,
		];
		if (!empty($filter)) {
			$req_params['filter'] = $filter;
		}
		$resp = $this->request('scroll-short-product-by-filter', $req_params);
		$list = $resp['data'] ?? [];
		return $list;
	}

	/**
	 * Get product info
	 */
	public function getProduct($product_id) {
		$resp = $this->request('product/get-seller-product', ['product_id' => $product_id]);
		$result = $resp['data'] ?? [];
		return $result;
	}

	/**
	 * Add products
	 */
	public function addProducts($products) {
		$resp = $this->request('product/create', ['products' => $products]);
		$result = $resp ?? false;
		return $result;
	}

	/**
	 * Add product
	 */
	public function addProduct($fields) {
		$result = $this->addProducts([$fields]);
		return $result;
	}

	/**
	 * Add products
	 */
	public function updateProducts($products) {
		$resp = $this->request('product/edit', ['products' => $products]);
		$result = $resp ?? false;
		return $result;
	}

	/**
	 * Add product
	 */
	public function updateProduct($product_id, $fields) {
		$fields['product_id'] = $product_id;
		$resp = $this->updateProducts([$fields]);
		$result = $resp['results'] ?? false;
		return $result;
	}

	/**
	 * Add product
	 */
	public function getProductTasks($group_id, $task_ids) {
		$tasks = [];
		$resp = $this->request('tasks?group_id=' . $group_id, [], false, 'GET');
		if (isset($resp['data'])) {
			foreach ($resp['data'] as $item) {
				if (in_array($item['id'], $task_ids)) {
					$tasks[$item['id']] = $item;
				}
			}
		}
		return $tasks;
	}

	/**
	 * Add product
	 */
	public function getBrands($limit=100) {
		$brands = [];
		$resp = $this->request('brand/get-brand-list', [
//			'filters' => [
//				'status' => ['approved']
//			],
			'limit' => $limit
		]);
		if (isset($resp['data']['list'])) {
			$brands = $resp['data']['list'];
		}
		return $brands;
	}

	/**
	 * Get size chart templates
	 */
	public function getSizeChartTemplates($category_id, $locale=false) {
		$filter = [
			'category_id' => $category_id,
		];
		if ($locale) {
			$filter['locale'] = $locale;
		}
		$resp = $this->request('product/edit', $filter);
		$result = $resp['data'] ?? [];
		return $result;
	}

	/**
	 * Get delivery templates
	 */
	public function getDelivTemplates() {
		$resp = $this->request('sellercenter/get-count-product-on-onboarding-template', [], false, self::METHOD_GET);
		$result = $resp['data']['templates'] ?? [];
		return $result;
	}

	/**
	 * Get values of category property
	 */
	public function getCategPropDictionaryValues($categ_id, $prop_id, $is_sku=false) {
		$resp = $this->request('categories/values-dictionary', [
			'category_id' => $categ_id,
			'property_id' => $prop_id,
			'is_sku_property' => $is_sku,
		]);
		$result = $resp['values'] ?? [];
		return $result;
	}

	/**
	 * Get values of category property
	 */
	public static function getStatusName($status_id) {
		$status_types = [
			1 => Loc::getMessage('ACRIT_EXPORT_PLUGIN_ALIAPILOC_API_TASK_STATUS_1'),
			2 => Loc::getMessage('ACRIT_EXPORT_PLUGIN_ALIAPILOC_API_TASK_STATUS_2'),
			3 => Loc::getMessage('ACRIT_EXPORT_PLUGIN_ALIAPILOC_API_TASK_STATUS_3'),
			4 => Loc::getMessage('ACRIT_EXPORT_PLUGIN_ALIAPILOC_API_TASK_STATUS_4'),
		];
		return $status_types[$status_id];
	}

}
