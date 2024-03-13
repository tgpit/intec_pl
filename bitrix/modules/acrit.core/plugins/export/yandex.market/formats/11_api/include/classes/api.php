<?
/**
 * Acrit Core: Wildberries
 * @documentation https://yandex.ru/dev/market/partner-marketplace-cd/doc/dg/reference/post-campaigns-id-offer-mapping-entries-updates.html
 */

namespace Acrit\Core\Export\Plugins\YandexMarketApiHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Plugins\YandexMarketApiHelpers\Response;

Helper::loadMessages();

class Api {

	protected const SERVER_URL = 'https://api.partner.market.yandex.ru';
	protected const ATTEMPT_COUNT = 3;

	protected $intProfileId;
	protected $strModuleId;
	protected $strCampaignId = '';
	protected $strBusinessId = '';
	protected $strOAuthToken = '';
	protected $strClientId = '';
	protected $obHttp = null;
	protected $obResult = null;

	public function __construct($intProfileId, $strModuleId, string $strCampaignId, string $strBusinessId, string $strOAuthToken, string $strClientId){
		$this->intProfileId = $intProfileId;
		$this->strModuleId = $strModuleId;
		$this->strCampaignId = $strCampaignId;
		$this->strBusinessId = $strBusinessId;
		$this->strOAuthToken = $strOAuthToken;
		$this->strClientId = $strClientId;
	}

	/**
	 * Get array with access token and refresh token
	 * @return null|array: ['ACCESS_TOKEN' => '...', 'REFRESH_TOKEN' => '...']
	 */
	public function getOAuthToken(string $strClientId, string $strClientSecretId, string $strConfirmCode){
		$arResult = null;
		$strUrl = 'https://oauth.yandex.ru/token';
		$obHttp = new \Bitrix\Main\Web\HttpClient;
		$obHttp->setHeader('Authorization', 'Basic '.
			base64_encode(sprintf('%s:%s', $strClientId, $strClientSecretId)));
		$arPost = [
			'grant_type' => 'authorization_code',
			'code' => $strConfirmCode,
		];
		$strResponse = $obHttp->post($strUrl, http_build_query($arPost));
		$arJson = [];
		try{
			$arJson = Json::decode($strResponse);
			if(Helper::strlen($arJson['access_token'])){
				$arResult = [
					'ACCESS_TOKEN' => $arJson['access_token'],
					'REFRESH_TOKEN' => $arJson['refresh_token'],
					'TOKEN_TYPE' => $arJson['token_type'],
					'EXPIRES_IN' => $arJson['expires_in'],
					'EXPIRE_TIMESTAMP' => time() + $arJson['expires_in'],
				];
			}
		}
		catch(\Throwable $obError){
			$this->addToLog(sprintf('Error get json response from oauth.yandex.ru (%s): %s', $obError->getMessage(),
				$strResponse));
		}
		return $arResult;
	}

	public function execute(string $strMethod, array $arData=[], array $arParams=[]):Response{
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
			$this->obResult = $this->executeInternal($strMethod, $arData, $arParams);
			if(!in_array($this->obResult->getStatus(), [500, 502, 503, 504, 520])){
				break;
			}
			else{
				$this->addToLog(static::getMessage('NOTICE_ATTEMPT', [
					'#COMMAND#' => $strMethod,
					'#TYPE#' => $arParams['METHOD'],
					'#INDEX#' => $intAttemptIndex,
					'#COUNT#' => $intAttemptCount,
					'#CODE#' => $this->obResult->getStatus(),
				]), true);
				sleep(2);
				continue;
			}
		}
		# If something went wrong
		if(!is_a($this->obResult, '\Acrit\Core\Export\Plugins\YandexMarketApiHelpers\Response')){
			$this->obResult = new Response();
		}
		return $this->obResult;
	}
	
	public function executeInternal(string $strMethod, array $arData=[], array $arParams=[]):Response{
		$obResult = new Response();
		$strType = $arParams['METHOD'] ?? 'POST';
		$bPost = $strType == 'POST';
		$bPut = $strType == 'PUT';
		$bDelete = $strType == 'DELETE';
		$strCampaignId = $this->strCampaignId;
		if(Helper::strlen($arParams['CAMPAIGN_ID'])){
			$strCampaignId = $arParams['CAMPAIGN_ID'];
		}
		$strBusinessId = $this->strBusinessId;
		if(Helper::strlen($arParams['BUSINESS_ID'])){
			$strBusinessId = $arParams['BUSINESS_ID'];
		}
		$arReplace = [
			'{campaignId}' => $strCampaignId,
			'{businessId}' => $strBusinessId,
		];
		$strRequestUrl = str_replace(array_keys($arReplace), array_values($arReplace), static::SERVER_URL.$strMethod);
		# Prepare
		$obHttp = new \Bitrix\Main\Web\HttpClient();
		$obHttp->disableSslVerification();
		$strAuth = sprintf('OAuth oauth_token="%s", oauth_client_id="%s"', $this->strOAuthToken, $this->strClientId);
		if(Helper::strlen($arParams['AUTHORIZATION'])){
			$strAuth = $arParams['AUTHORIZATION'];
		}
		$obHttp->setHeader('Authorization', $strAuth);
		# Some replaces in URL
		foreach($arData as $key => $value){
			if(preg_match('#^{.*?}$#', $key)){
				$strRequestUrl = str_replace($key, rawurlencode($value), $strRequestUrl);
				unset($arData[$key]);
			}
		}
		# Continue execute
		if($bPost){
			$strJsonRequest = Json::encode($arData);
			$obHttp->setHeader('Content-Type', 'application/json');
			$obHttp->setHeader('Content-Length', Helper::strlen($strJsonRequest));
			$strJsonResponse = $obHttp->post($strRequestUrl, $strJsonRequest);
			unset($strJsonRequest);
		}
		elseif($bPut){
			$strJsonRequest = Json::encode($arData);
			$obHttp->setHeader('Content-Type', 'application/json');
			$obHttp->setHeader('Content-Length', Helper::strlen($strJsonRequest));
			$bResult = $obHttp->query(\Bitrix\Main\Web\HttpClient::HTTP_PUT, $strRequestUrl, $strJsonRequest);
			$strJsonResponse = $obHttp->getResult();
			unset($strJsonRequest);
		}
		elseif($bDelete){
			$strJsonRequest = Json::encode($arData);
			$obHttp->setHeader('Content-Type', 'application/json');
			$obHttp->setHeader('Content-Length', Helper::strlen($strJsonRequest));
			$bResult = $obHttp->query(\Bitrix\Main\Web\HttpClient::HTTP_DELETE, $strRequestUrl, $strJsonRequest);
			$strJsonResponse = $obHttp->getResult();
			unset($strJsonRequest);
		}
		else{
			if(!empty($arData)){
				$strRequestUrl .= '?'.http_build_query($arData);
			}
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
