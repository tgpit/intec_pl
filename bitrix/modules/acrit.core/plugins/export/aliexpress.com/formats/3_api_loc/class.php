<?
/**
 * Acrit Core: Aliexpress plugin API Local (Russian)
 * @documentation https://business.aliexpress.ru/docs/category/open-api
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Log,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Json,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\Plugins\AliHelpers\Products,
	\Acrit\Core\Export\Plugins\AliexpressComApiLocalHelpers\TaskTable as Task,
	\PhpOffice\PhpSpreadsheet\Exception;

class AliexpressComApiLocal extends AliexpressCom {

	const DATE_UPDATED = '2023-02-23';
	const KEY_SEND_RESULT_TASKS = 'tasks';
	const KEY_SEND_RESULT_GROUP_ID = 'group_id';
	const KEY_SESSION_TASKS = 'SEND_TASKS';
	const KEY_EXPORT_ITERATION = 'EXPORT_ITERATION';
	const KEY_CHECK_ITERATION = 'CHECK_ITERATION';
	const KEY_CHECK_STARTTIME = 'CHECK_STARTTIME';
	const TASKS_MAN_WAIT_MAX = 180;
	const TASKS_MAN_WAIT_STEP = 15;
	const TASKS_CRON_WAIT_STEP = 60;
	const TASKS_CRON_WAIT_MAX = 3600;

	protected static $bSubclass = true;

	# General
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $bApi = true;
//	protected $arSupportedCurrencies = ['RUB'];
//	protected $bCategoryCustomName = true;
	protected $intExportPerStep = 50;
	protected $bOffersPreprocess = true;

	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = false;


	/**
	 * Get module id
	 */
	public function getModuleId() {
		return $this->strModuleId;
	}

	/**
	 * Get object for api requests
	 */

	public function getApi() {
		$api = new Products($this);
		return $api;
	}

	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__ . '/lib/api/products.php';
		require_once __DIR__ . '/lib/task.php';
		require_once __DIR__ . '/include/db_table_create.php';
	}

	/**
	 *	Add custom step
	 */
	protected function onUpGetSteps(&$arSteps){
		$arSteps['ALI_STEP_CHECK_TASKS'] = [
			'NAME' => static::getMessage('STEP_CHECK_TASKS'),
			'SORT' => 5000,
			'FUNC' => [$this, 'stepCheckTasks'],
		];
	}

	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['TOKEN'] = $this->includeHtml(__DIR__ . '/include/settings/token.php');
		if ($this->arParams['TOKEN']) {
			$arSettings['SECTIONS'] = $this->includeHtml(__DIR__ . '/include/settings/sections.php');
		}
		$arSettings['WAIT_TIME'] = $this->includeHtml(__DIR__ . '/include/settings/wait_time.php');
	}

	public function getTokenLink() {
		$link = "https://seller.aliexpress.ru/token-management/active";
		return $link;
	}

	/**
	 *	Show custom data at tab 'Log'
	 */
	public function getLogContent(&$strLogCustomTitle, $arGet){
		ob_start();
		require __DIR__.'/include/tasks/log.php';
		return ob_get_clean();
	}

	/**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		parent::ajaxAction($strAction, $arParams, $arJsonResult);
		#$arProfile = Profile::getProfiles($arParams['PROFILE_ID']);
		$strVkGroupId = strval($this->arProfile['PARAMS']['GROUP_ID']);
		switch ($strAction) {
			case 'check_connection':
				$token = $arParams['POST']['token'];
				$api = $this->getApi();
				$res = $api->checkConnection($token, $message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
			case 'get_sections':
				$selected_categs = $arParams['POST']['selected_categs'];
				if (!$selected_categs || empty($selected_categs)) {
					$selected_categs = $this->arProfile['PARAMS']['SECTION'];
				}
				$arCategLists = $this->getAliCategoryLists($selected_categs);
				$arJsonResult['lists'] = $arCategLists;
				$arJsonResult['result'] = 'ok';
				break;
			case 'refresh_tasks_list':
				$strLogCustomTitle=false;
				$arJsonResult['HTML'] = $this->getLogContent($strLogCustomTitle, $arParams['GET']);
				break;
			case 'update_task_status':
				$arJsonResult['HTML'] = $this->updateTaskStatus($arParams['GET']['task_id'], $arJsonResult);
				break;
		}
	}


	/**
	 *	Get selected section id
	 */
	public function getAliCategoryId() {
		$category_id = false;
		$arSections = $this->arProfile['PARAMS']['SECTION'];
		if (!is_array($arSections)) {
			$arSections = [];
		}
		$arSections = array_diff($arSections, ['']);
		if (count($arSections) > 0) {
			$category_id = $arSections[count($arSections) - 1];
		}
		return $category_id;
	}

	/**
	 * Correct $bProcessElement and $bProcessOffers
	 **/
	public function getProcessEntities(&$bProcessElement, &$bProcessOffers, $arProfile, $intIBlockID, $arElement){
		$this->bOriginalProcessElement = $bProcessElement;
		$this->bOriginalProcessOffers = $bProcessOffers;
		$bProcessElement = true;
	}


	/**
	 * Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		// Schema info
		$category_id = $this->getAliCategoryId();
		try {
			$arCategInfo = self::getAliCategAttribs($category_id);
		} catch (\Exception $e) {
			echo '<div class="adm-info-message-wrap"><div class="adm-info-message" style="color:red">' . static::getMessage('LOAD_ERROR') . ': ' . $e->getMessage() . ' (' . $e->getCode() . ')' . '</div></div>';
		}
//		$res = self::getAliProduct(1005002524642762);
//		echo '<pre>'; print_r($res); echo '</pre>';
		if (!$arCategInfo) {
			echo '<div class="adm-info-message-wrap"><div class="adm-info-message" style="color:red">' . static::getMessage('LOAD_WARNING') . '</div></div>';
		}
		// Iblock offers mode
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		if ($arCatalog['OFFERS'] == 'Y') {
			$bIBOffersMode = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['PARAMS']['OFFERS_MODE'];
		}
		else {
			$bIBOffersMode = $this->arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['OFFERS_MODE'];
		}
		$bOffers = Helper::isOffersIBlock($intIBlockID);
		// Full set of fields
		if ($bIBOffersMode != 'all' || !$bOffers) {
			// General fields
			$arResult['HEADER_GENERAL'] = [];
			$arResult['title'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
			$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'REQUIRED' => true];
			$arResult['description']['PARAMS']['HTMLSPECIALCHARS'] = 'skip';
			$arResult['sku_code'] = ['FIELD' => 'PROPERTY_ARTNUMBER', 'REQUIRED' => true];
			$arResult['main_image_urls_list'] = ['FIELD' => 'PROPERTY_MORE_PHOTO', 'MULTIPLE' => true, 'REQUIRED' => true];
			$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
			$arResult['discount_price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
			$arResult['inventory'] = ['FIELD' => 'CATALOG_QUANTITY_RESERVED'];
			$arResult['language'] = ['CONST' => 'ru', 'REQUIRED' => true];
			$arResult['weight'] = ['REQUIRED' => true];
			$arResult['package_width'] = ['REQUIRED' => true];
			$arResult['package_length'] = ['REQUIRED' => true];
			$arResult['package_height'] = ['REQUIRED' => true];
			$arResult['package_type'] = ['CONST' => false, 'REQUIRED' => true];
			$arResult['package_type']['ALLOWED_VALUES'] = [
				static::getMessage('SETTINGS_VALUES_package_type_values_pieces'),
				static::getMessage('SETTINGS_VALUES_package_type_values_lots'),
			];
			$arResult['package_type']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['package_type']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			$arResult['lot_num'] = [];
			$arResult['product_unit'] = ['CONST' => '100000013', 'REQUIRED' => true];
			$arResult['product_unit']['ALLOWED_VALUES'] = [
				'100000013' => static::getMessage('SETTINGS_VALUES_product_unit_values_13'),
				'100000014' => static::getMessage('SETTINGS_VALUES_product_unit_values_14'),
				'100000015' => static::getMessage('SETTINGS_VALUES_product_unit_values_15'),
				'100000017' => static::getMessage('SETTINGS_VALUES_product_unit_values_17'),
				'100000019' => static::getMessage('SETTINGS_VALUES_product_unit_values_19'),
			];
			$arResult['product_unit']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['product_unit']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			$arResult['shipping_lead_time'] = ['CONST' => '30', 'REQUIRED' => true];
			$arResult['size_chart_id'] = [];
			$arResult['size_chart_id']['ALLOWED_VALUES'] = $this->getAliSizeChartTemplates($category_id);
			$arResult['size_chart_id']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['size_chart_id']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			$arResult['bulk_discount'] = ['CONST' => ''];
			$arResult['bulk_order'] = ['CONST' => ''];
			$arResult['tnved_codes'] = ['CONST' => ''];
			$arResult['gtin'] = ['CONST' => ''];
			$arResult['okpd2_codes'] = ['CONST' => ''];
			$arResult['freight_template_id'] = ['CONST' => '', 'REQUIRED' => true];
			$arResult['freight_template_id']['ALLOWED_VALUES'] = $this->getAliDelivTemplates();
			$arResult['freight_template_id']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['freight_template_id']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			// Category base properties
			if (isset($arCategInfo['properties'])) {
				foreach ($arCategInfo['properties'] as $arProp) {
					$strKey = 'categ_prop_' . $arProp['id'];
					$arResult[$strKey] = [
						'NAME'     => $arProp['name'],
						'CONST'    => '',
						'REQUIRED' => $arProp['is_required'],
					];
					$arValues = [];
					// Exclude for brands
					if ($arProp['id'] == 2) {
						$arValues = $this->getAliSellerBrands();
					}
					// Other properties
					else {
						if (isset($arProp['values'])) {
							$arValues[''] = '';
							foreach ($arProp['values'] as $arItem) {
								$arValues[$arItem['id']] = $arItem['name'];
							}
						}
					}
					if ( ! empty($arValues)) {
						$arResult[$strKey]['ALLOWED_VALUES'] = $arValues;
						$arResult[$strKey]['ALLOWED_VALUES_USE_SELECT'] = true;
						$arResult[$strKey]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
					}
				}
			}
			if ($bIBOffersMode != 'all') {
				// Category SKU properties
				if (isset($arCategInfo['sku_properties'])) {
					foreach ($arCategInfo['sku_properties'] as $arProp) {
						$strKey = 'categ_sku_prop_' . $arProp['id'];
						$arResult[$strKey] = [
							'NAME'     => $arProp['name'],
							'CONST'    => '',
							'REQUIRED' => $arProp['is_required'],
						];
						$arValues = [];
						if (isset($arProp['values'])) {
							$arValues[''] = '';
							foreach ($arProp['values'] as $arItem) {
								$arValues[$arItem['id']] = $arItem['name'];
							}
						}
						if ( ! empty($arValues)) {
							$arResult[$strKey]['ALLOWED_VALUES'] = $arValues;
							$arResult[$strKey]['ALLOWED_VALUES_USE_SELECT'] = true;
							$arResult[$strKey]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
						}
					}
				}
			}
		}
		// Fields for sku (displayed on the sku tab)
		else {
			$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
			$arResult['discount_price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
			$arResult['inventory'] = ['FIELD' => 'CATALOG_QUANTITY_RESERVED'];
			$arResult['sku_code'] = ['FIELD' => 'PROPERTY_ARTNUMBER', 'REQUIRED' => true];
			// Category SKU properties
			if (isset($arCategInfo['sku_properties'])) {
				foreach ($arCategInfo['sku_properties'] as $arProp) {
					$strKey = 'categ_sku_prop_' . $arProp['id'];
					$arResult[$strKey] = [
						'NAME'     => $arProp['name'],
						'CONST'    => '',
						'REQUIRED' => $arProp['is_required'],
					];
					$arValues = [];
					if (isset($arProp['values'])) {
						$arValues[''] = '';
						foreach ($arProp['values'] as $arItem) {
							$arValues[$arItem['id']] = $arItem['name'];
						}
					}
					if ( ! empty($arValues)) {
						$arResult[$strKey]['ALLOWED_VALUES'] = $arValues;
						$arResult[$strKey]['ALLOWED_VALUES_USE_SELECT'] = true;
						$arResult[$strKey]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
					}
				}
			}
		}
		return $arResult;
	}

	protected function processElement_BuildJson($arElement, $arFields, $arElementSections, $intMainIBlockId) {
		$arResult = parent::processElement_BuildJson($arElement, $arFields, $arElementSections, $intMainIBlockId);
		# Add additional fields
		$arResult['DATA_MORE']['ADDITIONAL_FIELDS'] = [];
		$intIBlockId = $arElement['IBLOCK_ID'];
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock',
			[$this->intProfileId, $intIBlockId]);
		if (is_array($arAdditionalFields) && !empty($arAdditionalFields)) {
			foreach ($arAdditionalFields as $arAdditionalField) {
				if (isset($arFields[$arAdditionalField['FIELD']])) {
					$arResult['DATA_MORE']['ADDITIONAL_FIELDS'][$arAdditionalField['NAME']] = $arFields[$arAdditionalField['FIELD']];
				}
			}
		}
		return $arResult;
	}


	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		$mResult = Exporter::RESULT_ERROR;
		if ($this->bCron) {
			do {
				$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
			}
			while ($mResult === Exporter::RESULT_CONTINUE);
		}
		else {
			$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
		}
		return $mResult;
	}

	/**
	 *	Export data by API (one step)
	 */
	protected function stepExport_ExportApi_Step(&$arSession, $arStep) {
		$arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_EXPORT_ITERATION]++;
		// Reset tasks of previous export
		if ($arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_EXPORT_ITERATION] == 1) {
			$this->resetPrevTasks();
		}
		// Get category info
		$category_id = $this->getAliCategoryId();
		if (!$category_id) {
			$this->addToLog(static::getMessage('ERROR_NOT_SET_CATEG_ID'));
			return Exporter::RESULT_ERROR;
		}
		$arCategInfo = self::getAliCategAttribs($category_id, false);
		if (!$arCategInfo) {
			$this->addToLog(static::getMessage('ERROR_NOT_LOADED_CATEG_ATTRIBS'));
			return Exporter::RESULT_ERROR;
		}
		// Get items
		$arItems = $this->getExportDataItems();
		if (!empty($arItems)) {
			$arAddProducts = [];
			$arUpdateProducts = [];
			foreach ($arItems as $arItem) {
				// Get export data
				$arEncodedItem = Json::decode($arItem['DATA']);
				$this->addToLog('arEncodedItem ' . print_r($arEncodedItem, true), true);
				// Export mode
				$intIBlockID = $arItem['IBLOCK_ID'];
				$bOffer = Helper::isOffersIBlock($intIBlockID);
				if($bOffer && isset($arEncodedItem['_OFFER_PREPROCESS'])){
					unset($arEncodedItem['_OFFER_PREPROCESS']);
				}
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if ($arCatalog['OFFERS'] == 'Y') {
					$bIBOffersMode = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['PARAMS']['OFFERS_MODE'];
				}
				else {
					$bIBOffersMode = $this->arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['OFFERS_MODE'];
				}
				// Forming export element
				$arProductsFields = [];
				if ($bIBOffersMode == 'all' || $bIBOffersMode == '' || $bIBOffersMode == 'none' ||
					($bIBOffersMode == 'only' && (!isset($arEncodedItem['_OFFER_PREPROCESS']) || empty($arEncodedItem['_OFFER_PREPROCESS'])))) {
					$get_sku_list = ($bIBOffersMode == 'all') ? true : false;
					$arProductsFields[] = $this->getExportItem($arItem, $arCategInfo, $get_sku_list);
				}
				if ($bIBOffersMode == 'only' || $bIBOffersMode == 'offers') {
					// Get items from offers
					if (isset($arEncodedItem['_OFFER_PREPROCESS']) && ! empty($arEncodedItem['_OFFER_PREPROCESS'])) {
						foreach ($arEncodedItem['_OFFER_PREPROCESS'] as $arOfferItem) {
							if ($arOfferItem['DATA']) {
								$arProductsFields[] = $this->getExportItem($arOfferItem, $arCategInfo);
							}
						}
					}
				}
				// Save items
				$this->addToLog('arProductsFields ' . print_r($arProductsFields, true), true);
				foreach ($arProductsFields as $arProductFields) {
					$sku_code = $this->getExportItemSkuCode($arProductFields);
					$this->addToLog('arProductFields json ' . Json::encode($arProductFields), true);
					$this->addToLog('sku_code ' . print_r($sku_code, true), true);
					// Fill data
					$product_id = self::getAliProductIdBySku($sku_code);
					$this->addToLog('product_id ' . $product_id, true);
					if ( ! $product_id) {
						$arAddProducts[$arItem['ELEMENT_ID']] = $arProductFields;
					} else {
						$arProductFields['product_id'] = $product_id;
						$arUpdateProducts[$arItem['ELEMENT_ID']] = $arProductFields;
					}
					// Save step result
					$this->setDataItemExported($arItem['ID']);
					$arSession['INDEX'] ++;
				}
			}
			// Send new products data
			$this->addToLog('arAddProducts cnt ' . count($arAddProducts), true);
			if (!empty($arAddProducts)) {
				$arErrors = [];
				$arItemsTasks = [];
				$intGroupId = $this->addAliProducts($arAddProducts, $arErrors, $arItemsTasks);
				// For next step
				$arSession[self::KEY_SESSION_TASKS][] = [
					self::KEY_SEND_RESULT_GROUP_ID => $intGroupId,
					self::KEY_SEND_RESULT_TASKS    => $arItemsTasks
				];
				// For delayed check
				$this->saveTasks($intGroupId, $arItemsTasks, Task::TYPE_ADD);
				// Display errors
				foreach ($arErrors as $intItemId => $arItemErrors) {
					$this->addToLog(static::getMessage('ERRORS_FOR_ADD_ITEM', ['#ITEM_ID#' => $intItemId]) . ': ' . print_r($arItemErrors, true));
//					return Exporter::RESULT_ERROR;
				}
			}
			// Send existed products data
			$this->addToLog('arUpdateProducts cnt ' . count($arUpdateProducts), true);
			if (!empty($arUpdateProducts)) {
				$arErrors = [];
				$arItemsTasks = [];
				$intGroupId = $this->updateAliProducts($arUpdateProducts, $arErrors, $arItemsTasks);
				// For next step
				$arSession[self::KEY_SESSION_TASKS][] = [
					self::KEY_SEND_RESULT_GROUP_ID => $intGroupId,
					self::KEY_SEND_RESULT_TASKS    => $arItemsTasks
				];
				// For delayed check
				$this->saveTasks($intGroupId, $arItemsTasks, Task::TYPE_UPDATE);
				// Display errors
				foreach ($arErrors as $intItemId => $arItemErrors) {
					$this->addToLog(static::getMessage('ERRORS_FOR_EDIT_ITEM', ['#ITEM_ID#' => $intItemId]) . ': ' . print_r($arItemErrors, true));
//					return Exporter::RESULT_ERROR;
				}
			}
			return Exporter::RESULT_CONTINUE;
		}
		return Exporter::RESULT_SUCCESS;
	}

	public function getExportItem($arItem, $arCategInfo, $bSkuList=false) {
		$arEncodedItem = Json::decode($arItem['DATA']);
		// $arDataMore = unserialize($arItem['DATA_MORE']);
		$arDataMore = $arItem['DATA_MORE'];
		$arProductFields = [];
		$category_id = $this->getAliCategoryId();
		$arProductFields['aliexpress_category_id'] = $category_id;
		$arProductFields['freight_template_id'] = $arEncodedItem['freight_template_id'];
		$arProductFields['language'] = $arEncodedItem['language'];
		$arProductFields['lot_num'] = $arEncodedItem['lot_num'];
		$arProductFields['main_image_urls_list'] = $arEncodedItem['main_image_urls_list'];
		$arProductFields['multi_language_subject_list'] = [[
			'language' => $arEncodedItem['language'],
			'subject'  => $arEncodedItem['title'],
		]];
		$arProductFields['multi_language_description_list'] = [[
			'language' => $arEncodedItem['language'],
			'web'      => $arEncodedItem['description'],
			'mobile'   => $arEncodedItem['description'],
		]];
		$arProductFields['package_length'] = $arEncodedItem['package_length'];
		$arProductFields['package_height'] = $arEncodedItem['package_height'];
		$arProductFields['package_width'] = $arEncodedItem['package_width'];
		$arProductFields['package_type'] = (boolean)$arEncodedItem['package_type'];
		$arProductFields['weight'] = $arEncodedItem['weight'];
		$arProductFields['product_unit'] = $arEncodedItem['product_unit'];
		$arProductFields['shipping_lead_time'] = $arEncodedItem['shipping_lead_time'];
		$arProductFields['size_chart_id'] = $arEncodedItem['size_chart_id'];
		$arProductFields['bulk_discount'] = (int)$arEncodedItem['bulk_discount'];
		$arProductFields['bulk_order'] = (int)$arEncodedItem['bulk_order'];
		// Add category attributes
		$arProductFields['attribute_list'] = [];
		foreach ($arEncodedItem as $key => $value) {
			if (strpos($key, 'categ_prop_') === 0 && $value) {
				$strId = str_replace('categ_prop_', '', $key);
				if (isset($arCategInfo['properties'][$strId])) {
					$arPropInfo = $arCategInfo['properties'][$strId];
					$arAttr = [
						'attribute_name_id' => $strId
					];
					if ($arPropInfo['is_enum_prop']) {
						$arAttr['attribute_value_id'] = $value;
					}
					else {
						$arAttr['attribute_value'] = $value;
					}
					$arProductFields['attribute_list'][] = $arAttr;
				}
			}
		}
		// Add list of SKU
		$arProductFields['sku_info_list'] = $this->getExportItemSku($arItem, $arCategInfo, $bSkuList);
		return $arProductFields;
	}

	public function getExportItemSku($arItem, $arCategInfo, $sku_list=false) {
		$list = [];
		$arEncodedItem = Json::decode($arItem['DATA']);
		if ($sku_list && isset($arEncodedItem['_OFFER_PREPROCESS']) && ! empty($arEncodedItem['_OFFER_PREPROCESS'])) {
			foreach ($arEncodedItem['_OFFER_PREPROCESS'] as $arOfferItem) {
				if ($arOfferItem['DATA']) {
					$arOfferEncodedItem = Json::decode($arOfferItem['DATA']);
					$this->addToLog('arOfferEncodedItem ' . print_r($arOfferEncodedItem, true), true);
					// Basic fields
					$arSkuItem = [
						'sku_code'       => $arOfferEncodedItem['sku_code'],
						'inventory'      => $arOfferEncodedItem['inventory'],
						'price'          => $arOfferEncodedItem['price'],
						'discount_price' => $arOfferEncodedItem['discount_price'],
					];
					// Additional attributes
					$arSkuItem['sku_attributes_list'] = [];
					foreach ($arOfferEncodedItem as $key => $value) {
						if (strpos($key, 'categ_sku_prop_') === 0 && $value) {
							$strId = str_replace('categ_sku_prop_', '', $key);
							if (isset($arCategInfo['properties'][$strId])) {
								$arPropInfo = $arCategInfo['properties'][$strId];
								$arAttr = [
									'sku_attribute_name_id' => $strId
								];
								if ($arPropInfo['is_enum_prop']) {
									$arAttr['sku_attribute_value_id'] = $value;
								}
								else {
									$arAttr['sku_attribute_value'] = $value;
								}
								$arSkuItem['sku_attributes_list'][] = $arAttr;
							}
						}
					}
					$list[] = $arSkuItem;
				}
			}
		} else {
			// Basic fields
			$arSkuItem = [
				'sku_code'       => $arEncodedItem['sku_code'],
				'inventory'      => $arEncodedItem['inventory'],
				'price'          => $arEncodedItem['price'],
				'discount_price' => $arEncodedItem['discount_price'],
			];
			// Additional attributes
			$arSkuItem['sku_attributes_list'] = [];
			foreach ($arEncodedItem as $key => $value) {
				if (strpos($key, 'categ_sku_prop_') === 0 && $value) {
					$strId = str_replace('categ_sku_prop_', '', $key);
					if (isset($arCategInfo['properties'][$strId])) {
						$arPropInfo = $arCategInfo['properties'][$strId];
						$arAttr = [
							'sku_attribute_name_id' => $strId
						];
						if ($arPropInfo['is_enum_prop']) {
							$arAttr['sku_attribute_value_id'] = $value;
						}
						else {
							$arAttr['sku_attribute_value'] = $value;
						}
						$arSkuItem['sku_attributes_list'][] = $arAttr;
					}
				}
			}
			$list[0] = $arSkuItem;
		}
		return $list;
	}

	public function getExportItemSkuCode($arFields) {
		$sku_code = false;
		if (is_array($arFields['sku_info_list']) && !empty($arFields['sku_info_list'])) {
			$sku_code = $arFields['sku_info_list'][0]['sku_code'];
		}
		return $sku_code;
	}

	/**
	 * Check tasks of sent items
	 *
	 * @param mixed $intProfileID
	 * @param mixed $arSession
	 */
	public function stepCheckTasks($intProfileID, &$arSession) {
		// For manual export
		$arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_CHECK_ITERATION]++;
		$this->addToLog('iteration num ' . $arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_CHECK_ITERATION], true);
		if ($arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_CHECK_ITERATION] == 1) {
			$arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_CHECK_STARTTIME] = time();
		}
		// Data for checking
		$arGroups = $arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_SESSION_TASKS];
		$this->addToLog('cron ' . print_r($this->bCron, true), true);
		$arErrors = [];
		$arSuccess = [];
		// Run checking iteration
		$this->addToLog('arGroups ' . print_r($arGroups, true), true);
		$time_start = time();
		$wait_time = $this->bCron ? self::TASKS_CRON_WAIT_STEP : self::TASKS_MAN_WAIT_STEP;
		$max_wait_time = $this->bCron ? self::TASKS_CRON_WAIT_MAX : 0;
		$wait_time_end = $time_start + $max_wait_time;
		do {
			sleep($wait_time);
			$this->addToLog('start iteration', true);
			$proc_cnt = $this->stepCheckTasks_Iteration($arGroups, $arErrors, $arSuccess, $arLeftItems);
		} while (time() < $wait_time_end && !empty($arLeftItems));
		// Result
		$result = Exporter::RESULT_SUCCESS;
		if ($this->bCron) {
			// Display results of iteration
			$this->addToLog(static::getMessage('CHECKED_COUNT') . $proc_cnt);
			$this->stepCheckTasks_Report($arErrors, $arSuccess, $arLeftItems);
		}
		else {
			$wait_time_max = $this->arProfile['PARAMS']['WAIT_TIME'] ? : self::TASKS_MAN_WAIT_MAX;
			$wait_time_end = $arSession['PROFILE']['SESSION']['EXPORT'][self::KEY_CHECK_STARTTIME] + $wait_time_max;
			if (time() < $wait_time_end && !empty($arLeftItems)) {
				$result = Exporter::RESULT_CONTINUE;
				// Display results of iteration
				$this->stepCheckTasks_Report($arErrors, $arSuccess, []);
			}
			else {
				// Display results of iteration
				$this->stepCheckTasks_Report($arErrors, $arSuccess, $arLeftItems);
			}
		}
		return $result;
	}

	public function stepCheckTasks_Iteration(&$arGroups, &$arErrors, &$arSuccess, &$arLeftItems) {
		$proc_cnt = 0;
		$arLeftItems = [];
		foreach ($arGroups as $intGIndex => $arGroup) {
			// Check tasks group
			$intGroupId = $arGroup[self::KEY_SEND_RESULT_GROUP_ID];
			$arItemsTasks = $arGroup[self::KEY_SEND_RESULT_TASKS];
			$arCheckRes = $this->checkAliProductsTasks($intGroupId, array_values($arItemsTasks));
			$this->addToLog('arCheckRes ' . print_r($arCheckRes, true), true);
			foreach ($arItemsTasks as $intItemId => $intTaskId) {
				$arTaskCheck = $arCheckRes[$intTaskId];
				if ($arTaskCheck['status'] == 4) {
					$arErrors[$intItemId] = $arTaskCheck['error'];
					// Exclude from next iterations
					unset($arGroups[$intGIndex][self::KEY_SEND_RESULT_TASKS][$intItemId]);
					$proc_cnt++;
				} elseif ($arTaskCheck['status'] == 3) {
					$arSuccess[] = $intItemId;
					// Exclude from next iterations
					unset($arGroups[$intGIndex][self::KEY_SEND_RESULT_TASKS][$intItemId]);
					$proc_cnt++;
				}
			}
		}
		// Left tasks
		foreach ($arGroups as $arGroup) {
			// Check tasks group
			$arItemsTasks = $arGroup[self::KEY_SEND_RESULT_TASKS];
			foreach ($arItemsTasks as $intItemId => $intTaskId) {
				$arLeftItems[] = $intItemId;
			}
		}
		return $proc_cnt;
	}

	public function stepCheckTasks_Report($arErrors, $arSuccess, $arLeftItems) {
		foreach ($arErrors as $intItemId => $arItemErrors) {
			$this->addToLog(static::getMessage('ERRORS_FOR_SENT_ITEM', ['#ITEM_ID#' => $intItemId]) . print_r($arItemErrors, true));
		}
		if (!empty($arSuccess)) {
			$this->addToLog(static::getMessage('SUCCESS_SENT_ITEMS') . implode(', ', $arSuccess));
		}
		if (!empty($arLeftItems)) {
			$this->addToLog(static::getMessage('NOT_CHECKED_ITEMS') . implode(', ', $arLeftItems));
		}
	}

	public function resetPrevTasks() {
		Task::deleteByFilter([
			'PROFILE_ID' => $this->intProfileId,
		]);
	}

	public function saveTasks($intGroupId, $arItemsTasks, $type=0) {
		foreach ($arItemsTasks as $intProductId => $intTaskId) {
			$obDate = new \Bitrix\Main\Type\Datetime();
			$arTask = [
				'MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'PRODUCT_ID' => $intProductId,
				'GROUP_ID' => $intGroupId,
				'TASK_ID' => $intTaskId,
				'TYPE' => $type,
				'SESSION_ID' => session_id(),
				'TIMESTAMP_X' => $obDate,
			];
			Task::add($arTask);
		}
	}

	/**
	 * Update tasks
	 */
	public function updateSavedTasks($task_ids=[]) {
		$arTaskGroups = [];
		// Get base list
		$filter = [
//			'<STATUS_ID' => 3,
		];
		if (!empty($task_ids)) {
			$filter['ID'] = $task_ids;
		}
		$tasks = Task::getList([
			'filter' => $filter
		]);
		while ($task = $tasks->fetch()) {
			$arTaskGroups[$task['GROUP_ID']][] = $task['TASK_ID'];
		}
		// Get status info
		foreach ($arTaskGroups as $intGroupId => $arTaskIds) {
			$arCheckResult = $this->checkAliProductsTasks($intGroupId, $arTaskIds);
			foreach ($arCheckResult as $intTaskId => $arCheckInfo) {
				$errors = $arCheckInfo['error'] ? json_encode($arCheckInfo['error']) : $arCheckInfo['error'];
				Task::updateTaskStatus($intTaskId, $arCheckInfo['status'], $errors);
			}
		}
		return true;
	}

	/**
	 *	Handle click on button 'update'
	 */
	protected function updateTaskStatus($intTaskId, &$arJsonResult) {
		$strResultHtml = [];
		$intTaskId = intVal($intTaskId);
		if ($intTaskId) {
			if ($arTask = $this->updateSingleTaskStatus($intTaskId)) {
				$strResultHtml = $this->displayTaskStatus($arTask);
			}
		}
		return $strResultHtml;
	}

	/**
	 *	Update status for one task
	 *	@return $arTask - array of task
	 */
	protected function updateSingleTaskStatus($intTaskId) {
		$mResult = false;
		$intTaskId = intVal($intTaskId);
		if ($intTaskId) {
			$this->updateSavedTasks([$intTaskId]);
			$arQuery = [
				'filter' => [
					'ID' => $intTaskId
				],
			];
			$arTasks = Task::getListData($arQuery);
			$mResult = $arTasks[0];
		}
		return $mResult;
	}

	/**
	 *	Display status for one task
	 */
	protected function displayTaskStatus($arTask) {
		$strResultHtml = [
			'status' => '',
			'errors' => '',
		];
		if (is_array($arTask) && !empty($arTask)) {
			$strResultHtml['status'] = $arTask['STATUS_NAME'] . ' [' . $arTask['STATUS_ID'] . ']';
			if (is_array($arTask['ERRORS']) && count($arTask['ERRORS']) == 2) {
				$errors = $arTask['ERRORS']['message'] . ' [' . $arTask['ERRORS']['code'] . ']';
			}
			else {
				$errors = print_r($arTask['ERRORS'], true);
			}
			$strResultHtml['errors'] = $errors;
		}
		return $strResultHtml;
	}


	/// --- API WRAPPERS --- ///

	/**
	 * Get products list
	 */
	public function getAliProductIdBySku($sku_code) {
		$product_id = false;
		$filter = [
			'search_content' => [
				'content_values' => [$sku_code],
				'content_type' => 'SKU_SELLER_SKU'
			]
		];
		$api = $this->getApi();
		$list = $api->getProductList($filter, 1);
		if (!empty($list)) {
			$product_id = $list[0]['id'];
		}
		return $product_id;
	}

	/**
	 * Get product info
	 */
	public function getAliProduct($product_id) {
		$api = $this->getApi();
		return $api->getProduct($product_id);
	}

	/**
	 * Add the product
	 */
	public function addAliProducts($products, &$errors, &$tasks) {
		$api = $this->getApi();
		$send_res = $api->addProducts(array_values($products));
		$group_id = $send_res['group_id'];
		if (isset($send_res['results'])) {
			$i = 0;
			foreach ($products as $item_id => $item) {
				if (isset($send_res['results'][$i])) {
					$item_res = $send_res['results'][$i];
					if (!isset($item_res['ok']) || !$item_res['ok']) {
						$errors[$item_id] = $item_res['errors'];
					}
					else {
						$tasks[$item_id] = $item_res['task_id'];
					}
				}
				$i++;
			}
		}
		return $group_id;
	}

	/**
	 * Update the product
	 */
	public function updateAliProducts($products, &$errors, &$tasks) {
		$api = $this->getApi();
		$send_res = $api->updateProducts(array_values($products));
		$this->addToLog('updateAliProducts result ' . print_r($send_res, true), true);
		$group_id = $send_res['group_id'];
		if (isset($send_res['results'])) {
			$i = 0;
			foreach ($products as $item_id => $item) {
				if (isset($send_res['results'][$i])) {
					$item_res = $send_res['results'][$i];
					if (!isset($item_res['ok']) || !$item_res['ok']) {
						$errors[$item_id] = $item_res['errors'];
					}
					else {
						$tasks[$item_id] = $item_res['task_id'];
					}
				}
				$i++;
			}
		}
		return $group_id;
	}

	/**
	 * Get product info
	 */
	public function checkAliProductsTasks($group_id, $task_ids) {
		$tasks = [];
		if (!empty($task_ids)) {
			$api = $this->getApi();
			$tasks = $api->getProductTasks($group_id, $task_ids);
		}
		return $tasks;
	}

	/**
	 * Get attributes for the products of category
	 */
	public function getAliCategAttribs($category_id, $load_dict=true) {
		$api = $this->getApi();
		$arCategInfo = $api->getCateg($category_id);
		if ($load_dict) {
			if (isset($arCategInfo['properties'])) {
				foreach ($arCategInfo['properties'] as $k => $arProp) {
					if ($arProp['is_enum_prop']) {
						$arCategInfo['properties'][$k]['values'] = $api->getCategPropDictionaryValues($category_id, $arProp['id']);
					}
				}
			}
			if (isset($arCategInfo['sku_properties'])) {
				foreach ($arCategInfo['sku_properties'] as $k => $arProp) {
					if ($arProp['is_enum_prop']) {
						$arCategInfo['sku_properties'][$k]['values'] = $api->getCategPropDictionaryValues($category_id, $arProp['id'], true);
					}
				}
			}
		}
		return $arCategInfo;
	}

	/**
	 * Get size chart templates
	 */
	public function getAliSizeChartTemplates($category_id, $locale=false) {
		$list = [];
		$api = $this->getApi();
		$templates = $api->getSizeChartTemplates($category_id, $locale);
		foreach ($templates as $template) {
			$list[$template['id']] = $template['name'];
		}
		return $list;
	}

	/**
	 * Get delivery templates
	 */
	public function getAliDelivTemplates() {
		$list = [];
		$api = $this->getApi();
		$templates = $api->getDelivTemplates();
		foreach ($templates as $template) {
			$list[$template['templateId']] = $template['templateName'];
		}
		return $list;
	}

	/**
	 * Get delivery templates
	 */
	public function getAliSellerBrands() {
		$list = [];
		$api = $this->getApi();
		$brands = $api->getBrands();
		foreach ($brands as $brand) {
			$list[$brand['id']] = $brand['name'];
		}
		return $list;
	}

	/**
	 * Get categories list
	 */
	public function getAliCategories($parent_category_id) {
		$list = [];
		$api = $this->getApi();
		$categ_list = $api->getCategList($parent_category_id);
		if (!empty($categ_list)) {
			foreach ($categ_list as $item) {
				$name = $item['name'];
				$list[] = [
					'id' => $item['id'],
					'name' => $name,
				];
			}
		}
		return $list;
	}

	/**
	 * Get categories lists
	 */
	public function getAliCategoryLists($arSelected) {
		$arCategLists = [];
		if (!is_array($arSelected) || empty($arSelected)) {
			$arSelected = [];
		}
		$arSList = $this->getAliCategories(0);
		foreach ($arSelected as $section_id) {
			$arSIDs = [];
			foreach ($arSList as $item) {
				$arSIDs[] = $item['id'];
			}
			if (in_array($section_id, $arSIDs)) {
				$arCategList = [];
				foreach ($arSList as $item) {
					$arCategItem = $item;
					if ($item['id'] == $section_id) {
						$arCategItem['selected'] = true;
					}
					$arCategList[] = $arCategItem;
				}
				$arCategLists[] = $arCategList;
				$arSList = $this->getAliCategories($section_id);
			}
		}
		if (!empty($arSList)) {
			$arCategList = [];
			foreach ($arSList as $item) {
				$arCategItem = $item;
				if ($item['id'] == $section_id) {
					$arCategItem['selected'] = true;
				}
				$arCategList[] = $arCategItem;
			}
			$arCategLists[] = $arCategList;
		}
		return $arCategLists;
	}

	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		return $this->getExtFileOpenLink('https://seller.aliexpress.ru/', 
			Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}
	
}
