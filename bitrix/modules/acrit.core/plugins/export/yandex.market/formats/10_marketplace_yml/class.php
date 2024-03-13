<?
/**
 * Acrit Core: Yandex.Market YML for FBY, FBY+, FBS
 * @documentation https://yandex.ru/support/marketplace/catalog/yml-requirements.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\Plugins\YandexMarketplaceHelpers\StockTable as Stock,
	\Acrit\Core\Export\Plugins\YandexMarketplaceHelpers\StockHistoryTable as StockHistory;

class YandexMarketplaceYml extends UniversalPlugin {
	
	const DATE_UPDATED = '2023-09-14';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'yandex_marketplace.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'UAH', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = true;
	protected $bCurrenciesExport = true;
	protected $bCategoriesList = true;
	protected $strCategoriesUrl = 'http://download.cdn.yandex.net/market/market_categories.xls';
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	protected $arXmlMultiply = ['delivery-options.option@days', 'pickup-options.option@days'];
	
	# Other export settings
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$bNewMode = $this->isNewMode();
		$arResult['HEADER_GENERAL'] = [];
		if($bNewMode){
			$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		}
		else{
			$arResult['shop-sku'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		}
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y'], 'REQUIRED' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		if($bNewMode){
			$arResult['picture'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true, 'MAX_COUNT' => 10];
		}
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER', 'REQUIRED' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		if($bNewMode){
			$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true, 'REQUIRED' => true];
			$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
			$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_YANDEX'];
		}
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true, 'REQUIRED' => true];
		$arResult['count'] = ['FIELD' => ''];
		$arResult['manufacturer'] = ['FIELD' => ['PROPERTY_MANUFACTURER', 'PROPERTY_BRAND']];
		$arResult['country_of_origin'] = ['FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['vendorCode'] = ['FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTNUMBER', 'PROPERTY_ARTICLE'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'], 'PARAMS' => ['HTMLSPECIALCHARS' => 'cdata']];
		$arResult['period-of-validity-days'] = ['CONST' => 'P1Y2M10D'];
		$arResult['comment-validity-days'] = ['CONST' => static::getMessage('DEFAULT_comment-validity-days')];
		$arResult['service-life-days'] = ['CONST' => 'P1Y2M10D'];
		$arResult['comment-life-days'] = ['CONST' => static::getMessage('DEFAULT_comment-life-days')];
		$arResult['warranty-days'] = ['CONST' => 'P1Y2M10D'];
		$arResult['comment-warranty'] = ['CONST' => static::getMessage('DEFAULT_comment-warranty')];
		$arResult['certificate'] = ['CONST' => ''];
		$arResult['dimensions'] = ['CONST' => ['{=catalog.CATALOG_LENGTH} / 10', '{=catalog.CATALOG_WIDTH} / 10', '{=catalog.CATALOG_HEIGHT} / 10'], 'CONST_PARAMS' => ['MATH' => 'Y'], 'PARAMS' => ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'slash'], 'REQUIRED' => true];
		$arResult['weight'] = ['CONST' => '{=catalog.CATALOG_WEIGHT} / 1000', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		//$arResult['tn-ved-code'] = ['CONST' => ''/* , 'MULTIPLE' => true */];
		$arResult['tn-ved-codes.tn-ved-code'] = ['CONST' => ['', ''], 'MULTIPLE' => true];
		$arResult['cpa'] = ['CONST' => '1'];
		$arResult['adult'] = ['CONST' => ''];
		$arResult['age'] = ['CONST' => ''];
		$arResult['age@unit'] = ['CONST' => ''];
		$arResult['condition@type'] = ['CONST' => ''];
		$arResult['condition.quality'] = ['CONST' => ''];
		$arResult['condition.reason'] = ['CONST' => ''];
		
		#
		$arResult['HEADER_ADDITIONAL'] = [];
		$arResult['disabled'] = ['CONST' => ''];
		$arResult['available'] = ['CONST' => ''];
		$arResult['min-quantity'] = ['CONST' => ''];
		$arResult['step-quantity'] = ['CONST' => ''];
		$arResult['sales-notes'] = ['CONST' => ''];
		$arResult['store'] = ['CONST' => ''];
		$arResult['delivery'] = ['CONST' => ''];
		$arResult['delivery-options.option@cost'] = ['CONST' => '', 'MULTIPLE' => true];
		$arResult['delivery-options.option@days'] = ['CONST' => '', 'MULTIPLE' => true];
		$arResult['delivery-options.option@order-before'] = ['CONST' => '', 'MULTIPLE' => true];
		$arResult['pickup'] = ['CONST' => ''];
		$arResult['pickup-options.option@cost'] = ['CONST' => '', 'MULTIPLE' => true];
		$arResult['pickup-options.option@days'] = ['CONST' => '', 'MULTIPLE' => true];
		$arResult['pickup-options.option@order-before'] = ['CONST' => '', 'MULTIPLE' => true];
		
		#
		$arResult['HEADER_STORES'] = [];
		$arResult['market-sku'] = [];
		$arResult['availability'] = [
			'TYPE' => 'CONDITION',
			'CONDITIONS' => $this->getFieldFilter($intIBlockID, [
				'FIELD' => 'CATALOG_QUANTITY',
				'LOGIC' => 'MORE',
				'VALUE' => '0',
			]),
			'DEFAULT_VALUE' => [
				[
					'TYPE' => 'CONST',
					'CONST' => 'ACTIVE',
					'SUFFIX' => 'Y',
				],
				[
					'TYPE' => 'CONST',
					'CONST' => 'INACTIVE',
					'SUFFIX' => 'N',
				],
			],
		];
		$arResult['transport-unit'] = ['CONST' => '2'];
		$arResult['min-delivery-pieces'] = ['CONST' => '10'];
		$arResult['quantum'] = ['CONST' => '12'];
		$arResult['leadtime'] = ['CONST' => '1'];
		$arResult['box-count'] = ['CONST' => '2'];
		$arResult['delivery-weekdays.delivery-weekday'] = ['CONST' => ['MONDAY', 'FRIDAY'], 'MULTIPLE' => true];
		#
		if($this->useStores()){
			if($arStores = $this->getStores()){
				foreach($arStores as $intStoreId => $strStoreName){
					$arResult['HEADER_STOCK_'.$intStoreId] = [
						'NAME' => static::getMessage('HEADER_STOCK', ['#ID#' => $intStoreId, '#NAME#' => $strStoreName]),
					];
					$arResult['stock_'.$intStoreId.'_count'] = [
						'NAME' => static::getMessage('STOCK_COUNT'),
						'DISPLAY_CODE' => 'count',
						'CONST' => '1',
					];
					$arResult['stock_'.$intStoreId.'_type'] = [
						'NAME' => static::getMessage('STOCK_TYPE'),
						'DISPLAY_CODE' => 'type',
						'CONST' => 'FIT',
					];
					$arResult['stock_'.$intStoreId.'_updatedAt'] = [
						'NAME' => static::getMessage('STOCK_UPDATED_AT'),
						'DISPLAY_CODE' => 'updatedAt',
						'FIELD' => 'TIMESTAMP_X',
						'FIELD_PARAMS' => [
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::DATE_FORMAT,
						],
					];
					$arStockAndPriceAllowedFields[] = 'stock_'.$intStoreId;
				}
			}
		}
		# Make some fields not required
		if($arIBlockParams = $this->getIBlockParams($intIBlockID)){
			if(Helper::strlen($arIBlockParams['YANDEX_MARKET_NOT_REQUIRED_FIELDS'])){
				if(!empty($arValues = Helper::explodeValues($arIBlockParams['YANDEX_MARKET_NOT_REQUIRED_FIELDS']))){
					foreach($arValues as $strField){
						if(is_array($arResult[$strField]) && $arResult[$strField]['REQUIRED']){
							unset($arResult[$strField]['REQUIRED']);
						}
					}
				}
			}
		}
		#
		return $arResult;
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		require_once __DIR__.'/include/classes/stock.php';
		require_once __DIR__.'/include/classes/stockhistory.php';
		require_once __DIR__.'/include/db_table_create.php';
	}

	/**
	 * Add settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['ASSORTMENT_MODE'] = require __DIR__.'/include/settings/assortment_mode.php';
		$arSettings['SHOP_NAME'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/shop_name.php'),
			'SORT' => 150,
		];
		$arSettings['SHOP_COMPANY'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/shop_company.php'),
			'SORT' => 151,
		];
		$arSettings['SHOP_CPA'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/shop_cpa.php'),
			'SORT' => 152,
		];
		$arSettings['EXPORT_STOCKS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_stocks.php'),
			'SORT' => 158,
		];
		$arSettings['EXPORT_NEW_DATE_FORMAT'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_new_date_format.php'),
			'SORT' => 159,
		];
		$arSettings['EXTERNAL_REQUEST'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/external_request.php'),
			'SORT' => 160,
		];
		$arSettings['YANDEX_MARKET_PARTNER'] = [
			'HTML' => \Acrit\Core\Export\Plugins\YandexMarket::showDefaultSettingsYandexMarketPartner(false),
			'FULL' => true,
			'SORT' => 10000,
		];
	}

	/**
	 * Check current mode
	 */
	protected function isNewMode(){
		return $this->arParams['ASSORTMENT_MODE'] == 'new';
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<name>#SHOP_NAME#</name>'.static::EOL;
		$strXml .= '		<company>#SHOP_COMPANY#</company>'.static::EOL;
		$strXml .= '		<url>#SHOP_URL#</url>'.static::EOL;
		$strXml .= '		<cpa>#SHOP_CPA#</cpa>'.static::EOL;
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		$date = date('Y-m-d H:i');
		if ($this->useNewDateFormat())
			$date = date('c');
		# Replace macros
		$arReplace = [			
			'#XML_DATE#' => $date,
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			#
			'#SHOP_NAME#' => $this->output(($this->arParams['SHOP_NAME'])),
			'#SHOP_COMPANY#' => $this->output(($this->arParams['SHOP_COMPANY'])),
			'#SHOP_URL#' => $this->output(Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y')),
			'#SHOP_CPA#' => in_array($this->arParams['SHOP_CPA'], ['0', '1']) ? $this->arParams['SHOP_CPA'] : '1',
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

	/**
	 * Send stocks data?
	 */
	protected function useStores(){
		return $this->arParams['EXPORT_STOCKS'] == 'Y';
	}
	
	/**
	 * use new date format?
	 */
	protected function useNewDateFormat(){
		return $this->arParams['EXPORT_NEW_DATE_FORMAT'] != 'Y';
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
	 *	Handler on generate XML for single item
	 */
	protected function onUpBuildXml(&$arXmlTags, &$arXmlAttr, &$strXmlItem, &$arElement, &$arFields, &$arElementSections, &$mDataMore){
		if($arFields['oldprice'] <= $arFields['price']){
			unset($arXmlTags['oldprice'], $arFields['oldprice']);
		}
		$mDataMore = [
			'SKU_ID' =>  $this->isNewMode() ? $arFields['@id'] : $arFields['shop-sku'],
		];
		if($this->useStores()){
			$arStocks = [];
			foreach($arFields as $field => $value){
				if(preg_match('#^stock_(\d+)_([A-z0-9-_]+)$#', $field, $arMatch)){
					$arStocks[$arMatch[1]][$arMatch[2]] = $value;
					unset($arFields[$field]);
					unset($arXmlTags[$field]);
				}
			}
			if(!empty($arStocks)){
				$mDataMore['STOCKS'] = $arStocks;
			}
			unset($arStocks);
		}
	}
	
	/**
	 *	Add custom step
	 */
	protected function onUpGetSteps(&$arSteps){
		if($this->useStores()){
			$arSteps['YM_PROCESS_STOCKS'] = [
				'NAME' => static::getMessage('STEP_PROCESS_STOCKS'),
				'SORT' => 5010,
				'FUNC' => [$this, 'stepProcessStocks'],
			];
			$arSteps['YM_RESET_OLD_STOCKS'] = [
				'NAME' => static::getMessage('STEP_RESET_OLD_STOCKS'),
				'SORT' => 5020,
				'FUNC' => [$this, 'stepResetOldStocks'],
			];
		}
	}
	
	/**
	 * Save stocks to DB
	 */
	public function stepProcessStocks($intProfileID, $arData){
		$this->intExportPerStep = 100000;
		$arExportItems = $this->getExportDataItems(null, ['ID', 'ELEMENT_ID', 'DATA_MORE', '_SKIP_DATA_FIELD'], true);
		foreach($arExportItems as $arItem){
			$arDataMore = unserialize($arItem['DATA_MORE']);
			if(Helper::strlen($arDataMore['SKU_ID']) && !empty($arDataMore['STOCKS'])){
				foreach($arDataMore['STOCKS'] as $intWarehouseId => $arStockData){
					$arStock = [
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'ELEMENT_ID' => $arItem['ELEMENT_ID'],
						'SKU' => $arDataMore['SKU_ID'],
						'WAREHOUSE_ID' => $intWarehouseId,
						'TYPE' => $arStockData['type'],
						'COUNT' => $arStockData['count'],
						'UPDATED_AT' => $arStockData['updatedAt'],
						'SESSION_ID' => $arData['SESSION']['SESSION_ID'],
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
						$obResult = Stock::update($arDbStock['ID'], $arStock);
					}
					else{
						$obResult = Stock::add($arStock);
					}
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 * Reset old stocks to 0
	 */
	public function stepResetOldStocks($intProfileID, $arData){
		$arQuery = [
			'filter' => [
				'=MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'!=SESSION_ID' => $arData['SESSION']['SESSION_ID'],
			],
			'select' => ['ID'],
		];
		$obDate = new \Bitrix\Main\Type\Datetime;
		$strUpdatedAt = $obDate->format(static::DATE_FORMAT);
		$resOldStocks = Stock::getList($arQuery);
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

	/**
	 * Get SKU data for selected warehouse
	 * Example: $arJson = $this->getWarehouseSkuData(1, '48286');
	 */
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

	/**
	 * Direct execute plugin/profile from ProfileTable
	 */
	public function execPlugin($arParams=[]){
		Json::prepare();
		$arResult = [];
		if($arJson = $this->getRequestJson()){
			if(is_array($arJson['skus'])){
				$obDate = new \Bitrix\Main\Type\Datetime;
				$arData = $this->getWarehouseSkuData($arJson['warehouseId'], $arJson['skus'], true);
				foreach($arJson['skus'] as $strSku){
					if(isset($arData[$strSku])){
						$arResult[] = $arData[$strSku];
					}
					else{
						$arResult[] = [
							'sku' => strVal($strSku),
							'warehouseId' => strVal($arJson['warehouseId']),
							'items' => [
								[
									'type' => 'FIT',
									'count' => "0",
									'updatedAt' => $obDate->format(static::DATE_FORMAT),
								]
							],
						];
					}
				}
				StockHistory::add([
					'MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'WAREHOUSE_ID' => $arJson['warehouseId'],
					'SKUS_INPUT' => Json::encode($arJson['skus']),
					'SKUS_OUTPUT' => Json::encode($arResult),
					'IP' => $_SERVER['REMOTE_ADDR'],
					'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
				]);
			}
		}
		$arResult = ['skus' => $arResult];
		print Json::output($arResult);
		die();
	}

	protected function getRequestJson(){
		$arJson = null;
		$strJson = file_get_contents('php://input');
		try{
			$arJson = \Bitrix\Main\Web\Json::decode($strJson);
		}catch(\Exception $obError){}
		return $arJson;
	}

	protected function processUpdatedCategories($strTmpFile){
		require_once(realpath(__DIR__.'/../../../../../include/php_excel_reader/excel_reader2.php'));
		$obExcelData = new \Spreadsheet_Excel_Reader($strTmpFile, false);
		$intRowCount = $obExcelData->rowcount();
		#
		$strCategories = '';
		for($intLine=0; $intLine<=$intRowCount; $intLine++) {
			$strCategories .= $obExcelData->val($intLine, 1)."\n";
		}
		@unlink($strTmpFile);
		if(Helper::strlen($strCategories)){
			if(!Helper::isUtf()){
				$strCategories = Helper::convertEncoding($strCategories, 'UTF-8', 'CP1251');
			}
			return $strCategories;
		}
		return false;
	}

	/**
	 * Debug: show sku stocks
	 */
	protected function showStocks($arItemId, $bPrint=true){
		if(!is_array($arItemId)){
			if(($arItemId = intVal($arItemId)) > 0){
				$arItemId = [$arItemId];
			}
		}
		$arQuery = [
			'order' => ['ID' => 'ASC'],
			'filter' => [
				'=MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'=SKU' => $arItemId,
			],
			'select' => ['ID', 'SKU', 'WAREHOUSE_ID', 'TYPE', 'COUNT', 'UPDATED_AT'],
		];
		$arStocks = [];
		$resStocks = Stock::getList($arQuery);
		while($arStock = $resStocks->fetch()){
			$arStocks[$arStock['SKU']] = [
				'sku' => $arStock['SKU'],
				'warehouseId' => intVal($intWarehouseId),
				'items' => [
					[
						'type' => $arStock['TYPE'],
						'count' => intVal($arStock['COUNT']),
						'updatedAt' => $arStock['UPDATED_AT'],
					]
				],
			];
		}
		if($bPrint){
			print_r($arStocks);
		}
		return $arStocks;
	}
	
	/**
	 *	Show custom data at tab 'Log'
	 */
	public function getLogContent(&$strLogCustomTitle, $arGet){
		ob_start();
		require __DIR__.'/include/log_stocks.php';
		return ob_get_clean();
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'refresh_stocks_log':
				$this->refreshStocksLog($arParams, $arJsonResult);
				break;
		}
	}

	/**
	 * Button 'refresh stocks' (tab 'log') clicked
	 */
	protected function refreshStocksLog($arParams, &$arJsonResult){
		$arJsonResult['HTML'] = $this->getLogContent($strLogCustomTitle, $arParams['GET']);
	}
	
	/**
	 *	Show options on general subtab (bottom)
	 */
	public function addHtmlOptionsGeneralBottom($intIBlockID){
		$arIBlockParams = $this->getIBlockParams($intIBlockID);
		?>
		<tr class="heading"><td colspan="2"><?=static::getMessage('GENERAL_SETTINGS_HEADER_PARAM');?></td></tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(static::getMessage('GENERAL_SETTINGS_HINT_NOT_REQUIRED_FIELDS'));?>
				<?=static::getMessage('GENERAL_SETTINGS_NOT_REQUIRED_FIELDS');?>:
			</td>
			<td width="60%">
				<div>
					<input type="text" name="iblockparams[<?=$intIBlockID;?>][YANDEX_MARKET_NOT_REQUIRED_FIELDS]" value="<?=htmlspecialcharsbx($arIBlockParams['YANDEX_MARKET_NOT_REQUIRED_FIELDS']);?>" size="40" />
				</div>
			</td>
		</tr>
		<?
	}

	/**
	 * Show partner info
	 */
	public static function showFormatNotice(){
		return \Acrit\Core\Export\Plugins\YandexMarket::showFormatNotice();
	}

}

?>