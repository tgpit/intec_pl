<?
/**
 * Acrit Core: CRM integration plugin for CDEK.Market
 * Documentation: https://api.cdek.market/api/documentation
 */

namespace Acrit\Core\Orders\Plugins\CdekHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;
use PhpOffice\PhpSpreadsheet\Exception;

Helper::loadMessages(__FILE__);

class Request {
	const URL_PROD = 'https://api.cdek.market/api/v1/';
	const URL_TEST = 'https://test.api.cdek.market/api/v1/';
	const DATE_FORMAT = 'Y-m-d';

	protected $obPlugin;
	protected $strApiKey;
	protected $intProfileId;
	protected $strModuleId;
	protected $boolTestMode;
	protected $strToken;

	/**
	 *	Constructor
	 */
	public function __construct($obPlugin) {
		$arProfile = $obPlugin->getProfileArray();
		$this->obPlugin = $obPlugin;
		$this->intProfileId = $arProfile['ID'];
		$this->strModuleId = $obPlugin->getModuleId();
		$this->strApiKey = $arProfile['CONNECT_CRED']['apikey'];
		$this->boolTestMode = ($arProfile['CONNECT_CRED']['testmode'] == 'Y');
		$this->strToken = $arProfile['SECRET_CODE'];
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

//	/**
//	 *	Save data to log
//	 */
//	public function addToLog($strMessage, $bDebug=false){
//		return Log::getInstance($this->strModuleId)->add($strMessage, $this->intProfileId, $bDebug);
//	}

//	/**
//	 *	Is debug mode for log?
//	 */
//	public function isDebugMode(){
//		return Log::getInstance($this->strModuleId)->isDebugMode();
//	}

	/**
	 * Get access token
	 */
	public function getUrl() {
		return $this->boolTestMode ? self::URL_TEST : self::URL_PROD;
	}

	/**
	 * Get access token
	 */
	public function updateToken($api_key=false) {
		$api_key = $api_key ? : $this->strApiKey;
		$result = $this->execute('auth/login', null, [
			'METHOD' => 'POST',
			'CONTENT' => json_encode([
				'api_key' => $api_key,
			]),
		]);
		if ($result['access_token']) {
			Helper::call($this->strModuleId, 'OrdersProfiles', 'update', [$this->intProfileId, [
				'SECRET_CODE' => $result['access_token'],
			]]);
			return $result['access_token'];
		}
		return false;
	}

	/**
	 *	Request wrapper
	 */
	public function request($method, $data=[], $api_key=false){
		$token = $this->strToken;
		if ($api_key) {
			$token = $this->updateToken($api_key);
		}
		$params = [
			'METHOD' => 'GET',
			'CONTENT' => json_encode($data),
			'HEADER_ADDITIONAL' => [
				'Authorization' => 'Bearer ' . $token,
			],
		];
		$result = $this->execute($method, null, $params);
		if (strpos($result['message'], 'Unauthenticated') === 0) {
			$token = $this->updateToken();
			$params['HEADER_ADDITIONAL']['Authorization'] = 'Bearer ' . $token;
			$result = $this->execute($method, null, $params);
		}
		return $result;
	}

	/**
	 *	Execute http-request
	 */
	public function execute($strCommand, $arFields=null, $arParams=[]){
		usleep(100000);
		$bSkipErrors = false;
		if ($arParams['SKIP_ERRORS']) {
			$bSkipErrors = true;
			unset($arParams['SKIP_ERRORS']);
		}
		$arParams['HEADER'] = [
			'Content-Type' => 'application/json',
		];
		if (is_array($arParams['HEADER_ADDITIONAL'])) {
			$arParams['HEADER'] = array_merge($arParams['HEADER'], $arParams['HEADER_ADDITIONAL']);
		}
		if (is_array($arFields)) {
			$strCommand .= '?' . http_build_query($arFields);
		}
		elseif (is_string($arFields)) {
			$strCommand .= '?' . $arFields;
		}
		$arParams['TIMEOUT'] = 30;
		$arParams['GET_REQUEST_HEADERS'] = true;
		$strJson = HttpRequest::getHttpContent($this->getUrl() . $strCommand, $arParams);
		if ($strJson === false && static::getHeaders() === []) {
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
				'message' => 'Timeout on URL '.$this->getUrl() . $strCommand,
				'code' => 'TIMEOUT',
			]]);
		}
		$arRequestHeaders = HttpRequest::getRequestHeaders();
		Log::getInstance($this->strModuleId, 'orders')->add('request: ' . print_r($arRequestHeaders, true), $this->intProfileId, true);
		if (strlen($strJson)) {
			$arJson = json_decode($strJson, true);
			if (!is_array($arJson)) {
				$arJson = $strJson;
			}
			if (is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors){
//				$strMessage = 'ERROR_GENERAL'.($this->isDebugMode() ? '_DEBUG' : '');
				$strMessage = 'ERROR_GENERAL';
				$strError = sprintf('%s [%s]', $arJson['error']['message'], $arJson['error']['code']);
				$strMessage = sprintf(static::getMessage($strMessage,  [
					'#COMMAND#' => $strCommand,
					'#JSON#' => $arParams['CONTENT'],
					'#ERROR#' => $strError,
				]));
//				$this->addToLog($strMessage);
			}
			return $arJson;
		}
//		$strMessage = 'ERROR_REQUEST'.($this->isDebugMode() ? '_DEBUG' : '');
//		$strMessage = sprintf(static::getMessage($strMessage,  [
//			'#COMMAND#' => $strCommand,
//			'#JSON#' => $arParams['CONTENT'],
//			'#RESPONSE#' => $strJson,
//		]));
//		$this->addToLog($strMessage);
		return false;
	}
	
	/**
	 *	Get headers from last request
	 */
	public function getHeaders(){
		return HttpRequest::getHeaders();
	}

}
