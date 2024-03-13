<?
/**
 * Acrit Core: ozon.ru plugin
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\Api,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\CategoryTable as Category,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\AttributeValueTable as AttributeValue,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\TaskTable as Task,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\HistoryTable as History,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\HistoryStockTable as HistoryStock;
	

class OzonRuV2 extends UniversalPlugin {
	
	const DATE_UPDATED = '2022-06-06';
	const CACHE_VALID_TIME = 24*60*60; // Too many values => we would not update often
	const AJAX_STEP_TIME = 5; // 5 seconds to every ajax step
	const ATTRIBUTE_ID = 'attribute_%s_%s';
	const STOCKS_V2_COUNT_PER_REQUEST = 100;

	const ATTR_ID_IMAGE = 4194;
	const ATTR_ID_IMAGES = 4195;
	const ATTR_ID_YOUTUBE_COMPLEX_ID = 4018;
	const ATTR_ID_YOUTUBE_CODE = 21841; # 4074
	const ATTR_ID_YOUTUBE_TITLE = 21837; # 4068
	const ATTR_ID_JSON_RICH_CONTENT = 11254;
	
	const GROUPED_CODE = 'GROUPED';

	protected static $bSubclass = true;
	
	# Basic settings
	protected $arSupportedFormats = ['JSON']; // Формат выгрузки - JSON
	protected $bApi = true; // Выгружаем не в файл, а по АПИ
	protected $bCategoriesExport = true; // Нужно чтобы в целом была возможность работать с категориями, хотя категории отдельно не выгружаются
	protected $bCategoriesList = true; // В плагине доступен список категорий, необходимо для работы со списком категорий
	protected $bCategoriesUpdate = true; // Разрешаем обновлять категории
	protected $bCategoriesStrict = true; // На озоне важно указывать только «озоновские» категории
	protected $bCategoryCustomName = true; // Добавляем возможность использовать значение «Использовать поля товаров» в опции «Источник названий категорий»
	protected $arSupportedEncoding = [self::UTF8];
	protected $intExportPerStep = 50; // 50 товаров за 1 шаг
	
	# Misc
	protected $strCategoryLevelDelimiter = ' / '; // Символ(ы) для разделения категорий разных уровней (пример: Авто / Оборудование / Магнитолы)
	protected $obStocksDate = null;
	
	# API class
	protected $API;
	
	# Cache
	protected $arCacheRequiredAttributes = [];
	protected $arCacheDictionaryAttributes = [];
	protected $arCacheSkipValidateAttributes = [];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		if($this->isStockAndPrice()){
			$arStockAndPriceAllowedFields = ['HEADER_GENERAL', 'offer_id', 'price', 'old_price', 'min_price', 'premium_price', 'auto_action_enabled', 'stock'];
		}
		# Add common attributes
		$arResult['HEADER_GENERAL'] = [];
		$arResult['offer_id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
		$arResult['images'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true, 'REQUIRED' => true];
		$arResult['primary_image'] = [];
		$arResult['image_group_id'] = ['CONST' => ''];
		$arResult['pdf_list'] = ['FIELD' => 'PROPERTY_PDF', 'MULTIPLE' => true,];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'REQUIRED' => !$this->isStockAndPrice()];
		$arResult['old_price'] = ['FIELD' => 'CATALOG_PRICE_1'];
		$arResult['min_price'] = [];
		$arResult['premium_price'] = [];
		$arResult['auto_action_enabled'] = ['CONST' => ''];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_FLOAT', 'REQUIRED' => true];
		$arResult['stock'] = []; # ToDo: if(!$this->useStores()) {$arResult['stock'] = []}
		if($this->useStores()){
			foreach($this->getStores() as $intStoreId => $strStoreName){
				$arResult['stock_'.$intStoreId] = [
					'NAME' => static::getMessage('STOCK_X', ['#ID#' => $intStoreId, '#NAME#' => $strStoreName]),
				];
				$arStockAndPriceAllowedFields[] = 'stock_'.$intStoreId;
			}
		} # ToDo: add "else" with $arResult['stock'] = []
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE', 'REQUIRED' => $this->isBarcodeRequired($intIBlockId)];
		$arResult['depth'] = ['FIELD' => 'CATALOG_LENGTH', 'REQUIRED' => true, 'FIELD_PARAMS' => $this->getRoundParams()];
		$arResult['width'] = ['FIELD' => 'CATALOG_WIDTH', 'REQUIRED' => true, 'FIELD_PARAMS' => $this->getRoundParams()];
		$arResult['height'] = ['FIELD' => 'CATALOG_HEIGHT', 'REQUIRED' => true, 'FIELD_PARAMS' => $this->getRoundParams()];
		$arResult['dimension_unit'] = ['CONST' => 'mm', 'REQUIRED' => true];
		$arResult['weight'] = ['FIELD' => 'CATALOG_WEIGHT', 'REQUIRED' => true, 'FIELD_PARAMS' => $this->getRoundParams()];
		$arResult['weight_unit'] = ['CONST' => 'g', 'REQUIRED' => true];
		$arResult['category_id'] = ['REQUIRED' => !!$this->bAdmin];
		$arResult['video_youtube'] = ['MULTIPLE' => true, 'FIELD' => ['PROPERTY_YOUTUBE', 'PROPERTY_VIDEO'],
			'FIELD_PARAMS' => ['MULTIPLE' => 'multiple'], 'PARAMS' => ['MULTIPLE' => 'multiple']];
		# Add special attributes (depends on category)
		if($this->isStockAndPrice()){
			$arResult = array_intersect_key($arResult, array_flip($arStockAndPriceAllowedFields));
		}
		else{
			$arData = $this->getDataForFields($intIBlockId);
			foreach($arData as $arCategory){
				if(!isset($arCategory['CATEGORY_ID'])){
					continue;
				}
				$arResult['HEADER_CATEGORY_'.$arCategory['CATEGORY_ID']] = [
					'NAME' => $this->formatCategoryName($arCategory['CATEGORY_ID'], $arCategory['NAME']),
					'NORMAL_CASE' => true,
				];
				foreach($arCategory['ATTRIBUTES'] as $arAttribute){
					$strAttributeId = sprintf(static::ATTRIBUTE_ID, $arCategory['CATEGORY_ID'], $arAttribute['ATTRIBUTE_ID']);
					$arField = [
						'NAME' => $arAttribute['NAME'],
						'DISPLAY_CODE' => 'attribute_'.$arAttribute['ATTRIBUTE_ID'],
						'DESCRIPTION' => $this->descriptionToHint($arAttribute['DESCRIPTION']),
						'REQUIRED' => count($arData) == 1 && $arAttribute['IS_REQUIRED'] == 'Y' 
							|| $arAttribute['FORCE_REQUIRED'] == 'Y',
						'MULTIPLE' => true,
						'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
						'PARAMS' => ['MULTIPLE' => 'multiple'],
					];
					if($arAttribute['DICTIONARY_ID']){
						$arField['ALLOWED_VALUES_CUSTOM'] = true;
					}
					$this->guessDefaultValue($arField, $arAttribute);
					$arResult[$strAttributeId] = $arField;
				}
			}
		}
		#
		return $arResult;
	}

	protected function isBarcodeRequired($intIBlockId){
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
			? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		return $this->arProfile['IBLOCKS'][$intMainIBlockId]['PARAMS']['BARCODE_NOT_REQUIRED'] != 'Y';
	}

	protected function getSkipValidateAttributes($intIBlockId){
		if(!is_array($this->arCacheSkipValidateAttributes[$intIBlockId])){
			$arCatalog = Helper::getCatalogArray($intIBlockId);
			$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
				? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
			$this->arCacheSkipValidateAttributes[$intIBlockId] = 
				Helper::explodeValues($this->arProfile['IBLOCKS'][$intMainIBlockId]['PARAMS']['ATTRIBUTES_CANCEL_VALIDATE']);
			if(!is_array($this->arCacheSkipValidateAttributes[$intIBlockId])){
				$this->arCacheSkipValidateAttributes[$intIBlockId] = [];
			}
		}
		return $this->arCacheSkipValidateAttributes[$intIBlockId];
	}

	/**
	 * Get defaut param for round (for length, width, height, weight)
	 */
	protected function getRoundParams(){
		return [
			'ROUND' => 'Y',
			'ROUND_round_type' => 'upper',
			'ROUND_round_precision' => '0',
		];
	}

	/**
	 * Stock-and-price mode?
	 */
	protected function isStockAndPrice(){
		return $this->arParams['STOCK_AND_PRICE'] == 'Y';
	}

	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		if($this->isStockAndPrice()){
			return false;
		}
		return parent::areCategoriesExport();
	}

	/**
	 * Send stocks data?
	 */
	protected function useStores(){
		return $this->arParams['EXPORT_STOCKS'] == 'Y';
	}

	/**
	 * Get stores from profile params
	 */
	protected function getStores($bAddEmpty=false){
		$arStocks = $this->arParams['STOCKS'];
		if(!is_array($arStocks)){
			$arStocks = [];
		}
		if(!is_array($arStocks['ID'])){
			$arStocks['ID'] = [];
		}
		if(!is_array($arStocks['NAME'])){
			$arStocks['NAME'] = [];
		}
		$arStocks = array_combine($arStocks['ID'], $arStocks['NAME']);
		foreach($arStocks as $intStoreId => $strStoreName){
			if(!is_numeric($intStoreId) || $intStoreId <= 0){
				unset($arStocks[$intStoreId]);
			}
		}
		if($bAddEmpty && empty($arStocks)){
			$arStocks[''] = '';
		}
		return $arStocks;
	}

	/**
	 * Get stores from Ozon
	 */
	protected function getOzonStores(){
		$arResult = [];
		$arResponse = $this->API->execute('/v1/warehouse/list', [], ['METHOD' => 'POST']);
		$this->addToLog(['/v1/warehouse/list', $this->arParams['CLIENT_ID'], $this->arParams['API_KEY'], $arResponse, $this->API->getHeaders()[0]], true);
		if(is_array($arResponse['result'])){
			foreach($arResponse['result'] as $arStore){
				$arResult[$arStore['warehouse_id']] = $arStore['name'].($arStore['is_rfbs'] ? ' (rFBS)' : '');
			}
		}
		return $arResult;
	}
	
	/**
	 *	Convert simple description text to hint
	 */
	protected function descriptionToHint($strDescription){
		$strDescription = htmlspecialcharsbx($strDescription);
		$strPattern = '#((http|https|ftp|ftps)://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(\/\S*)?)#';
		$strReplace = '<a href="$1" target="_blank">$1</a>';
		$strDescription = preg_replace($strPattern, $strReplace, $strDescription);
		$strDescription = nl2br($strDescription);
		return $strDescription;
	}
	
	/**
	 *	Prepare fields data for getUniversalFields()
	 */
	protected function getDataForFields($intIBlockId){
		$arResult = [];
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
			? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		$arUsedCategories = $this->getUsedCategories($intMainIBlockId);
		$arUsedCategoriesId = array_keys($arUsedCategories);
		$arNotRequiredAttributes = $this->arProfile['IBLOCKS'][$intMainIBlockId]['PARAMS']['ATTRIBUTES_CANCEL_REQUIRED'];
		$arNotRequiredAttributes = Helper::explodeValues($arNotRequiredAttributes);
		$arNotRequiredAttributes = array_filter($arNotRequiredAttributes);
		if(!empty($arUsedCategoriesId)){
			$resCategories = Category::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'NAME'],
			]);
			while($arCategory = $resCategories->fetch()){
				$arCategory['ATTRIBUTES'] = [];
				$arResult[$arCategory['CATEGORY_ID']] = $arCategory;
			}
			$resAttributes = Attribute::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'ATTRIBUTE_ID', 'DICTIONARY_ID', 'NAME', 'DESCRIPTION', 'TYPE', 
					'IS_COLLECTION', 'IS_REQUIRED', 'GROUP_ID', 'GROUP_NAME'],
			]);
			while($arAttribute = $resAttributes->fetch()){
				if(in_array($arAttribute['ATTRIBUTE_ID'], $arNotRequiredAttributes)){
					$arAttribute['IS_REQUIRED'] = 'N';
				}
				$arResult[$arAttribute['CATEGORY_ID']]['ATTRIBUTES'][$arAttribute['ATTRIBUTE_ID']] = $arAttribute;
			}
		}
		# Group
		if(!empty($arResult) && $this->isAttributesGrouped($intMainIBlockId)){
			# Find common attributes
			$arCommonAttributes = [];
			foreach($arResult as $arGroup){
				$arCommonAttributes[] = $arGroup['ATTRIBUTES'];
			}
			if(count($arCommonAttributes) > 1){
				$arCommonAttributes = call_user_func_array('array_intersect_key', $arCommonAttributes);
				#
				foreach($arCommonAttributes as $intAttrId => $arAttr){
					if(!$this->isAttributeDictionaryCommon($intAttrId)){
						unset($arCommonAttributes[$intAttrId]);
					}
				}
				# Check required
				foreach($arCommonAttributes as $intAttrId => $arAttr){
					foreach($arResult as $arGroup){
						if($arGroup['ATTRIBUTES'][$intAttrId]['IS_REQUIRED'] == 'Y'){
							$arCommonAttributes[$intAttrId]['IS_REQUIRED'] = 'Y';
							$arCommonAttributes[$intAttrId]['FORCE_REQUIRED'] = 'Y';
						}
					}
				}
				# Remove common attributes from categories
				if(!empty($arCommonAttributes)){
					foreach($arResult as $intGroupId => $arGroup){
						foreach($arCommonAttributes as $intAttrId => $arAttr){
							if(isset($arGroup['ATTRIBUTES'][$intAttrId])){
								unset($arResult[$intGroupId]['ATTRIBUTES'][$intAttrId]);
							}
						}
					}
				}
				# Create new virtual category
				if(!empty($arCommonAttributes)){
					$arResult = array_merge([
						static::GROUPED_CODE => [
							'ID' => false,
							'CATEGORY_ID' => static::GROUPED_CODE,
							'NAME' => static::getMessage('GROUPED_ATTRIBUTES_HEADER'),
							'ATTRIBUTES' => $arCommonAttributes,
						],
					], $arResult);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Check if attributes are grouped for iblock
	 */
	protected function isAttributesGrouped($intIBlockId){
		return $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS']['GROUP_ATTRIBUTES'] == 'Y';
	}
	
	/**
	 *	Try to guess default value
	 */
	protected function guessDefaultValue(&$arField, $arAttribute){
		if($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_IMAGE){ // Izobrazhenie
			$arField['FIELD'] = ['DETAIL_PICTURE'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_IMAGES){ // Izobrazheniya
			$arField['FIELD'] = ['PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS', 'PROPERTY_PICS_NEWS'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'space'];
			$arField['PARAMS'] = ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'space'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_YOUTUBE_CODE){ // YouTube
			$arField['FIELD'] = ['PROPERTY_YOUTUBE', 'PROPERTY_VIDEO'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_JSON_RICH_CONTENT){ // RichContent
			$arField['FIELD'] = [''];
			$arField['FIELD_PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
			$arField['PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_BRAND')){
			$arField['FIELD'] = ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_GROUP')){
			$arField['FIELD'] = ['ID'];
		}
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/attributevalue.php';
		require_once __DIR__.'/include/classes/category.php';
		require_once __DIR__.'/include/classes/task.php';
		require_once __DIR__.'/include/classes/history.php';
		require_once __DIR__.'/include/classes/historystock.php';
		require_once __DIR__.'/include/db_table_create.php';
		if($_GET['download'] == 'ozon_reference'){
			$this->downloadFieldReference();
		}
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$this->API = new OzonRuHelpers\Api($this->arParams['CLIENT_ID'], $this->arParams['API_KEY'], $this->intProfileId,
				$this->strModuleId);
		}
	}
	
	/**
	 *	Get saved categories, if not exists - download it
	 */
	public function getCategoriesList($intProfileId){
		$intLastUpdateTime = $this->getCategoriesDate();
		if(!$intLastUpdateTime || $intLastUpdateTime <= time() - 24*60*60){
			$this->updateCategories($this->intProfileId);
		}
		$arResult = [];
		$resCategories = Category::getList(['order' => ['NAME' => 'ASC'], 'select' => ['CATEGORY_ID', 'NAME']]);
		while($arCategory = $resCategories->fetch()){
			$arResult[$arCategory['CATEGORY_ID']] = $arCategory['NAME'];
		}
		// Sort categories, but numerical (such a "18+") in the end of the list
		uasort($arResult, function($a, $b){
			if(is_numeric(substr($a, 0, 1)) xor is_numeric(substr($b, 0, 1))){
				return strnatcmp($a, $b) * -1;
			}
			return strnatcmp($a, $b);
		});
		array_walk($arResult, function(&$value, $key) {
			$value = $this->formatCategoryName($key, $value);
		});
		unset($resCategories, $arCategory);
		return $arResult;
	}

	/**
	 *	Get categories date update
	 */
	public function getCategoriesDate(){
		$resCategory = Category::getList(['order' => ['TIMESTAMP_X' => 'DESC'], 'select' => ['TIMESTAMP_X'], 'limit' => 1]);
		if($arCategory = $resCategory->fetch()){
			if(is_object($arCategory['TIMESTAMP_X'])){
				return $arCategory['TIMESTAMP_X']->getTimeStamp();
			}
		}
		unset($resCategory, $arCategory);
		return false;
	}
	
	/**
	 *	Update categories from server using API
	 */
	public function updateCategories($intProfileId){
		$bSuccess = false;
		$strCommand = '/v2/category/tree';
		$arJsonResponse = $this->API->execute($strCommand, [], ['METHOD' => 'POST', 'SKIP_ERRORS' => true]);
		$strSessionId = session_id();
		if(is_array($arJsonResponse['result'])){
			$this->processUpdatedCategory($arJsonResponse['result'], $strSessionId);
			$bSuccess = true;
		}
		else{
			$strLogMessage = static::getMessage('ERROR_CATEGORIES_EMPTY_ANSWER', ['#URL#' => $strCommand, '#JSON#' => print_r($arJsonResponse, true)]);
			$this->addToLog($strLogMessage);
		}
		Category::deleteByFilter([
			'!SESSION_ID' => $strSessionId,
		]);
		return $bSuccess;
	}
	
	/**
	 *	Convert categories tree to plain list (recursively)
	 */
	protected function processUpdatedCategory($arCategoriesCurrent, $strSessionId, $arName=[], $bRecurred=false){
		if(is_array($arCategoriesCurrent)){
			foreach($arCategoriesCurrent as $arCategory){
				$arNameChain = array_merge($arName, [$arCategory['category_id'] => $arCategory['title']]);
				if(!empty($arCategory['children'])){
					$this->processUpdatedCategory($arCategory['children'], $strSessionId, $arNameChain, true);
				}
				else{
					$arFields = [
						'CATEGORY_ID' => $arCategory['category_id'],
						'NAME' => implode($this->strCategoryLevelDelimiter, $arNameChain),
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					];
					$resDBItem = Category::getList(['filter' => $arFilter, 'select' => ['ID']]);
					if($arDbItem = $resDBItem->fetch()){
						Category::update($arDbItem['ID'], $arFields);
					}
					else{
						Category::add($arFields);
					}
				}
			}
		}
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockID, $arIBlockParams){
		if($this->isStockAndPrice()){
			return '';
		}
		return $this->includeHtml(__DIR__.'/include/attribute_update/settings.php', [
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['CLIENT_ID'] = $this->includeHtml(__DIR__.'/include/settings/client_id.php');
		$arSettings['API_KEY'] = $this->includeHtml(__DIR__.'/include/settings/api_key.php');
		$arSettings['LIMITS'] = $this->includeHtml(__DIR__.'/include/settings/limits.php');
		$arSettings['EXPORT_STOCKS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_stocks.php'),
			'SORT' => 160,
		];
		$arSettings['STOCK_AND_PRICE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/stock_and_price.php'),
			'SORT' => 170,
		];
		$arSettings['ZERO_PRICES'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/zero_prices.php'),
			'SORT' => 180,
		];
		$arSettings['HISTORY_SAVE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/history_save.php'),
			'SORT' => 200,
		];
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'check_access':
				$this->checkAccess($arParams, $arJsonResult);
				break;
			case 'check_limits':
				$this->checkLimits($arParams, $arJsonResult);
				break;
			case 'load_stores':
				$this->loadStores($arParams, $arJsonResult);
				break;
			case 'category_attributes_update':
				$this->ajaxUpdateCategories($arParams, $arJsonResult);
				break;
			case 'refresh_tasks_list':
				$strLogCustomTitle=false;
				$arJsonResult['HTML'] = $this->getLogContent($strLogCustomTitle, $arParams['GET']);
				break;
			case 'update_task_status':
				$arJsonResult['HTML'] = $this->updateTaskStatus($arParams['GET']['task_id'], $arJsonResult);
				break;
			case 'allowed_values_custom':
				$arJsonResult['HTML'] = $this->getAllowedValuesContent($arParams['GET']);
				break;
			case 'allowed_values_filter':
				$arJsonResult['HTML'] = $this->getAllowedValuesFilteredContent($arParams['GET']);
				break;
			case 'task_json_preview':
				$arJsonResult['HTML'] = $this->getTaskJsonPreview($arParams['GET']);
				break;
			case 'history_item_json_preview':
				$arJsonResult['HTML'] = $this->getHistoryItemJsonPreview($arParams['GET']);
				break;
			case 'category_attributes_check':
				$this->ajaxCheckCategories($arParams, $arJsonResult);
				break;
			case 'open_offer_json':
				$this->ajaxOpenOfferJson($arParams, $arJsonResult);
				break;
			case 'open_task_json':
				$this->ajaxOpenTaskJson($arParams, $arJsonResult);
				break;
		}
	}
	
	/**
	 *	Check clientId and apiKey (for info only)
	 */
	protected function checkAccess($arParams, &$arJsonResult){
		$arJsonResult['Success'] = false;
		$strClientId = $arParams['GET']['client_id'];
		$strApiKey = $arParams['GET']['api_key'];
		$arJsonRequest = [
			'offer_id' => '#ACRIT_CHECK#',
		];
		$obApi = new OzonRuHelpers\Api($strClientId, $strApiKey, $this->intProfileId, $this->strModuleId);
		$arQueryResult = $obApi->execute('/v2/product/info', $arJsonRequest, ['METHOD' => 'POST', 'SKIP_ERRORS' => true]);
		$arJsonResult['Response'] = $arQueryResult;
		$arJsonResult['Code'] = $arQueryResult['code'];
		unset($obApi);
		$this->addToLog(['/v2/product/info', $strClientId, $strApiKey, $arQueryResult, $this->API->getHeaders()[0]], true);
		$intOzonCodeError = 5; // 'Product not found'
		if($arQueryResult['code'] == $intOzonCodeError){
			$arJsonResult['Success'] = true;
			$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_SUCCESS');
		}
		else{
			switch($arQueryResult['message']){
				case 'Invalid Api-Key, please contact support':
					$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_DENIED');
					break;
				case 'Company is blocked, please contact support':
					$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_COMPANY_BLOCKED');
					break;
				default:
					$arJsonResult['Message'] = $arQueryResult['message'] ?? static::getMessage('MESSAGE_CHECK_ACCESS_DENIED');
					break;
			}
		}
	}
	
	/**
	 *	Check limits
	 */
	protected function checkLimits($arParams, &$arJsonResult){
		$arJsonResult['Success'] = false;
		if($arLimits = $this->getLimits($arParams['GET']['client_id'], $arParams['GET']['api_key'])){
			$arJsonResult['Success'] = true;
			$arJsonResult['Html'] = $this->showLimits($arLimits);
		}
		else{
			$arJsonResult['Message'] = static::getMessage('LIMITS_ERROR');
		}
	}

	/**
	 * Execute request for get limits info
	 */
	protected function getLimits($strClientId=null, $strApiKey=null){
		$strMethod = '/v4/product/info/limit';
		$arData = [];
		$arParams = ['METHOD' => 'POST', 'SKIP_ERRORS' => true];
		if(strlen($strClientId) || strlen($strApiKey)){
			$obApi = new OzonRuHelpers\Api($strClientId, $strApiKey, $this->intProfileId, $this->strModuleId);
			$arQueryResult = $obApi->execute($strMethod, $arData, $arParams);
			unset($obApi);
		}
		else{
			$arQueryResult = $this->API->execute($strMethod, $arData, $arParams);
		}
		if(!isset($arQueryResult['total']['limit'])){
			$arQueryResult = null;
		}
		return $arQueryResult;
	}

	/**
	 * Get limits html
	 */
	protected function showLimits($arLimits){
		ob_start();
		require __DIR__.'/include/limits.php';
		return ob_get_clean();
	}
	
	/**
	 *	Load stores via AJAX
	 */
	protected function loadStores($arParams, &$arJsonResult){
		$arJsonResult['Success'] = true;
		$arJsonResult['Stores'] = $this->getOzonStores();
		if(empty($arJsonResult['Stores'])){
			$arJsonResult['Message'] = static::getMessage('EXPORT_STOCKS_ADD_AUTO_EMPTY');
		}
	}
	
	/**
	 *	Update category attributes and dictionaries
	 */
	protected function ajaxUpdateCategories($arParams, &$arJsonResult){
		$arSession = &$_SESSION['ACRIT_EXP_OZON_CAT_ATTR_UPDATE'];
		$arPost = &$arParams['POST'];
		$bStart = false;
		if($arPost['start'] == 'Y'){
			$bStart = true;
			$arJsonResult['Action'] = 'Start';
			$arSession = [
				'ID' => session_id(),
				'CATEGORIES' => $this->getUsedCategories($arPost['iblock_id'], true),
				'ATTRIBUTES' => [],
				'COUNT' => 0,
				'INDEX' => 0,
				'CATEGORY_ID' => false,
				'CATEGORY_NAME' => false,
				'ATTRIBUTE_ID' => false,
				'ATTRIBUTE_NAME' => false,
				'SUB_INDEX' => 0,
				'JUST_ATTR' => $arPost['just_attr'] == 'Y',
				#'SUB_COUNT' => 0, // Unfortunately, ozon does not provide this information :(
			];
			$arSession['COUNT'] = count($arSession['CATEGORIES']);
			$arJsonResult['Continue'] = true;
			foreach($arSession['CATEGORIES'] as $intCategoryId){
				Attribute::deleteByFilter(['CATEGORY_ID' => $intCategoryId]);
				AttributeValue::deleteByFilter(['CATEGORY_ID' => $intCategoryId]);
			}
		}
		else{
			# Update values if it in queue
			if(!empty($arSession['ATTRIBUTES'])){
				$arJsonResult['Action'] = 'Attributes';
				do{
					foreach($arSession['ATTRIBUTES'] as $intAttrId => $arAttr){
						$arAttr['START_TIME'] = isset($arAttr['TIME_START']) ? $arAttr['TIME_START'] : microtime(true);
						$arAttr['COUNT_SUCCESS'] = isset($arAttr['COUNT_SUCCESS']) ? $arAttr['COUNT_SUCCESS'] : 0;
						$arAttr['ID'] = $intAttrId;
						$arSession['ATTRIBUTE_ID'] = $arAttr['ID'];
						$arSession['ATTRIBUTE_NAME'] = $arAttr['NAME'];
						$arSession['ATTRIBUTE_DICTIONARY_ID'] = $arAttr['DIC'];
						$arUpdateResult = $this->updateAttrubuteValues($arAttr, $arSession['ID']);
						$arAttr['COUNT_SUCCESS'] += $arUpdateResult['COUNT_SUCCESS'];
						if($arUpdateResult['CONTINUE'] && $arUpdateResult['LAST_ID']){
							$arAttr['LAST_ID'] = $arUpdateResult['LAST_ID'];
							$arSession['ATTRIBUTES'][$intAttrId] = $arAttr;
							$arSession['SUB_INDEX'] += $arUpdateResult['COUNT_SUCCESS'];
						}
						else{
							# Save some metainfo to attribute
							$arAttributeFields = [
								'LAST_VALUES_COUNT' => $arAttr['COUNT_SUCCESS'],
								'LAST_VALUES_DATETIME' => new \Bitrix\Main\Type\Datetime(),
								'LAST_VALUES_ELAPSED_TIME' => microtime(true) - $arAttr['START_TIME'],
							];
							$arFilter = [
								'CATEGORY_ID' => $arAttr['CAT'],
								'ATTRIBUTE_ID' => $intAttrId,
							];
							$resDbAttribute = Attribute::getList(['filter' => $arFilter, 'select' => ['ID']]);
							if($arDbAttribute = $resDbAttribute->fetch()){
								Attribute::update($arDbAttribute['ID'], $arAttributeFields);
							}
							# Inc index
							$arSession['ATTRIBUTE_INDEX']++;
							# Clear temporary data
							$arSession['SUB_INDEX'] = 0;
							unset($arSession['ATTRIBUTES'][$intAttrId]);
						}
						break;
					}
				} while($this->ajaxHaveTime());
			}
			# Update attribtutes if it in queue
			else{
				$arJsonResult['Action'] = 'Categories';
				foreach($arSession['CATEGORIES'] as $key1 => $intCategoryId){
					$arSession['ATTRIBUTE_ID'] = false;
					$arSession['ATTRIBUTE_NAME'] = false;
					$arAttributes = $this->updateCategoryAttrubutes($intCategoryId, $arSession['ID']);
					if($arAttributes){
						foreach($arAttributes as $key2 => $arAttr){
							if($arAttr['DICTIONARY_ID']){
								$arAttributes[$key2] = [
									'CAT' => $arAttr['CATEGORY_ID'],
									'NAME' => $arAttr['NAME'],
									'DIC' => $arAttr['DICTIONARY_ID'],
								];
							}
							else{
								unset($arAttributes[$key2]);
							}
						}
						if($arSession['JUST_ATTR'] != 'Y'){
							$arSession['ATTRIBUTES'] = $arAttributes;
							$arSession['ATTRIBUTE_ID'] = $arAttr['ID'];
							$arSession['ATTRIBUTE_NAME'] = $arAttr['NAME'];
							$arSession['ATTRIBUTE_DICTIONARY_ID'] = $arAttr['DICTIONARY_ID'];
							$arSession['ATTRIBUTE_INDEX'] = 1;
							$arSession['ATTRIBUTE_COUNT'] = count($arAttributes);
						}
					}
					$arSession['INDEX']++;
					$arSession['SUB_INDEX'] = 0;
					$arSession['CATEGORY_ID'] = $intCategoryId;
					$arSession['CATEGORY_NAME'] = $this->getCategoryName($intCategoryId);
					unset($arSession['CATEGORIES'][$key1]);
					if(!$this->ajaxHaveTime()){
						break;
					}
				}
			}
			$arJsonResult['Continue'] = true;
			if(empty($arSession['CATEGORIES']) && empty($arSession['ATTRIBUTES'])){
				$arSession['FINISHED'] = true;
				$arJsonResult['Continue'] = false;
			}
		}
		$arJsonResult['SessionId'] = $arSession['ID'];
		$arJsonResult['Count'] = $arSession['COUNT'];
		$arJsonResult['Index'] = $arSession['INDEX'];
		$arJsonResult['Percent'] = $arSession['COUNT'] == 0 ? 0 : round($arSession['INDEX'] * 100 / $arSession['COUNT']);
		$arJsonResult['CategoryId'] = $arSession['CATEGORY_ID'];
		$arJsonResult['CategoryName'] = $arSession['CATEGORY_NAME'];
		$arJsonResult['AttributeId'] = $arSession['ATTRIBUTE_ID'];
		$arJsonResult['AttributeName'] = $arSession['ATTRIBUTE_NAME'];
		$arJsonResult['AttributeIndex'] = $arSession['ATTRIBUTE_INDEX'];
		$arJsonResult['AttributeCount'] = $arSession['ATTRIBUTE_COUNT'];
		$arJsonResult['AttributeDictionaryId'] = $arSession['ATTRIBUTE_DICTIONARY_ID'];
		$arJsonResult['SubIndex'] = $arSession['SUB_INDEX'];
		ob_start();
		require __DIR__.'/include/attribute_update/status.php';
		$arJsonResult['Html'] = ob_get_clean();
		# Save update time
		if(!$arJsonResult['Continue']){
			$strCategoriesHash = md5(serialize($this->getUsedCategories($arPost['iblock_id'], true)));
			$intTime = time();
			Helper::setOption($this->strModuleId, 'ozon_attributes_update_hash_'.$this->intProfileId, $strCategoriesHash);
			Helper::setOption($this->strModuleId, 'ozon_attributes_update_time_'.$this->intProfileId, $intTime);
			$arJsonResult['AttrubuteUpdateTime'] = \Bitrix\Main\Type\Datetime::createFromTimestamp($intTime)->toString();
		}
	}
	
	/**
	 *	Check time for ajax step
	 */
	protected function ajaxHaveTime(){
		return false;
		return microtime(true) - $this->fTimeStart < static::AJAX_STEP_TIME;
	}
	
	/**
	 *	Get used ozon categories from redefinitions
	 */
	protected function getUsedCategories($intIBlockId=null, $bJustIds=false){
		$arResult = [];
		$arIBlockParams = $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS'];
		if($arIBlockParams['CATEGORIES_ALTERNATIVE'] == 'Y'){
			if(is_array($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'])){
				foreach($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'] as $intCategoryId){
					if(is_numeric($intCategoryId)){
						$arResult[$intCategoryId] = $this->getCategoryName($intCategoryId);
					}
				}
			}
		}
		else{
			$arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', 
				[$this->intProfileId, $intIBlockId]);
			if(!is_array($arRedefinitions)){
				$arRedefinitions = [];
			}
			$arSelectedCategories = Exporter::getInvolvedSectionsID($intIBlockId, 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_ID'], 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_MODE']);
			$arUsedCategories = array_intersect_key($arRedefinitions, array_flip($arSelectedCategories));
			foreach($arUsedCategories as $strCategoryName){
				if(strlen($strCategoryName)){
					$strCategoryId = $this->parseCategoryId($strCategoryName);
					$arResult[$strCategoryId] = $strCategoryName;
				}
			}
		}
		if($bJustIds){
			$arResult = array_keys($arResult);
		}
		return $arResult;
	}
	
	/**
	 *	"[17034083] Category 1 / Category 2 / Product name" => 17034083, &name => "Category 1 / Category 2 / Product name"
	 */
	protected function parseCategoryId(&$strCategoryName){
		if(strlen($strCategoryName) && preg_match('#^\[(\d+)\][\s]*(.*?)$#', $strCategoryName, $arMatch)){
			$strCategoryName = $arMatch[2];
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function getCategoryName($intCategoryId){
		$strResult = '';
		$resCategory = Category::getList(['filter' => ['CATEGORY_ID' => $intCategoryId], 'select' => ['NAME']]);
		if($arCategory = $resCategory->fetch()){
			$strResult = $arCategory['NAME'];
		}
		return $strResult;
	}
	
	/**
	 *	Format category name with ID
	 */
	protected function formatCategoryName($intCategoryId, $strCategoryName=null){
		if(!strlen($strCategoryName) && $intCategoryId > 0){
			$strCategoryName = $this->getCategoryName($intCategoryId);
		}
		if(!is_numeric($intCategoryId) || !$intCategoryId){
			return $strCategoryName;
		}
		return sprintf('[%d] %s', $intCategoryId, $strCategoryName);
	}
	
	/**
	 *	Update attributes for categories
	 */
	protected function updateCategoryAttrubutes($arCategoryId, $strSessionId){
		$strCommand = '/v3/category/attribute';
		if(!is_array($arCategoryId)){
			$arCategoryId = [$arCategoryId];
		}
		else{
			$arCategoryId = array_values($arCategoryId);
		}
		$arJsonRequest = [
			'attribute_type' => 'ALL',
			'category_id' => $arCategoryId,
			'language' => 'DEFAULT',
		];
		$arJsonResponse = $this->API->execute($strCommand, $arJsonRequest, ['METHOD' => 'POST']);
		if(is_array($arJsonResponse['result']) && !empty($arJsonResponse['result'])){
			$arResult = [];
			foreach($arJsonResponse['result'] as $arCategory){
				foreach($arCategory['attributes'] as $arItem){
					$arFields = [
						'CATEGORY_ID' => $arCategory['category_id'],
						'ATTRIBUTE_ID' => $arItem['id'],
						'DICTIONARY_ID' => $arItem['dictionary_id'],
						'NAME' => $arItem['name'],
						'DESCRIPTION' => $arItem['description'],
						'TYPE' => $arItem['type'],
						'IS_COLLECTION' => $arItem['is_collection'] == 1 ? 'Y' : 'N',
						'IS_REQUIRED' => $arItem['is_required'] == 1 ? 'Y' : 'N',
						'GROUP_ID' => $arItem['group_id'],
						'GROUP_NAME' => $arItem['group_name'],
						'SESSION_ID' => $strSessionId,
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $arFields['CATEGORY_ID'],
						'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
					];
					$arSelect = [
						'ID',
						'LAST_VALUES_COUNT',
						'LAST_VALUES_DATETIME',
					];
					$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
					if($arDbItem = $resDBItem->fetch()){
						Attribute::update($arDbItem['ID'], $arFields);
						$arFields['LAST_VALUES_COUNT'] = $arDbItem['LAST_VALUES_COUNT'];
						if(is_object($arDbItem['LAST_VALUES_DATETIME'])){
							$bActual = microtime(true) - $arDbItem['LAST_VALUES_DATETIME']->getTimestamp() < static::CACHE_VALID_TIME;
							if($bActual){
								unset($arFields);
							}
						}
					}
					else{
						Attribute::add($arFields);
					}
					if($arFields){
						$arResult[$arItem['id']] = $arFields;
					}
				}
			}
			Attribute::deleteByFilter([
				'CATEGORY_ID' => $arCategoryId,
				'!SESSION_ID' => $strSessionId,
			]);
			unset($arJsonResponse['result']);
			return $arResult;
		}
		else{
			$this->addToLog(static::getMessage('ERROR_UPDATE_ATTRIBUTES', ['#CATEGORY_ID#' => implode(', ', $arCategoryId), '#ERROR#' => print_r($arJsonResponse, true)]));
			return false;
		}
		return false;
	}
	
	/**
	 *	Update attributes for single category
	 */
/* 	protected function updateCategoryAttrubutes($intCategoryId, $strSessionId){
		$strCommand = '/v2/category/attribute';
		$arJsonRequest = [
			'category_id' => $intCategoryId,
		];
		$arJsonResponse = $this->API->execute($strCommand, $arJsonRequest, ['METHOD' => 'POST']);
		if(is_array($arJsonResponse['result']) && !empty($arJsonResponse['result'])){
			$arResult = [];
			foreach($arJsonResponse['result'] as $arItem){
				$arFields = [
					'CATEGORY_ID' => $intCategoryId,
					'ATTRIBUTE_ID' => $arItem['id'],
					'DICTIONARY_ID' => $arItem['dictionary_id'],
					'NAME' => $arItem['name'],
					'DESCRIPTION' => $arItem['description'],
					'TYPE' => $arItem['type'],
					'IS_COLLECTION' => $arItem['is_collection'] == 1 ? 'Y' : 'N',
					'IS_REQUIRED' => $arItem['is_required'] == 1 ? 'Y' : 'N',
					'GROUP_ID' => $arItem['group_id'],
					'GROUP_NAME' => $arItem['group_name'],
					'SESSION_ID' => $strSessionId,
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
				];
				$arFilter = [
					'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
				];
				$arSelect = [
					'ID',
					'LAST_VALUES_COUNT',
					'LAST_VALUES_DATETIME',
				];
				$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
				if($arDbItem = $resDBItem->fetch()){
					Attribute::update($arDbItem['ID'], $arFields);
					$arFields['LAST_VALUES_COUNT'] = $arDbItem['LAST_VALUES_COUNT'];
					if(is_object($arDbItem['LAST_VALUES_DATETIME'])){
						$bActual = microtime(true) - $arDbItem['LAST_VALUES_DATETIME']->getTimestamp() < static::CACHE_VALID_TIME;
						if($bActual){
							unset($arFields);
						}
					}
				}
				else{
					Attribute::add($arFields);
				}
				if($arFields){
					$arResult[$arItem['id']] = $arFields;
				}
			}
			Attribute::deleteByFilter([
				'CATEGORY_ID' => $intCategoryId,
				'!SESSION_ID' => $strSessionId,
			]);
			unset($arJsonResponse['result']);
			return $arResult;
		}
		return false;
	} */
	
	/**
	 *	Update dictionary
	 *	@return true if process is not finished (by has_next)
	 */
	protected function updateAttrubuteValues($arAttr, $strSessionId){
		$arResult = [
			'LAST_ID' => false,
			'CONTINUE' => false,
			'COUNT_SUCCESS' => 0,
		];
		$strCommand = '/v2/category/attribute/values';
		$intLimit = intVal(Helper::getOption($this->strModuleId, 'ozon_new_api_step_size'));
		if($intLimit <= 0){
			$intLimit = 5000; // max tested allowed value - 5000, but ozon support recommends 1000
		}
		$arJsonRequest = [
			'category_id' => $arAttr['CAT'],
			'attribute_id' => $arAttr['ID'],
			'limit' => $intLimit,
		];
		if($arAttr['LAST_ID']){
			$arJsonRequest['last_value_id'] = $arAttr['LAST_ID'];
		}
		$arJsonResponse = $this->API->execute($strCommand, $arJsonRequest, ['METHOD' => 'POST']);
		if(is_array($arJsonResponse['result'])){
			$strSaveData = '';
			\Bitrix\Main\Application::getConnection()->startTransaction();
			foreach($arJsonResponse['result'] as $arItem){
				$arFields = [
					'CATEGORY_ID' => $arAttr['CAT'],
					'ATTRIBUTE_ID' => $arAttr['ID'],
					'DICTIONARY_ID' => $arAttr['DIC'],
					'VALUE_ID' => $arItem['id'],
					'VALUE' => $arItem['value'],
					'SESSION_ID' => $strSessionId,
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
				];
				$arFilter = [
					'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
					'DICTIONARY_ID' => $arFields['DICTIONARY_ID'],
					'VALUE_ID' => $arFields['VALUE_ID'],
				];
				if($this->isAttributeDictionaryCommon($arAttr['ID'])){
					unset($arFields['CATEGORY_ID'], $arFilter['CATEGORY_ID']);
				}
				$resDBItem = AttributeValue::getList(['filter' => $arFilter, 'select' => ['ID']]);
				if($arDbItem = $resDBItem->fetch()){
					AttributeValue::update($arDbItem['ID'], $arFields);
				}
				else{
					AttributeValue::add($arFields);
				}
				$arResult['LAST_ID'] = $arItem['id'];
				$arResult['COUNT_SUCCESS']++;
			}
			\Bitrix\Main\Application::getConnection()->commitTransaction();
			if(!$arJsonResponse['has_next']){
				$arDeleteFilter = [
					'CATEGORY_ID' => $arAttr['CAT'],
					'ATTRIBUTE_ID' => $arAttr['ID'],
					'!SESSION_ID' => $strSessionId,
				];
				if($this->isAttributeDictionaryCommon($arAttr['ID'])){
					unset($arDeleteFilter['CATEGORY_ID']);
				}
				AttributeValue::deleteByFilter($arDeleteFilter);
			}
			else {
				$arResult['CONTINUE'] = true;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Check atribute required (used in processElement)
	 */
	protected function isAttributeRequired($intCategoryId, $intAttributeId){
		if(!is_array($this->arCacheRequiredAttributes[$intCategoryId])){
			$this->arCacheRequiredAttributes[$intCategoryId] = [];
			$resQuery = Attribute::getList([
				'filter' => ['CATEGORY_ID' => $intCategoryId, 'IS_REQUIRED' => 'Y'],
				'select' => ['ATTRIBUTE_ID'],
			]);
			while($arItem = $resQuery->fetch()){
				$this->arCacheRequiredAttributes[$intCategoryId][$arItem['ATTRIBUTE_ID']] = true;
			}
		}
		return isset($this->arCacheRequiredAttributes[$intCategoryId][$intAttributeId]);
	}
	
	/**
	 *	Check atribute is a dictionary
	 *	@return false || dictionary_id
	 */
	protected function isAttributeDictionary($intCategoryId, $intAttributeId){
		if(!is_array($this->arCacheDictionaryAttributes[$intCategoryId])){
			$this->arCacheDictionaryAttributes[$intCategoryId] = [];
			$arFilter = [
				'filter' => ['>DICTIONARY_ID' => 0],
				'select' => ['ATTRIBUTE_ID', 'DICTIONARY_ID'],
			];
			if(is_numeric($intCategoryId)){
				$arFilter['filter']['CATEGORY_ID'] = $intCategoryId;
			}
			$resQuery = Attribute::getList($arFilter);
			while($arItem = $resQuery->fetch()){
				$this->arCacheDictionaryAttributes[$intCategoryId][$arItem['ATTRIBUTE_ID']] = $arItem['DICTIONARY_ID'];
			}
		}
		$intDictionaryId = $this->arCacheDictionaryAttributes[$intCategoryId][$intAttributeId];
		return $intDictionaryId > 0 ? $intDictionaryId : false;
	}
	
	/**
	 *	Check if dictionary has same values: all attributes but Type and Commercial types [by support, 2020-08-05]
	 */
	protected function isAttributeDictionaryCommon($intAttributeId){
		$arUniqueDictionaries = [
			8229, // Type
			9461, // Commercial type
			85, // Brand
		];
		return !in_array($intAttributeId, $arUniqueDictionaries);
	}
	
	/**
	 *	Parse attribute id: attribute_1231231_213
	 */
	protected function parseAttributeId($strAttributeId){
		$strPattern = static::ATTRIBUTE_ID;
		$strPattern = str_replace('%s', '([A-z0-9]+)', $strPattern);
		$strPattern = sprintf('#^%s$#', $strPattern);
		if(preg_match($strPattern, $strAttributeId, $arMatch)){
			return [
				'CATEGORY_ID' => $arMatch[1],
				'ATTRIBUTE_ID' => $arMatch[2],
			];
		}
		return false;
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$arIBlockParams = $this->getIBlockParams($arElement['IBLOCK_ID']);
		# Correct int/string types for height, depth, price, vat, ...
		$this->correctFieldTypes($arItem, $arElement, $arFields, $arElementSections, $arDataMore);
		# Remove empty youtube field
		if(empty($arItem['video_youtube'])){
			unset($arItem['video_youtube']);
		}
		#
		$arDataMore = [
			'OFFER_ID' => $arItem['offer_id'],
			'STOCK' => null, // General stock
			'STOCKS' => [], // All stocks
		];
		# Transfer stock from main data to DATA_MORE
		if(is_numeric($arFields['stock']) && !$this->useStores()){
			$arDataMore['STOCK'] = intVal($arFields['stock']);
		}
		unset($arItem['stock'], $arFields['stock']);
		# Transfer new stocks
		foreach($arItem as $key => $value){
			if(preg_match('#^stock_(\d+)$#', $key, $arMatch)){
				$arDataMore['STOCKS'][$arMatch[1]] = $value;
				unset($arItem[$key], $arFields[$key]);
			}
		}
		# Check consider reserved stock
		if($arIBlockParams['OZON_CONSIDER_RESERVED_STOCK'] == 'Y'){
			$arProductInfo = $this->getOzonProductInfo($arFields['offer_id']);
			if($this->useStores()){
				# ToDo
				$arDataMore['STOCKS_RESERVED'] = [];
				$intSkuId = isset($arProductInfo['sku']) && $arProductInfo['sku'] > 0 ? $arProductInfo['sku'] : $arProductInfo['fbs_sku'];
				if(is_array($arWarehouseStocks = $this->getOzonProductWarehousesStocks($intSkuId))){
					foreach($arWarehouseStocks as $arWarehouseStock){
						if(isset($arWarehouseStock['reserved']) && $arWarehouseStock['reserved'] > 0){
							$arDataMore['STOCKS_RESERVED'][$arWarehouseStock['warehouse_id']] = $arWarehouseStock['reserved'];
						}
					}
				}
			}
			else{
				if($arProductInfo['stocks']){
					$arDataMore['STOCK_RESERVED'] = $arProductInfo['stocks']['reserved'];
					$arDataMore['STOCK_RESULT'] = $arDataMore['STOCK'] - $arDataMore['STOCK_RESERVED'];
					$arDataMore['STOCK_RESULT'] = max(0, $arDataMore['STOCK_RESULT']);
				}
			}
		}
		# Detect category id
		if(!$this->isStockAndPrice()){
			$intProductCategoryId = false;
			if($arIBlockParams['CATEGORIES_ALTERNATIVE'] == 'Y'){
				$intProductCategoryId = $arFields['category_id'];
			}
			$this->handler('onOzonNewApiGetCategoryId', [&$intProductCategoryId, &$arItem, &$arElement, &$arFields, 
				&$arElementSections]);
			if(!$intProductCategoryId){
				$intProductSectionId = false;
				$intProductSectionId = intVal(reset($arElementSections));
				if(!$intProductSectionId){
					return [
						'ERRORS' => [static::getMessage('ERROR_WRONG_PRODUCT_SECTION', [
							'#ELEMENT_ID#' => $arElement['ID'],
						])],
					];
				}
				#
				$arCatalog = Helper::getCatalogArray($arElement['IBLOCK_ID']);
				$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
					? $arCatalog['PRODUCT_IBLOCK_ID'] : $arElement['IBLOCK_ID'];
				$arQuery = [
					'filter' => [
						'PROFILE_ID' => $this->intProfileId,
						'IBLOCK_ID' => $intMainIBlockId,
						'SECTION_ID' => $intProductSectionId,
					],
					'select' => ['SECTION_NAME'],
				];
				if($arRedefinition = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getList', [$arQuery])->fetch()){
					$intProductCategoryId = $this->parseCategoryId($arRedefinition['SECTION_NAME']);
				}
				if(!$intProductCategoryId){
					return [
						'ERRORS' => [static::getMessage('ERROR_WRONG_PRODUCT_CATEGORY', [
							'#ELEMENT_ID#' => $arElement['ID'],
						])],
					];
				}
			}
			$arItem['category_id'] = intVal($intProductCategoryId);
		}
		# Handler
		$this->handler('onOzonPrepareJson', [&$intProductCategoryId, &$arItem, &$arElement, &$arFields, &$arElementSections]);
		# Prepare images
		if(!$this->isStockAndPrice()){
			if(is_array($arItem['images'])){
				$arItem['images'] = array_values($arItem['images']);
			}
			elseif(Helper::strlen($arItem['images'])){
				$arItem['images'] = [$arItem['images']];
			}
		}
		if(!Helper::strlen($arItem['primary_image'])){
			unset($arItem['primary_image']);
		}
		# Prepare PDF
		if(!$this->isStockAndPrice()){
			$strSiteUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y');
			$intIndex = 0;
			foreach($arItem['pdf_list'] as $key => $mPdf){
				if(is_numeric($mPdf) && $mPdf > 0){
					if($arFile = \CFile::getFileArray($mPdf)){
						$mPdf = [
							'index' => $intIndex++,
							'name' => strlen($arFile['DESCRIPTION']) ? $arFile['DESCRIPTION'] : 
								pathinfo($arFile['ORIGINAL_NAME'], PATHINFO_FILENAME),
							'src_url' => $strSiteUrl.$arFile['SRC'],
						];
					}
					else{
						unset($mPdf);
					}
				}
				else{
					$mPdf = [
						'index' => $intIndex++,
						'name' => pathinfo($mPdf, PATHINFO_FILENAME),
						'src_url' => $mPdf,
					];
				}
				if(!is_array($mPdf)){
					unset($arItem['pdf_list'][$key]);
					continue;
				}
				$arItem['pdf_list'][$key] = $mPdf;
			}
		}
		# Remove attributes for other categories
		if(!$this->isStockAndPrice()){
			foreach($arFields as $strField => $mValue){
				if($arAttribute = $this->parseAttributeId($strField)){
					if($arAttribute['CATEGORY_ID'] != static::GROUPED_CODE && $arAttribute['CATEGORY_ID'] != $intProductCategoryId){
						unset($arFields[$strField]);
						unset($arItem[$strField]);
					}
				}
			}
		}
		# Check empty required fields (for each category)
		if(!$this->isStockAndPrice()){
			if($arErrors = $this->checkRequiredFields($arElement['IBLOCK_ID'], $arElement['ID'], $arFields, $intProductCategoryId)){
				return [
					'ERRORS' => $arErrors,
				];
			}
		}
		# Transform some fields
		if($arItem['price'] >= $arItem['old_price']){
			if($this->arParams['ZERO_PRICE_OLD'] != 'N'){
				$arItem['old_price'] = '0';
			}
			else{
				unset($arItem['old_price']);
			}
		}
		if($arItem['premium_price'] >= $arItem['price']){
			if($this->arParams['ZERO_PRICE_PREMIUM'] != 'N'){
				$arItem['premium_price'] = '0';
			}
			else{
				unset($arItem['premium_price']);
			}
		}
		if(!$this->isStockAndPrice() && isset($arItem['auto_action_enabled'])){
			if(Helper::strlen($arItem['auto_action_enabled'])){
				$arDataMore['AUTO_ACTION_ENABLED'] = $arItem['auto_action_enabled'];
			}
			unset($arItem['auto_action_enabled']);
		}
		if(!$this->isStockAndPrice()){
			$arNumericToFloat = ['depth', 'height', 'width', 'weight'];
			foreach($arNumericToFloat as $strField){
				if(is_numeric($arItem[$strField])){
					$arItem[$strField] = floatVal($arItem[$strField]);
				}
				elseif(empty($arItem[$strField])){
					$arItem[$strField] = 0;
				}
			}
		}
		$arUnsetEmptyFields = ['pdf_list', 'image_group_id', 'old_price', 'min_price', 'premium_price'];
		foreach($arUnsetEmptyFields as $strField){
			$arField = $arItem[$strField];
			if(is_string($arField) && !strlen($arField) || is_array($arField) && empty($arField)){
				unset($arItem[$strField]);
			}
		}
		# Prepare complex attributes
		if(!$this->isStockAndPrice()){
			$arComplex = [];
			# Complex: Prepare YouTube
			if(!Helper::isEmpty($mValue = $arItem['video_youtube'])){
				if(!empty($arVideo = $this->prepareYouTubeVideos($mValue))){
					$arComplex[] = [
						'complex_id' => static::ATTR_ID_YOUTUBE_COMPLEX_ID,
						'id' => static::ATTR_ID_YOUTUBE_CODE,
						'values' => array_map(function($value){
								return ['value' => 'https://www.youtube.com/watch?v='.$value];
							}, array_column($arVideo, '_video_id')),
					];
					$arComplex[] = [
						'complex_id' => static::ATTR_ID_YOUTUBE_COMPLEX_ID,
						'id' => static::ATTR_ID_YOUTUBE_TITLE,
						'values' => array_map(function($value){
								return ['value' => $value];
							}, array_column($arVideo, 'title')),
					];
				}
			}
		}
		# Transform attributes
		if(!$this->isStockAndPrice()){
			$arAttributes = [];
			foreach($arItem as $strField => $mValue){
				if($arAttribute = $this->parseAttributeId($strField)){
					if(!Helper::isEmpty($mValue)){
						$arValues = [];
						$mValue = is_array($mValue) ? $mValue : [$mValue];
						$intDictionaryId = $this->isAttributeDictionary($arAttribute['CATEGORY_ID'], $arAttribute['ATTRIBUTE_ID']);
						foreach($mValue as $strValue){
							$strValue = strVal($strValue);
							if($intDictionaryId){
								$arValuesFilter = [
									'CATEGORY_ID' => $arAttribute['CATEGORY_ID'],
									'=ATTRIBUTE_ID' => $arAttribute['ATTRIBUTE_ID'],
									'=VALUE' => $strValue,
								];
								if($this->isAttributeDictionaryCommon($arAttribute['ATTRIBUTE_ID'])){
									unset($arValuesFilter['CATEGORY_ID']);
								}
								if(in_array($arAttribute['ATTRIBUTE_ID'], $this->getSkipValidateAttributes($arElement['IBLOCK_ID']))){
									unset($arValuesFilter['CATEGORY_ID']);
								}
								$arDictionaryValueQuery = [
									'filter' => $arValuesFilter,
									'select' => ['VALUE_ID', 'VALUE'],
								];
								$arDictionaryValues = [];
								$resDictionaryValue = AttributeValue::getList($arDictionaryValueQuery);
								while($arDictionaryValue = $resDictionaryValue->fetch()){
									$arDictionaryValues[$arDictionaryValue['VALUE_ID']] = $arDictionaryValue['VALUE'];
								}
								$intValueId = array_search($strValue, $arDictionaryValues, true);
								if(!$intValueId && !empty($arDictionaryValues)){ // If brand is 'vincent', but available brands are ['Vincent', 'VINCENT']
									$intValueId = key($arDictionaryValues);
								}
								if(!$intValueId){
									$bCommon = $this->isAttributeDictionaryCommon($arAttribute['ATTRIBUTE_ID']);
									$this->handler('onOzonAttrValueNotFound', [&$intValueId, &$strValue, $arAttribute, $arItem, $arElement, $bCommon]);
								}
								if($intValueId){
									$arValues[] = [
										'dictionary_value_id' => intVal($intValueId),
										'value' => $strValue,
									];
								}
								else{
									$strAttributeName = $arAttribute['ATTRIBUTE_ID'];
									$resDbAttribute = Attribute::getList([
										'filter' => [
											'CATEGORY_ID' => $arAttribute['CATEGORY_ID'],
											'ATTRIBUTE_ID' => $arAttribute['ATTRIBUTE_ID'],
										],
										'select' => ['NAME'],
									]);
									if($arDbAttribute = $resDbAttribute->fetch()){
										$strAttributeName = sprintf('[%d] %s', $arAttribute['ATTRIBUTE_ID'], $arDbAttribute['NAME']);
									}
									return [
										'ERRORS' => [static::getMessage('ERROR_WRONG_DICTIONARY_VALUE', [
											'#ELEMENT_ID#' => $arElement['ID'],
											'#VALUE#' => htmlspecialcharsbx($strValue),
											'#ATTRIBUTE#' => $strAttributeName,
										])],
									];
								}
							}
							else{
								$arValues[] = [
									'value' => $strValue,
								];
							}
						}
						$arAttributes[] = [
							'id' => intVal($arAttribute['ATTRIBUTE_ID']),
							'values' => $arValues,
						];
					}
					unset($arItem[$strField]);
				}
			}
			$arItem['attributes'] = $arAttributes;
		}
		# Put complex attributes
		if(!$this->isStockAndPrice()){
			if(!empty($arComplex)){
				$arItem['complex_attributes'][] = [
					'attributes' => $arComplex,
				];
			}
		}
		#
		if($this->isStockAndPrice()){
			if(isset($arItem['auto_action_enabled']) && !Helper::strlen($arItem['auto_action_enabled'])){
				unset($arItem['auto_action_enabled']);
			}
		}
	}
	
	/**
	 *	Correct value types (for example, "vat": 0 => "vat": "0")
	 */
	protected function correctFieldTypes(&$arItem, $arElement, $arFields, $arElementSections, $arDataMore){
		$arStringFields = ['offer_id', 'price', 'old_price', 'min_price', 'premium_price', 'vat'];
		foreach($arStringFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = strVal($arItem[$strKey]);
			}
		}
		$arIntFields = ['depth', 'height', 'weight', 'width'];
		foreach($arIntFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = intVal(ceil(floatVal($arItem[$strKey])));
			}
		}
	}
	
	/**
	 *	Check empty required fields (for each category)
	 */
	protected function checkRequiredFields($intIBlockId, $intElementId, $arFields){
		$arEmptyRequiredFields = [];
		$arFieldsAll = $this->getFieldsCached($this->intProfileId, $intIBlockId, true);
		foreach($arFields as $strField => $mValue){
			if($arFieldsAll[$strField]){
				$bEmpty = Helper::isEmpty($mValue, $arFieldsAll[$strField]->isSimpleEmptyMode());
				if($bEmpty && $arFieldsAll[$strField]->isCustomRequired()){
					$arAttributeId = static::parseAttributeId($strField);
					if(is_array($arAttributeId)){
						if($this->isAttributeRequired($arAttributeId['CATEGORY_ID'], $arAttributeId['ATTRIBUTE_ID'])){
							if(!is_array($arEmptyRequiredFields[$arAttributeId['CATEGORY_ID']])){
								$arEmptyRequiredFields[$arAttributeId['CATEGORY_ID']] = [];
							}
							$arEmptyRequiredFields[$arAttributeId['CATEGORY_ID']][] = $arFieldsAll[$strField]->getName();
						}
					}
				}
			}
		}
		if(!empty($arEmptyRequiredFields)){
			$arErrors = [];
			foreach($arEmptyRequiredFields as $intCategoryId => $arErrorFields){
				$resCategory = Category::getList(['filter' => ['CATEGORY_ID' => $intCategoryId], 'select' => ['NAME']]);
				if($arCategory = $resCategory->fetch()){
					$arErrors[] = static::getMessage('ERROR_EMPTY_REQUIRED_FIELDS', [
						'#ELEMENT_ID#' => $intElementId,
						'#CATEGORY#' => $this->formatCategoryName($intCategoryId, $arCategory['NAME']),
						'#FIELDS#' => implode(', ', $arErrorFields),
					]);
				}
			}
			return $arErrors;
		}
		return false;
	}

	/**
	 * Prepare YouTube videos
	 */
	protected function prepareYouTubeVideos($arValue){
		if(!is_array($arValue)){
			$arValue = Helper::strlen($arValue) ? [$arValue] : [];
		}
		return array_map(function($strVideo){
			return Helper::getYoutubeVideoInfo($strVideo);
		}, $arValue);
	}
	
	/**
	 *	Cancel save json to file
	 */
	protected function onUpJsonExportItem(&$arItem, &$strJson, &$arSession, &$bWrite){
		$bWrite = false;
	}
	
	/**
	 *	Add custom step
	 */
	protected function onUpGetExportSteps(&$arExportSteps, &$arSession){
		$arUnsetSteps = ['EXPORT_HEADER', 'EXPORT_FOOTER', 'REPLACE_FILE', 'REPLACE_TMP_FILES'];
		foreach($arUnsetSteps as $strStep){
			unset($arExportSteps[$strStep]);
		}
	}
	
	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		$mResult = Exporter::RESULT_ERROR;
		if($this->bCron){
			do{
				$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
			}
			while($mResult === Exporter::RESULT_CONTINUE);
		}
		else{
			$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
		}
		return $mResult;
	}
	
	/**
	 *	Export data by API (one step)
	 */
	protected function stepExport_ExportApi_Step(&$arSession, $arStep){
		$bExported = false;
		$arItems = $this->getExportDataItems(null, null);
		if(!empty($arItems)){
			$arJsonItems = [];
			$arDataMore = [];
			foreach($arItems as $arItem){
				$arEncodedItem = Json::decode($arItem['DATA']);
				$arEncodedItemDataMore = unserialize($arItem['DATA_MORE']);
				if(Helper::strlen($arEncodedItemDataMore['AUTO_ACTION_ENABLED'])){
					$arEncodedItem['auto_action_enabled'] = $arEncodedItemDataMore['AUTO_ACTION_ENABLED'];
				}
				$arJsonItems[$arItem['ELEMENT_ID']] = $arEncodedItem;
				$arDataMore[$arItem['ELEMENT_ID']] = $arEncodedItemDataMore;
			}
			$arItemsId = array_column($arJsonItems, 'offer_id');
			$this->addToLog('Mode: '.($this->isStockAndPrice() ? 'stocks and prices' : 'default'), true);
			$strLastMethod = null;
			# Mode: stocks
			if($this->isStockAndPrice()){
				$arJsonItemsFull = [
					'prices' => array_values($arJsonItems),
				];
				$strLastMethod = '/v1/product/import/prices';
				$arResult = $this->API->execute('/v1/product/import/prices', $arJsonItemsFull, ['METHOD' => 'POST']);
				if(is_array($arResult) && !empty($arResult['result'])){
					$this->addToLog('[1] Result for /v1/product/import/prices: '.print_r($arResult, true), true);
					$this->addToLog('Exported prices: '.print_r($arJsonItemsFull, true), true);
					$intOzonTaskId = 0;
					$bExported = true;
				}
				else{
					$strError = isset($arResult['message']) ? print_r($arResult['message'], 1) : null;
					$strLogMessage = static::getMessage('ERROR_EXPORT_PRICES_BY_API', ['#METHOD#' => $strLastMethod, '#ERROR#' => $strError]);
					$this->addToLog($strLogMessage);
					$this->addToLog('JSON: '.Json::encode($arJsonItemsFull));
					$this->addToLog('Items: '.implode(', ', $arItemsId), true);
					$this->addToLog('Response: '.Json::prettyPrint($arResult), true);
					# Display error
					require __DIR__.'/include/popup_error.php';
					return Exporter::RESULT_ERROR;
				}
			}
			# Mode: default
			else{
				# Prepare prices
				$arPrices = [];
				$arKeys = ['offer_id', 'price', 'old_price', 'min_price', 'premium_price', 'auto_action_enabled'];
				foreach($arJsonItems as $key => $arJsonItem){
					$arPrice = array_intersect_key($arJsonItem, array_flip($arKeys));
					if($arPrice['price'] !== ''){
						$arPrices[] = $arPrice;
					}
				}
				# Prepare cards to export
				$arJsonItemsFull = [
					'items' => array_values($arJsonItems),
				];
				$strLastMethod = '/v2/product/import';
				$arResult = $this->API->execute('/v2/product/import', $arJsonItemsFull, ['METHOD' => 'POST']);
				if(is_array($arResult) && $arResult['result']['task_id']){
					$this->addToLog('Exported by /v2/product/import: '.print_r($arResult, true), true);
					$intOzonTaskId = $arResult['result']['task_id'];
					# Send prices
					if(!empty($arPrices)){
						$arPrices = [
							'prices' => $arPrices,
						];
						$strLastMethod = '/v1/product/import/prices';
						$arResult = $this->API->execute('/v1/product/import/prices', $arPrices, ['METHOD' => 'POST']);
						if(is_array($arResult) && !empty($arResult['result'])){
							$this->addToLog('[2] Exported by /v1/product/import/prices: '.print_r($arResult, true), true);
							$this->addToLog('Exported prices: '.print_r($arPrices, true), true);
						}
						else{
							$strError = isset($arResult['message']) ? print_r($arResult['message'], 1) : null;
							$strLogMessage = static::getMessage('ERROR_EXPORT_PRICES_BY_API', ['#METHOD#' => $strLastMethod, '#ERROR#' => $strError]);
							$this->addToLog($strLogMessage);
							$this->addToLog('JSON: '.Json::encode($arPrices));
							$this->addToLog('Items: '.implode(', ', $arItemsId), true);
							$this->addToLog('Response: '.Json::prettyPrint($arResult), true);
						}
					}
					$bExported = true;
				}
				else{
					$strError = isset($arResult['error']) ? print_r($arResult['error'], 1) : null;
					if(is_null($strError) && isset($arResult['message'])){
						$strError = sprintf('[%d] %s', $arResult['code'], $arResult['message']);
					}
					if(is_null($strError)){
						if($arResult['result']['task_id'] === 0){
							$strError = static::getMessage('ERROR_EXPORT_ITEMS_BY_API_TASK_0');
						}
					}
					$strLogMessage = static::getMessage('ERROR_EXPORT_ITEMS_BY_API', ['#METHOD#' => $strLastMethod, '#ERROR#' => $strError]);
					$this->addToLog($strLogMessage);
					$this->addToLog('JSON: '.Json::encode($arJsonItemsFull), true);
					$this->addToLog('Items: '.implode(', ', $arItemsId), true);
					$this->addToLog('Headers: '.implode(PHP_EOL, $this->API->getHeaders()), true);
					# Display error
					require __DIR__.'/include/popup_error.php';
					return Exporter::RESULT_ERROR;
				}
			}
			if($bExported){
				$obDate = new \Bitrix\Main\Type\Datetime();
				# Save state
				foreach($arItems as $arItem){
					$this->setDataItemExported($arItem['ID']);
					$arSession['INDEX']++;
				}
				# Add task
				if($this->isHistoryTaskSave()){
					$strJsonFullRaw = Json::encode($arJsonItemsFull);
					if(!Helper::isUtf()){
						$strJsonFullRaw = Helper::convertEncoding($strJsonFullRaw, 'UTF-8', 'CP1251');
					}
					$arTask = [
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'TASK_ID' => $intOzonTaskId,
						'PRODUCTS_COUNT' => count($arItemsId),
						'JSON' => $strJsonFullRaw,
						'RESPONSE' => \Bitrix\Main\Web\Json::encode($arResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => $obDate,
					];
					$obTask = Task::add($arTask);
					$intTaskId = $obTask->getId();
					# Add task items to history
					foreach($arJsonItems as $intElementId => $arItem){
						$strJson = Json::encode($arItem);
						if(!Helper::isUtf()){
							$strJson = Helper::convertEncoding($strJson, 'UTF-8', 'CP1251');
						}
						$strResponse = null;
						if(is_array($arResult['result']) && isset($arResult['result'][0])){
							foreach($arResult['result'] as $arResultItem){
								if($arResultItem['offer_id'] == $arItem['offer_id']){
									$strResponse = \Bitrix\Main\Web\Json::encode($arResultItem, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
									if(!$arResultItem['updated']){
										$this->addToLog('Error export product data ['.$strLastMethod.']: '.print_r($arResultItem, true));
									}
									break;
								}
							}
						}
						if($this->isHistoryProductSave()){
							History::add([
								'MODULE_ID' => $this->strModuleId,
								'PROFILE_ID' => $this->intProfileId,
								'TASK_ID' => $intTaskId,
								'TASK_ID_OZON' => $intOzonTaskId,
								'OFFER_ID' => $arItem['offer_id'],
								'ELEMENT_ID' => $intElementId,
								'JSON' => $strJson,
								'RESPONSE' => $strResponse,
								'SESSION_ID' => session_id(),
								'TIMESTAMP_X' => $obDate,
							]);
						}
					}
				}
				# Continue load: export stocks
				$arStocks = []; // simple stocks
				$arStocksExt = []; // stocks by warehouses
				if(is_array($arDataMore)){
					foreach($arDataMore as $arStock){
						if(Helper::strlen($arStock['OFFER_ID'])){
							if(Helper::strlen($arStock['STOCK'])){
								if(isset($arStock['STOCK_RESULT'])){
									$arStock['STOCK'] = $arStock['STOCK_RESULT'];
								}
								$arStocks[] = [
									'offer_id' => strVal($arStock['OFFER_ID']),
									'stock' => intVal($arStock['STOCK']),
								];
							}
							if(!empty($arStock['STOCKS'])){
								foreach($arStock['STOCKS'] as $intStoreId => $intStock){
									$strOfferId = strVal($arStock['OFFER_ID']);
									$intWarehouseId = intVal($intStoreId);
									$intStock = intVal($intStock);
									if(is_array($arStock['STOCKS_RESERVED']) && isset($arStock['STOCKS_RESERVED'][$intWarehouseId])){
										if($arStock['STOCKS_RESERVED'][$intWarehouseId] > 0){
											$intStock -= $arStock['STOCKS_RESERVED'][$intWarehouseId];
										}
										if($intStock < 0){
											$intStock = 0;
										}
									}
									$arStockExt = [
										'offer_id' => $strOfferId,
										'stock' => $intStock,
										'warehouse_id' => $intWarehouseId,
									];
									$arStocksExt[] = $arStockExt;
								}
							}
						}
					}
				}
				# Send stocks
				if(!empty($arStocks) || !empty($arStocksExt)){
					$this->sendStocks($intTaskId, $arStocks, $arStocksExt);
				}
			}
			return Exporter::RESULT_CONTINUE;
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Send stocks data via API and save results to table acrit_ozon_history
	 */
	protected function sendStocks($intTaskId, array $arStocks, array $arStocksExt){
		# Send simple stocks
		$this->sendStocksV1($intTaskId, $arStocks);
		# Send warehouse stocks
		$this->sendStocksV2($intTaskId, $arStocksExt);
	}

	/**
	 *	Send simple stocks
	 */
	protected function sendStocksV1($intTaskId, array $arStocks){
		if(!empty($arStocks)){
			$arPost = [
				'stocks' => $arStocks,
			];
			$this->addToLog('Stocks (V1): '.print_r($arPost, true), true);
			$this->obStocksDate = new \Bitrix\Main\Type\Datetime();
			$arResponse = $this->API->execute('/v2/products/stocks', $arPost, ['METHOD' => 'POST']);
			if(is_array($arResponse['result']) && !empty($arResponse['result'])){
				foreach($arResponse['result'] as $arStock){
					$this->processStockResult($intTaskId, $arStocks, $arStock);
				}
			}
			else{
				$this->addToLog('Error send stocks (v1): '.print_r($arResponse, true));
			}
			usleep(750000); # Limit: 80 requests per minute
		}
		else{
			$this->addToLog('Stocks are empty (V1).', true);
		}
	}

	/**
	 *	Send ext stocks
	 */
	protected function sendStocksV2($intTaskId, array $arStocks){
		if(!empty($arStocks)){
			$arStocksChunks = array_chunk($arStocks, static::STOCKS_V2_COUNT_PER_REQUEST);
			foreach($arStocksChunks as $atStocksChunk){
				$arPost = [
					'stocks' => $atStocksChunk,
				];
				$this->addToLog('Stocks (V2): '.print_r($arPost, true), true);
				$this->obStocksDate = new \Bitrix\Main\Type\Datetime();
				$arResponse = $this->API->execute('/v2/products/stocks', $arPost, ['METHOD' => 'POST']);
				if(is_array($arResponse['result']) && !empty($arResponse['result'])){
					foreach($arResponse['result'] as $arStock){
						$this->processStockResult($intTaskId, $atStocksChunk, $arStock);
					}
					$this->addToLog('Result for /v2/products/stocks: '.print_r($arResponse, true), true);
				}
				else{
					$this->addToLog('Error send stocks /v2/products/stocks (v2): '.print_r($arResponse, true));
				}
				usleep(750000); # Limit: 80 requests per minute
			}
		}
		else{
			$this->addToLog('Stocks are empty (V2).', true);
		}
	}

	/**
	 * Get history item id by task_id and offer_id
	 */
	protected function getHistoryItemId($intTaskId, $strOfferId){
		$arQuery = [
			'filter' => [
				'=MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'TASK_ID' => $intTaskId,
				'=OFFER_ID' => $strOfferId,
			],
			'select' => [
				'ID',
			],
		];
		if($arHistoryItem = History::getList($arQuery)->fetch()){
			return $arHistoryItem['ID'];
		}
		return false;
	}

	/**
	 *	Process (save) each stock in send stock results
	 */
	protected function processStockResult($intTaskId, array $arOriginalStocks, $arStock){
		if(is_array($arStock) && Helper::strlen($strOfferId = $arStock['offer_id'])){
			$intWarehouseId = $arStock['warehouse_id'];
			if($intHistoryItemId = $this->getHistoryItemId($intTaskId, $strOfferId)){
				$intStock = 0;
				foreach($arOriginalStocks as $arOriginalStock){
					if($arOriginalStock['offer_id'] == $strOfferId){
						if(is_null($intWarehouseId) || $arOriginalStock['warehouse_id'] == $intWarehouseId){
							$intStock = $arOriginalStock['stock'];
							break;
						}
					}
				}
				if($this->isHistoryStockSave()){
					$arHistoryStock = [
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'HISTORY_ID' => $intHistoryItemId,
						'OFFER_ID' => $strOfferId,
						'PRODUCT_ID' => $arStock['product_id'],
						'WAREHOUSE_ID' => $intWarehouseId,
						'STOCK' => $intStock,
						'UPDATED' => $arStock['updated'] ? 'Y' : 'N',
						'ERRORS' => implode("\n", array_map(function($arItem){
							return sprintf('[%s] %s', $arItem['code'], $arItem['message']);
						}, is_array($arStock['errors']) ? $arStock['errors'] : [])),
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => $this->obStocksDate,
					];
					HistoryStock::add($arHistoryStock);
				}
			}
		}
	}
	
	/**
	*	Show notices
	*/
	public function showMessages(){
		print Helper::showNote(static::getMessage('NOTICE_SUPPORT'), true);
		$this->doMigrateTaskId();
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
	 *	Handle click on button 'update'
	 */
	protected function updateTaskStatus($intTaskId, &$arJsonResult){
		$strResultHtml = '';
		$intTaskId = intVal($intTaskId);
		if($intTaskId){
			if($arTask = $this->updateSingleTaskStatus($intTaskId)){
				$arJsonResult['StatusUpdateDatetime'] = $arTask['STATUS_DATETIME']->toString();
				$strResultHtml = $this->displayTaskStatus($arTask);
			}
		}
		return $strResultHtml;
	}
	
	/**
	 *	Update status for one task
	 *	@return $arTask - array of task
	 */
	protected function updateSingleTaskStatus($intTaskId){
		$mResult = false;
		$intTaskId = intVal($intTaskId);
		if($intTaskId){
			$arJson = $this->API->execute('/v1/product/import/info', ['task_id' => $intTaskId], ['METHOD' => 'POST']);
			$arFilter = ['TASK_ID' => $intTaskId, 'PROFILE_ID' => $this->intProfileId];
			if($arTask = Task::getList(['filter' => $arFilter])->fetch()){
				$arCount = [
					'Status' => [],
					'Count' => $arJson['result']['total'],
				];
				foreach($arJson['result']['items'] as $arItem){
					$strStatus = ucFirst($arItem['status']);
					if(!isset($arCount['Status'][$strStatus])){
						$arCount['Status'][$strStatus] = 0;
					}
					$arCount['Status'][$strStatus]++;
				}
				$strStatusData = serialize($arCount);
				#
				$obDate = new \Bitrix\Main\Type\Datetime();
				$arUpdateFields = [
					'STATUS' => $strStatusData,
					'STATUS_DATETIME' => $obDate,
				];
				$obResult = Task::update($arTask['ID'], $arUpdateFields);
				if($obResult->isSuccess()){
					foreach($arJson['result']['items'] as $arItem){
						$resHistoryItem = History::getList([
							'filter' => [
								'TASK_ID_OZON' => $intTaskId,
								'=OFFER_ID' => $arItem['offer_id'],
							],
							'select' => ['ID', 'ELEMENT_ID', 'STATUS'],
						]);
						if($arHistoryItem = $resHistoryItem->fetch()){
							History::update($arHistoryItem['ID'], [
								'PRODUCT_ID' => $arItem['product_id'],
								'STATUS' => $arItem['status'],
								'STATUS_DATETIME' => $obDate,
							]);
							$this->checkSetImported($arHistoryItem, $arItem['status'], $arItem['offer_id']);
						}
					}
					$mResult = array_merge($arTask, $arUpdateFields);
				}
			}
		}
		return $mResult;
	}
	
	/**
	 *	Display status for one task
	 */
	protected function displayTaskStatus($arTask){
		$strResultHtml = '';
		$arStatus = unserialize($arTask['STATUS']);
		if(is_array($arStatus) || $this->isStockAndPrice()){
			ob_start();
			$strFile = __DIR__.'/include/tasks/status.php';
			Helper::loadMessages($strFile);
			require $strFile;
			$strResultHtml = ob_get_clean();
		}
		return $strResultHtml;
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesContent($arGet){
		ob_start();
		$strField = $arGet['field'];
		if($arAttribute = $this->parseAttributeId($strField)){
			require __DIR__.'/include/allowed_values/popup.php';
		}
		else{
			print Helper::showError(static::getMessage('ERROR_PARSE_ATTRIBUTE', ['#ATTRIBUTE#' => $strField]));
		}
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesFilteredContent($arGet){
		ob_start();
		$strField = $arGet['field'];
		if($arAttribute = $this->parseAttributeId($strField)){
			require __DIR__.'/include/allowed_values/filtered.php';
		}
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getTaskJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				#'TASK_ID' => $arGet['task_id'],
			],
			'select' => [
				'ID',
				'TASK_ID',
				'JSON',
				'RESPONSE',
				'TIMESTAMP_X',
			],
		];
		$arParams = [
			'ALLOW_COPY' => true,
			'DISPLAY_TASK_NOTE' => true,
		];
		if($arGet['task_id']){
			$arQuery['filter']['TASK_ID'] = $arGet['task_id'];
		}
		elseif($arGet['item_id']){
			$arQuery['filter']['ID'] = $arGet['item_id'];
		}
		return $this->displayPopupJson(Task::getList($arQuery)->fetch(), 'JSON', $arParams);
	}
	
	/**
	 *	
	 */
	protected function getHistoryItemJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['history_item_id'],
			],
			'select' => [
				'ID',
				'JSON',
				'STOCK_VALUE',
				'STOCK_UPDATED',
				'STOCK_ERRORS',
				#
				'TASK_ID',
				'TASK_ID_OZON',
				'OFFER_ID',
				'TIMESTAMP_X',
			],
		];
		$arParams = [
			'ALLOW_COPY' => true,
			'SHOW_STOCKS' => true,
			'DISPLAY_OFFER_NOTE' => true,
		];
		if($arHistoryItem = History::getList($arQuery)->fetch()){
			$arHistoryItem['STOCKS'] = [];
			$arQuery = [
				'filter' => [
					'HISTORY_ID' => $arHistoryItem['ID'],
				],
				'select' => [
					'ID',
					'HISTORY_ID',
					'WAREHOUSE_ID',
					'STOCK',
					'UPDATED',
					'ERRORS',
				],
				'order' => [
					'WAREHOUSE_ID' => 'asc',
					'ID' => 'asc',
				],
			];
			$resHistoryStocks = HistoryStock::getList($arQuery);
			while($arHistoryStock = $resHistoryStocks->fetch()){
				$arHistoryItem['STOCKS'][] = $arHistoryStock;
			}
			if(empty($arHistoryItem['STOCKS'])){ // collect legacy data
				$arHistoryItem['STOCKS'][] = [
					'ID' => null,
					'WAREHOUSE_ID' => null,
					'STOCK' => $arHistoryItem['STOCK_VALUE'],
					'UPDATED' => $arHistoryItem['STOCK_UPDATED'],
					'ERRORS' => $arHistoryItem['STOCK_ERRORS'],
				];
			}
			return $this->displayPopupJson($arHistoryItem, 'JSON', $arParams);
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function displayPopupJson($arData, $strJsonKey, $arParams=[]){
		if(is_array($arData) && strlen($strJsonKey)){
			$strFile = __DIR__.'/include/popup_json.php';
			Helper::loadMessages($strFile);
			ob_start();
			$strJson = &$arData[$strJsonKey];
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}
	
	/**
	 *	Modify teachers
	 */
	public function addTeachers(&$arTeachers){
		$arOzonTeacher = $this->getTeacher();
		if(is_array($arOzonTeacher['STEPS'])){
			foreach($arOzonTeacher['STEPS'] as $strStep => $arStep){
				if(is_null($arStep)){
					unset($arOzonTeacher['STEPS'][$strStep]);
				}
			}
		}
		$arTeachers[] = $arOzonTeacher;
	}
	
	/**
	 *	Modify default teacher
	 */
	public function modifyDefaultTeacher(&$arDefaultTeacher){
		$arOzonTeacher = $this->getTeacher();
		if(is_array($arOzonTeacher['STEPS'])){
			foreach($arOzonTeacher['STEPS'] as $strStep => $arStep){
				if(is_null($arStep)){
					unset($arDefaultTeacher['STEPS'][$strStep]);
				}
				elseif(is_array($arStep) && is_array($arDefaultTeacher['STEPS'][$strStep])){
					$arDefaultTeacher['STEPS'][$strStep] = array_merge($arDefaultTeacher['STEPS'][$strStep], $arStep);
					continue;
				}
				else{
					$strAfterKey = $arStep['AFTER'];
					unset($arStep['AFTER']);
					$this->teaacherAddItem($arDefaultTeacher['STEPS'], $strStep, $arStep, $strAfterKey);
				}
			}
		}
	}
	
	/**
	 *	Get teacher array fron include/teacher.php
	 *	This will be used in $this->addTeachers() and $this->modifyDefaultTeacher();
	 */
	protected function getTeacher(){
		return require __DIR__.'/include/teacher.php';
	}
	
	/**
	 *	Show options on general subtab (bottom)
	 */
	public function addHtmlOptionsGeneralBottom($intIBlockID){
		$strCode = static::getCode();
		$arIBlockParams = $this->getIBlockParams($intIBlockID);
		?>
		<tr class="heading"><td colspan="2"><?=static::getMessage('GENERAL_SETTINGS_HEADER_STOCK');?></td></tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(static::getMessage('GENERAL_SETTINGS_HINT_CONSIDER_RESERVED_STOCK'));?>
				<?=static::getMessage('GENERAL_SETTINGS_CONSIDER_RESERVED_STOCK');?>:
			</td>
			<td width="60%">
				<div>
					<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][OZON_CONSIDER_RESERVED_STOCK]" value="N" />
					<input type="checkbox" name="iblockparams[<?=$intIBlockID;?>][OZON_CONSIDER_RESERVED_STOCK]" value="Y" 
						data-role="ozon_consider_reserved_stock"
						<?if($arIBlockParams['OZON_CONSIDER_RESERVED_STOCK']=='Y'):?>checked="checked"<?endif?>
						id="checkbox_OZON_CONSIDER_RESERVED_STOCK"
					/>
				</div>
			</td>
		</tr>
		<?
	}

	/**
	 *	Get product data from Ozon by API
	 */
	public function getOzonProductInfo($strOfferId){
		$arQuery = [
			'offer_id' => $strOfferId,
		];
		$arResult = $this->API->execute('/v2/product/info', $arQuery, ['METHOD' => 'POST', 'SKIP_ERRORS' => true]);
		return is_array($arResult['result']) ? $arResult['result'] : [];
	}

	/**
	 *	Get product warehouses stocks
	 */
	public function getOzonProductWarehousesStocks($intSkuId){
		$arQuery = [
			'sku' => [$intSkuId],
		];
		$arResult = $this->API->execute('/v1/product/info/stocks-by-warehouse/fbs', $arQuery, ['METHOD' => 'POST', 'SKIP_ERRORS' => true]);
		return is_array($arResult['result']) ? $arResult['result'] : [];
	}
	
	/**
	 *	Check to set imported for item
	 *	$arHistoryItem contains ID, ELEMENT_ID, STATUS
	 */
	protected function checkSetImported(array $arHistoryItem, $strNewStatus, $strOfferId){
		#$strOldStatus = $arHistoryItem['STATUS'];
		# ToDo
	}
	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		return $this->getExtFileOpenLink('https://seller.ozon.ru/products?filter=all', 
			Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}

	protected function doMigrateTaskId(){
		set_time_limit(0);
		while(true){
			$arQuery = [
				'filter' => [
					'=MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'TASK_ID' => 0,
					'>TASK_ID_OZON' => 0,
				],
				'select' => ['ID', 'TASK_ID', 'TASK_ID_OZON', 'TASK_ID_RAW' => 'TASK.ID'],
				'runtime' => [
					'TASK' => [
						'data_type' => '\Acrit\Core\Export\Plugins\OzonRuHelpers\TaskTable',
						'reference' => [
							'this.TASK_ID_OZON' => 'ref.TASK_ID',
						],
						'join_type' => 'left',
					],
				],
				'limit' => 100,
			];
			$resItems = History::getList($arQuery);
			if(!$resItems->getSelectedRowsCount()){
				break;
			}
			while($arItem = $resItems->fetch()){
				History::update($arItem['ID'], ['TASK_ID' => $arItem['TASK_ID_RAW']]);
			}
		}

	}

	public function getHistorySaveTypes(){
		return [
			'task_product_stock' => static::getMessage('HISTORY_SAVE_TASK_PRODUCT_STOCK'),
			'task_product' => static::getMessage('HISTORY_SAVE_TASK_PRODUCT'),
			'task' => static::getMessage('HISTORY_SAVE_TASK'),
			'nothing' => static::getMessage('HISTORY_SAVE_NOTHING'),
		];
	}

	public function isHistoryTaskSave(){
		return empty($this->arParams['HISTORY_SAVE']) || $this->arParams['HISTORY_SAVE'] != 'nothing';
	}
	
	public function isHistoryProductSave(){
		return empty($this->arParams['HISTORY_SAVE']) || $this->arParams['HISTORY_SAVE'] == 'task_product_stock'
			|| $this->arParams['HISTORY_SAVE'] == 'task_product';
	}

	public function isHistoryStockSave(){
		return empty($this->arParams['HISTORY_SAVE']) || $this->arParams['HISTORY_SAVE'] == 'task_product_stock';
	}

	public function downloadFieldReference(){
		if(Helper::strlen($strField = $_GET['field'])){
			if(preg_match('#^attribute_(\d+|\w+)_(\d+)$#', $strField, $arMatch)){
				$intCategoryId = intVal($arMatch[1]);
				$intAttributeId = intVal($arMatch[2]);
				Helper::obRestart();
				header('Content-Type: text/plain');
				header(sprintf('Content-Disposition: attachment; filename="attribute_%d_values.txt"', $intAttributeId));
				if(/* $intCategoryId > 0 &&  */$intAttributeId > 0){
					$resAttributes = AttributeValue::getList([
						'order' => ['VALUE' => 'ASC'],
						'filter' => [
							'ATTRIBUTE_ID' => $intAttributeId,
							[
								'LOGIC' => 'OR',
								['CATEGORY_ID' => $intCategoryId],
								['CATEGORY_ID' => '0'],
							],
						],
						'select' => ['VALUE'],
					]);
					while($arAttribute = $resAttributes->fetch()){
						print $arAttribute['VALUE'].PHP_EOL;
					}
				}
				die();
			}
		}
	}
	
	/**
	 *	Update category attributes and dictionaries
	 */
	protected function ajaxCheckCategories($arParams, &$arJsonResult){
		$arPost = &$arParams['POST'];
		#
		$arJsonResult['Continue'] = false;
		$arJsonResult['LastId'] = null;
		$intCount = intVal($arPost['count']);
		#
		$arJsonRequest = [
			'category_id' => intVal($arPost['category_id']),
			'attribute_id' => intVal($arPost['attribute_id']),
			'limit' => 5000,
		];
		if(is_numeric($arPost['last_id']) && $arPost['last_id'] > 0){
			$arJsonRequest['last_value_id'] = $arPost['last_id'];
		}
		$strCommand = '/v2/category/attribute/values';
		$arJsonResponse = $this->API->execute($strCommand, $arJsonRequest, ['METHOD' => 'POST']);
		if(is_array($arJsonResponse) && !empty($arJsonResponse) && $arJsonResponse['result']){
			$arItems = [];
			foreach($arJsonResponse['result'] as $arItem){
				$arItems[] = sprintf('%s [%d]', $arItem['value'], $arItem['id']);
			}
			$arJsonResult['Text'] = implode(PHP_EOL, $arItems).PHP_EOL;
			$arJsonResult['Count'] = $intCount + count($arItems);
			if($arJsonResponse['has_next']){
				$arJsonResult['Continue'] = true;
				$arJsonResult['LastId'] = $arItem['id'];
			}
			else{
				$arJsonResult['Message'] = static::getMessage('ATTRIBUTE_VALUES_CHECK_SUCCESS', [
					'#COUNT#' => $arJsonResult['Count'],
				]);
			}
		}
		else{
			$arJsonResult['Message'] = static::getMessage('ATTRIBUTE_VALUES_CHECK_ERROR');
		}
		$arJsonResult['Request'] = $arJsonRequest;
		$arJsonResult['Response'] = $arJsonResponse;
	}
	
	/**
	 *	Find history item data by offer_id
	 */
	protected function ajaxOpenOfferJson($arParams, &$arJsonResult){
		if(Helper::strlen($strOfferId = $arParams['GET']['offer_id'])){
			$arQuery = [
				'order' => [
					'ID' => 'DESC',
				],
				'filter' => [
					'=MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'=OFFER_ID' => $strOfferId,
				],
				'select' => [
					'ID',
					'TASK_ID',
					'TASK_ID_OZON',
					'ELEMENT_ID',
				],
			];
			if($arHistoryItem = History::getList($arQuery)->fetch()){
				$arJsonResult['HistoryItem'] = $arHistoryItem;
			}
			else{
				$arJsonResult['Message'] = static::getMessage('HISTORY_OFFER_NOT_FOUND', ['#OFFER_ID#' => $strOfferId]);
			}
		}
		else{
			$arJsonResult['Message'] = static::getMessage('HISTORY_OFFER_EMPTY_ID');
		}
	}
	
	/**
	 *	Find history item data by offer_id
	 */
	protected function ajaxOpenTaskJson($arParams, &$arJsonResult){
		if(Helper::strlen($strTaskId = $arParams['GET']['task_id'])){
			$arQuery = [
				'order' => [
					'ID' => 'DESC',
				],
				'filter' => [
					'=MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'TASK_ID' => $strTaskId,
				],
				'select' => [
					'ID',
					'TASK_ID',
				],
			];
			if($arTask = Task::getList($arQuery)->fetch()){
				$arJsonResult['Task'] = $arTask;
			}
			else{
				$arJsonResult['Message'] = static::getMessage('HISTORY_TASK_NOT_FOUND', ['#TASK_ID#' => $strTaskId]);
			}
		}
		else{
			$arJsonResult['Message'] = static::getMessage('HISTORY_TASK_EMPTY_ID');
		}
	}

	/**
	 * Get API object for work with plugin outside
	 */
	public function getApi(){
		return $this->API;
	}

}

?>