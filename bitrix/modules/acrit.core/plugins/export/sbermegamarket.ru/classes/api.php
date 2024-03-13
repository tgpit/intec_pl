<?
/**
 * Acrit Core: SberMegaMarket
 * @documentation https://openapi.wb.ru/
 */

namespace Acrit\Core\Export\Plugins\SberMegaMarketHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Plugins\SberMegaMarketHelpers\Response;

Helper::loadMessages();

class Api {

	protected const SERVER_URL_PROD = 'https://api.megamarket.tech/api/merchantIntegration/v1/offerService';
	protected const SERVER_URL_TEST = 'https://api-test.megamarket.tech/api/merchantIntegration/v1/offerService';
	// protected const SERVER_URL_PROD = 'https://partner.sbermegamarket.ru/api/merchantIntegration/v1/offerService';
	// protected const SERVER_URL_TEST = 'https://partner.goodsteam.tech/api/merchantIntegration/v1/offerService';

	protected const ATTEMPT_COUNT = 5;

	protected $intProfileId;
	protected $strModuleId;
	protected $arMethods = [];
	protected $strAuthorizationKey = '';
	protected $bTest = true;
	protected $obHttp = null;
	protected $obResult = null;

	public function __construct($intProfileId, $strModuleId, string $strAuthorizationKey, bool $bTest=true){
		$this->intProfileId = $intProfileId;
		$this->strModuleId = $strModuleId;
		$this->strAuthorizationKey = $strAuthorizationKey;
		$this->bTest = $bTest;
		$this->arMethods = $this->getMethods();
	}

	/**
	 * Get base URL address (test or production)
	 */
	public function getApiBaseUrl(){
		return $this->bTest ? static::SERVER_URL_TEST : static::SERVER_URL_PROD;
	}

	/**
	 * Return array of supported methods.
	 * Supported keys for each method:
	 * 		METHOD ['GET' or 'POST']
	 * 		ARGMAP - key/value (local/real) ['SEARCH_TEXT' => 'query', 'CATEGORY_NAME' => 'objectName', 'COUNT' => 'top']
	 * 			LOCAL is this: SEARCH_TEXT, CATEGORY_NAME, COUNT
	 */
	public function getMethods(){
		return [
			# Обновление остатков
			'/stock/update' => [
				'METHOD' => 'POST',
			],
			# Обновление цен
			'/manualPrice/save' => [
				'METHOD' => 'POST',
			],
		];
	}

	public function getMethod(string $strMethod){
		if($arMethod = $this->getMethods()[$strMethod]){
			return array_merge(['PATH' => $strMethod], $arMethod);
		}
		return false;
	}

	public function execute(string $strApiMethod, array $arData=[], array $arParams=[]):Response{
		# Execute requests, using attempts (errors are often)
		# I catched this errors: 504, 408 (Whole, this errors may occurs: 408, 500, 502, 503, 504)
		$intAttemptCount = static::ATTEMPT_COUNT;
		if(is_numeric($arParams['ATTEMPT_COUNT']) && $arParams['ATTEMPT_COUNT'] > 0){
			$intAttemptCount = $arParams['ATTEMPT_COUNT'];
		}
		elseif($arParams['ATTEMPT_COUNT'] === false){
			$intAttemptCount = 99;
		}
		for($intAttemptIndex = 1; $intAttemptIndex <= $intAttemptCount; $intAttemptIndex++){
			$this->obResult = $this->executeInternal($strApiMethod, $arData, $arParams);
			if(in_array($this->obResult->getStatus(), [200, 400])){
				break;
			}
			else{
				$this->addToLog(static::getMessage('NOTICE_ATTEMPT', [
					'#COMMAND#' => $strApiMethod,
					'#INDEX#' => $intAttemptIndex,
					'#COUNT#' => $intAttemptCount,
					'#CODE#' => $this->obResult->getStatus(),
				]), true);
				sleep(2);
				continue;
			}
		}
		# If something went wrong
		if(!is_a($this->obResult, __NAMESPACE__.'\Response')){
			$this->obResult = new Response();
		}
		return $this->obResult;
	}
	
	public function executeInternal(string $strApiMethod, array $arData=[], array $arParams=[]):Response{
		$obResult = new Response();
		if($arMethod = $this->arMethods[$strApiMethod]){
			$bPost = toUpper($arMethod['METHOD']) == 'POST';
			# Prepare
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->disableSslVerification();
			$obHttp->setHeader('User-agent', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36');
			# Prepare method params
			if($arMethodArgumentMap = $arMethod['ARGMAP']){
				if(is_array($arParams['ARGUMENTS'])){
					foreach($arMethodArgumentMap as $strParamLocal => $strParamReal){
						if(isset($arParams['ARGUMENTS'][$strParamLocal])){
							$arData[$strParamReal] = $arParams['ARGUMENTS'][$strParamLocal];
						}
					}
				}
			}
			# Auth
			$strToken = Helper::strlen($arParams['AUTH_TOKEN']) ? $arParams['AUTH_TOKEN'] : $this->strAuthorizationKey;
			if(is_bool($arParams['IS_TEST_ENVIRONMENT'])){
				$this->bTest = $arParams['IS_TEST_ENVIRONMENT'];
			}
			# Get URL
			$strServerUrl = $this->getApiBaseUrl();
			$strRequestUrl = $strServerUrl.$strApiMethod;
			# Some replaces in URL
			foreach($arData as $key => $value){
				if(preg_match('#^{.*?}$#', $key)){
					$strRequestUrl = str_replace($key, rawurlencode($value), $strRequestUrl);
					unset($arData[$key]);
				}
			}
			# Continue execute
			if($bPost){
				if(!is_array($arData['data'])){
					$arData = [
						'meta' => [],
						'data' => $arData,
					];
				}
				$arData['data'] = array_merge([
					'token' => $strToken,
				], $arData['data']);
				$strJsonRequest = Json::encode($arData);
				$obResult->setRequest($strJsonRequest);
				$obResult->setRequestArray($arData);
				$obResult->setRequestUrl($strRequestUrl);
				$obHttp->setHeader('Content-Type', 'application/json');
				$obHttp->setHeader('Content-Length', Helper::strlen($strJsonRequest));

				$strJsonResponse = $obHttp->post($strRequestUrl, $strJsonRequest);
				unset($strJsonRequest);
			}
			else{
				if(!empty($arData)){
					$strRequestUrl .= '?'.http_build_query($arData);
				}
				$obResult->setRequestUrl($strRequestUrl);
				$strJsonResponse = $obHttp->get($strRequestUrl);
			}
			$obResult->setUrl($strRequestUrl);
			$arRequestHeader = [];
			if(method_exists($obHttp, 'getRequestHeaders')){ // New bitrix with method \Bitrix\Main\Web\HttpClient::requestHeaders
				$arRequestHeader = $obHttp->getRequestHeaders()->toArray();
			}
			else{ // Old bitrix with no method \Bitrix\Main\Web\HttpClient::requestHeaders
				$obProp = new \ReflectionProperty('\Bitrix\Main\Web\HttpClient', 'requestHeaders');
				$obProp->setAccessible(true);
				if($obValue = $obProp->getValue($obHttp)){
					$arRequestHeader = $obValue->toArray();
					unset($obValue);
				}
				unset($obProp);
			}
			$obResult->setRequestHeaders($arRequestHeader);
			$obResult->setResponseHeaders($obHttp->getHeaders()->toArray());
			$obResult->setResponse(strVal($strJsonResponse));
			$obResult->setStatus(intVal($obHttp->getStatus()));
			unset($obHttp);
		}
		else{
			$obResult->addError(sprintf('Unknown method: %s', $strApiMethod));
		}
		return $obResult;
	}
	
	/**
	 *	Wrapper for Loc::getMessage()
	 */
	public static function getMessage($strMessage, $arReplace=null){
		static $strLang;
		$strFile = realpath(__DIR__.'/../class.php');
		if(is_null($strLang)){
			\Acrit\Core\Export\Exporter::getLangPrefix($strFile, $strLang, $strHead, $strName, $strHint);
		}
		return Helper::getMessage($strLang.$strMessage, $arReplace);
	}
	
	/**
	 *	Save data to log
	 */
	public function addToLog($strMessage, $bDebug=false){
		return Log::getInstance($this->strModuleId)->add($strMessage, $this->intProfileId, $bDebug);
	}

}
