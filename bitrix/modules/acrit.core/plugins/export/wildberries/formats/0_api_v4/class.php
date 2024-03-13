<?
/**
 * Acrit Core: Wildberries
 * @documentation https://openapi.wb.ru/
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\ExternalIdTable as ExternalId,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\Api,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\Response,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\TaskTable as Task,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\HistoryTable as History;
	

class WildberriesV4 extends UniversalPlugin {
	
	const DATE_UPDATED = '2022-11-07';

	const ATTRIBUTE_ID = 'attribute_%s';

	# Types
	const C = 'C'; // CARD
	const N = 'N'; // NOMENCLATURE (child of card)
	const V = 'V'; // VARIATION (child of nomenclature)

	const KEY_N = 'nomenclatures';
	const KEY_V = 'variations';
	const KEY_A = 'addin';

	protected static $bSubclass = true;
	
	# Basic settings
	protected $bOffersPreprocess = true;
	protected $arSupportedFormats = ['JSON'];
	protected $bApi = true;
	protected $bCategoriesExport = false;
	protected $bCategoriesList = false;
	protected $bCategoriesUpdate = false;
	protected $bCategoriesStrict = false;
	protected $bCategoryCustomName = false;
	protected $arSupportedEncoding = [self::UTF8];
	protected $intExportPerStep = 50;
	
	# API class
	protected $API;
	
	# Cache
	protected $arCacheRequiredAttributes = [];
	protected $arCacheDictionaryAttributes = [];
	
	# Misc
	protected $strTokenTmp = null;
	protected $arParsedAttributes = [];
	protected $bOriginalProcessElement = false;
	protected $bOriginalProcessOffers = false;
	protected $arDataMoreTmp = [];
	protected $bSkipExportImages = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$bOffer = Helper::isOffersIBlock($intIBlockId);
		$bElement = !$bOffer;
		#
		$arResult = [];
		$arResult['object'] = ['REQUIRED' => !$this->isStockAndPrice()];
		$arResult['vendorCode'] = ['REQUIRED' => true];
		$arResult['techSize'] = ['CONST' => ''];
		$arResult['wbSize'] = ['CONST' => ''];
		$arResult['price'] = [/* 'REQUIRED' => true */];
		$arResult['skus'] = ['MULTIPLE' => true];
		$arResult['photos'] = ['MULTIPLE' => true, 'FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS']];
		#
		if(in_array($this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS']['OFFERS_MODE'], ['all', 'offers'])){
			$arResult['vendorCode']['REQUIRED'] = false;
			$arResult['price']['REQUIRED'] = false;
		}
		#
		if(!empty($arAttributes = $this->getStructureAttributes(static::N, $intIBlockId))){
			$arResult = array_merge($arResult, $arAttributes);
		}
		if($this->isExportStock()){
			if(!empty($arStockFields = $this->getStockFields())){
				$arResult['HEADER_STOCKS'] = [];
				$arResult = array_merge($arResult, $arStockFields);
			}
		}
		return $arResult;
	}

	/**
	 * Check stock to be exported
	 */
	protected function isExportStock(){
		return $this->arParams['EXPORT_STOCKS'] == 'Y';
	}

	/**
	 * Check 'Stock and price' mode
	 */
	protected function isStockAndPrice(){
		return $this->arParams['STOCK_AND_PRICE'] == 'Y';
	}

	/**
	 * Correct $bProcessElement and $bProcessOffers
	 **/
	public function getProcessEntities(&$bProcessElement, &$bProcessOffers, $arProfile, $intIBlockID, $arElement){
		$this->bOriginalProcessElement = $bProcessElement;
		$this->bOriginalProcessOffers = $bProcessOffers;
		$bProcessElement = true; // For correct preprocess, we always need product. But we manually will export (or not) it.
	}

	/**
	 * Get fields for stocks
	 */
	public function getStockFields(){
		$arResult = [];
		foreach($this->getStores(false) as $intStoreId => $strStoreName){
			$arResult['stock_'.$intStoreId] = [
				'FIELD' => 'CATALOG_QUANTITY',
				'NAME' => static::getMessage('FIELD_STOCK', [
					'#STORE_ID#' => $intStoreId,
					'#STORE_NAME#' => $strStoreName,
				]),
				'DESCRIPTION' => static::getMessage('FIELD_STOCK_DESCRIPTION'),
			];
		}
		return $arResult;
	}

	/**
	 * Get attributes for $strStructure (for getUniversalFields)
	 */
	public function getStructureAttributes($strStructure, $intIBlockId){
		$arResult = [];
		# 1. Get all fields
		$arData = [];
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$bOffer = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'];
		$intMainIBlockId = $bOffer ? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		$arUsedCategories = $this->getUsedCategories($intMainIBlockId);
		if(!empty($arUsedCategories)){
			$arUsedCategories = array_values($arUsedCategories);
			$arSort = ['SORT' => 'ASC', 'TYPE' => 'ASC', 'NAME' => 'ASC'];
			$arFilter = ['=CATEGORY_NAME' => $arUsedCategories, 'TYPE' => $strStructure];
			$arSelect = ['*'];
			$resAttributes = Attribute::getList([
				'order' => $arSort,
				'filter' => $arFilter,
				'select' => $arSelect,
			]);
			while($arAttribute = $resAttributes->fetch()){
				if(strlen($arAttribute['UNIT'])){
					$arAttribute['NAME_SUFFIX'] .= ', '.$arAttribute['UNIT'];
				}
				if($arAttribute['USE_ONLY_DICTIONARY_VALUES'] == 'Y'){
					$arAttribute['NAME_SUFFIX'] .= static::getMessage('REF');
				}
				$arData[$arAttribute['CATEGORY_NAME']][] = $arAttribute;
			}
		}
		# 2. 
		if(is_array($arData)){
			foreach($arData as $strCategoryName => $arAttributes){
				$arResult[sprintf('HEADER_%s_%s', $strStructure, md5($strCategoryName))] = [
					'NAME' => static::getMessage(sprintf('HEADER_%s_ATTRIBUTES', $strStructure), ['#NAME#' => $strCategoryName]),
					'NORMAL_CASE' => true,
				];
				foreach($arAttributes as $arAttribute){
					$strAttributeId = sprintf(static::ATTRIBUTE_ID, $arAttribute['HASH']);
					$arField = [
						'NAME' => $arAttribute['NAME'],
						'NAME_SUFFIX' => $arAttribute['NAME_SUFFIX'],
						'DISPLAY_CODE' => $strAttributeId,
						'REQUIRED' => false,
						'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
						'PARAMS' => [],
					];
					$bString = $arAttribute['CHARACTER_TYPE'] == 1;
					$bNumber = $arAttribute['CHARACTER_TYPE'] == 4;
					if($arAttribute['MAX_COUNT'] > 1){
						$arField['MULTIPLE'] = true;
						$arField['MAX_COUNT'] = $arAttribute['MAX_COUNT'];
						$arField['PARAMS']['MULTIPLE'] = 'multiple';
						$arField['DESCRIPTION'] = static::getMessage('DESCRIPTION_TYPE_'.($bNumber ? 'NUMBER' : 'STRING').'_ARRAY');
					}
					else{
						$arField['DESCRIPTION'] = static::getMessage('DESCRIPTION_TYPE_'.($bNumber ? 'NUMBER' : 'STRING'));
					}
					if($this->getAttributeDictionaryInfo($arAttribute['NAME'])){
						$arField['ALLOWED_VALUES_CUSTOM'] = true;
					}
					$arResult[$strAttributeId] = $arField;
				}
			}
		}
		#
		return $arResult;
	}

	/**
	 * Get attribute dictionary by attribute name
	 */
	protected function getAttributeDictionaryInfo($strAttributeName){
		$arResult = false;
		$arDictionaries = [
			static::getMessage('DICTIONARY_colors') => '/content/v1/directory/colors',
			static::getMessage('DICTIONARY_kinds') => '/content/v1/directory/kinds',
			static::getMessage('DICTIONARY_countries') => '/content/v1/directory/countries',
			static::getMessage('DICTIONARY_collections') => '/content/v1/directory/collections',
			static::getMessage('DICTIONARY_seasons') => '/content/v1/directory/seasons',
			static::getMessage('DICTIONARY_contents') => '/content/v1/directory/contents',
			static::getMessage('DICTIONARY_consists') => '/content/v1/directory/consists',
			static::getMessage('DICTIONARY_brands') => '/content/v1/directory/brands',
			static::getMessage('DICTIONARY_tnved') => '/content/v1/directory/tnved',
		];
		if($strMethod = $arDictionaries[$strAttributeName]){
			$arResult = $this->API->getMethod($strMethod);
		}
		return $arResult;
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
	 * Include js with jquery.inputMask
	 */
	public function includeJs(){
		return '<script src="/bitrix/js/'.ACRIT_CORE.'/jquery.inputmask5/dist/jquery.inputmask.min.js"></script>'
			.PHP_EOL.parent::includeJs();
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/task.php';
		require_once __DIR__.'/include/classes/history.php';
		require_once __DIR__.'/include/classes/response.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$this->API = new Api($this->intProfileId, $this->strModuleId, $this->getAuthToken($strAuthToken));
		}
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockID, $arIBlockParams){
		return $this->includeHtml(__DIR__.'/include/categories/settings.php', [
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['AUTHORIZATION'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/authorization.php'),
			'SORT' => 130,
		];
		$arSettings['EXPORT_STOCKS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_stocks.php'),
			'SORT' => 170,
		];
		$arSettings['STOCK_AND_PRICE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/stock_and_price.php'),
			'SORT' => 180,
		];
		$arSettings['EXPORT_PRICES_BY_1'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_prices_by_1.php'),
			'SORT' => 190,
		];
		$arSettings['SKIP_APPEND_SIZES'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/skip_append_sizes.php'),
			'SORT' => 200,
		];
		$arSettings['SKIP_UPDATE_CARDS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/skip_update_cards.php'),
			'SORT' => 210,
		];
		$arSettings['SKIP_CREATE_UNAVAILABLE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/skip_create_unavailable.php'),
			'SORT' => 220,
		];
		$arSettings['GROUP_BY_COLORS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/group_by_colors.php'),
			'SORT' => 230,
		];
		$arSettings['HISTORY_SAVE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/history_save.php'),
			'SORT' => 300,
		];
	}

	/**
	 * Check skip update cards
	 */
	protected function canUpdateCards(){
		return $this->arParams['SKIP_UPDATE_CARDS'] != 'Y';
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'login':
				$this->login($arParams, $arJsonResult);
				break;
			case 'login_confirm':
				$this->loginConfirm($arParams, $arJsonResult);
				break;
			case 'token_check':
				$this->tokenCheck($arParams, $arJsonResult);
				break;
			case 'token_clear':
				$this->tokenClear($arParams, $arJsonResult);
				break;
			case 'popup_categories_add':
				$this->displayPopupCategoryAdd($arParams, $arJsonResult);
				break;
			case 'category_attributes_update':
				$this->ajaxUpdateAttributes($arParams, $arJsonResult);
				break;
			case 'allowed_values_custom':
				$this->getAllowedValuesContent($arParams, $arJsonResult);
				break;
			case 'refresh_tasks_list':
				$strLogCustomTitle = false;
				$arJsonResult['HTML'] = $this->getLogContent($strLogCustomTitle, $arParams['GET']);
				break;
			case 'task_json_preview':
				$arJsonResult['HTML'] = $this->getTaskJsonPreview($arParams['GET']);
				break;
			case 'history_item_json_preview':
				$arJsonResult['HTML'] = $this->getHistoryItemJsonPreview($arParams['GET']);
				break;
			case 'cards_browser':
				$arJsonResult['HTML'] = $this->cardsBrowser($arParams['POST']);
				break;
		}
	}

	/**
	 * Check if token is actual
	 */
	public function tokenCheck($arParams, &$arJsonResult){
		$arGet = ['name' => ''];
		$arParams = ['AUTH_TOKEN' => $arParams['GET']['auth_token']];
		$obResponse = $this->API->execute('/content/v1/object/characteristics/list/filter', $arGet, $arParams);
		$arJsonResult['Response'] = $obResponse->getResponse();
		$arJsonResult['Success'] = $obResponse->getStatus() == 200;
		if(!$arJsonResult['Success']){
			$this->addToLog(sprintf('Error check token [%s]: %s', $obResponse->getStatus(), $arJsonResult['Response']));
		}
	}

	/**
	 * Get auth token from profile settings
	 */
	protected function getAuthToken($strAuthToken=null){
		return strVal(Helper::strlen($strAuthToken) ? $strAuthToken : $this->arParams['AUTH_TOKEN']);
	}

	/**
	 * Get supplierID from profile settings
	 */
	protected function getSupplierId(){
		return $this->arParams['SUPPLIER_ID'];
	}

	/**
	 * Clear token
	 */
	public function tokenClear($arParams, &$arJsonResult){
		Helper::deleteOption($this->strModuleId, $this->getCookieTokenName());
		Helper::deleteOption($this->strModuleId, $this->getRefreshTokenName());
		$arJsonResult['Success'] = true;
	}

	/**
	 * Get param name for store refresh token
	 */
	public function getRefreshTokenName(){
		return sprintf('token_refresh_%s_%s', $this->getCode(), $this->intProfileId);
	}

	/**
	 * Get param name for store common refresh token
	 */
	public function getRefreshTokenNameCommon(){
		return sprintf('token_refresh_%s_common', $this->getCode());
	}

	/**
	 * Get refresh token for current profile
	 */
	public function getRefreshToken(){
		$tokenCommon = Helper::getOption($this->strModuleId, $this->getRefreshTokenNameCommon());
		$tokenProfile = Helper::getOption($this->strModuleId, $this->getRefreshTokenName());
		if(strlen($tokenProfile)){
			return $tokenProfile;
		}
		return $tokenCommon;
	}

	/**
	 * Saverefresh  token for current profile
	 */
	public function setRefreshToken($strToken){
		Helper::deleteOption($this->strModuleId, $this->getCookieTokenName());
		if($this->intProfileId){
			return Helper::setOption($this->strModuleId, $this->getRefreshTokenName(), $strToken);
		}
		return Helper::setOption($this->strModuleId, $this->getRefreshTokenNameCommon(), $strToken);
	}

	/**
	 * Get param name for store token
	 */
	public function getCookieTokenName(){
		return sprintf('token_cookie_%s_%s', $this->getCode(), $this->intProfileId);
	}

	/**
	 * Get token for current profile
	 */
	public function getCookieToken(){
		return Helper::getOption($this->strModuleId, $this->getCookieTokenName());
	}

	/**
	 * Save token for current profile
	 */
	public function setCookieToken($strToken){
		return Helper::setOption($this->strModuleId, $this->getCookieTokenName(), $strToken);
	}
	
	/**
	 *	Update category attritbutes and dictionaries
	 */
	protected function ajaxUpdateAttributes($arParams, &$arJsonResult){
		$arSession = &$_SESSION['ACRIT_WB_CAT_ATTR_UPDATE'];
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
				'CATEGORY_NAME' => false,
				'ATTRIBUTE_ID' => false,
				'ATTRIBUTE_NAME' => false,
				'SUB_INDEX' => 0,
			];
			$arSession['COUNT'] = count($arSession['CATEGORIES']);
			$arJsonResult['Continue'] = true;
			if($arSession['FORCED']){
				foreach($arSession['CATEGORIES'] as $strCategoryName){
					Attribute::deleteByFilter(['CATEGORY_NAME' => $strCategoryName]);
				}
			}
		}
		else{
			$arJsonResult['Action'] = 'Categories';
			foreach($arSession['CATEGORIES'] as $key1 => $strCategoryName){
				$arSession['ATTRIBUTE_NAME'] = false;
				$this->updateCategoryAttrubutes($strCategoryName, $arSession['ID']);
				$arSession['INDEX']++;
				$arSession['SUB_INDEX'] = 0;
				$arSession['CATEGORY_NAME'] = $strCategoryName;
				unset($arSession['CATEGORIES'][$key1]);
			}
			#
			$arJsonResult['Continue'] = true;
			if(empty($arSession['CATEGORIES'])){
				$arSession['FINISHED'] = true;
				$arJsonResult['Continue'] = false;
			}
		}
		$arJsonResult['SessionId'] = $arSession['ID'];
		$arJsonResult['Count'] = $arSession['COUNT'];
		$arJsonResult['Index'] = $arSession['INDEX'];
		$arJsonResult['Percent'] = $arSession['COUNT'] == 0 ? 0 : round($arSession['INDEX'] * 100 / $arSession['COUNT']);
		$arJsonResult['CategoryName'] = $arSession['CATEGORY_NAME'];
		ob_start();
		require __DIR__.'/include/categories/status.php';
		$arJsonResult['Html'] = ob_get_clean();
	}
	
	/**
	 *	Get used WB categories from redefinitions
	 */
	protected function getUsedCategories($intIBlockId=null, $bJustIds=false){
		$arResult = [];
		$arIBlockParams = $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS'];
		if(is_array($arIBlockParams['CATEGORIES_LIST'])){
			foreach($arIBlockParams['CATEGORIES_LIST'] as $strCategoryName){
				if(Helper::strlen($strCategoryName)){
					$arResult[$strCategoryName] = $strCategoryName;
				}
			}
		}
		if($bJustIds){
			$arResult = array_keys($arResult);
		}
		return $arResult;
	}
	
	/**
	 *	Update attributes for single category
	 */
	protected function updateCategoryAttrubutes($strCategoryName, $strSessionId){
		$arResult = [];
		$strCategoryNameUrl = $strCategoryName;
		if(!Helper::isUtf()){
			$strCategoryNameUrl = Helper::convertEncoding($strCategoryNameUrl, 'CP1251', 'UTF-8');
		}
		$obResponse = $this->API->execute('/content/v1/object/characteristics/{objectName}',
			['{objectName}' => $strCategoryNameUrl]);
		if($obResponse->getStatus() == 200){
			$arQueryResult = $obResponse->getJsonResult();
			if(is_array($arQueryResult) && $arQueryResult['data']){
				$arSources = [
					static::N => [
						'data' => $arQueryResult['data'],
					],
				];
				$arDictionaryAttributes = [
					'colors',
					'kinds',
					'countries',
					'collections',
					'seasons',
					'contents',
					'consists',
					'brands',
					'tnved',
				];
				foreach($arDictionaryAttributes as $key => $value){
					$arDictionaryAttributes[$key] = static::getMessage('DICTIONARY_'.$value);
				}
				foreach($arSources as $strSourceType => $arSource){
					if($arSource['data']){
						foreach($arSource['data'] as $arItem){
							$arFields = [
								'CATEGORY_NAME' => $strCategoryName,
								'HASH' => $this->getAttributeHash($strCategoryName, $arItem['name']),
								'NAME' => $arItem['name'],
								'TYPE' => $strSourceType,
								'SORT' => is_numeric($arItem['sort']) && $arItem['sort'] > 0 ? $arItem['sort'] : 100,
								'USE_ONLY_DICTIONARY_VALUES' => in_array($arItem['name'], $arDictionaryAttributes) ? 'Y' : 'N',
								'MAX_COUNT' => $arItem['maxCount'],
								'IS_REQUIRED' => $arItem['required'] == 1 ? 'Y' : 'N',
								'IS_NUMBER' => $arItem['charcType'] == 4 ? 'Y' : 'N',
								'POPULAR' => $arItem['popular'] == 1 ? 'Y' : 'N',
								'UNIT' => $arItem['unitName'],
								'CHARACTER_TYPE' => $arItem['charcType'],
								'SESSION_ID' => $strSessionId,
								'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
							];
							$arFilter = [
								'HASH' => $arFields['HASH'],
							];
							$arSelect = [
								'ID',
							];
							$resItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
							if($arDbItem = $resItem->fetch()){
								Attribute::update($arDbItem['ID'], $arFields);
							}
							else{
								Attribute::add($arFields);
							}
							$arResult[] = $arFields;
						}
					}
				}
				Attribute::deleteByFilter([
					'=CATEGORY_NAME' => $strCategoryName,
					'!SESSION_ID' => $strSessionId,
				]);
			}
			else{
				$this->addToLog(static::getMessage('LOG_UPDATE_ATTRIBUTES_ERROR', [
					'#CATEGORY#' => $strCategoryName,
					'#ERROR#' => print_r($arQueryResult, true),
				]));
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get hash for attribute
	 */
	protected function getAttributeHash($strCategoryName, $strAttributeName){
		return Helper::substr(md5(implode('|', [$strCategoryName, $strAttributeName])), 0, 16);
	}
	
	/**
	 *	Check attribute required (used in processElement)
	 */
	protected function isAttributeRequired($strCategoryName, $strAttributeName){
		if(!is_array($this->arCacheRequiredAttributes[$strCategoryName])){
			$this->arCacheRequiredAttributes[$strCategoryName] = [];
			$resQuery = Attribute::getList([
				'filter' => ['CATEGORY_NAME' => $strCategoryName, 'IS_REQUIRED' => 'Y'],
				'select' => ['NAME'],
			]);
			while($arItem = $resQuery->fetch()){
				$this->arCacheRequiredAttributes[$strCategoryName][$arItem['NAME']] = true;
			}
		}
		return isset($this->arCacheRequiredAttributes[$strCategoryName][$strAttributeName]);
	}
	
	/**
	 *	Parse attribute id: attribute_3aa264514530447a
	 */
	protected function parseAttribute($strAttributeId){
		static $arResultAll = [];
		$arResult = &$arResultAll[$strAttributeId];
		if(is_array($arResult)){
			return $arResult;
		}
		$strPattern = static::ATTRIBUTE_ID;
		$strPattern = str_replace('%s', '([A-z0-9]+)', $strPattern);
		$strPattern = sprintf('#^%s$#', $strPattern);
		if(preg_match($strPattern, $strAttributeId, $arMatch)){
			$strAttributeHash = $arMatch[1];
			if(!array_key_exists($strAttributeHash, $this->arParsedAttributes)){
				$arQuery = [
					'filter' => ['HASH' => $strAttributeHash],
				];
				if($arAttribute = Attribute::getList($arQuery)->fetch()){
					unset($arAttribute['TIMESTAMP_X'], $arAttribute['SESSION_ID']);
					$arAttribute['ATTRIBUTE_ID'] = $strAttributeId;
					$this->arParsedAttributes[$strAttributeHash] = $arAttribute;
				}
			}
			if(array_key_exists($strAttributeHash, $this->arParsedAttributes)){
				$arResult = $this->arParsedAttributes[$strAttributeHash];
				return $arResult;
			}
		}
		return false;
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		# Correct fields
		if(!is_array($arItem['skus'])){
			$arItem['skus'] = Helper::strlen($arItem['skus']) ? [$arItem['skus']] : [];
		}
		if(Helper::strlen($arItem['price'])){
			$arItem['price'] = intVal($arItem['price']);
		}
		# Prepare
		$intIBlockId = intVal($arElement['IBLOCK_ID']);
		$bOffer = Helper::isOffersIBlock($intIBlockId);
		$bProduct = !$bOffer;
		$strOffersMode = $this->getOffersMode($intIBlockId);
		$arOffers = Helper::arrayExtract($arItem, '_OFFER_PREPROCESS');
		# Detect mode
		$bModeOnly = $strOffersMode == 'only';
		$bModeAll = $strOffersMode == 'all';
		$bModeProduct = $strOffersMode == 'none';
		$bModeOffers = $strOffersMode == 'offers';
		if(!$bModeOnly && !$bModeAll && !$bModeProduct && !$bModeOffers){
			$bModeProduct = true;
		}
		# Process offer
		if($bOffer){
			if($bModeOnly){
				// If product has offers, use only offers; else use only product
				$this->moveAttributes($arItem);
				$this->moveSizes($arItem);
			}
			elseif($bModeAll){
				// Product is NM, each offer is element of 'sizes'
				$this->moveSizes($arItem);
				$arItem = is_array($arItem['sizes']) && !empty($arItem['sizes']) ? reset($arItem['sizes']) : [];
			}
			elseif($bModeProduct){
				// Just products, no offers
				// Nothing! This will never happen
			}
			elseif($bModeOffers){
				// Each offer is separate NM, no parent product
				$this->moveAttributes($arItem);
				$this->moveSizes($arItem);
			}
		}
		# Process product
		if($bProduct){
			if($bModeOnly){
				// If product has offers, use only offers; else use only product
				if(empty($arOffers)){
					$this->moveAttributes($arItem);
					$this->moveSizes($arItem);
					$arItem = [$arItem];
				}
				else{
					$arItemResult = [];
					foreach($arOffers as $arOffer){
						if($arOfferJson = $this->tryJsonDecode($arOffer['DATA'])){
							$arItemResult[] = $arOfferJson;
						}
					}
					$arItem = $arItemResult;
				}
			}
			elseif($bModeAll){
				// Product is NM, each offer is element of 'sizes'
				$this->moveAttributes($arItem);
				$this->moveSizes($arItem);
				foreach($arOffers as $arOffer){
					if($arSize = $this->tryJsonDecode($arOffer['DATA'])){
						$arItem['sizes'][] = $arSize;
					}
				}
				$arItem = [$arItem];
			}
			elseif($bModeProduct){
				// Just products, no offers
				$this->moveAttributes($arItem);
				$this->moveSizes($arItem);
				$arItem = [$arItem];
			}
			elseif($bModeOffers){
				// Each offer is separate NM, no parent product
				$arItemResult = [];
				foreach($arOffers as $arOffer){
					if($arOfferJson = $this->tryJsonDecode($arOffer['DATA'])){
						$arItemResult[] = $arOfferJson;
					}
				}
				$arItem = $arItemResult;
			}
			# Get current data from WB
			if($this->getPreviewMode()){
				$this->getPreviewDataFromWb($arItem, $arDataMore);
			}
			if($strOffersMode == 'offers' && $this->arParams['GROUP_BY_COLORS'] == 'Y'){
				$this->groupOffersByColorsAndSizes($arItem);
			}
		}
		# Move some additional data to MORE_DATA (stocks, prices, vendorCode, photos)
		foreach($arItem as $key => &$arNm){
			$this->moveInfoToMoreData($arNm, $arDataMore);
		}
		unset($arNm);
		#
		if($this->getPreviewMode() && $this->isStockAndPrice()){
			static $bShown = false;
			if(!$bShown){
				Helper::showNote(static::getMessage('NOTICE_PREVIEW_STOCK_AND_PRICE'), true);
				print '<br/>';
				$bShown = true;
			}
		}
		#
		if($this->arParams['SKIP_CREATE_UNAVAILABLE'] == 'Y' && !$this->isStockAndPrice()){
			if(isset($arItem[0]['vendorCode'])){
				$strVendorCode = $arItem[0]['vendorCode'];
				if(!isset($arDataMore['REMOTE_CARDS'])){
					$arDataMore['REMOTE_CARDS'] = $this->getRemoteCardsByVendorCodes([$strVendorCode]);
				}
				$bExists = is_array($arDataMore['REMOTE_CARDS'][$strVendorCode]);
				if(!$bExists){
					$intStockSumm = 0;
					if(is_array($arDataMore['STOCKS'])){
						$intStockSumm = array_sum(array_column($arDataMore['STOCKS'], 'stock'));
					}
					if($intStockSumm <= 0){
						if($this->getPreviewMode()){
							Helper::showNote(static::getMessage('SKIP_CREATE_UNAVAILABLE_LOG', ['#ID#' => $arElement['ID']]), true);
							print '<br/>';
						}
						else{
							$this->addToLog(static::getMessage('LOG_PRODUCT_SKIPPED_EMPTY_STOCK_FOR_NEW_CARD', [
								'#ELEMENT_ID#' => $arElement['ID'],
								'#VENDOR_CODE#' => $strVendorCode,
							]), true);
						}
						return [
							'TYPE' => \Acrit\Core\Export\ExportDataTable::TYPE_DUMMY,
						];
					}
				}
			}
		}
	}

	/**
	 * Group offers by colors and sizes (just for mode 'offers')
	 */
	protected function groupOffersByColorsAndSizes(&$arOffers){
		if(is_array($arOffers)){
			usort($arOffers, function($a, $b){
				return strcmp($a['vendorCode'], $b['vendorCode']);
			});
			$strColorMess = static::getMessage('DICTIONARY_colors');
			#
			$arExistColors = [];
			foreach($arOffers as $keyOffer => $arOffer){
				if(is_array($arOffer['characteristics'])){
					foreach($arOffer['characteristics'] as $keyChar => $arCharacteristic){
						if(isset($arCharacteristic[$strColorMess])){
							if(isset($arCharacteristic[$strColorMess])){
								$strColor = $arCharacteristic[$strColorMess];
								if(is_array($strColor)){
									$strColor = reset($strColor);
								}
								if(isset($arExistColors[$strColor])){
									$arOffers[$arExistColors[$strColor]]['sizes'][] = reset($arOffer['sizes']);
									unset($arOffers[$keyOffer]);
									break;
								}
								else{
									$arExistColors[$strColor] = $keyOffer;
								}
							}
							break;
						}
					}
				}
			}
			$arOffers = array_values($arOffers);
		}
	}

	/**
	 * Collect data from WB by vendorCode
	 */
	protected function getPreviewDataFromWb(array $arItem, &$arDataMore){
		$arDataMore['VENDOR_CODES'] = array_column($arItem, 'vendorCode');
		$arDataMore['REMOTE_CARDS'] = $this->getRemoteCardsByVendorCodes($arDataMore['VENDOR_CODES']);
		$arDataMore['REMOTE_PRICES'] = !empty($arDataMore['REMOTE_CARDS']) 
			? $this->getRemotePricesByCards($arDataMore['REMOTE_CARDS']) : [];
	}

	/**
	 * Collect all barcodes
	 */
	protected function collectNmBarcodes(array $arNm){
		$arResult = [];
		$arSizes = is_array($arNm['sizes']) ? $arNm['sizes'] : [];
		foreach($arSizes as $arSize){
			if(is_array($arSize['skus'])){
				$arResult = array_merge($arResult, $arSize['skus']);
			}
		}
		$arResult = array_unique($arResult);
		return $arResult;
	}

	/**
	 * Move attributes from common place to ['characteristics']
	 */
	protected function deleteAttributes(&$arItem){
		if(is_array($arItem)){
			foreach($arItem as $key => $value){
				if($arAttribute = $this->parseAttribute($key)){
					unset($arItem[$key]);
				}
			}
		}
	}

	/**
	 * Move attributes from common place to ['characteristics']
	 */
	protected function moveAttributes(&$arItem){
		if(is_array($arItem)){
			$strObject = $arItem['object'];
			foreach($arItem as $key => $value){
				if($arAttribute = $this->parseAttribute($key)){
					if($strObject == $arAttribute['CATEGORY_NAME'] && !Helper::isEmpty($value)){
						if(is_array($value)){
							if($arAttribute['MAX_COUNT'] == 1){
								$value = reset($value);
							}
							elseif(count($value) > $arAttribute['MAX_COUNT']){
								$value = array_slice($value, 0, $arAttribute['MAX_COUNT']);
							}
						}
						if($arAttribute['IS_NUMBER'] == 'Y'){
							if(is_array($value)){
								foreach($value as $key => $item){
									$value[$key] = floatVal($item);
								}
							}
							else{
								$value = floatVal($value);
							}
						}
						if(!is_array($arItem['characteristics'])){
							$arItem['characteristics'] = [];
						}
						$arItem['characteristics'][] = [$arAttribute['NAME'] => $value];
					}
					unset($arItem[$key]);
				}
				elseif($key == 'object'){
					if(!is_array($arItem['characteristics'])){
						$arItem['characteristics'] = [];
					}
					$arItem['characteristics'][] = [static::getMessage('F_NAME_object') => $value];
					unset($arItem[$key]);
				}
			}
		}
	}

	/**
	 * Move sizes from common place to ['sizes']
	 */
	protected function moveSizes(&$arItem){
		if(is_array($arItem)){
			$arMoveKeys = ['techSize', 'wbSize', 'price', 'skus'];
			foreach($this->getStores(false) as $intStoreId => $strStoreName){ // Stocks are moved to each size, and them migrate to DATA_MORE
				$arMoveKeys[] = 'stock_'.$intStoreId;
			}
			$arMoveFields = [];
			foreach($arItem as $key => $value){
				if(in_array($key, $arMoveKeys)){
					if(is_array($value) && !empty($value) || !is_array($value) && Helper::strlen($value)){
						$arMoveFields[$key] = $value;
					}
					unset($arItem[$key]);
				}
			}
			if(!empty($arMoveFields)){
				$bTechSize = !!Helper::strlen($arMoveFields['techSize']);
				$bWbSize = !!Helper::strlen($arMoveFields['wbSize']);
				$bSkus = is_array($arMoveFields['skus']) && !empty($arMoveFields['skus']);
				$bPrice = !!Helper::strlen($arMoveFields['price']);
				if($bTechSize || $bWbSize || $bSkus || $bPrice){
					$arItem['sizes'] = is_array($arItem['sizes']) ? $arItem['sizes'] : [];
					$arItem['sizes'][] = $arMoveFields;
				}
			}
		}
	}

	/**
	 * Move some additional data to MORE_DATA (stocks, prices, vendorCode, photos)
	 * This method must be executed just for main product
	 */
	protected function moveInfoToMoreData(&$arItem, &$arDataMore){
		if(is_array($arItem) && is_array($arItem['sizes'])){
			# Prices
			if(!is_array($arDataMore['PRICES'])){
				$arDataMore['PRICES'] = [];
			}
			$arDataMore['PRICES'][] = [
				'vendorCode' => $arItem['vendorCode'],
				'price' => array_column(is_array($arItem['sizes']) ? $arItem['sizes'] : [], 'price'),
			];
			# Stocks
			if(!is_array($arDataMore['STOCKS'])){
				$arDataMore['STOCKS'] = [];
			}
			foreach($arItem['sizes'] as $key1 => $arSize){
				foreach($arSize as $key2 => $value){
					if($intStockId = $this->isStock($key2)){
						$arStock = [
							'barcode' => $arSize['skus'],
							'stock' => intVal($value),
							'warehouseId' => $intStockId,
						];
						if(is_array($arStock['barcode'])){
							$arStock['barcode'] = reset($arStock['barcode']);
						}
						$arDataMore['STOCKS'][] = $arStock;
						unset($arItem['sizes'][$key1][$key2]);
					}
				}
			}
			# Photos
			if(!is_array($arDataMore['PHOTOS'])){
				$arDataMore['PHOTOS'] = [];
			}
			if(isset($arItem['photos'])){
				if(is_array($arItem['photos'])){
					$arPhotos = [];
					foreach($arItem['photos'] as $strPhotoUrl){
						$arPhotos[] = $strPhotoUrl;
					}
					if(!empty($arPhotos)){
						$arDataMore['PHOTOS'][] = [
							'vendorCode' => $arItem['vendorCode'],
							'data' => $arPhotos,
						];
					}
				}
				unset($arItem['photos']);
			}
		}
	}

	/**
	 * Check field is stock
	 */
	protected function isStock($strField){
		if(preg_match('#^stock_(\d+)$#', $strField, $arMatch)){
			return intVal($arMatch[1]);
		}
		return false;
	}

	/**
	 * Move nomenclature prices to more data
	 */
	protected function movePricesToMoreData(&$arResult, &$arDataMore){
		foreach($arResult[static::KEY_N] as $intNomenclatureIndex => $arNomenclature){
			if(isset($arNomenclature['price'])){
				if(!Helper::isEmpty($arNomenclature['price'])){
					$arDataMore['PRICES'][] = [
						'vendorCode' => $arNomenclature['vendorCode'],
						'price' => $arNomenclature['price'],
					];
				}
				unset($arResult[static::KEY_N][$intNomenclatureIndex]['price']);
			}
		}
	}

	/**
	 * @param mixed $strFilename
	 */
	protected function detectImageType($strFilename){
		$type = 'image/jpeg';
		$ext = toLower(pathinfo($strFilename, PATHINFO_EXTENSION));
		switch($ext){
			case 'PNG':
				$type = 'image/png';
				break;
			case 'GIF':
				$type = 'image/gif';
				break;
			case 'BMP':
				$type = 'image/bmp';
				break;
			case 'WEBP':
				$type = 'image/webp';
				break;
		}
		return $type;
	}
	
	/**
	 *	Cancel save json to file
	 */
	protected function onUpJsonExportItem(&$arItem, &$strJson, &$arSession, &$bWrite){
		$bWrite = false;
	}
	
	/**
	 *	Add custom substep for export step
	 */
	protected function onUpGetExportSteps(&$arExportSteps, &$arSession){
		$arUnsetSteps = ['EXPORT_HEADER', 'EXPORT_FOOTER', 'REPLACE_FILE', 'REPLACE_TMP_FILES'];
		foreach($arUnsetSteps as $strStep){
			unset($arExportSteps[$strStep]);
		}
	}
	
	/**
	 *	Add custom step
	 */
	protected function onUpGetSteps(&$arSteps){
		$arSteps['WB_EXPORT_PRICES'] = [
			'NAME' => static::getMessage('STEP_EXPORT_PRICES'),
			'SORT' => 5010,
			'FUNC' => [$this, 'stepExportPrices'],
		];
		if($this->arParams['EXPORT_STOCKS'] == 'Y'){
			$arSteps['WB_EXPORT_STOCKS'] = [
				'NAME' => static::getMessage('STEP_EXPORT_STOCKS'),
				'SORT' => 5020,
				'FUNC' => [$this, 'stepExportStocks'],
			];
		}
	}
	
	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		if($this->isStockAndPrice()){
			return Exporter::RESULT_SUCCESS;
		}
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
		$arExportItems = $this->getExportDataItems(null, null);
		if(is_array($arExportItems)){
			foreach($arExportItems as $arItem){
				$result = $this->exportItem($arItem, $arSession);
				if($result === Exporter::RESULT_ERROR){
					return Exporter::RESULT_ERROR;
				}
				elseif($result === Exporter::RESULT_CONTINUE){
					if(!Exporter::getInstance($this->strModuleId)->haveTime()){
						return Exporter::RESULT_CONTINUE;
					}
				}
			}
			if(count($arExportItems) == $this->intExportPerStep){
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * Export (create or update) single product
	 * @param $arItem
	 */
	protected function exportItem($arItem, &$arSession){
		try{
			$arJsonItems = Json::decode($arItem['DATA']);
		}
		catch(\Exception $obException){
			$strMessage = 'Export item error: wrong JSON.';
			print $strMessage;
			$this->addToLog($strMessage);
			return Exporter::RESULT_ERROR;
		}
		if(is_array($arJsonItems) && !empty($arJsonItems)){
			$intElementId = intVal($arItem['ELEMENT_ID']);
			$arDataMore = unserialize($arItem['DATA_MORE']);
			# Collect all used vendorCodes from json
			if(!empty($arVendorCodes = $this->collectExportItemVendorCodes($arJsonItems, $intElementId))){
				# Get remote cards for all found nomenclatures
				$arRemoteCards = $this->getRemoteCardsByVendorCodes($arVendorCodes);
				# Get arrays of items for create, and array of items for update
				$arQueue = $this->separateNmList($arJsonItems, $arRemoteCards);
				# Firstly, create items from queue
				if(!empty($arCreate = $arQueue['CREATE'])){
					$strAddVendorCode = $this->getAddVendorCode($arRemoteCards);
					$intMaxCountPerRequest = 5; // This is system limit from Wildberries
					$arJsonChunks = array_chunk($arCreate, $intMaxCountPerRequest);
					foreach($arJsonChunks as $arJsonChunk){
						# Do export! if $strAddVendorCode is NULL, then /content/v1/cards/upload, else /content/v1/cards/upload/add
						$this->exportChunk($arJsonChunk, $intElementId, $strAddVendorCode);
						# Refresh local variables (remote cards can be changed):
						$arRemoteCards = $this->getRemoteCardsByVendorCodes($arVendorCodes);
						if(is_null($strAddVendorCode)){
							$strAddVendorCode = $this->getAddVendorCode($arRemoteCards);
						}
					}
				}
				# Secondly, update items from queue
				if(!empty($arUpdate = $arQueue['UPDATE'])){
					if($this->canUpdateCards()){
						# Transform associated array [vendorCode => item] to a list [item1, item2]
						$arJsonUpdate = array_values($arUpdate);
						# Do export!
						$this->updateItems($arJsonUpdate, $intElementId);
					}
					elseif($arDataMore['PHOTOS'] && is_array($arDataMore['PHOTOS']) && !empty($arDataMore['PHOTOS'])){
						$arUpdateVendorCodes = array_keys($arUpdate);
						foreach($arDataMore['PHOTOS'] as $key => $arPhoto){
							if(in_array($arPhoto['vendorCode'], $arUpdateVendorCodes)){
								unset($arDataMore['PHOTOS'][$key]);
								$strMessage = static::getMessage('SKIP_UPDATE_CARDS_LOG', ['#VENDOR_CODE#' => $arPhoto['vendorCode']]);
								$this->addToLog($strMessage, true);
							}
						}
					}
				}
				# Thirdly, export photos (ToDo: skip images for just created [at step 1] cards)
				if($arDataMore['PHOTOS'] && is_array($arDataMore['PHOTOS']) && !empty($arDataMore['PHOTOS'])){
					foreach($arDataMore['PHOTOS'] as $arPhoto){
						if(Helper::strlen($arPhoto['vendorCode'])){
							if(is_array($arPhoto['data']) && !empty($arPhoto['data'])){
								$obResponse = $this->API->execute('/content/v1/media/save', $arPhoto);
								if($obResponse->getStatus() === 200){
									$this->addToLog(static::getMessage('LOG_PHOTOS_EXPORTED', [
										'#ELEMENT_ID#' => $intElementId,
										'#COUNT#' => count($arPhoto['data']),
										'#PHOTOS#' => Json::prettyPrint($arPhoto),
									]), true);
								}
								else{
									$strMessage = static::getMessage('LOG_PHOTOS_ERROR', [
										'#ELEMENT_ID#' => $intElementId,
										'#VENDOR_CODE#' => $arPhoto['vendorCode'],
										'#TEXT#' => $obResponse->getResponse(),
										'#RESPONSE_CODE#' => $obResponse->getStatus(),
										'#PHOTOS#' => Json::prettyPrint($arPhoto),
									]);
									$this->addToLog($strMessage);
								}
							}
						}
					}
				}
			}
			else{
				$this->addToLog(static::getMessage('ITEM_HAS_NO_VENDOR_CODES', ['#ELEMENT_ID#' => $arItem['ELEMENT_ID']]));
			}
		}
		$this->setDataItemExported($arItem['ID']);
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * Get exists vendorCode for /content/v1/cards/upload/add
	 */
	protected function getAddVendorCode(array $arRemoteCards){
		$strResult = null;
		if(!empty($arRemoteCards)){
			reset($arRemoteCards);
			$strResult = key($arRemoteCards);
		}
		return $strResult;
	}

	/**
	 * Filter items for create/update
	 */
	protected function separateNmList(array $arJsonItems, array $arRemoteCards){
		$arResult = [
			'CREATE' => [],
			'UPDATE' => [],
		];
		foreach($arJsonItems as $key => $arJsonItem){
			if(is_array($arRemoteCard = $arRemoteCards[$arJsonItem['vendorCode']])){
				if($this->arParams['SKIP_APPEND_SIZES'] != 'Y'){
					$this->copyChrtIdFromRemoteCard($arJsonItem, $arRemoteCard['CARD']);
				}
				$arResult['UPDATE'][$arJsonItem['vendorCode']] = [
					'DATA' => $arJsonItem,
					'IMT_ID' => $arRemoteCard['IMT_ID'],
					'NM_ID' => $arRemoteCard['NM_ID'],
				];
			}
			else{
				$arResult['CREATE'][$arJsonItem['vendorCode']] = [
					'DATA' => $arJsonItem,
				];
			}
		}
		return $arResult;
	}

	/**
	 * Copy chrtID (for each size in sizes) from remote card to local card (for update)
	 */
	protected function copyChrtIdFromRemoteCard(array &$arLocalCard, array $arRemoteCard){
		# Copy chrtID
		if(is_array($arLocalCard['sizes'])){
			foreach($arLocalCard['sizes'] as $key1 => $arLocalSize){
				foreach($arRemoteCard['sizes'] as $key2 => $arRemoteSize){
					if(is_array($arLocalSize['skus']) && is_array($arRemoteSize['skus'])){
						if(count(array_intersect($arLocalSize['skus'], $arRemoteSize['skus']))){
							$arLocalCard['sizes'][$key1]['chrtID'] = $arRemoteSize['chrtID'];
							break;
						}
					}
				}
			}
		}
		# Copy absent sizes (prevent errors): size cannot be delete!
		$arLocalChrtId = array_column($arLocalCard['sizes'], 'chrtID');
		$arRemoteChrtId = array_column($arRemoteCard['sizes'], 'chrtID');
		$arAbsentChrtId = array_diff($arRemoteChrtId, $arLocalChrtId);
		if(!empty($arAbsentChrtId)){
			foreach($arRemoteCard['sizes'] as $arRemoteSize){
				if(in_array($arRemoteSize['chrtID'], $arAbsentChrtId)){
					$arLocalCard['sizes'][] = $arRemoteSize;
				}
			}
		}
	}

	/**
	 * Export products chunk-by-chunk
	 * $arJsonChunk is an array of nomenclatures - [...]
	 * $strAddVendorCode is a string with vendor code:
	 * 		if empty, then NM will be created in a new card, if not empty - in the same card
	 */
	protected function exportChunk(array $arJsonChunk, $intElementId, $strAddVendorCode=null){
		$bSuccess = false;
		foreach($arJsonChunk as &$arJsonChunkItem){
			$this->correctJsonChunk($arJsonChunkItem);
		}
		unset($arJsonChunkItem);
		#
		$strMethod = null;
		if(is_string($strAddVendorCode) && Helper::strlen($strAddVendorCode)){
			$strMethod = '/content/v1/cards/upload/add';
			$arJson = [
				'vendorCode' => $strAddVendorCode,
				'cards' => $arJsonChunk,
			];
		}
		else{
			$strMethod = '/content/v1/cards/upload';
			$arJson = [
				$arJsonChunk,
			];
		}
		$obResponse = $this->API->execute($strMethod, $arJson);
		$bSuccess = $obResponse->getStatus() == 200;
		$this->writeHistory($intElementId, $strMethod, $arJson, $arJsonChunk, $obResponse);
		return $bSuccess;
	}

	/**
	 * Update several items (in array)
	 * $arJsonChunk is array of nomenclatures - [...]
	 */
	protected function updateItems(array $arJsonItems, int $intElementId){
		foreach($arJsonItems as &$arJsonItem){
			$this->correctJsonChunk($arJsonItem, true);
		}
		unset($arJsonItem);
		#
		$strMethod = '/content/v1/cards/update';
		$obResponse = $this->API->execute($strMethod, $arJsonItems);
		$bSuccess = $obResponse->getStatus() == 200;
		$this->writeHistory($intElementId, $strMethod, $arJsonItems, $arJsonItems, $obResponse);
		return $bSuccess;
	}

	/**
	 * Get all vendor codes from export item
	 */
	protected function collectExportItemVendorCodes(array $arItemJson, $intElementId){
		$arResult = [];
		foreach($arItemJson as $key => $arNm){
			if(Helper::strlen($arNm['vendorCode'])){
				$arResult[] = $arNm['vendorCode'];
			}
			else{
				$this->addToLog(static::getMessage('ITEM_HAS_NO_VENDOR_CODES', ['#ELEMENT_ID#' => $intElementId]), true);
			}
		}
		return $arResult;
	}

	/**
	 * Convert ['DATA' => [ARRAY]] to [ARRAY]
	 */
	protected function correctJsonChunk(array &$arJsonChunkItem, $bCopyNmId=false){
		if(is_array($arJsonChunkItem['DATA'])){
			if($bCopyNmId && isset($arJsonChunkItem['NM_ID'])){
				$arJsonChunkItem['DATA'] = array_merge(['nmID' => $arJsonChunkItem['NM_ID']], $arJsonChunkItem['DATA']);
			}
			$arJsonChunkItem = $arJsonChunkItem['DATA'];
		}
	}

	/**
	 * Get cards from WB
	 * Return format:
	 * 	$arResult = [
	 * 		vendorCode_1: [IMT_ID: imtId_1, NM_ID: nmId_1, CARD: [...]],
	 * 		vendorCode_2: [IMT_ID: imtId_2, NM_ID: nmId_2, CARD: [...]],
	 * 		vendorCode_3: [IMT_ID: imtId_3, NM_ID: nmId_3, CARD: [...]],
	 * 	]
	 */
	protected function getRemoteCardsByVendorCodes(array $arVendorCodes){
		$arResult = [];
		$obFilterResult = $this->API->execute('/content/v1/cards/filter', ['vendorCodes' => $arVendorCodes]);
		if($obFilterResult->getStatus() == 200){
			if($arJsonResult = $obFilterResult->getJsonResult()){
				if(is_array($arJsonResult['data'])){
					foreach($arJsonResult['data'] as $arItem){
						$arResult[$arItem['vendorCode']] = [
							'IMT_ID' => $arItem['imtID'],
							'NM_ID' => $arItem['nmID'],
							'CARD' => $arItem,
						];
					}
				}
			}
		}
		# Sort by IMT_ID
		uasort($arResult, function($a, $b){
			return $a['IMT_ID'] - $b['IMT_ID'];
		});
		# Return
		return $arResult;
	}

	/**
	 * Get prices from WB
	 */
	protected function getRemotePricesByCards(array $arRemoteCards=[]){
		$arResult = [];
		$obFilterResult = $this->API->execute('/public/api/v1/info', []);
		if($obFilterResult->getStatus() == 200){
			if(is_array($arPrices = $obFilterResult->getJsonResult())){
				foreach($arPrices as $arPrice){
					$arResult[$arPrice['nmId']] = $arPrice;
				}
			}
		}
		if(!empty($arRemoteCards)){
			$arNmId = array_column($arRemoteCards, 'NM_ID');
			$arResult = array_intersect_key($arResult, array_flip($arNmId));
		}
		return $arResult;
	}
	
	/**
	 * Write task & history
	 */
	protected function writeHistory($intElementId, string $strMethod, array $arJson, array $arNomenclatures, Response $obResponse){
		# Add task
		if($this->isHistoryTaskSave()){
			$arTask = [
				'MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'METHOD' => $strMethod,
				'JSON' => Json::encode($arJson),
				'PRODUCTS_COUNT' => count($arNomenclatures),
				'RESPONSE' => $obResponse->getResponse(),
				'RESPONSE_CODE' => $obResponse->getStatus(),
				'SESSION_ID' => session_id(),
				'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
			];
			if($intTaskId = Task::add($arTask)->getId()){
				# Add history
				if($this->isHistoryProductSave()){
					foreach($arNomenclatures as $arNomenclature){
						$obHistoryResult = History::add([
							'MODULE_ID' => $this->strModuleId,
							'PROFILE_ID' => $this->intProfileId,
							'TASK_ID' => $intTaskId,
							'NM_ID' => $arNomenclature['nmID'],
							'VENDOR_CODE' => $arNomenclature['vendorCode'],
							'BARCODE' => implode(',', $this->collectNmBarcodes($arNomenclature)),
							'ELEMENT_ID' => $intElementId,
							'JSON' => Json::encode($arNomenclature),
							'SESSION_ID' => session_id(),
							'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
						]);
					}
				}
			}
		}
	}

	/**
	*	Show notices
	*/
	public function showMessages(){
		print Helper::showNote(static::getMessage('TEST_VERSION'), true);
		print Helper::showNote(static::getMessage('NOTICE_SUPPORT'), true);
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
	 *	Display status for one task
	 */
	protected function displayTaskStatus($arTask){
		ob_start();
		$strFile = __DIR__.'/include/tasks/status.php';
		Helper::loadMessages($strFile);
		require $strFile;
		return ob_get_clean();
	}
	
	/**
	 *	Search allowed values
	 */
	protected function getAllowedValuesContent($arParams, &$arJsonResult){
		ob_start();
		$strField = $arParams['GET']['field'];
		if($arAttribute = $this->parseAttribute($strField)){
			require __DIR__.'/include/popup/allowed_values.php';
		}
		elseif($strField == 'countryProduction'){ 
			$arAttribute = [
				'DICTIONARY' => '/countries',
			];
			require __DIR__.'/include/popup/allowed_values.php';
		}
		else{
			print Helper::showError(static::getMessage('ERROR_PARSE_ATTRIBUTE', ['#ATTRIBUTE#' => $strField]));
		}
		$arJsonResult['HTML'] = ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesFilteredContent($arGet){
		ob_start();
		$strField = $arGet['field'];
		if($arAttribute = $this->parseAttribute($strField)){
			require __DIR__.'/include/popup/allowed_values_filtered.php';
		}
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getTaskJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['task_id'],
			],
			'select' => [
				'JSON',
			],
		];
		$arParams = [
			'TASK_ID' => $arGet['task_id'],
			'ALLOW_COPY' => true,
		];
		return $this->displayPopupJson(Task::getList($arQuery)->fetch(), $arParams);
	}
	
	/**
	 *	Ajax: preview history item
	 */
	protected function getHistoryItemJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['history_item_id'],
			],
			'select' => [
				'JSON',
			],
		];
		$arParams = [
			'TASK_ID' => $arGet['task_id'],
			'ALLOW_COPY' => true,
		];
		return $this->displayPopupJson(History::getList($arQuery)->fetch(), $arParams);
	}
	
	/**
	 *	Get html for popup for preview history item JSON
	 */
	protected function displayPopupJson($arData, $arParams=[]){
		if(is_array($arData) && strlen('JSON')){
			$strFile = __DIR__.'/include/popup/json.php';
			Helper::loadMessages($strFile);
			ob_start();
			$strJson = &$arData['JSON'];
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}
	
	/**
	 *	Get html for popup for preview history item JSON
	 */
	protected function displayPopupTaskStocksJson($arTask, $arParams=[]){
		if(is_array($arTask)){
			$strFile = __DIR__.'/include/popup/task_stocks.php';
			Helper::loadMessages($strFile);
			ob_start();
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}
	
	/**
	 *	Display popup via AJAX: category add
	 */
	protected function displayPopupCategoryAdd($arParams, &$arJsonResult){
		$strFile = __DIR__.'/include/popup/category_add.php';
		Helper::loadMessages($strFile);
		ob_start();
		require $strFile;
		$arJsonResult['HTML'] = ob_get_clean();
	}
	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		return $this->getExtFileOpenLink('https://suppliers-portal.wildberries.ru/goods/products-card/', 
			Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}
	
	/**
	 * Custom step: export prices
	 */
	public function stepExportPrices($intProfileID, $arData){
		# Prepare
		$arPrices = [];
		$this->intExportPerStep = $this->arParams['EXPORT_PRICES_BY_1'] == 'Y' ? 1 : 100;
		# Set all profiles product as non-exported: 
		Helper::call($this->strModuleId, 'ExportData', 'setAllDataItemsNotExported', [$intProfileID]);
		# Collect data
		$this->addToLog(static::getMessage('LOG_PRICES_PREPARE'), true);
		# Get all remote prices
		$arRemotePrices = $this->getRemotePricesByCards([]);
		$intPriceIncreaseLimitPercent = 20;
		# Export
		while(true){
			$arExportItems = $this->getExportDataItems(null, ['ID', 'ELEMENT_ID', 'DATA_MORE', '_SKIP_DATA_FIELD']);
			if(!is_array($arExportItems) || empty($arExportItems)){
				$this->addToLog(static::getMessage('LOG_PRICES_NOT_FOUND'), true);
				break;
			}
			# Build array of vendorCode => price
			$arPrices = [];
			foreach($arExportItems as $arItem){
				if($arDataMore = unserialize($arItem['DATA_MORE'])){
					if(is_array($arDataMore['PRICES']) && !empty($arDataMore['PRICES'])){
						foreach($arDataMore['PRICES'] as $arPrice){
							if(!empty($arPrice['price'])){
								$arPrices[$arPrice['vendorCode']] = is_array($arPrice['price']) ? max($arPrice['price']) : $arPrice['price'];
							}
						}
					}
				}
				$this->setDataItemExported($arItem['ID']);
			}
			$this->addToLog(static::getMessage('LOG_PRICES_COUNT', ['#COUNT#' => count($arPrices)]), true);
			# Export prices
			if(!empty($arPrices)){
				$arUpdatePrices = [];
				$arVendorCodes = array_map(function($strVendorCode){
					return strVal($strVendorCode);
				}, array_keys($arPrices));
				$this->addToLog(static::getMessage('LOG_PRICES_VENDOR_CODES', [
					'#VENDOR_CODES#' => Json::prettyPrint($arVendorCodes, true),
				]), true);
				$obResult = $this->API->execute('/content/v1/cards/filter', ['vendorCodes' => $arVendorCodes]);
				if($obResult->getStatus() == 200){
					if(is_array($arCards = $obResult->getJsonResult()['data'])){
						foreach($arCards as $arCard){
							if(isset($arPrices[$arCard['vendorCode']])){
								$arPrice = [
									'nmId' => $arCard['nmID'],
									'price' => $arPrices[$arCard['vendorCode']],
								];
								# Check price limit
								if(is_array($arRemotePrices[$arCard['nmID']])){
									if($intOldPrice = $arRemotePrices[$arCard['nmID']]['price']){
										$intNewPrice = $arPrice['price'];
										$intPercent = ceil((($intNewPrice - $intOldPrice) / $intOldPrice) * 100);
										if($intPercent > $intPriceIncreaseLimitPercent){
											$arPrice['price'] = floor($intOldPrice * (1 + $intPriceIncreaseLimitPercent / 100));
											$strMessage = static::getMessage('LOG_PRICES_PERCENT_LIMIT', [
												'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
												'#PRICE_EXPORTED#' => $arPrice['price'],
												'#PRICE_TARGET#' => $intNewPrice,
												'#PRICE_REMOTE#' => $intOldPrice,
												'#PERCENT#' => $intPercent,
												'#NM_ID#' => $arCard['nmID'],
												'#VENDOR_CODE#' => $arCard['vendorCode'],
											]);
											$this->addToLog($strMessage);
										}
									}
									else{
										$this->addToLog(sprintf('Wrong old price for set price limit: %s', $intOldPrice), true);
									}
								}
								# Save price for export
								$arUpdatePrices[] = $arPrice;
							}
						}
					}
				}
				else{
					$strMessage = static::getMessage('LOG_PRICES_FILTER_ERROR', [
						'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
						'#ERROR#' => $obResult->getResponse(),
						'#PRICES#' => Json::prettyPrint($arUpdatePrices, true),
						'#RESPONSE_CODE#' => $obResult->getStatus(),
						'#HEADERS#' => print_r($obResult->getResponseHeaders(), true),
					]);
					$this->addToLog($strMessage);
				}
				# If found prices for update
				if(!empty($arUpdatePrices)){
					$obResponse = $this->API->execute('/public/api/v1/prices', $arUpdatePrices);
					if($obResponse->getStatus() == 200){
						$this->addToLog(static::getMessage('LOG_PRICES_EXPORTED', [
							'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
							'#COUNT#' => count($arUpdatePrices),
							'#PRICES#' => Json::prettyPrint($arUpdatePrices, true),
						]), true);
					}
					else{
						$arJsonResult = $obResponse->getJsonResult();
						$strMessage = static::getMessage('LOG_PRICES_ERROR', [
							'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
							'#ERROR#' => $obResponse->getResponse(),
							'#PRICES#' => Json::prettyPrint($arUpdatePrices, true),
							'#RESPONSE_CODE#' => $obResponse->getStatus(),
							'#HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
						]);
						$this->addToLog($strMessage);
					}
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 * Custom step: export stocks
	 */
	public function stepExportStocks($intProfileID, $arData){
		# Prepare
		// $arStocks = []; // Moved below
		$this->intExportPerStep = 100;
		# Set all profiles product as non-exported: 
		Helper::call($this->strModuleId, 'ExportData', 'setAllDataItemsNotExported', [$intProfileID]);
		$this->addToLog(static::getMessage('LOG_STOCKS_PREPARE'), true);
		# Collect data
		while(true){
			$arExportItems = $this->getExportDataItems(null, ['ID', 'ELEMENT_ID', 'DATA_MORE', '_SKIP_DATA_FIELD']);
			if(!is_array($arExportItems) || empty($arExportItems)){
				$this->addToLog(static::getMessage('LOG_STOCKS_NOT_FOUND'), true);
				break;
			}
			$this->addToLog(static::getMessage('LOG_STOCKS_COUNT', ['#COUNT#' => count($arExportItems)]), true);
			$arStocks = [];
			foreach($arExportItems as $arItem){
				if($arDataMore = unserialize($arItem['DATA_MORE'])){
					if(is_array($arDataMore['STOCKS']) && !empty($arDataMore['STOCKS'])){
						$arStocks = array_merge($arStocks, $arDataMore['STOCKS']);
					}
				}
				$this->setDataItemExported($arItem['ID']);
			}
			# Export stocks
			if(!empty($arStocks)){
				$arWarehouseStocks = []; # Support for new method '/api/v3/stocks/{warehouse}'
				foreach($arStocks as $arStock){
					$arWarehouseStocks[$arStock['warehouseId']][] = [
						'sku' => strVal($arStock['barcode']),
						'amount' => intVal($arStock['stock']),
					];
				}
				foreach($arWarehouseStocks as $strWarehouseId => $arStocks){
					$this->addToLog(static::getMessage('LOG_STOCKS_BARCODES', [
						'#BARCODES#' => Json::prettyPrint(array_column($arStocks, 'sku'), true),
					]), true);
					$arStocks = ['stocks' => $arStocks];
					$arStocksSend = ['warehouse' => $strWarehouseId, 'data' => $arStocks];
					$obResponse = $this->API->execute('/api/v3/stocks/{warehouse}', $arStocksSend);
					if($obResponse->getStatus() == 204){
						$this->addToLog(static::getMessage('LOG_STOCKS_EXPORTED', [
							'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
							'#COUNT#' => count($arStocks),
							'#STOCKS#' => Json::prettyPrint($arStocks),
						]), true);
					}
					else{
						$arJsonResult = $obResponse->getJsonResult();
						$strMessage = static::getMessage('LOG_STOCKS_ERROR', [
							'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
							'#ERROR#' => (isset($arJsonResult[0]['message']) ? $arJsonResult[0]['message'].'.'.PHP_EOL : '')
								.$obResponse->getResponse(),
							'#STOCKS#' => print_r($arStocks, true),
							'#RESPONSE_CODE#' => $obResponse->getStatus(),
							'#HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
							'#CONTENT#' => print_r($arJsonResult, true),
							'#STOCKS#' => Json::prettyPrint($arStocks, true),
						]);
						$this->addToLog($strMessage);
					}
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

	public function getHistorySaveTypes(){
		return [
			'task_product' => static::getMessage('HISTORY_SAVE_TASK_PRODUCT'),
			'task' => static::getMessage('HISTORY_SAVE_TASK'),
			'nothing' => static::getMessage('HISTORY_SAVE_NOTHING'),
		];
	}

	public function isHistoryTaskSave(){
		return empty($this->arParams['HISTORY_SAVE']) || $this->arParams['HISTORY_SAVE'] != 'nothing';
	}
	
	public function isHistoryProductSave(){
		return empty($this->arParams['HISTORY_SAVE']) || $this->arParams['HISTORY_SAVE'] == 'task_product';
	}

	/**
	 * Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = [];
		$arResult[] = array(
			'DIV' => 'files',
			'TAB' => static::getMessage('TAB_CARDS_NAME'),
			'TITLE' => static::getMessage('TAB_CARDS_DESC'),
			'SORT' => 35,
			'FILE' => __DIR__.'/tabs/cards_browser.php',
		);
		return $arResult;
	}

	/**
	 * Work with cards browser
	 */
	protected function cardsBrowser($arPost){
		ob_start();
		$strType = preg_replace('#\W#', '', $arPost['type']);
		if(in_array($strType, ['cards', 'filter', 'errors', 'attributes', 'prices', 'set_price'])){
			require sprintf(__DIR__.'/include/cards_browser/%s.php', $strType);
		}
		$strHtml = ob_get_clean();
		# Remove html forms
		$strHtml = preg_replace('#<form[^>]*>#i', '', $strHtml);
		$strHtml = preg_replace('#</form>#i', '', $strHtml);
		#
		return $strHtml;
	}

	/**
	 * Display json (request, response) in cards browser
	 */
	protected function cardsBrowserDisplayJson(array $arData, Response $obResponse){
		require sprintf(__DIR__.'/include/cards_browser/.json.php', $strType);
	}

}

?>