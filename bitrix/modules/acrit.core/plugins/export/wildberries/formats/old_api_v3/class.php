<?
/**
 * Acrit Core: wildberries
 * @documentation https://suppliers.wildberries.ru/remote-wh-site/api-content.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\ExternalIdTable as ExternalId,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\Api,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\ImageTable as Image,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\CategoryTable as Category,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\TaskTable as Task,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\HistoryTable as History,
	\Acrit\Core\Export\Plugins\WildberriesV3Helpers\HistoryStockTable as HistoryStock;
	

class WildberriesV3 extends UniversalPlugin {
	
	const DATE_UPDATED = '2022-05-31';

	const ATTRIBUTE_ID = 'attribute_%s';

	# Types
	const C = 'C'; // CARD
	const N = 'N'; // NOMENCLATURE (child of card)
	const V = 'V'; // VARIATION (child of nomenclature)

	const KEY_N = 'nomenclatures';
	const KEY_V = 'variations';
	const KEY_A = 'addin';
	
	// const TMP_UUID_PREFIX = 'tmp_uuid___';
	// const TMP_NOMENCLATURE_ADDIN = '_nomenclatures_addin';
	// const TMP_PRODUCT_VARIATION = '_variation';

	protected static $bSubclass = true;
	
	# Basic settings
	#protected $bOffersPreprocess = true; # Moved to method
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
		$bElement = !Helper::isOffersIBlock($intIBlockId);
		$bOffer = !$bElement;
		# Get current type (one of 3 will be true)
		$bC = $this->getStructureType() == static::C;
		$bN = $this->getStructureType() == static::N;
		$bV = $this->getStructureType() == static::V;
		# What to show?
		$bShowC = $bElement || $bOffer && ($bC);
		$bShowN = $bElement || $bOffer && ($bC || $bN);
		$bShowV = $bElement || $bOffer && ($bC || $bN || $bV);
		# Show fields
		if($bShowC){
			if(!empty($arFields = $this->getStructureFields(static::C, $bOffer))){
				$arResult['HEADER_C'] = [];
				$arResult = array_merge($arResult, $arFields);
			}
			if(!$this->isStockAndPrice()){
				if(!empty($arAttributes = $this->getStructureAttributes(static::C, $intIBlockId))){
					$arResult = array_merge($arResult, $arAttributes);
				}
			}
		}
		if($bShowN && !empty($arFields = $this->getStructureFields(static::N, $bOffer))){
			if(!empty($arFields = $this->getStructureFields(static::N, $bOffer))){
				$arResult['HEADER_N'] = [];
				$arResult = array_merge($arResult, $arFields);
			}
			if(!$this->isStockAndPrice()){
				if(!empty($arAttributes = $this->getStructureAttributes(static::N, $intIBlockId))){
					$arResult = array_merge($arResult, $arAttributes);
				}
			}
		}
		if($bShowV && !empty($arFields = $this->getStructureFields(static::V, $bOffer))){
			if(!empty($arFields = $this->getStructureFields(static::V, $bOffer))){
				$arResult['HEADER_V'] = [];
				$arResult = array_merge($arResult, $arFields);
			}
			if(!$this->isStockAndPrice()){
				if(!empty($arAttributes = $this->getStructureAttributes(static::V, $intIBlockId))){
					$arResult = array_merge($arResult, $arAttributes);
				}
			}
			if($this->isExportStock()){
				if(!empty($arStockFields = $this->getStockFields(static::C, $bOffer))){
					$arResult['HEADER_STOCKS'] = [];
					$arResult = array_merge($arResult, $arStockFields);
				}
			}
		}
		return $arResult;
	}

	/**
	 * Get all structure types
	 */
	public function getStructureTypes(){
		return [
			static::C => static::getMessage('OFFERS_STRUCTURE_C'),
			static::N => static::getMessage('OFFERS_STRUCTURE_N'),
			static::V => static::getMessage('OFFERS_STRUCTURE_V'),
		];
	}
	
	/**
	 *	Get structure type
	 */
	public function getStructureType(){
		$strResult = $this->arParams['OFFERS_STRUCTURE'];
		if(!array_key_exists($strResult, $this->getStructureTypes())){
			$strResult = key($this->getStructureTypes());
		}
		return $strResult;
	}

	/**
	 * Get fields for structure
	 */
	public function getStructureFields($strStructure, $bOffer, $bNoObject=false){
		$arResult = [];
		switch($strStructure){
			case static::C:
				$arResult = [
					'supplierVendorCode' => ['FIELD' => '', 'REQUIRED' => true],
					'object' => ['CONST' => '', 'REQUIRED' => true],
					'countryProduction' => ['REQUIRED' => true, 'ALLOWED_VALUES_CUSTOM' => true, 'FIELD' => ($bOffer ? 'PARENT.' : '').'PROPERTY_COUNTRY'],
				];
				if($this->isStockAndPrice()){
					unset($arResult['object'], $arResult['countryProduction']);
				}
				break;
			case static::N:
				$arResult = [
					'object' => ['CONST' => '', 'REQUIRED' => true],
					'vendorCode' => ['FIELD' => 'PROPERTY_ARTNUMBER', 'REQUIRED' => true],
					'price' => ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'],
				];
				if($this->isStockAndPrice()){
					unset($arResult['object']);
				}
				break;
			case static::V:
				$arResult = [
					'object' => ['CONST' => '', 'REQUIRED' => true],
					'barcode' => ['FIELD' => 'CATALOG_BARCODE'],
				];
				if($this->isStockAndPrice()){
					unset($arResult['object']);
				}
				break;
		}
		if($bNoObject){ # When generate offer in product, we don't need 'object' in offers
			unset($arResult['object']);
		}
		return $arResult;
	}

	/**
	 * Get fields for stocks
	 */
	public function getStockFields($strStructure, $bOffer){
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
	 * Get attributes for $strStructure (foe getUniversalFields)
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
						'REQUIRED' => count($arData) == 1 && $arAttribute['IS_REQUIRED'] == 'Y',
						'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
						'PARAMS' => [],
					];
					if($arAttribute['MAX_COUNT']){
						$arField['MULTIPLE'] = true;
						$arField['MAX_COUNT'] = $arAttribute['MAX_COUNT'];
						$arField['PARAMS']['MULTIPLE'] = 'multiple';
					}
					if($arAttribute['DICTIONARY']){
						$arField['ALLOWED_VALUES_CUSTOM'] = true;
					}
					$this->guessDefaultValue($arField, $arAttribute, $intIBlockId);
					$this->addDescriptions($arField, $arAttribute, $intIBlockId);
					$arResult[$strAttributeId] = $arField;
				}
			}
		}
		#
		return $arResult;
	}

	/**
	 *	Preprocess si need if the structure is nomenclature or variation
	 */
	public function isOffersPreprocess(){
		return in_array($this->getStructureType(), [static::N, static::V]);
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
	 *	Try to guess default value
	 */
	protected function guessDefaultValue(&$arField, $arAttribute, $intIBlockId){
		$bOffer = Helper::isOffersIBlock($intIBlockId);
		switch($arAttribute['NAME']){
			case static::getMessage('GUESS_BRAND'):
				$arField['FIELD'] = $bOffer ? ['PARENT.PROPERTY_BRAND', 'PARENT.PROPERTY_BRAND_FER'] 
				: ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF'];
				break;
			case static::getMessage('GUESS_DESCRIPTION'):
				$arField['FIELD'] = ['DETAIL_TEXT'];
				$arField['PARAMS'] = ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => '1000', 'HTMLSPECIALCHARS' => 'skip'];
				$arField['FIELD_PARAMS'] = ['HTMLSPECIALCHARS' => 'skip', 'HTML2TEXT' => 'Y', 'HTML2TEXT_mode' => 'html2text'];
				break;
			case static::getMessage('GUESS_TNVED'):
				$arField['FIELD'] = $bOffer ? ['PARENT.PROPERTY_TNVED'] : ['PROPERTY_TNVED'];
				break;
			case static::getMessage('CUSTOM_ATTR_PHOTO'):
				$arField['FIELD'] = ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'];
				$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
				$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
				break;
		}
	}
	
	/**
	 *	Add descriptions
	 */
	protected function addDescriptions(&$arField, $arAttribute, $intIBlockId){
		$strHint = 'DESC_';
		switch($arAttribute['NAME']){
			case static::getMessage('CUSTOM_ATTR_PHOTO'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_PHOTO');
				break;
			case static::getMessage('CUSTOM_ATTR_KEYWORDS'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_KEYWORDS');
				break;
			case static::getMessage('CUSTOM_ATTR_DESCRIPTION'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_DESCRIPTION');
				break;
			case static::getMessage('CUSTOM_ATTR_INGREDIENTS'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_INGREDIENTS');
				break;
			case static::getMessage('CUSTOM_ATTR_NAME'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_NAME');
				break;
		}
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/category.php';
		require_once __DIR__.'/include/classes/image.php';
		require_once __DIR__.'/include/classes/task.php';
		require_once __DIR__.'/include/classes/history.php';
		require_once __DIR__.'/include/classes/historystock.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$this->API = new Api($this->intProfileId, $this->strModuleId);
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
		$arSettings['OFFERS_STRUCTURE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/offers_structure.php'),
			'SORT' => 150,
		];
		$arSettings['EXPORT_STOCKS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_stocks.php'),
			'SORT' => 160,
		];
		$arSettings['STOCK_AND_PRICE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/stock_and_price.php'),
			'SORT' => 170,
		];
		$arSettings['CONTINUE_ON_ERROR'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/continue_on_error.php'),
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
			case 'history_item_json_preview':
				$arJsonResult['HTML'] = $this->getHistoryItemJsonPreview($arParams['GET']);
				break;
			case 'task_stocks_json_preview':
				$arJsonResult['HTML'] = $this->getTaskStocksJsonPreview($arParams['GET']);
				break;
		}
	}

	/**
	 * Check token is actual
	 */
	public function tokenCheck($arParams, &$arJsonResult){
		$strSupplierId = $arParams['GET']['supplier_id'];
		$strAuthToken = $arParams['GET']['auth_token'];
		# Execute content API method
		$arJsonRequest = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'supplierID' => $strSupplierId,
				'filter' => [
					'order' => ['column' => 'createdAt', 'order' => 'desc'],
				],
				'query' => [
					'limit' => 1,
					'offset' => 0,
				],
			],
		];
		$arQueryResult = $this->API->execute('/card/list', $arJsonRequest, [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken($strAuthToken),
			],
		]);
		$arJsonResult['Request'] = $arJsonRequest;
		$arJsonResult['Response'] = $arQueryResult;
		$arJsonResult['Success'] = is_array($arQueryResult['result']);
	}

	/**
	 * Get auth token from profile settings
	 */
	protected function getAuthToken($strAuthToken=null){
		return strlen($strAuthToken) ? $strAuthToken : $this->arParams['AUTH_TOKEN'];
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
		$strUrl = '/api/v1/config/get/object/translated';
		$strUrl .= '?'.http_build_query([
			'name' => $strCategoryNameUrl,
			'lang' => 'ru',
		]);
		$arQueryResult = $this->API->execute($strUrl, null, [
			'METHOD' => 'GET',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
		]);
		if(is_array($arQueryResult) && $arQueryResult['data']){
			$arSources = [
				static::C => [
					'data' => $arQueryResult['data']['addin'],
				],
				static::V => [
					'data' => $arQueryResult['data']['nomenclature']['variation']['addin'],
				],
				static::N => [
					'data' => $arQueryResult['data']['nomenclature']['addin'],
				],
			];
			$this->correctAttributes($arSources);
			foreach($arSources as $strSourceType => $arSource){
				if($arSource['data']){
					foreach($arSource['data'] as $arItem){
						$arFields = [
							'CATEGORY_NAME' => $strCategoryName,
							'HASH' => $this->getAttributeHash($strCategoryName, $arItem['type']),
							'NAME' => $arItem['type'],
							'TYPE' => $strSourceType,
							'SORT' => is_numeric($arItem['sort']) && $arItem['sort'] > 0 ? $arItem['sort'] : 100,
							'USE_ONLY_DICTIONARY_VALUES' => $arItem['useOnlyDictionaryValues'] == 1 ? 'Y' : 'N',
							'MAX_COUNT' => $arItem['maxCount'],
							'IS_REQUIRED' => $arItem['required'] == 1 ? 'Y' : 'N',
							'IS_AVAILABLE' => $arItem['isAvailable'] == 1 ? 'Y' : 'N',
							'IS_NUMBER' => $arItem['isNumber'] == 1 ? 'Y' : 'N',
							'UNIT' => is_array($arItem['units']) ? reset($arItem['units']) : null,
							'UNITS' => is_array($arItem['units']) ? implode(',', $arItem['units']) : null,
							'DICTIONARY' => $arItem['dictionary'],
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
			$this->addNameAtribute($strCategoryName, $arItem['type'], $strSessionId);
			Attribute::deleteByFilter([
				'=CATEGORY_NAME' => $strCategoryName,
				'!SESSION_ID' => $strSessionId,
			]);
		}
		else{
			$this->addToLog(static::getMessage('LOG_UPDATE_ATTRIBUTES_ERROR', [
				'#ERROR#' => print_r($arQueryResult, true),
			]));
		}
		return $arResult;
	}

	/**
	 * Correct attributes on import from WB
	 */
	protected function correctAttributes(&$arSources){
		# Add retail price (for variations addin)
		if(is_array($arSources[static::V]['data'])){
			$arPriceAttribute = [
				'isAvailable' => 1,
				'required' => 1,
				'isNumber' => 1,
				'useOnlyDictionaryValues' => 0,
				'type' => static::getMessage('CUSTOM_ATTR_PRICE'),
				'units' => [static::getMessage('CUSTOM_ATTR_PRICE_UNIT')],
				'sort' => 10,
			];
			array_unshift($arSources[static::V]['data'], $arPriceAttribute);
		}
		# Add photo
		if(is_array($arSources[static::N]['data'])){
			$arPhotoAttribute = [
				'isAvailable' => 1,
				'required' => 0,
				'useOnlyDictionaryValues' => 0,
				'maxCount' => 10,
				'type' => static::getMessage('CUSTOM_ATTR_PHOTO'),
			];
			array_unshift($arSources[static::N]['data'], $arPhotoAttribute);
		}
		# Add photo360
		if(is_array($arSources[static::N]['data'])){
			$arPhotoAttribute = [
				'isAvailable' => 1,
				'required' => 0,
				'useOnlyDictionaryValues' => 0,
				'maxCount' => 10,
				'type' => static::getMessage('CUSTOM_ATTR_PHOTO360'),
			];
			array_unshift($arSources[static::N]['data'], $arPhotoAttribute);
		}
		# Add video
		if(is_array($arSources[static::N]['data'])){
			$arVideoAttribute = [
				'isAvailable' => 1,
				'required' => 0,
				'useOnlyDictionaryValues' => 0,
				'maxCount' => 10,
				'type' => static::getMessage('CUSTOM_ATTR_VIDEO'),
			];
			array_unshift($arSources[static::N]['data'], $arVideoAttribute);
		}
		# Additional images: set multiple
		if(is_array($arSources[static::N]['data'])){
			foreach($arSources[static::N]['data'] as $key => &$arItem){
				if($arItem['type'] == static::getMessage('CUSTOM_ATTR_ADDITIONAL_COLORS')){
					$arItem['maxCount'] = 10;
				}
			}
			unset($arItem);
		}
		# Add keywords
		if(is_array($arSources[static::C]['data'])){
			$arKeywordsAttribute = [
				'isAvailable' => 1,
				'required' => 0,
				'useOnlyDictionaryValues' => 0,
				'maxCount' => 16,
				'type' => static::getMessage('CUSTOM_ATTR_KEYWORDS'),
			];
			array_unshift($arSources[static::C]['data'], $arKeywordsAttribute);
		}
	}

	/**
	 * Add name attributes on import fr om WB
	 */
	protected function addNameAtribute($strCategoryName, $strType, $strSessionId) {
		$strName = static::getMessage('CUSTOM_ATTR_NAME');
		$arFilter = [
			'NAME' => $strName,
			'CATEGORY_NAME' => $strCategoryName,
		];
		$arSelect = [
			'ID',
		];
		$resItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
		if(!$resItem->fetch()){
			$arFields = [
				'CATEGORY_NAME' => $strCategoryName,
				'HASH' => $this->getAttributeHash($strCategoryName, $strType.$strName),
				'NAME' => $strName,
				'TYPE' => 'C',
				'SORT' => 2,
				'USE_ONLY_DICTIONARY_VALUES' => 'N',
				'MAX_COUNT' => null,
				'IS_REQUIRED' => 'N',
				'IS_AVAILABLE' => 'Y',
				'IS_NUMBER' => 'N',
				'UNIT' => null,
				'UNITS' => null,
				'DICTIONARY' => null,
				'SESSION_ID' => $strSessionId,
				'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
			];
			Attribute::add($arFields);
		}
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
		$arResult = $arItem;
		$bOffer = Helper::isOffersIBlock($arElement['IBLOCK_ID']);
		$strMode = $this->getStructureType();
		$arItemAttributes = $this->getItemAttributes($arResult);
		$arCategoryAttributes = $this->getCategoryAttributes($arFields['object']);
		if($bOffer){
			# OFFER
			unset($arResult['_OFFER_PREPROCESS']);
			switch($strMode){
				case static::C: $this->buildJson_Offer_C($arResult, $arItemAttributes, $arCategoryAttributes, $arElement['ID']); break;
				case static::N: $this->buildJson_Offer_N($arResult, $arItemAttributes, $arCategoryAttributes, $arElement['ID']); break;
				case static::V: $this->buildJson_Offer_V($arResult, $arItemAttributes, $arCategoryAttributes, $arElement['ID']); break;
			}
		}
		else{
			# PRODUCT
			$arOffers = $arResult['_OFFER_PREPROCESS']; unset($arResult['_OFFER_PREPROCESS']);
			if(is_array($arOffers)){
				$this->transformPreprocessedOffers($arOffers);
			}
			$this->buildJson_Product($arResult, $arItemAttributes, $arCategoryAttributes, $arElement, $strMode, $arOffers);
		}
		# Copy supplierVendorCode to more data
		$this->copySupplierVendorCodeToMoreData($arResult, $arDataMore);
		# Move stocks from card/nomenclature/variation to more data [we use it just for cards]
		if(!$bOffer || $strMode == static::C){
			$this->moveStocksToMoreData($arResult, $arDataMore);
		}
		# Move prices to more data
		$this->movePricesToMoreData($arResult, $arDataMore);
		# Replace $arItem
		$arItem = $arResult;
		# If preview, show actual data from WB
		if($this->isPreview() && (!$bOffer || $strMode == static::C)){
			$arDataMore['WILDBERRIES_ACTUAL_DATA'] = $this->getCardBySupplierVendorCode($arItem['supplierVendorCode']);
		}
	}
	protected function buildJson_Product(&$arItem, $arItemAttributes, $arCategoryAttributes, $arElement, $strMode, $arOffers){
		$intElementId = $arElement['ID'];
		# Prepare base fields
		$arCard = array_intersect_key($arItem, $this->getStructureFields(static::C, $bOffer=false));
		$arNomenclature = array_intersect_key($arItem, $this->getStructureFields(static::N, $bOffer=false, $bNoObject=true));
		$arVariation = array_intersect_key($arItem, $this->getStructureFields(static::V, $bOffer=false, $bNoObject=true));
		# Prepare result item
		$this->addItemAddin($arCard, static::C, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Process variation
		$this->addItemAddin($arVariation, static::V, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Transfer stocks from fields to variations
		$this->copyStocksToVariation($arVariation, $arItem);
		# Process nomenclature
		$this->addItemAddin($arNomenclature, static::N, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Check general offers mode
		$strOffersMode = $this->arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['PARAMS']['OFFERS_MODE'];
		$bAllowProductExport = is_null($strOffersMode) || $strOffersMode == 'all' || $strOffersMode == 'none'
			|| $strOffersMode == 'only' && (!is_array($arOffers) || empty($arOffers));
		$bPutFirstVariation = true;
		if($this->getStructureType() == static::V){
			$bAllowProductExport = true;
			if($strOffersMode == 'only' && !empty($arOffers) || $strOffersMode == 'offers'){
				$bPutFirstVariation = false;
			}
		}
		# Put variations to nomenclature, and nomenclature to card
		$arNomenclature[static::KEY_V] = [];
		$arCard[static::KEY_N] = [];
		if($bAllowProductExport){
			if($bPutFirstVariation){
				$arNomenclature[static::KEY_V][] = $arVariation;
			}
			$arCard[static::KEY_N][] = $arNomenclature;
		}
		#
		if(is_array($arOffers) && !empty($arOffers)){
			if($strMode == static::N){
				foreach($arOffers as $arOffer){ 
					if(is_array($arOffer) && !empty($arOffer)){
						$arCard[static::KEY_N] = array_merge($arCard[static::KEY_N], [$arOffer]);
					}
				}
			}
			elseif($strMode == static::V){
				foreach($arOffers as $arOffer){
					if(is_array($arOffer) && !empty($arOffer)){
						foreach($arCard[static::KEY_N] as $intIndexN => &$arNomenclature){
							$arNomenclature[static::KEY_V] = array_merge($arNomenclature[static::KEY_V], [$arOffer]);
						}
						unset($arNomenclature);
					}
				}
			}
		}
		# Modify input item array
		$arItem = $arCard;
	}
	protected function buildJson_Offer_C(&$arItem, $arItemAttributes, $arCategoryAttributes, $intElementId){
		# Prepare base fields
		$arCard = array_intersect_key($arItem, $this->getStructureFields(static::C, $bOffer=true));
		$arNomenclature = array_intersect_key($arItem, $this->getStructureFields(static::N, $bOffer=true));
		$arVariation = array_intersect_key($arItem, $this->getStructureFields(static::V, $bOffer=true));
		# Prepare result item
		$this->addItemAddin($arCard, static::C, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Process variation
		$this->addItemAddin($arVariation, static::V, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Transfer stocks from fields to variations
		$this->copyStocksToVariation($arVariation, $arItem);
		# Save variation to nomenclature
		$arNomenclature[static::KEY_V] = [$arVariation];
		# Process nomenclature
		$this->addItemAddin($arNomenclature, static::N, $arItemAttributes, $arCategoryAttributes, $intElementId);
		$arCard[static::KEY_N] = [$arNomenclature];
		# Modify input item array
		$arItem = $arCard;
	}
	protected function buildJson_Offer_N(&$arItem, $arItemAttributes, $arCategoryAttributes, $intElementId){
		unset($arItem['object']);
		# Prepare base fields
		$arNomenclature = array_intersect_key($arItem, $this->getStructureFields(static::N, $bOffer=true));
		$arVariation = array_intersect_key($arItem, $this->getStructureFields(static::V, $bOffer=true));
		# Process variation
		$this->addItemAddin($arVariation, static::V, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Transfer stocks from fields to variations
		$this->copyStocksToVariation($arVariation, $arItem);
		# Save variation to nomenclature
		$arNomenclature[static::KEY_V] = [$arVariation];
		# Process nomenclature
		$this->addItemAddin($arNomenclature, static::N, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Modify input item array
		$arItem = $arNomenclature;
	}
	protected function buildJson_Offer_V(&$arItem, $arItemAttributes, $arCategoryAttributes, $intElementId){
		unset($arItem['object']);
		# Prepare base fields
		$arVariation = array_intersect_key($arItem, $this->getStructureFields(static::V, $bOffer=true));
		# Process variation
		$this->addItemAddin($arVariation, static::V, $arItemAttributes, $arCategoryAttributes, $intElementId);
		# Transfer stocks from fields to variations
		$this->copyStocksToVariation($arVariation, $arItem);
		# Modify input item array
		$arItem = $arVariation;
	}

	/**
	 * Copy stock_1234 from source $arItem to $arVariation
	 */
	protected function copyStocksToVariation(&$arVariation, $arItem){
		foreach($arItem as $strField => $mValue){
			if($this->isStock($strField)){
				$arVariation[$strField] = $mValue;
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
	 * Add addin to item (for C, N, V)
	 */
	protected function addItemAddin(&$arItem, $strType, $arItemAttributes, $arCategoryAttributes, $intElementId){
		if(is_array($arAllAttributes = $arCategoryAttributes[$strType])){
			if(!empty($arAllAttributes)){
				foreach($arAllAttributes as $strHash => $arAttribute){
					if(!Helper::isEmpty($mValue = $arItemAttributes[$strHash])){
						$arValues = is_array($mValue) ? $mValue : [$mValue];
						$arAddin = [];
						foreach($arValues as $strValue){
							$strKey = 'value';
							if($arAttribute['IS_NUMBER'] == 'Y'){
								$strKey = 'count';
								$strValue = floatVal($strValue);
							}
							$arValueItem = [
								$strKey => $strValue,
							];
							if(strlen($arAttribute['UNIT'])){
								$arValueItem['units'] = $arAttribute['UNIT'];
							}
							# Price: remove 'units'
							if($arAttribute['NAME'] == static::getMessage('CUSTOM_ATTR_PRICE')){
								unset($arValueItem['units']);
							}
							if(is_array($arValueItem)){
								$arAddin[] = $arValueItem;
							}
						}
						if($arAttribute['NAME'] == static::getMessage('CUSTOM_ATTR_INGREDIENTS')){
							$this->processIngredients($arAddin, $intElementId);
						}
						$arAddin = [
							'type' => $arAttribute['NAME'],
							'params' => $arAddin,
						];
						$arItem[static::KEY_A][] = $arAddin;
					}
				}
			}
		}
	}

	/**
	 * Transform raw _OFFER_PREPROCESS to its DATA key
	 */
	protected function transformPreprocessedOffers(&$arOffers){
		if(is_array($arOffers)){
			$arOffersTmp = [];
			foreach($arOffers as $arOffer){
				try{
					$arOffersTmp[] = Json::decode($arOffer['DATA']);
				}
				catch(\Exception $obError){}
			}
			$arOffers = $arOffersTmp;
		}
	}
	

	/**
	 * Get all attributes for category (by name)
	 */
	protected function getCategoryAttributes($strCategoryName){
		$strCategoryName = trim($strCategoryName);
		$arResult = [];
		$resQuery = Attribute::getList([
			'filter' => ['CATEGORY_NAME' => $strCategoryName], # , 'IS_REQUIRED' => 'Y'
			'select' => ['HASH', 'NAME', 'TYPE', 'USE_ONLY_DICTIONARY_VALUES', 'DICTIONARY', 'MAX_COUNT', 'IS_REQUIRED', 
				'IS_AVAILABLE', 'IS_NUMBER', 'UNIT', 'UNITS'],
		]);
		$arResult = [
			static::C => [],
			static::N => [],
			static::V => [],
		];
		while($arItem = $resQuery->fetch()){
			$strType = $arItem['TYPE']; unset($arItem['TYPE']);
			$strHash = $arItem['HASH']; unset($arItem['HASH']);
			$arResult[$strType][$strHash] = $arItem;
		}
		return $arResult;
	}

	/**
	 * Get all attributes for category (by name)
	 */
	protected function getItemAttributes(&$arResult){
		$arAttributes = [];
		foreach($arResult as $strField => $mValue){
			if($arAttribute = $this->parseAttribute($strField)){
				$arAttributes[$arAttribute['HASH']] = $arResult[$strField];
				unset($arResult[$strField]);
			}
		}
		return $arAttributes;
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
	 * Copy supplierVendorCode to more data
	 */
	protected function copySupplierVendorCodeToMoreData(&$arResult, &$arDataMore){
		$arDataMore['supplierVendorCode'] = $arResult['supplierVendorCode'];
	}

	/**
	 * Move all fields stock_*** to $arMoreData
	 */
	protected function moveStocksToMoreData(&$arResult, &$arDataMore){
		foreach($arResult[static::KEY_N] as $intNomenclatureIndex => $arNomenclature){
			foreach($arNomenclature[static::KEY_V] as $intVariationIndex => $arVariation){
				foreach($arVariation as $strField => $mValue){
					if($intStockId = $this->isStock($strField)){
						$arDataMore['STOCKS'][] = [
							'barcode' => $arVariation['barcode'],
							'stock' => intVal($mValue),
							'warehouseId' => $intStockId,
						];
						unset($arResult[static::KEY_N][$intNomenclatureIndex][static::KEY_V][$intVariationIndex][$strField]);
					}
				}
			}
		}
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
	 * Add .id for both product and offers
	 */
	protected function addTmpIds(&$arItem, $arElement){
		$arItem = array_merge([
			'.id' => intVal($arElement['ID']),
			'.iblock_id' => intVal($arElement['IBLOCK_ID']),
		], $arItem);
	}

	/**
	 * Remove all .id for both product and offers
	 */
	protected function clearTmpIds(&$arItem, $bOffer=false){
		// if($bOffer && !$this->isOffersNewMode()){
		// 	return;
		// }
		// #unset($arItem['.id'], $arItem['.iblock_id']);
		// if(is_array($arItem['nomenclatures'][0]['variations'])){
		// 	foreach($arItem['nomenclatures'][0]['variations'] as $key => &$arVariation){
		// 		unset($arVariation['.id']);
		// 	}
		// 	unset($arVariation);
		// }
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
	 * Upload single image
	 *
	 * @param $strFile relative path to file
	 * @param $strUuid UUID
	 */
	protected function uploadImage($intElementId, $strFileBinary, $url, $strImageType, $strUuid){
		if(!strlen($strFileBinary)){
			return;
		}
		#
		$strBoundary = 'boundaryImage';
		$EOL = "\r\n";
		#
		$strContent = $EOL;
		$strContent .= sprintf('--%s', $strBoundary).$EOL;
		$strContent .= 'Content-Disposition: form-data; name="uploadfile"; filename="image.jpg"'.$EOL;
		$strContent .= 'Content-Type: '.$strImageType.$EOL.$EOL;
		$strContent .= $strFileBinary.$EOL;
		$strContent .= sprintf('--%s--', $strBoundary);
		#
		$arHeader = [
			'Content-Type' => sprintf('multipart/form-data; boundary=%s', $strBoundary),
			'Authorization' => $this->getAuthToken(),
			'X-File-Id' => $strUuid,
			'X-Supplier-ID' => $this->getSupplierId(),
			'Accept' => 'application/json',
			'Connection' => 'close',
		];
		#
		$arRequest = [
			'METHOD' => 'POST',
			'HEADER' => $arHeader,
			'CONTENT' => $strContent,
			'SKIP_HTTPS_CHECK' => true,
			'RETURN_TEXT' => true,
			'ADD_SYSTEM_HEADERS' => false,
			'ELEMENT_ID' => $intElementId,
			'USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.116 Safari/537.36',
		];
		#
		$strJsonResponse = $this->API->execute('/card/upload/file/multipart', null, $arRequest);
		$strResponseCode = $this->API->getResponseCode();
		$arLogRequest = $arRequest;
		if($strResponseCode == 200){
			$strResponse = implode($EOL, $this->API->getHeaders()).$EOL.$EOL.$strJsonResponse;
			if($arImage = $this->saveImage($intElementId, $url, $strUuid, $strImageType, $strResponse)){
				return $arImage;
			}
		}
		else{
			$this->addToLog(static::getMessage('LOG_IMAGE_UPLOAD_ERROR', [
				'#ELEMENT_ID#' => $intElementId,
				'#UUID#' => $strUuid,
				'#URL#' => $url,
				'#RESPONSE_CODE#' => $strResponseCode,
				'#RESPONSE#' => $strJsonResponse,
			]));
		}
		return false;
	}
	
	/**
	 *	Check empty required fields (for each category)
	 */
	protected function checkRequiredFields($intIBlockId, $arFields, $strCategoryName){
		$arEmptyRequiredFields = [];
		$arFieldsAll = $this->getFieldsCached($this->intProfileId, $intIBlockId, true);
		foreach($arFields as $strField => $mValue){
			if($arFieldsAll[$strField]){
				$bEmpty = Helper::isEmpty($mValue, $arFieldsAll[$strField]->isSimpleEmptyMode());
				if($bEmpty && $arFieldsAll[$strField]->isCustomRequired()){
					$arAttribute = static::parseAttribute($strField);
					if(is_array($arAttribute)){
						if($this->isAttributeRequired($arAttribute['CATEGORY_NAME'], $arAttribute['NAME'])){
							if(!is_array($arEmptyRequiredFields[$arAttribute['CATEGORY_NAME']])){
								$arEmptyRequiredFields[$arAttribute['CATEGORY_NAME']] = [];
							}
							$arEmptyRequiredFields[$arAttribute['CATEGORY_NAME']][] = $arFieldsAll[$strField]->getName();
						}
					}
				}
			}
		}
		if(!empty($arEmptyRequiredFields[$strCategoryName])){
			$arErrors = [];
			$arErrors[] = static::getMessage('ERROR_EMPTY_REQUIRED_FIELDS', [
				'#CATEGORY#' => $strCategoryName,
				'#FIELDS#' => implode(', ', $arEmptyRequiredFields[$strCategoryName]),
				]);
			return $arErrors;
		}
		return false;
	}
	
	/**
	 *	Cancel save json to file
	 */
	protected function onUpJsonExportItem(&$arItem, &$strJson, &$arSession, &$bWrite){
		$bWrite = false;
	}
	
	/**
	 * Process each image in result item
	 *
	 * @param array $arItem
	 */
	protected function processImage($intElementId, &$arValueItem, &$arDataMore){
		if(is_array($arValueItem) && !empty($arValueItem['value'])){
			$strImageBinary = null;
			$strImageType = null;
			$url = $arValueItem['value'];
			if(is_string($url)){
				# Check file exists (file caching)
				// if($arImage = $this->checkImage($intElementId, $url)){
				// 	$arValueItem['value'] = $arImage['UUID'];
				// 	$arValueItem['units'] = $arImage['TYPE'];
				// 	return true;
				// }
				# Prepare file data
				$strSiteUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y', '/');
				if(Helper::strpos($url, $strSiteUrl) === 0){
					$strFile = Helper::substr($url, strlen($strSiteUrl) - 1);
					$strImageBinary = file_get_contents(Helper::root().$strFile);
					$strImageType = $this->detectImageType($url);
				}
				else{
					if(strlen($strResponse = HttpRequest::get($url))){
						$strImageBinary = $strResponse;
						$strImageType = $this->detectImageType($url);
					}
					unset($strResponse);
				}
			}
			# Upload file
			if(is_string($strImageBinary) && strlen($strImageBinary)){
				$strUuid = Helper::generateUuid();
				if($this->getPreviewMode()){
					$arImage = [
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'ELEMENT_ID' => $intElementId,
						'UUID' => $strUuid,
						'URL' => $url,
						'TYPE' => $strImageType,
					];
					$arValueItem['value'] = $strUuid;
					$arValueItem['units'] = $strImageType;
					$arDataMore['imagesInfo'][] = $arImage;
					return $arImage;
				}
				else{
					if($arImage = $this->uploadImage($intElementId, $strImageBinary, $url, $strImageType, $strUuid)){
						$arValueItem['value'] = $strUuid;
						$arValueItem['units'] = $strImageType;
						return $arImage;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Check image is already sent
	 * @param mixed $intElementId
	 * @param mixed $strUrl
	 */
	protected function checkImage($intElementId, $strUrl){
		$arQuery = [
			'filter' => [
				'MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'ELEMENT_ID' => $intElementId,
				'URL' => $strUrl,
			],
		];
		if($arImage = Image::getList($arQuery)->fetch()){
			$arImage['TIMESTAMP_X'] = $arImage['TIMESTAMP_X']->toString();
			return $arImage;
		}
		return false;
	}

	/**
	 * Save image to acrit_wb_image
	 * @param $intElementId
	 * @param $strUrl
	 * @param $strUuid
	 * @param $strImageType
	 */
	protected function saveImage($intElementId, $strUrl, $strUuid, $strImageType, $strResponse){
		$obResult = Image::add([
			'MODULE_ID' => $this->strModuleId,
			'PROFILE_ID' => $this->intProfileId,
			'ELEMENT_ID' => $intElementId,
			'UUID' => $strUuid,
			'URL' => $strUrl,
			'TYPE' => $strImageType,
			'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
		]);
		if($obResult->isSuccess()){
			return $this->checkImage($intElementId, $strUrl);
		}
		return false;
	}

	/**
	 * Process attribute 'Ingredients'
	 */
	protected function processIngredients(&$arValues, $intElementId){
		if(!is_array($arValues)){
			return;
		}
		$ingredients = implode(', ', array_column($arValues, 'value'));
		$ingredients = trim($ingredients);
		$ingredients = trim($ingredients, '.,;');
		$ingredients = str_replace(';', ',', $ingredients);
		$arIngredientsRaw = Helper::explodeValues($ingredients);
		$arIngredients = [];
		foreach($arIngredientsRaw as $key => $strItem){
			if(strlen(trim($strItem))){
				# Hlopok 100%
				if(preg_match('#^(.*?)[\W]*([\d]+)[\W]*$#s'.BX_UTF_PCRE_MODIFIER, $strItem, $arMatch)){
					if(empty($arMatch[1])){
						$arMatch[1] = $arMatch[0];
					}
					$arIngredients[] = [
						'value' => $arMatch[1],
						'count' => intVal($arMatch[2]),
					];
				}
				# 100% Hlopok
				elseif(preg_match('#^[\W]*([\d]+)[\W]*(.*?)$#s'.BX_UTF_PCRE_MODIFIER, $strItem, $arMatch)){
					$arIngredients[] = [
						'value' => $arMatch[2],
						'count' => intVal($arMatch[1]),
					];
				}
				# Other: pkh, plastik
				else{
					$arIngredients[] = [
						'value' => $strItem,
						'count' => round(100 / count($arIngredientsRaw)),
					];
				}
			}
		}
		# Check result
		if(!empty($arIngredients)){
			$bSummIs100 = array_sum(array_column($arIngredients, 'count')) == 100;
			if(!$bSummIs100){
				// Share 100% between all items
				$items = count($arIngredients); // 6
				$count = floor(100 / $items); // 16
				$arIngredients = array_map(function($arItem)use($count){
					$arItem['count'] = $count;
					return $arItem;
				}, $arIngredients);
				$delta = 100 - $items * $count; // 4 => + 1 to first 4 items
				if($delta > 0){
					for($i = 0; $i < $delta; $i++){
						$arIngredients[$i]['count']++;
					}
				}
			}
		}
		# Replace result
		$arValues = $arIngredients;
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
		// if($this->arParams['EXPORT_STOCKS'] == 'Y'){
			$arSteps['WB_PAUSE_BEFORE_STOCKS'] = [
				'NAME' => static::getMessage('STEP_PAUSE_BEFORE_STOCKS'),
				'SORT' => 5000,
				'FUNC' => [$this, 'stepPauseBeforeStocks'],
			];
			$arSteps['WB_EXPORT_STOCKS'] = [
				'NAME' => static::getMessage('STEP_EXPORT_STOCKS'),
				'SORT' => 5010,
				'FUNC' => [$this, 'stepExportStocks'],
			];
		// }
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
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * Export (create or update) single product
	 * @param $arItem
	 */
	protected function exportItem($arItem, &$arSession){
		try{
			$arItemJson = Json::decode($arItem['DATA']);
		}
		catch(\Exception $obException){
			$strMessage = 'Export item error: wrong JSON.';
			print $strMessage;
			$this->addToLog($strMessage);
			return Exporter::RESULT_ERROR;
		}
		if(is_array($arItemJson) && !empty($arItemJson)){
			if(Helper::strlen($strSupplierVendorCode = $arItemJson['supplierVendorCode'])){
				$strMethod = '/card/create';
				# Check product exists in WB: if so, add imtId (for card), nmId (for nomenclatures), chrtId (for variations)
				$arProduct = $this->getCardBySupplierVendorCode($strSupplierVendorCode);
				if(is_array($arProduct)){
					$strMethod = '/card/update';
					$this->copyKeys($arProduct, $arItemJson, ['imtId']);
					foreach($arItemJson[static::KEY_N] as &$arNomenclature){
						$arRemoteNomenclature = $this->getNomenclatureByVendorCode($arProduct, $arNomenclature['vendorCode']);
						if($arRemoteNomenclature){
							$this->copyKeys($arRemoteNomenclature, $arNomenclature, ['nmId']);
							foreach($arNomenclature[static::KEY_V] as &$arVariation){
								$arRemoteVariation = $this->getVariationByBarcode($arRemoteNomenclature, $arVariation['barcode']);
								if($arRemoteVariation){
									$this->copyKeys($arRemoteVariation, $arVariation, ['chrtId']);
								}
							}
							unset($arVariation);
						}
					}
					unset($arNomenclature);
				}
				# Build result JSON
				$arJson = [
					'id' => Helper::generateUuid(),
					'jsonrpc' => '2.0',
					'params' => [
						'card' => $arItemJson,
					],
				];
				# Execute request
				$arQueryResult = $this->API->execute($strMethod, $arJson, [
					'METHOD' => 'POST',
					'HEADER' => [
						'Authorization' => $this->getAuthToken(),
					],
					'ELEMENT_ID' => $arItem['ELEMENT_ID'],
				]);
				# Log
				$arProductLog = is_array($arProduct) ? array_merge($arProduct, [
					'addin' => 'array(...)',
					'nomenclatures' => 'array(...)',
				]) : static::getMessage('LOG_EXPORT_ITEM_NOT_FOUND');
				if(is_array($arProductLog) && isset($arProductLog['batchID'])){
					unset($arProductLog['batchID']);
				}
				$this->addToLog(static::getMessage('LOG_EXPORT_ITEM', [
					'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
					'#SUPPLIER_VENDOR_CODE#' => $strSupplierVendorCode,
					'#METHOD#' => $strMethod,
					'#PRODUCT#' => print_r($arProductLog, true),
					'#RESULT#' => print_r($arQueryResult, true),
				]), true);
				# Check request result
				$bSent = $arQueryResult['id'] == $arJson['id'] && $arQueryResult['result'] === [] && !$arQueryResult['error'];
				$this->writeHistory($arItem['ELEMENT_ID'], $arJson, $bSent, $arQueryResult, $arSession);
				if($bSent){
					$this->setDataItemExported($arItem['ID']);
					return Exporter::RESULT_SUCCESS;
				}
				else{
					$this->addToLog(static::getMessage('LOG_ELEMENT_DEBUG', [
						'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
						'#VENDOR_CODE#' => $arItemJson['supplierVendorCode'],
						'#METHOD#' => $strMethod,
						'#JSON#' => print_r($arJson, true),
						'#RESULT#' => print_r($arQueryResult, true),
						'#METHOD#' => $strMethod,
					]), true);
					# Display error
					if($this->arParams['CONTINUE_ON_ERROR'] == 'Y'){
						return Exporter::RESULT_SUCCESS;
					}
					else{
						require __DIR__.'/include/popup/error.php';
						return Exporter::RESULT_ERROR;
					}
				}
			}
			else{
				$this->addToLog('Export item error: empty supplierVendorCode.');
			}
		}
		$this->addToLog('Export item error: wrong $arItem[\'DATA\'].');
		return Exporter::RESULT_ERROR;
	}

	/**
	 * Search nomenclature in remote card (by vendorCode)
	 */
	protected function getNomenclatureByVendorCode($arRemoteCard, $strVendorCode){
		if(Helper::strlen($strVendorCode)){
			foreach($arRemoteCard[static::KEY_N] as $arNomenclature){
				if($arNomenclature['vendorCode'] == $strVendorCode){
					return $arNomenclature;
				}
			}
		}
		return false;
	}

	/**
	 * Search variation in nomenclature of remote card (by barcode)
	 */
	protected function getVariationByBarcode($arRemoteNomenclature, $strBarcode){
		if(Helper::strlen($strBarcode)){
			foreach($arRemoteNomenclature[static::KEY_V] as $arVariation){
				if(is_array($arVariation['barcodes']) && in_array($strBarcode, $arVariation['barcodes'])){
					return $arVariation;
				}
			}
		}
		return false;
	}

	/**
	 * Search variation in nomenclature of remote card (by barcode)
	 */
	protected function copyKeys($arSourceItem, &$arTargetItem, $arKeys){
		foreach($arKeys as $strKey){
			if(isset($arSourceItem[$strKey])){
				$arTargetItem = array_merge([$strKey => $arSourceItem[$strKey]], $arTargetItem);
			}
		}
	}
	
	/**
	 * Write task & history
	 */
	protected function writeHistory($intElementId, $arJson, $bSent, $arQueryResult, &$arSession){
		# Add task
		if(!isset($arSession['TASK_ID'])){
			if($this->isHistoryTaskSave()){
				$arTask = [
					'MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'TASK_UUID' => Helper::generateUuid(),
					'SUPPLIER_ID' => $this->getSupplierId(),
					'SESSION_ID' => session_id(),
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
				];
				if($intTaskId = Task::add($arTask)->getId()){
					$arSession['TASK_UUID'] = $arTask['TASK_UUID'];
					$arSession['TASK_ID'] = $intTaskId;
				}
			}
		}
		# Add history
		if($arSession['TASK_ID']){
			if($this->isHistoryProductSave()){
				$arCard = &$arJson['params']['card'];
				$obHistoryResult = History::add([
					'MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'TASK_ID' => $arSession['TASK_ID'],
					'REQUEST_ID' => $arJson['id'],
					'SUPPLIER_VENDOR_CODE' => $arCard['supplierVendorCode'],
					'CARD_ID' => $arCard['id'],
					'IMT_ID' => $arCard['imtId'],
					'NOMENCLATURE_ID' => $arCard['nomenclatures'][0]['id'],
					'NM_ID' => $arCard['nomenclatures'][0]['nmId'],
					'VENDOR_CODE' => $arCard['nomenclatures'][0]['vendorCode'],
					'CHRT_ID' => implode(',', array_column($arCard['nomenclatures'][0]['variations'], 'chrtId')),
					'BARCODE' => implode(',', array_column($arCard['nomenclatures'][0]['variations'], 'barcode')),
					'ELEMENT_ID' => $intElementId,
					'JSON' => Json::encode($arJson),
					'RESPONSE' => Json::encode($arQueryResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
					'SUCCESS' => $bSent ? 'Y' : 'N',
					'SESSION_ID' => session_id(),
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
				]);
				// if($obHistoryResult->isSuccess()){
				// 	$arQuery = ['filter' => ['ID' => $arSession['TASK_ID']], 'select' => ['ID', 'PRODUCTS_COUNT']];
				// 	if($arTask = Task::getList($arQuery)->fetch()){
				// 		Task::update($arTask['ID'], ['PRODUCTS_COUNT' => $arTask['PRODUCTS_COUNT'] + 1]);
				// 	}
				// }
			}
			// Set products count to task
			$arQuery = ['filter' => ['ID' => $arSession['TASK_ID']], 'select' => ['ID', 'PRODUCTS_COUNT']];
			if($arTask = Task::getList($arQuery)->fetch()){
				Task::update($arTask['ID'], ['PRODUCTS_COUNT' => $arTask['PRODUCTS_COUNT'] + 1]);
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
	 * Correct $bProcessElement and $bProcessOffers
	 **/
	public function getProcessEntities(&$bProcessElement, &$bProcessOffers, $arProfile, $intIBlockID, $arElement){
		$this->bOriginalProcessElement = $bProcessElement;
		$this->bOriginalProcessOffers = $bProcessOffers;
		if($arCatalog = Helper::getCatalogArray($intIBlockID)){ # If iblock has no offers, set element=true, offer=false
			if(!$arCatalog['OFFERS_IBLOCK_ID']){
				$this->bOriginalProcessElement = true;
				$this->bOriginalProcessOffers = false;
			}
		}
		if($this->getStructureType() != static::C){ # If mode is N or V, we also need base product
			$bProcessElement = true;
		}
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
				'TASK_ID' => $arGet['task_id'],
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
				'RESPONSE',
				'SUCCESS',
			],
		];
		$arParams = [
			'TASK_ID' => $arGet['task_id'],
			'ALLOW_COPY' => true,
			'SHOW_STOCKS' => true,
		];
		return $this->displayPopupJson(History::getList($arQuery)->fetch(), $arParams);
	}
	
	/**
	 *	Get html for popup for preview history item JSON
	 */
	protected function displayPopupJson($arArray, $arParams=[]){
		if(is_array($arArray) && strlen('JSON')){
			$strFile = __DIR__.'/include/popup/json.php';
			Helper::loadMessages($strFile);
			ob_start();
			$strJson = &$arArray['JSON'];
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}

	/**
	 *	Ajax: preview history item
	 */
	protected function getTaskStocksJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['task_id'],
			],
			'select' => [
				'STOCKS_REQUEST',
				'STOCKS_RESPONSE',
				'STOCKS_RESPONSE_CODE',
			],
		];
		$arParams = [
			'ALLOW_COPY' => true,
		];
		return $this->displayPopupTaskStocksJson(Task::getList($arQuery)->fetch(), $arParams);
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
	 * Get card by supplierVendorCode
	 * @param $supplierVendorCode - UUID-code
	 */
	protected function getCardBySupplierVendorCode($supplierVendorCode, $intElementId=null){
		$result = false;
		$filter = [];
		$filter = [
			'column' => 'supplierVendorCode',
			'search' => $supplierVendorCode,
		];
		$arJson = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'supplierID' => $this->getSupplierId(),
				'filter' => [
					'find' => [$filter],
					'order' => ['column' => 'createdAt', 'order' => 'asc'],
				],
				'query' => [
					'limit' => 10,
					'offset' => 0,
				],
			],
		];
		$arRequest = [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
			'ELEMENT_ID' => $intElementId,
		];
		$queryResult = $this->API->execute('/card/list', $arJson, $arRequest);
		if(is_array($queryResult) && is_array($queryResult['result']['cards']) && !empty($queryResult['result']['cards'])){
			foreach($queryResult['result']['cards'] as $card){
				if($card['supplierVendorCode'] == $supplierVendorCode){
					$result = $card;
				}
			}
		}
		return $result;
	}

	/**
	 * Get card by imtId (supplierVendorCode)
	 * @param mixed $imtID
	 */
	protected function getCardByImtId($imtID){
		$result = false;
		$filter = [];
		$filter = [
			'column' => 'supplierVendorCode',
			'search' => strVal($supplierVendorCode),
		];
		$arJson = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'imtID' => $imtID,
				'supplierID' => $this->getSupplierId(),
			],
		];
		$arRequest = [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
		];
		return $this->API->execute('/card/cardByImtID', $arJson, $arRequest);
	}

	/**
	 * Get card list
	 * @param mixed $imtID
	 */
	protected function getCardList($intLimit=100, $intOffset=0, $arFilter=[]){
		$result = false;
		$arJson = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'supplierID' => $this->getSupplierId(),
				'filter' => [
					'order' => ['column' => 'createdAt', 'order' => 'desc'],
				],
				'query' => [
					'limit' => $intLimit,
					'offset' => $intOffset,
				],
			],
		];
		if(!empty($arFilter)){
			$arJson['params']['filter']['find'] = [$arFilter];
		}
		$arRequest = [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
		];
		return $this->API->execute('/card/list', $arJson, $arRequest);
	}

	/**
	 * Clear values
	 * @param $supplierVendorCode
	 */
	// protected function clearExternalForSupplierVendorCode($supplierVendorCode){
	// 	$arRemove = [];
	// 	$arExternalId = [
	// 		'PROFILE_ID' => $this->intProfileId,
	// 		'IBLOCK_ID' => null,
	// 		'SEARCH_VALUE' => $supplierVendorCode,
	// 		'MODE' => ExternalId::EXT_MODE_EXTERNAL_VALUE,
	// 	];
	// 	if($arExistExternalId = Helper::call($this->strModuleId, 'ExternalId', 'getExt', array_values($arExternalId))){
	// 		$arRemove[] = $arExistExternalId['ELEMENT_ID'];
	// 		if($arCatalog = Helper::getCatalogArray($arExistExternalId['IBLOCK_ID'])){
	// 			if($arCatalog['OFFERS_IBLOCK_ID']){
	// 				$arFilter = [
	// 					'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
	// 					'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $arExistExternalId['ELEMENT_ID'],
	// 				];
	// 				$resOffers = \CIBlockElement::getList([], $arFilter, false, false, ['ID']);
	// 				while($arOffer = $resOffers->fetch()){
	// 					$arRemove[] = $arOffer['ID'];
	// 				}
	// 			}
	// 		}
	// 	}
	// 	if(!empty($arRemove)){
	// 		$arQuery = [
	// 			'filter' => [
	// 				'ELEMENT_ID' => $arRemove,
	// 			],
	// 			'select' => [
	// 				'ID',
	// 			],
	// 		];
	// 		$resExternals = Helper::call($this->strModuleId, 'ExternalId', 'getList', [$arQuery]);
	// 		while($arExternal = $resExternals->fetch()){
	// 			Helper::call($this->strModuleId, 'ExternalId', 'update', [$arExternal['ID'], [
	// 				'EXTERNAL_ID' => false,
	// 				'EXTERNAL_DATA' => false,
	// 			]]);
	// 		}
	// 	}
	// }
	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		return $this->getExtFileOpenLink('https://suppliers-portal.wildberries.ru/goods/products-card/', 
			Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}

	/**
	 * @param mixed $intProfileID
	 * @param mixed $arData
	 */
	public function stepPauseBeforeStocks($intProfileID, $arData){
		#sleep(10);
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 * @param mixed $intProfileID
	 * @param mixed $arData
	 */
	public function stepExportStocks($intProfileID, $arData){
		$intTaskId = $arData['SESSION']['EXPORT']['TASK_ID'];
		# Prepare stocks
		$arStocks = [];
		$arPrices = [];
		$this->intExportPerStep = 1000; // Prices and stocks exporting by 1000 per time
		# Set all profiles product as non-exported: 
		Helper::call($this->strModuleId, 'ExportData', 'setAllDataItemsNotExported', [$intProfileID]);
		while(true){
			$arExportItems = $this->getExportDataItems(null, ['ID', 'ELEMENT_ID', 'DATA_MORE', '_SKIP_DATA_FIELD']);
			if(!is_array($arExportItems) || empty($arExportItems)){
				break;
			}
			foreach($arExportItems as $arItem){
				if($arDataMore = unserialize($arItem['DATA_MORE'])){
					# Collect stocks
					if(is_array($arDataMore['STOCKS']) && !empty($arDataMore['STOCKS'])){
						$arStocks = array_merge($arStocks, $arDataMore['STOCKS']);
					}
					# Collect prices
					if(Helper::strlen($arDataMore['supplierVendorCode'])){
						if(is_array($arDataMore['PRICES']) && !empty($arDataMore['PRICES'])){
							if($arCard = $this->getCardBySupplierVendorCode($arDataMore['supplierVendorCode'])){
								if(is_array($arCard[static::KEY_N]) && !empty($arCard[static::KEY_N])){
									foreach($arDataMore['PRICES'] as $arPrice){
										if(Helper::strlen($arPrice['vendorCode']) && Helper::strlen($arPrice['price'])){
											foreach($arCard[static::KEY_N] as $arRemoteNomenclature){
												if($arRemoteNomenclature['vendorCode'] == $arPrice['vendorCode']){
													$intNmId = $arRemoteNomenclature['nmId'];
													$arPrices[] = [
														'nmId' => $intNmId,
														'price' => intVal($arPrice['price']),
													];
													break;
												}
											}
										}
									}
								}
							}
						}
					}
				}
				$this->setDataItemExported($arItem['ID']);
			}
			# Export prepared stocks
			if(!empty($arStocks)){
				$intStocksCount = count($arStocks);
				$arSendStocks = $arStocks;
				$arParams = [
					'METHOD' => 'POST',
					'HEADER' => [
						'Content-Type' => 'application/json',
						'Authorization' => $this->getAuthToken($strAuthToken),
					],
					'TIMEOUT' => 30,
					'HOST' => 'https://suppliers-api.wildberries.ru',
				];
				$strUrl = '/api/v2/stocks';
				$arJsonResponse = $this->API->execute($strUrl, $arSendStocks, $arParams);
				$intResponseCode = $this->API->getResponseCode();
				if(is_array($arJsonResponse)){
					$bSuccess = $intResponseCode == 200 && !$arJsonResponse['error'];
					// # Save general stocks info to task
					// # Update 2022-06-07: since this placed into while(true), this code does not work
					// if($intTaskId){
					// 	Task::update($intTaskId, [
					// 		'STOCKS_REQUEST' => Json::encode($arSendStocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
					// 		'STOCKS_RESPONSE' => Json::encode($arJsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
					// 		'STOCKS_RESPONSE_CODE' => $intResponseCode,
					// 	]);
					// }
					# Save info about stocks for each product
					if($bSuccess){
						$this->addToLog(static::getMessage('LOG_STOCKS_EXPORTED', [
							'#COUNT#' => $intStocksCount,
						]), true);
						# Save stocks to history
						// if($intTaskId && $this->isHistoryStockSave()){
						// 	foreach($arStocks as $arStock){
						// 		HistoryStock::add([
						// 			'MODULE_ID' => $this->strModuleId,
						// 			'PROFILE_ID' => $this->intProfileId,
						// 			'TASK_ID' => $intTaskId,
						// 			'NM_ID' => $arStockItem['nmId'],
						// 			'CHRT_ID' => $arStock['chrtId'],
						// 			'PRICE' => $arStock['price'],
						// 			'QUANTITY' => $arStock['quantity'],
						// 			'STORE_ID' => $arStock['storeId'],
						// 			'SUCCESS' => $bSuccess ? 'Y' : 'N',
						// 			'SESSION_ID' => session_id(),
						// 			'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
						// 		]);
						// 	}
						// }
					}
					else{
						$strMessage = static::getMessage('LOG_STOCKS_ERROR', [
							'#ERROR#' => $arJsonResponse['errorText'],
							'#STOCKS#' => print_r($arStocks, true),
							'#RESPONSE_CODE#' => $this->API->getResponseCode(true),
							'#HEADERS#' => print_r($this->API->getHeaders(), true),
							'#CONTENT#' => print_r($arJsonResponse, true),
						]);
						$this->addToLog($strMessage);
					}
				}
			}
			# Export prepared prices
			if(!empty($arPrices)){
				$intPricesCount = count($arPrices);
				$arSendPrices = $arPrices;
				$arParams = [
					'METHOD' => 'POST',
					'HEADER' => [
						'Content-Type' => 'application/json',
						'Authorization' => $this->getAuthToken($strAuthToken),
					],
					'TIMEOUT' => 30,
					'HOST' => 'https://suppliers-api.wildberries.ru',
				];
				$strUrl = '/public/api/v1/prices';
				$arJsonResponse = $this->API->execute($strUrl, $arSendPrices, $arParams);
				if(is_array($arJsonResponse)){
					$intResponseCode = $this->API->getResponseCode();
					$bSuccess = $intResponseCode == 200 && !$arJsonResponse['error'];
					# Save info about prices for each product
					if($bSuccess){
						$this->addToLog(static::getMessage('LOG_PRICES_EXPORTED', [
							'#COUNT#' => $intPricesCount,
						]), true);
					}
					else{
						$strMessage = static::getMessage('LOG_PRICES_ERROR', [
							'#ERROR#' => $arJsonResponse['errorText'],
							'#PRICES#' => print_r($arPrices, true),
							'#RESPONSE_CODE#' => $this->API->getResponseCode(true),
							'#HEADERS#' => print_r($this->API->getHeaders(), true),
							'#CONTENT#' => print_r($arJsonResponse, true),
						]);
						$this->addToLog($strMessage);
					}
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 
	 */
	protected function getImtIdBySupplierVendorCode($strSupplierVendorCode){
		if($arProduct = $this->getCardBySupplierVendorCode($strSupplierVendorCode)){
			return $arProduct['imtId'];
		}
		return false;
	}


	// OLD //
	protected function isOffersNewMode(){

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

}

?>