<?
/**
 * Acrit Core: SberMegaMarket
 * @documentation https://conf.goods.ru/merchant-api/2-opisanie-api-standartnoj-shemy/2-4-obnovlenie-tsen-po-api
 * @documentation https://conf.goods.ru/merchant-api/2-opisanie-api-standartnoj-shemy/2-3-obnovlenie-ostatkov-po-api
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Plugins\SberMegaMarketHelpers\Api,
	\Acrit\Core\Export\Plugins\SberMegaMarketHelpers\Response;

class SberMegaMarketRuStandart extends SberMegaMarketRu {
	
	const DATE_UPDATED = '2023-02-07';

	protected static $bSubclass = true;
	
	# General
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $bApi = true;
	
	# API class
	protected $API;
	
	/**
	 *	Include own classes
	 */
	public function includeClasses(){
		require_once __DIR__.'/../../classes/api.php';
		require_once __DIR__.'/../../classes/response.php';
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$this->API = new Api($this->intProfileId, $this->strModuleId, $this->getAuthToken(), $this->isTestEnvironment());
		}
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['offerId'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['HEADER_STOCKS'] = [];
		$arResult['quantity'] = ['FIELD' => 'CATALOG_STORE_AMOUNT_1'];
		$arResult['HEADER_PRICES'] = [];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true];
		$arResult['isDeleted'] = ['CONST' => 'false'];
		return $arResult;
	}

	/**
	 * Add settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['TEST_ENVIRONMENT'] = [
			'HTML' => $this->includeHtml(__DIR__.'/../../include/settings/test_environment.php'),
			'SORT' => 100,
		];
		$arSettings['AUTH_TOKEN'] = [
			'HTML' => $this->includeHtml(__DIR__.'/../../include/settings/auth_token.php'),
			'SORT' => 110,
		];
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$arDataMore['OFFER_ID'] = strVal($arItem['offerId']);
		# Move price to DATA_MORE
		if(isset($arItem['price'])){
			if(is_numeric($arItem['price']) && $arItem['price'] >= 0){
				$arDataMore['PRICE'] = [
					'price' => intVal($arItem['price']),
					'isDeleted' => $arItem['isDeleted'] == 'true' ? true : false,
				];
			}
			unset($arItem['price']);
		}
		# Move stocks to DATA_MORE
		if(isset($arItem['quantity'])){
			if(is_numeric($arItem['quantity']) && $arItem['quantity'] >= 0){
				$arDataMore['STOCK'] = [
					'quantity' => intVal($arItem['quantity']),
				];
			}
			unset($arItem['quantity'], $arItem['isDeleted']);
		}
		unset($arItem['offerId']);
	}
	
	/**
	 *	Export data by API
	 *	Nothins because price and stocks are exporting separately (see stepProcessStocks and stepProcessPrices)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Add custom steps
	 */
	protected function onUpGetSteps(&$arSteps){
		$arSteps['SMM_PROCESS_PRICES'] = [
			'NAME' => static::getMessage('STEP_PROCESS_PRICES'),
			'SORT' => 5010,
			'FUNC' => [$this, 'stepProcessPrices'],
		];
		$arSteps['SMM_PROCESS_STOCKS'] = [
			'NAME' => static::getMessage('STEP_PROCESS_STOCKS'),
			'SORT' => 5020,
			'FUNC' => [$this, 'stepProcessStocks'],
		];
	}
	
	/**
	 * Export prices
	 */
	public function stepProcessPrices($intProfileId, &$arData){
		if(!$arData['SESSION']['PRICES_START']){
			Helper::call($this->strModuleId, 'ExportData', 'setAllDataItemsNotExported', [$intProfileId]);
			$arData['SESSION']['PRICES_START'] = true;
		}
		$bCron = $arData['IS_CRON'];
		$this->intExportPerStep = 300;
		while($arExportItems = $this->getExportDataItems()){
			$arPrices = [];
			foreach($arExportItems as $arItem){
				$arDataMore = unserialize($arItem['DATA_MORE']);
				$arPrice = $arDataMore['PRICE'];
				if(is_array($arPrice)){
					$arPrice = array_merge(['offerId' => $arDataMore['OFFER_ID']], $arPrice);
				}
				if(is_numeric($arPrice['price']) && $arPrice['price'] >= 0){
					$arPrices[] = $arPrice;
				}
				$this->setDataItemExported($arItem['ID']);
			}
			if(!empty($arPrices)){
				$arPrices = ['prices' => $arPrices];
				$obResponse = $this->API->execute('/manualPrice/save', $arPrices);
				$arResponse = $obResponse->getJsonResult();
				$this->addToLog([
					'Status' => $obResponse->getStatus(),
					'Request' => $arPrices,
					'Response' => $arResponse,
				], true);
				if($arResponse['success']){
					$arResponse['data']['warnings'] = array_map(function($arItem){
						return sprintf('[%s] %s', $arItem['offerId'], $arItem['warning']);
					}, $arResponse['data']['warnings']);
					$strMessage = static::getMessage('LOG_PRICES_SUCCESS', [
						'#COUNT#' => count($arPrices['prices']),
						'#WARNINGS#' => !empty($arResponse['data']['warnings']) ? implode(", ", $arResponse['data']['warnings'])
							: toLower(Helper::getMessage('MAIN_NO')),
					]);
					$this->addToLog($strMessage, true);
					$this->addToLog($arPrices, true);
					if(!$bCron){
						return Exporter::RESULT_CONTINUE;
					}
				}
				else{
					$strMessage = static::getMessage('LOG_PRICES_ERROR', [
						'#RESPONSE#' => $obResponse->getResponse(),
						'#RESPONSE_CODE#' => $obResponse->getStatus(),
						'#RESPONSE_HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
						'#REQUEST#' => $obResponse->getRequest(),
						'#REQUEST_URL#' => $obResponse->getRequestUrl(),
					]);
					$this->addToLog($strMessage);
					$strErrorMessage = static::getMessage('LOG_PRICES_ERROR_TITLE');
					if(!\Acrit\Core\Cli::isCli()){
						$strErrorMessage = Helper::showError($strErrorMessage, $obResponse->getResponse(), true);
					}
					print $strErrorMessage.PHP_EOL;
					return Exporter::RESULT_ERROR;
				}
			}
			if(!$bCron){
				break;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 * Export stocks
	 */
	public function stepProcessStocks($intProfileId, &$arData){
		if(!$arData['SESSION']['STOCKS_START']){
			Helper::call($this->strModuleId, 'ExportData', 'setAllDataItemsNotExported', [$intProfileId]);
			$arData['SESSION']['STOCKS_START'] = true;
		}
		$bCron = $arData['IS_CRON'];
		$this->intExportPerStep = 300;
		while($arExportItems = $this->getExportDataItems()){
			$arStocks = [];
			foreach($arExportItems as $arItem){
				$arDataMore = unserialize($arItem['DATA_MORE']);
				$arStock = $arDataMore['STOCK'];
				if(is_array($arStock)){
					$arStock = array_merge(['offerId' => $arDataMore['OFFER_ID']], $arStock);
				}
				if(is_numeric($arStock['quantity']) && $arStock['quantity'] >= 0){
					$arStocks[] = $arStock;
				}
				$this->setDataItemExported($arItem['ID']);
			}
			if(!empty($arStocks)){
				$arStocks = ['stocks' => $arStocks];
				$obResponse = $this->API->execute('/stock/update', $arStocks);
				$arResponse = $obResponse->getJsonResult();
				if($arResponse['success']){
					$strMessage = static::getMessage('LOG_STOCKS_SUCCESS', [
						'#COUNT#' => count($arStocks['stocks']),
						'#WARNINGS#' => !empty($arResponse['data']['warnings']) ? implode(", ", $arResponse['data']['warnings'])
							: toLower(Helper::getMessage('MAIN_NO')),
					]);
					$this->addToLog($strMessage, true);
					$this->addToLog($arStocks, true);
					if(!$bCron){
						return Exporter::RESULT_CONTINUE;
					}
				}
				else{
					$strMessage = static::getMessage('LOG_STOCKS_ERROR', [
						'#RESPONSE#' => $obResponse->getResponse(),
						'#RESPONSE_CODE#' => $obResponse->getStatus(),
						'#RESPONSE_HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
						'#REQUEST#' => $obResponse->getRequest(),
						'#REQUEST_URL#' => $obResponse->getRequestUrl(),
					]);
					$this->addToLog($strMessage);
					$strErrorMessage = static::getMessage('LOG_STOCKS_ERROR_TITLE');
					if(!\Acrit\Core\Cli::isCli()){
						$strErrorMessage = Helper::showError($strErrorMessage, $obResponse->getResponse(), true);
					}
					print $strErrorMessage.PHP_EOL;
					return Exporter::RESULT_ERROR;
				}
			}
			if(!$bCron){
				break;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'token_check':
				$this->tokenCheck($arParams, $arJsonResult);
				break;
		}
	}

	/**
	 * Check if token is actual
	 */
	public function tokenCheck($arParams, &$arJsonResult){
		$strAuthToken = $this->getAuthToken($arParams['GET']['auth_token']);
		$bTestEnvironment = $this->isTestEnvironment($arParams['GET']['environment'] != 'prod');
		$arParams = ['AUTH_TOKEN' => $strAuthToken, 'IS_TEST_ENVIRONMENT' => $bTestEnvironment];
		$arPost = ['prices' => []];
		$obResponse = $this->API->execute('/manualPrice/save', $arPost, $arParams);
		$arResponse = $obResponse->getJsonResult();
		$arJsonResult['Request'] = $obResponse->getRequestArray();
		$arJsonResult['RequestUrl'] = $obResponse->getRequestUrl();
		$arJsonResult['Response'] = $arResponse;
		$arJsonResult['Status'] = $obResponse->getStatus();
		$arJsonResult['Token'] = $strAuthToken;
		$arJsonResult['Test'] = $bTestEnvironment;
		$arJsonResult['Success'] = strpos($arResponse['error']['message'], 'Авторизационный токен:') !== 0; # Example: error:{code:0, message:'Авторизационный токен: A44B5022-30C0-48DB-AF48-32156E8F5B3 невалидный.'}
	}

	/**
	 * Get auth token from profile settings
	 */
	protected function getAuthToken($strAuthToken=null){
		return strVal(Helper::strlen($strAuthToken) ? $strAuthToken : $this->arParams['AUTH_TOKEN']);
	}

	/**
	 * Get auth token from profile settings
	 */
	protected function isTestEnvironment($bTestEnvironment=null){
		return is_bool($bTestEnvironment) ? $bTestEnvironment : $this->arParams['ENVIRONMENT'] != 'prod';
	}

	/**
	 * 
	 */
	protected function onUpStepCheck(&$arSession){
		$this->addToLog(static::getMessage('LOG_ENVIRONMENT', [
			'#TYPE#' => static::getMessage('LOG_ENVIRONMENT_'.($this->isTestEnvironment() ? 'TEST' : 'PROD')),
		]), true);
	}

}

?>