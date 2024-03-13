<?
/**
 * Acrit Core: Yandex.Market API
 * @documentation https://yandex.ru/dev/market/partner-marketplace-common/
 * @documentation https://yandex.ru/dev/market/partner-dsbs/doc/dg/concepts/about.html
 * @documentation https://yandex.ru/dev/market/partner-marketplace-cd/doc/dg/concepts/about.html
 * @documentation https://yandex.ru/dev/market/partner-marketplace/doc/dg/concepts/about.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\ExternalIdTable as ExternalId,
	\Acrit\Core\Export\Plugins\YandexMarketApiHelpers\Api,
	\Acrit\Core\Export\Plugins\YandexMarketApiHelpers\Response,
	\Acrit\Core\Export\Plugins\YandexMarketApiHelpers\StockTable as Stock;
	

class YandexMarketApi extends UniversalPlugin {
	
	const DATE_UPDATED = '2023-02-08';
	
	const YANDEX_API_DATE_FORMAT = 'Y-m-d\TH:i:sP';

	const BATCH_DEFAULT = 100;
	const PICTURES_MAX = 10;

	protected static $bSubclass = true;
	
	# Basic settings
	protected $bApi = true;
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];

	# API class
	protected $API;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$bOffer = Helper::isOffersIBlock($intIBlockId);
		$arResult['HEADER_GENERAL'] = [];
		$arResult['shopSku'] = ['REQUIRED' => true, 'FIELD' => 'ID'];
		$arResult['name'] = ['REQUIRED' => true, 'FIELD' => 'NAME'];
		$arResult['category'] = ['FIELD' => 'SECTION__NAME'];
		$arResult['manufacturer'] = ['FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'], 'PARAMS' => ['HTMLSPECIALCHARS' => 'skip']];
		$arResult['manufacturerCountries'] = ['MULTIPLE' => true, 'REQUIRED' => true, 'FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['urls'] = ['MULTIPLE' => true, 'FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['pictures'] = ['MULTIPLE' => true, 'REQUIRED' => true, 'FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO']];
		$arResult['vendor'] = ['FIELD' => ['PROPERTY_BRAND', 'PROPERTY_MANUFACTURER']];
		$arResult['vendorCode'] = ['FIELD' => ['PROPERTY_ARTICLE', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTIKUL', 'PROPERTY_ARTNUMBER']];
		$arResult['barcodes'] = ['MULTIPLE' => true, 'FIELD' => 'CATALOG_BARCODE'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT'];
		$arResult['customsCommodityCodes'] = ['MULTIPLE' => true, 'FIELD' => 'PROPERTY_TNVED'];
		$arResult['certificate'] = [];
		$arResult['transportUnitSize'] = [];
		$arResult['minShipment'] = [];
		$arResult['quantumOfSupply'] = [];
		$arResult['supplyScheduleDays'] = ['MULTIPLE' => true, 'CONST' => ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY']];
		$arResult['deliveryDurationDays'] = ['CONST' => '2'];
		$arResult['boxCount'] = [];
		#
		if($this->arParams['EXPORT_PRODUCTS'] == 'N'){
			unset($arResult['name']['REQUIRED']);
			unset($arResult['manufacturerCountries']['REQUIRED']);
			unset($arResult['pictures']['REQUIRED']);
		}
		#
		$arResult['HEADER_DIMENSIONS'] = [];
		$arResult['weightDimensions.length'] = ['FIELD' => 'CATALOG_LENGTH_CM'];
		$arResult['weightDimensions.width'] = ['FIELD' => 'CATALOG_WIDTH_CM'];
		$arResult['weightDimensions.height'] = ['FIELD' => 'CATALOG_HEIGHT_CM'];
		$arResult['weightDimensions.weight'] = ['FIELD' => 'CATALOG_WEIGHT_KG'];
		#
		$arResult['HEADER_SHELF_LIFE'] = [];
		$arResult['shelfLife.timePeriod'] = [];
		$arResult['shelfLife.timeUnit'] = [];
		$arResult['shelfLife.comment'] = [];
		#
		$arResult['HEADER_LIFE_LIFE'] = [];
		$arResult['lifeTime.timePeriod'] = [];
		$arResult['lifeTime.timeUnit'] = [];
		$arResult['lifeTime.comment'] = [];
		#
		$arResult['HEADER_GUARANTEE_PERIOD'] = [];
		$arResult['guaranteePeriod.timePeriod'] = ['CONST' => '12'];
		$arResult['guaranteePeriod.timeUnit'] = ['CONST' => 'MONTH'];
		$arResult['guaranteePeriod.comment'] = [];
		#
		$arResult['HEADER_MAPPING'] = [];
		$arResult['mapping.marketSku'] = [];
		#
		if($this->usePrices()){
			$arResult['HEADER_BASE_PRICES'] = [];
			// $arResult['baseprice.currencyId'] = ['CONST' => ''];
			$arResult['baseprice.value'] = ['FIELD' => ''];
			$arResult['baseprice.discountBase'] = ['FIELD' => ''];
			#
			$arResult['HEADER_PRICES'] = [];
			// $arResult['price.currencyId'] = ['CONST' => 'RUR'];
			$arResult['price.value'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
			$arResult['price.discountBase'] = ['FIELD' => 'CATALOG_PRICE_1'];
			$arResult['price.vat'] = ['FIELD' => 'VAT_VALUE_YANDEX_API'];
		}
		#
		if($this->useStores()){
			foreach($this->getStores() as $intStoreId => $strStoreName){
				$arResult['HEADER_STOCKS'] = [
					'NAME' => static::getMessage('HEADER_STOCKS', ['#ID#' => $intStoreId, '#NAME#' => $strStoreName]),
				];
				$arResult['stocks.'.$intStoreId.'.type'] = [
					'NAME' => static::getMessage('STOCK_TYPE'),
					'CONST' => 'FIT'
				];
				$arResult['stocks.'.$intStoreId.'.count'] = [
					'NAME' => static::getMessage('STOCK_COUNT'),
					'FIELD' => 'CATALOG_QUANTITY',
				];
				$arResult['stocks.'.$intStoreId.'.updatedAt'] = [
					'NAME' => static::getMessage('STOCK_UPDATET_AT'),
					'CONST' => ''
				];
			}
		}
		#
		return $arResult;
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/response.php';
		require_once __DIR__.'/include/classes/stock.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$strOAuthToken = strVal($this->arParams['OAUTH_TOKEN']);
			$strClientId = strVal($this->arParams['OAUTH_CLIENT_ID']);
			$strCampaignId = strVal($this->arParams['CAMPAIGN_ID']);
			$strBusinessId = strVal($this->arParams['BUSINESS_ID']);
			$this->API = new Api($this->intProfileId, $this->strModuleId, $strCampaignId, $strBusinessId, $strOAuthToken, $strClientId);
		}
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['OAUTH_CLIENT_ID'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/oauth_client_id.php'),
			'SORT' => 100,
		];
		$arSettings['OAUTH_CLIENT_SECRET_ID'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/oauth_client_secret_id.php'),
			'SORT' => 110,
		];
		$arSettings['OAUTH_TOKEN'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/oauth_token.php'),
			'SORT' => 120,
		];
		$arSettings['CAMPAIGN_ID'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/campaign_id.php'),
			'SORT' => 130,
		];
		$arSettings['BUSINESS_ID'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/business_id.php'),
			'SORT' => 135,
		];
		$arSettings['EXPORT_PRODUCTS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_products.php'),
			'SORT' => 140,
		];
		$arSettings['EXPORT_PRICES'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_prices.php'),
			'SORT' => 150,
		];
		$arSettings['EXPORT_STOCKS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_stocks.php'),
			'SORT' => 160,
		];
		$arSettings['BATCH'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/batch.php'),
			'SORT' => 170,
		];
		$arSettings['EXTERNAL_REQUEST'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/external_request.php'),
			'SORT' => 180,
		];
		$arSettings['YANDEX_MARKET_PARTNER'] = [
			'HTML' => \Acrit\Core\Export\Plugins\YandexMarket::showDefaultSettingsYandexMarketPartner(false),
			'FULL' => true,
			'SORT' => 10000,
		];
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'get_oauth_token':
				$this->getOAuthToken($arParams, $arJsonResult);
				break;
			case 'check_campaign_id':
				$this->checkCampaignId($arParams, $arJsonResult);
				break;
			case 'check_business_id':
				$this->checkBusinessId($arParams, $arJsonResult);
				break;
			case 'load_businesses':
				$this->loadBusinesses($arParams, $arJsonResult);
				break;
		}
	}

	/**
	 * Get token for OAuth (using confirm code)
	 */
	public function getOAuthToken($arParams, &$arJsonResult){
		$arJsonResult['Success'] = false;
		$arGet = $arParams['GET'];
		if($arOAuth = $this->API->getOAuthToken($arGet['client_id'], $arGet['client_secret_id'], $arGet['confirm_code'])){
			$arJsonResult['AccessToken'] = $arOAuth['ACCESS_TOKEN'];
			$arJsonResult['RefreshToken'] = $arOAuth['REFRESH_TOKEN'];
			$arJsonResult['TokenType'] = $arOAuth['TOKEN_TYPE'];
			$arJsonResult['ExpiresIn'] = $arOAuth['EXPIRES_IN'];
			$arJsonResult['ExpireTimestamp'] = $arOAuth['EXPIRE_TIMESTAMP'];
			$arJsonResult['Success'] = true;
		}

	}

	/**
	 * Check if campaign_id is actual
	 */
	public function checkCampaignId($arParams, &$arJsonResult){
		$arRequest = [
			'AUTHORIZATION' => sprintf('OAuth oauth_token="%s", oauth_client_id="%s"',
				$arParams['GET']['oauth_token'], $arParams['GET']['client_id']),
			'CAMPAIGN_ID' => $arParams['GET']['campaign_id'],
		];
		$obResponse = $this->API->execute('/v2/campaigns/{campaignId}/stats/orders', ['limit' => 1], $arRequest);
		$bSuccess = $obResponse->getStatus() == 200;
		$arJsonResult['Success'] = $bSuccess;
		$arJsonResult['Url'] = $obResponse->getUrl();
		$arJsonResult['Response'] = $obResponse->getJsonResult();
		if(!$bSuccess){
			$strMessage = static::getMessage('ERROR_CHECK_CAMPAIGN_ID', [
				'#STATUS#' => $obResponse->getStatus(),
				'#MESSAGE#' => $obResponse->getResponse(),
				'#HEADERS#' => $obResponse->getResponseHeaders(),
			]);
			$this->addToLog($strMessage);
		}
	}

	/**
	 * Check if business_id is actual
	 */
	public function checkBusinessId($arParams, &$arJsonResult){
		$arRequest = [
			'AUTHORIZATION' => sprintf('OAuth oauth_token="%s", oauth_client_id="%s"',
				$arParams['GET']['oauth_token'], $arParams['GET']['client_id']),
			'CAMPAIGN_ID' => $arParams['GET']['campaign_id'],
			'BUSINESS_ID' => $arParams['GET']['business_id'],
			'METHOD' => 'GET',
		];
		$obResponse = $this->API->execute('/businesses/{businessId}/warehouses', [], $arRequest);
		$bSuccess = $obResponse->getStatus() == 200;
		$arJsonResult['Success'] = $bSuccess;
		$arJsonResult['Url'] = $obResponse->getUrl();
		$arJsonResult['Request'] = $arRequest;
		$arJsonResult['Response'] = $obResponse->getJsonResult();
		if(!$bSuccess){
			$strMessage = static::getMessage('ERROR_CHECK_CAMPAIGN_ID', [
				'#STATUS#' => $obResponse->getStatus(),
				'#MESSAGE#' => $obResponse->getResponse(),
				'#HEADERS#' => $obResponse->getResponseHeaders(),
			]);
			$this->addToLog($strMessage);
		}
	}

	/**
	 * Load businesss for popup
	 */
	public function loadBusinesses($arParams, &$arJsonResult){
		$obResponse = $this->API->execute('/campaigns', ['limit' => 1000], ['METHOD' => 'GET']);
		$bSuccess = $obResponse->getStatus() == 200;
		$arJsonResult['Success'] = $bSuccess;
		$arJsonResult['Url'] = $obResponse->getUrl();
		$arJsonResult['Response'] = $obResponse->getJsonResult();
		$arJsonResult['Title'] = static::getMessage('POPUP_BUSINESSES_TITLE');
		ob_start();
		$arCampaigns = $arJsonResult['Response']['campaigns'] ?? [];
		require __DIR__.'/include/popup/business.php';
		$arJsonResult['Html'] = ob_get_clean();
		if(!$bSuccess){
			$strMessage = static::getMessage('ERROR_GET_BUSINESSES_POPUP', [
				'#STATUS#' => $obResponse->getStatus(),
				'#MESSAGE#' => $obResponse->getResponse(),
				'#HEADERS#' => $obResponse->getResponseHeaders(),
			]);
			$this->addToLog($strMessage);
		}
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
	 * Send prices data?
	 */
	protected function usePrices(){
		return $this->arParams['EXPORT_PRICES'] == 'Y';
	}
	
	/**
	 *	Add custom step
	 */
	protected function onUpGetSteps(&$arSteps){
		if($this->arParams['EXPORT_STOCKS'] == 'Y'){
			$arSteps['YM_RESET_OLD_STOCKS'] = [
				'NAME' => static::getMessage('STEP_RESET_OLD_STOCKS'),
				'SORT' => 5020,
				'FUNC' => [$this, 'stepResetOldStocks'],
			];
		}
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		# Correct some fields
		$this->correctJsonFields($arItem);
		# Store SKU to more data
		$this->prepareDataSku($arItem, $arDataMore);
		# Store price to more data
		$this->prepareDataPrices($arItem, $arDataMore);
		# Store stocks to more data
		$this->prepareDataStocks($arItem, $arDataMore);
		# Pick out mapping
		$arMapping = $this->prepareFieldsMapping($arItem);
		# Build result array
		$arOffer = $arItem;
		$arItem = [
			'offer' => $arOffer,
		];
		if(!is_null($arMapping)){
			$arItem['mapping'] = $arMapping;
		}
		# Suggestions for preview mode
		if($this->getPreviewMode()){
			$arDataMore['SUGGESTION'] = $this->getOfferSuggestion($arOffer);
		}
	}

	/**
	 * Correct some fields in JSON
	 */
	protected function correctJsonFields(&$arItem){
		$this->removeEmptyFields($arItem);
		#
		if(is_array($arItem['manufacturerCountries'])){
			$arItem['manufacturerCountries'] = array_values($arItem['manufacturerCountries']);
		}
		#
		foreach(['weight', 'length', 'width', 'height'] as $strField){
			if(isset($arItem['weightDimensions'][$strField])){
				$arItem['weightDimensions'][$strField] = floatVal($arItem['weightDimensions'][$strField]);
			}
		}
		#
		foreach(['shelfLife', 'lifeTime', 'guaranteePeriod'] as $strGroup){
			if(isset($arItem[$strGroup]['timePeriod'])){
				$arItem[$strGroup]['timePeriod'] = intVal($arItem[$strGroup]['timePeriod']);
			}
		}
		#
		foreach(['shelfLife', 'lifeTime', 'guaranteePeriod'] as $strGroup){
			if(isset($arItem[$strGroup]['timePeriod']) || isset($arItem[$strGroup]['timePeriod'])){
				if(!in_array($arItem[$strGroup]['timeUnit'], ['HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR'])){
					$arItem[$strGroup]['timeUnit'] = 'MONTH';
				}
			}
		}
		#
		foreach(['minShipment', 'transportUnitSize', 'quantumOfSupply', 'deliveryDurationDays', 'boxCount'] as $strField){
			if(isset($arItem[$strField])){
				$arItem[$strField] = intVal($arItem[$strField]);
			}
		}
		#
		if(is_array($arItem['pictures'])){
			$arItem['pictures'] = array_values($arItem['pictures']); // Prevent exporting pictures as associative array
			if(count($arItem['pictures']) > static::PICTURES_MAX){
				$arItem['pictures'] = array_slice($arItem['pictures'], 0, static::PICTURES_MAX); // Prevent limit exceed
			}
		}
		#
		if(isset($arItem['mapping']['marketSku']) && Helper::strlen($arItem['mapping']['marketSku'])){
			$arItem['mapping']['marketSku'] = intVal($arItem['mapping']['marketSku']);
		}
	}

	protected function prepareDataSku(&$arItem, &$arDataMore){
		$arDataMore['SKU'] = $arItem['shopSku'];
	}

	protected function prepareDataPrices(&$arItem, &$arDataMore){
		# Base price
		if(isset($arItem['baseprice'])){
			if(is_array($arPrice = $arItem['baseprice']) && !empty($arPrice) && isset($arPrice['value']) && Helper::strlen($arPrice['value'])){
				$arPrice['currencyId'] = 'RUR';
				$arPrice['value'] = floatVal($arPrice['value']);
				if(Helper::strlen($arPrice['discountBase'])){
					$arPrice['discountBase'] = floatVal($arPrice['discountBase']);
					if($arPrice['discountBase'] <= $arPrice['value']){
						unset($arPrice['discountBase']);
					}
				}
				$arPrice = [
					'offerId' => $arItem['shopSku'],
					'price' => $arPrice,
				];
				$arDataMore['BASEPRICE'] = $arPrice;
			}
			unset($arItem['baseprice']);
		}
		# Shop price
		if(isset($arItem['price'])){
			if(is_array($arPrice = $arItem['price']) && !empty($arPrice) && isset($arPrice['value']) && Helper::strlen($arPrice['value'])){
				$arPrice['currencyId'] = 'RUR'; // Allowed just RUR
				$arPrice['value'] = floatVal($arPrice['value']);
				if(Helper::strlen($arPrice['discountBase'])){
					$arPrice['discountBase'] = floatVal($arPrice['discountBase']);
					if($arPrice['discountBase'] <= $arPrice['value']){
						unset($arPrice['discountBase']);
					}
				}
				if(Helper::strlen($arPrice['vat'])){
					$arPrice['vat'] = in_array(intVal($arPrice['vat']), [7, 2, 5, 6]) ? intVal($arPrice['vat']) : 6;
				}
				else{
					unset($arPrice['vat']);
				}
				$arPrice = [
					'id' => $arItem['shopSku'], # ToDo: now use offerId: https://yandex.ru/dev/market/partner-api/doc/ru/reference/assortment/updatePrices
					'price' => $arPrice,
				];
				$arDataMore['PRICE'] = $arPrice;
			}
			unset($arItem['price']);
		}
	}

	protected function prepareDataStocks(&$arItem, &$arDataMore){
		if(is_array($arStocks = $arItem['stocks']) && !empty($arStocks)){
			$arResultStocks = [];
			foreach($arStocks as $intWarehouseId => $arStock){
				$arStock = [
					'sku' => $arItem['shopSku'],
					'warehouseId' => intVal($intWarehouseId),
					'items' => [
						[
							'type' => Helper::strlen($arStock['type']) ? $arStock['type'] : 'FIT',
							'count' => intVal($arStock['count']),
							'updatedAt' => Helper::strlen($arStock['updatedAt']) ? $arStock['updatedAt'] : date('c'), // Example: 2017-11-21T00:42:42+03:00
						],
					],
				];
				$arResultStocks[] = $arStock;
			}
			$arDataMore['STOCKS'] = $arResultStocks;
			// $arDataMore['STOCKS_JSON'] = Json::prettyPrint($arResultStocks);
		}
		unset($arItem['stocks']);
	}

	protected function prepareFieldsMapping(&$arItem){
		$arMapping = $arItem['mapping'];
		unset($arItem['mapping']);
		return $arMapping;
	}

	/**
	 * Remove empty values from result json
	 */
	protected function removeEmptyFields(&$arItem){
		$arArrays = ['supplyScheduleDays', 'weightDimensions', 'shelfLife', 'lifeTime', 'guaranteePeriod', 'mapping'];
		foreach($arArrays as $strField){
			if(is_array($arItem[$strField])){
				$arItem[$strField] = array_filter($arItem[$strField], function($item){
					return trim($item) != '';
				});
			}
		}
		foreach($arItem as $strField => $value){
			if($arItem[$strField] === [] || $value === ''){
				unset($arItem[$strField]);
			}
		}
	}

	/**
	 * Get suggestion for single item
	 */
	protected function getOfferSuggestion($arOffer){
		$arSuggestions = $this->getOffersSuggestion([$arOffer]);
		return !empty($arSuggestions) ? reset($arSuggestions) : [];
	}

	/**
	 * Get suggestion for multiple items
	 */
	protected function getOffersSuggestion($arOffers){
		$arResult = [];
		$arJson = [
			'offers' => $arOffers,
		];
		$obResponse = $this->API->execute('/businesses/{businessId}/offer-mappings/suggestions', $arJson);
		$arResponse = $obResponse->getJsonResult();
		if($arResponse['status'] == 'OK'){
			if(is_array($arResponse['result']['offers'])){
				$arResult = $arResponse['result']['offers'];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Cancel save json to file
	 */
	protected function onUpJsonExportItem(&$arItem, &$strJson, &$arSession, &$bWrite){
		$bWrite = false;
	}

	/**
	 * Get batch size (products/prices/stocks per one request)
	 */
	protected function getBatchSize(){
		$intValue = intVal($this->arParams['BATCH']);
		if($intValue <= 0 || $intValue > 500){
			$intValue = static::BATCH_DEFAULT;
		}
		return $intValue;
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
		$intCountAll = $this->getExportDataItemsCount(null, true);
		$intCountQueue = $this->getExportDataItemsCount(null, false);
		$intCountDone = $intCountAll - $intCountQueue;
		$arSession['PERCENT'] = $intCountAll > 0 ? round($intCountDone * 100 / $intCountAll, 1) : 0;
		#
		$this->intExportPerStep = $this->getBatchSize();
		$arExportItems = $this->getExportDataItems();
		if(is_array($arExportItems) && !empty($arExportItems)){
			$result = $this->exportItems($arExportItems);
			if($result === Exporter::RESULT_ERROR){
				return Exporter::RESULT_ERROR;
			}
			foreach($arExportItems as $arExportItem){
				$this->setDataItemExported($arExportItem['ID']);
			}
			if(count($arExportItems) == $this->intExportPerStep){
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 
	 */
	protected function exportItems(array $arItems){
		foreach($arItems as $key => $arItem){
			try{
				$arItems[$key]['DATA_JSON'] = Json::decode($arItem['DATA']);
			}
			catch(\Throwable $obError){
				$strMessage = 'Export item error: wrong JSON: '.$obError->getMessage();
				print Helper::showError(static::getMessage('ERROR_JSON', [
					'#JSON#' => $arItem['DATA'],
					'#ERROR#' => $obError->getMessage(),
				]));
				$this->addToLog($strMessage);
				return Exporter::RESULT_ERROR;
			}
			$arItems[$key]['DATA_MORE'] = unserialize($arItem['DATA_MORE']);
		}
		if($this->exportCards($arItems)){
			if($this->exportBasePrices($arItems)){
				if($this->exportShopPrices($arItems)){
					if($this->exportStocks($arItems)){
						return Exporter::RESULT_SUCCESS;
					} else{$this->addToLog('Error exporting stocks', true);}
				} else{$this->addToLog('Error exporting shop prices', true);}
			} else{$this->addToLog('Error exporting base prices', true);}
		} else{$this->addToLog('Error exporting cards', true);}
		return Exporter::RESULT_ERROR;
	}

	/**
	 * Export cards: add/update
	 */
	protected function exportCards(array $arItems){
		if($this->arParams['EXPORT_PRODUCTS'] == 'N'){
			return true;
		}
		$this->obtainMarketSku($arItems);
		$arJsonItems = array_map(function($arJsonItem){
			# Replace 'shopSku' (from old API) to 'offerId' (from new API)
			$arJsonItem['offer'] = array_merge([
				'offerId' => $arJsonItem['offer']['shopSku'],
			], $arJsonItem['offer']);
			unset($arJsonItem['offer']['shopSku']);
			return $arJsonItem;
		}, array_column($arItems, 'DATA_JSON'));
		$arJson = [
			'offerMappings' => $arJsonItems,
		];
		$strMethod = '/businesses/{businessId}/offer-mappings/update';
		$obResponse = $this->API->execute($strMethod, $arJson);
		#
		$arResponse = $obResponse->getJsonResult();
		$bSuccess = $arResponse['status'] == 'OK';
		#
		$arSkus = array_map(function($arItem){
			return $arItem['offer']['offerId'];
		}, $arJsonItems);
		#
		if($bSuccess){
			$strMessage = static::getMessage('EXPORT_CARDS_SUCCESS', [
				'#SKUS#' => implode(', ', $arSkus),
				'#COUNT#' => count($arSkus),
				'#TEXT#' => Json::prettyPrint($arJson),
			]);
			$this->addToLog($strMessage, true);
		}
		else{
			$strMessage = is_array($arResponse['errors']) && !empty($arResponse['errors'])
				? array_map(function($arError){
						return sprintf('[%s] %s', $arError['code'], $arError['message']);
					}, $arResponse['errors'])
				: var_export($arResponse, true);
			$strMessage = static::getMessage('EXPORT_CARDS_ERROR', [
				'#SKUS#' => implode(', ', $arSkus),
				'#COUNT#' => count($arSkus),
				'#METHOD#' => $strMethod,
				'#TEXT#' => is_array($strMessage) ? implode(', ', $strMessage) : $strMessage,
			]);
			print Helper::showError(static::getMessage('EXPORT_CARDS_ERROR_TITLE'), $strMessage);
			$this->addToLog($strMessage);
		}
		$this->addToLog(Json::prettyPrint($arJson), true);
		$this->addToLog(Json::prettyPrint($arResponse), true);
		return $bSuccess;
	}

	/**
	 * Get market skus (if not set) for each item in batch
	 */
	protected function obtainMarketSku(&$arItems){
		$arOffers = [];
		foreach($arItems as $arItem){
			$arOffer = $arItem['DATA_JSON']['offer'];
			foreach(['urls', 'pictures', 'supplyScheduleDays', 'guaranteePeriod'] as $key){
				unset($arOffer[$key]);	
			}
			if(is_numeric($fPrice = $arItem['DATA_MORE']['PRICE']['price']['value'])){
				$arOffer['price'] = floatVal($fPrice);
			}
			$arOffers[] = $arOffer;
		}
		if(!empty($arOffers)){
			foreach($this->getOffersSuggestion($arOffers) as $arSuggestion){
				if(Helper::strlen($strVendorCode = $arSuggestion['offer']['vendorCode'])){
					foreach($arItems as $key => &$arItem){
						if($arItem['DATA_JSON']['offer']['vendorCode'] == $strVendorCode){
							if(Helper::strlen($arSuggestion['mapping']['marketCategoryName'])){
								if(!Helper::strlen($arItem['DATA_JSON']['offer']['category'])){
									$arItem['DATA_JSON']['offer']['category'] = $arSuggestion['mapping']['marketCategoryName'];
								}
							}
							if(Helper::strlen($arSuggestion['mapping']['marketSku'])){
								if(!Helper::strlen($arItem['DATA_JSON']['mapping']['marketSku'])){
									$arItem['DATA_JSON']['mapping']['marketSku'] = $arSuggestion['mapping']['marketSku'];
								}
							}
							break;
						}
					}
					unset($arItem);
				}
			}
		}
	}

	/**
	 * Export base prices
	 */
	protected function exportBasePrices(array $arItems){
		if($this->arParams['EXPORT_PRICES'] == 'N'){
			return true;
		}
		$bSuccess = false;
		$arDataMore = array_column($arItems, 'DATA_MORE');
		$arPrices = [];
		foreach($arDataMore as $arItem){
			if(is_array($arPrice = $arItem['BASEPRICE'])){
				if(!empty($arPrice) && is_numeric($arPrice['price']['value'])){
					$arPrices[] = $arPrice;
				}
			}
		}
		if(!empty($arPrices)){
			$arJson = [
				'offers' => $arPrices,
			];
			$obResponse = $this->API->execute('/businesses/{businessId}/offer-prices/updates', $arJson);
			#
			$arResponse = $obResponse->getJsonResult();
			$bSuccess = $arResponse['status'] == 'OK';
			#
			$arSkus = array_column($arPrices, 'offerId');
			#
			if($bSuccess){
				$strMessage = static::getMessage('EXPORT_BASE_PRICES_SUCCESS', [
					'#SKUS#' => implode(', ', $arSkus),
					'#COUNT#' => count($arSkus),
					'#TEXT#' => Json::prettyPrint($arJson),
				]);
				$this->addToLog($strMessage, true);
				$this->addToLog(Json::prettyPrint($arJson), true);
				# Delay
				$fDelay = count($arPrices) * (3 / 250); // '3/50' is Yandex limit: 5000 products per minute (60/5000 = 3/250)
				$this->addToLog(static::getMessage('DELAY_LOG', ['#TIME#' => sprintf('%0.2f', $fDelay)]), true);
				usleep($fDelay * 1000000);
			}
			else{
				$strMessage = is_array($arResponse['errors']) && !empty($arResponse['errors'])
					? array_map(function($arError){
							return sprintf('[%s] %s', $arError['code'], $arError['message']);
						}, $arResponse['errors'])
					: print_r($arResponse, true);
					$arResponse['errors'];
				$strMessage = static::getMessage('EXPORT_BASE_PRICES_ERROR', [
					'#SKUS#' => implode(', ', $arSkus),
					'#COUNT#' => count($arSkus),
					'#TEXT#' => implode(', ', $strMessage),
				]);
				print Helper::showError(static::getMessage('EXPORT_BASE_PRICES_ERROR_TITLE'), $strMessage);
				$this->addToLog($strMessage);
				$this->addToLog(Json::prettyPrint($arJson));
			}
		}
		else{
			$bSuccess = true;
		}
		return $bSuccess;
	}

	/**
	 * Export prices
	 */
	protected function exportShopPrices(array $arItems){
		if($this->arParams['EXPORT_PRICES'] == 'N'){
			return true;
		}
		$bSuccess = false;
		$arDataMore = array_column($arItems, 'DATA_MORE');
		$arPrices = [];
		foreach($arDataMore as $arItem){
			if(is_array($arPrice = $arItem['PRICE'])){
				if(!empty($arPrice) && is_numeric($arPrice['price']['value'])){
					$arPrices[] = $arPrice;
				}
			}
		}
		if(!empty($arPrices)){
			$arJson = [
				'offers' => $arPrices,
			];
			$obResponse = $this->API->execute('/v2/campaigns/{campaignId}/offer-prices/updates.json', $arJson);
			#
			$arResponse = $obResponse->getJsonResult();
			$bSuccess = $arResponse['status'] == 'OK';
			#
			$arSkus = array_column($arPrices, 'id');
			#
			if($bSuccess){
				$strMessage = static::getMessage('EXPORT_SHOP_PRICES_SUCCESS', [
					'#SKUS#' => implode(', ', $arSkus),
					'#COUNT#' => count($arSkus),
					'#TEXT#' => Json::prettyPrint($arJson),
				]);
				$this->addToLog($strMessage, true);
				$this->addToLog(Json::prettyPrint($arJson), true);
				# Delay
				$fDelay = count($arPrices) * (3 / 50); // '3/50' is Yandex limit: 1000 products per minute (60/1000 = 3/50)
				$this->addToLog(static::getMessage('DELAY_LOG', ['#TIME#' => sprintf('%0.2f', $fDelay)]), true);
				usleep($fDelay * 1000000);
			}
			else{
				$strMessage = is_array($arResponse['errors']) && !empty($arResponse['errors'])
					? array_map(function($arError){
							return sprintf('[%s] %s', $arError['code'], $arError['message']);
						}, $arResponse['errors'])
					: print_r($arResponse, true);
					$arResponse['errors'];
				$strMessage = static::getMessage('EXPORT_SHOP_PRICES_ERROR', [
					'#SKUS#' => implode(', ', $arSkus),
					'#COUNT#' => count($arSkus),
					'#TEXT#' => implode(', ', $strMessage),
				]);
				print Helper::showError(static::getMessage('EXPORT_SHOP_PRICES_ERROR_TITLE'), $strMessage);
				$this->addToLog($strMessage);
				$this->addToLog(Json::prettyPrint($arJson));
			}
		}
		else{
			$bSuccess = true;
		}
		return $bSuccess;
	}

	/**
	 * Export stocks
	 */
	protected function exportStocks(array $arItems){
		if($this->arParams['EXPORT_STOCKS'] == 'N'){
			return true;
		}
		$bSuccess = false;
		$arDataMore = array_column($arItems, 'DATA_MORE');
		$arElementSku = [];
		$arStocks = [];
		foreach($arItems as $arItem){
			if(is_array($arItem['DATA_MORE']['STOCKS'])){
				foreach($arItem['DATA_MORE']['STOCKS'] as $arStock){
					$arStockElements[$arStock['sku']] = $arItem['ELEMENT_ID'];
					$arStocks[] = $arStock;
				}
			}
		}
		// foreach($arDataMore as $arItem){
		// 	if(is_array($arItem['STOCKS'])){
		// 		foreach($arItem['STOCKS'] as $arStock){
		// 			$arStocks[] = $arStock;
		// 		}
		// 	}
		// }
		if(!empty($arStocks)){
			$arJson = [
				'skus' => $arStocks,
			];
			$obResponse = $this->API->execute('/v2/campaigns/{campaignId}/offers/stocks.json', $arJson,
				['METHOD' => 'PUT']);
			#
			$arResponse = $obResponse->getJsonResult();
			$bSuccess = $arResponse['status'] == 'OK';
			#
			$arSkus = array_column($arStocks, 'sku');
			#
			if($bSuccess){
				$strMessage = static::getMessage('EXPORT_STOCKS_SUCCESS', [
					'#SKUS#' => implode(', ', $arSkus),
					'#COUNT#' => count($arSkus),
					'#TEXT#' => Json::prettyPrint($arJson),
				]);
				$this->addToLog($strMessage, true);
				$this->addToLog(Json::prettyPrint($arJson), true);
				# Save stocks data for external requests
				foreach($arStocks as $arStockData){
					$arStock = [
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'ELEMENT_ID' => $arStockElements[$arStockData['sku']],
						'SKU' => $arStockData['sku'],
						'WAREHOUSE_ID' => $arStockData['warehouseId'],
						'TYPE' => $arStockData['items'][0]['type'],
						'COUNT' => $arStockData['items'][0]['count'],
						'UPDATED_AT' => $arStockData['items'][0]['updatedAt'],
						'SESSION_ID' => $this->arData['SESSION']['SESSION_ID'],
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
					];
					$arFilter = [
						'=MODULE_ID' => $arStock['MODULE_ID'],
						'PROFILE_ID' => $arStock['PROFILE_ID'],
						'=SKU' => $arStock['SKU'],
						'WAREHOUSE_ID' => $arStock['WAREHOUSE_ID'],
					];
					$arQuery = [
						'filter' => $arFilter,
						'select' => ['ID'],
						'limit' => 1,
					];
					if($arDbStock = Stock::getList($arQuery)->fetch()){
						$obResult = Stock::update($arDbStock['ID'] ,$arStock);
					}
					else{
						$obResult = Stock::add($arStock);
					}
				}
				# Delay
				$fDelay = count($arStocks) * (3 / 25); // '3/25' is Yandex limit: 500 products per minute (60/500 = 3/25)
				$this->addToLog(static::getMessage('DELAY_LOG', ['#TIME#' => sprintf('%0.2f', $fDelay)]), true);
				usleep($fDelay * 1000000);
			}
			else{
				$strMessage = is_array($arResponse['errors']) && !empty($arResponse['errors'])
					? array_map(function($arError){
							return sprintf('[%s] %s', $arError['code'], $arError['message']);
						}, $arResponse['errors'])
					: print_r($arResponse, true);
					$arResponse['errors'];
				$strMessage = static::getMessage('EXPORT_STOCKS_ERROR', [
					'#SKUS#' => implode(', ', $arSkus),
					'#COUNT#' => count($arSkus),
					'#TEXT#' => implode(', ', $strMessage),
				]);
				print Helper::showError(static::getMessage('EXPORT_STOCKS_ERROR_TITLE'), $strMessage);
				$this->addToLog($strMessage);
				$this->addToLog(Json::prettyPrint($arJson));
			}
		}
		return $bSuccess;
	}
	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		$strUrl = 'https://partner.market.yandex.ru/business';
		if(isset($this->arParams['BUSINESS_ID']) && is_string($this->arParams['BUSINESS_ID'])){
			if(Helper::strlen($this->arParams['BUSINESS_ID'])){
				$strUrl = sprintf('%s/%s/assortment?activeTab=offers', $strUrl, $this->arParams['BUSINESS_ID']);
			}
		}
		return $this->getExtFileOpenLink($strUrl, Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}

	/**
	 * Show partner info
	 */
	public static function showFormatNotice(){
		return \Acrit\Core\Export\Plugins\YandexMarket::showFormatNotice();
	}

	/**
	 * Direct execute plugin/profile from ProfileTable
	 */
	public function execPlugin($arParams=[]){
		$arJsonOutput = null;
		if(Helper::strpos($arParams['URL'], '/stocks') !== false){
			$this->handler('onUpYaApiStocksBefore', [&$arParams]);
			$arJsonOutput = $this->execPlugin_Stocks($arParams['JSON']);
			$this->handler('onUpYaApiStocksAfter', [&$arJsonOutput, $arParams]);
		}
		elseif(Helper::strpos($arParams['URL'], '/cart') !== false){
			$this->handler('onUpYaApiCartBefore', [&$arParams]);
			$arJsonOutput = $this->execPlugin_Cart($arParams['JSON']);
			$this->handler('onUpYaApiCartAfter', [&$arJsonOutput, $arParams]);
		}
		if(!is_null($arJsonOutput)){
			# Log: start
			$this->addToLog(static::getMessage('EXTERNAL_REQUEST_LOG_REQUEST', [
				'#URL#' => $arParams['URL'],
				'#JSON#' => Json::encode($arParams['JSON']),
			]), true);
			# Log: finish
			$this->addToLog(static::getMessage('EXTERNAL_REQUEST_LOG_RESPONSE', [
				'#JSON#' => Json::encode($arJsonOutput),
			]), true);
			# Output json
			Json::prepare();
			print Json::output($arJsonOutput);
			die();
		}
	}

	protected function execPlugin_Stocks($arJsonInput){
		$arResult = [];
		if(is_array($arJsonInput)){
			if(is_array($arJsonInput['skus']) && !empty($arJsonInput['skus'])){
				$obDate = new \Bitrix\Main\Type\Datetime;
				$arData = $this->getWarehouseSkuData($arJsonInput['warehouseId'], $arJsonInput['skus'], true);
				foreach($arJsonInput['skus'] as $strSku){
					if(isset($arData[$strSku])){
						$arResult[] = $arData[$strSku];
					}
					else{
						$arResult[] = [
							'sku' => strVal($strSku),
							'warehouseId' => strVal($arJsonInput['warehouseId']),
							'items' => [
								[
									'type' => 'FIT',
									'count' => "0",
									'updatedAt' => $obDate->format(static::YANDEX_API_DATE_FORMAT),
								]
							],
						];
					}
				}
				$arResult = ['skus' => $arResult];
			}
		}
		return $arResult;
	}

	public function getWarehouseSkuData($intWarehouseId, $arSku, $bForAllProfiles=false){
		$arQuery = [
			'order' => ['TIMESTAMP_X' => 'DESC'],
			'filter' => [
				'=MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'WAREHOUSE_ID' => $intWarehouseId,
				'=SKU' => $arSku,
			],
			'select' => ['ID', 'SKU', 'WAREHOUSE_ID', 'TYPE', 'COUNT', 'UPDATED_AT'],
		];
		if($bForAllProfiles){
			unset($arQuery['filter']['PROFILE_ID']);
		}
		$arStocks = [];
		$resStocks = Stock::getList($arQuery);
		while($arStock = $resStocks->fetch()){
			$arStocks[$arStock['SKU']] = [
				'sku' => $arStock['SKU'],
				'warehouseId' => strVal($intWarehouseId),
				'items' => [
					[
						'type' => trim($arStock['TYPE']),
						'count' => strVal(intVal($arStock['COUNT'])),
						'updatedAt' => $arStock['UPDATED_AT'],
					]
				],
			];
		}
		return $arStocks;
	}

	protected function execPlugin_Cart($arJsonInput){
		$arResult = [];
		if(is_array($arJsonInput)){
			if(is_array($arJsonInput['cart']['items']) && !empty($arJsonInput['cart']['items'])){
				foreach($arJsonInput['cart']['items'] as $key => $arItem){;
					$arData = $this->getWarehouseSkuData($arItem['warehouseId'], $arItem['offerId'], true);
					$arResult[] = [
						'feedId' => $arItem['feedId'],
						'offerId' => $arItem['offerId'],
						'count' => isset($arData[$arItem['offerId']]['items'][0]['count']) ? intVal($arData[$arItem['offerId']]['items'][0]['count']) : 0,
					];
				}
				$arResult = ['cart' => ['items' => $arResult]];
			}
		}
		return $arResult;
	}
	
	/**
	 * Reset old stocks to 0
	 */
	public function stepResetOldStocks($intProfileID, $arData){
		$obDate = new \Bitrix\Main\Type\Datetime;
		$strUpdatedAt = $obDate->format(static::YANDEX_API_DATE_FORMAT);
		$resOldStocks = Stock::getList([
			'filter' => [
				'=MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'!=SESSION_ID' => $arData['SESSION']['SESSION_ID'],
			],
			'select' => ['ID'],
		]);
		while($arOldStock = $resOldStocks->fetch()){
			Stock::update($arOldStock['ID'], [
				'COUNT' => 0,
				'UPDATED_AT' => $strUpdatedAt,
				'SESSION_ID' => $arData['SESSION']['SESSION_ID'],
				'DATE_RESET' => $obDate,
			]);
		}
		return Exporter::RESULT_SUCCESS;
	}

}

?>