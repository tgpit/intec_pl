<?
/**
 * Acrit Core: ozon.ru api
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Orders\Plugins\AliexpressLocHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;
use PhpOffice\PhpSpreadsheet\Exception;

Helper::loadMessages(__FILE__);

class Request {
	const URL = 'https://openapi.aliexpress.ru/seller-api/v1/';

	protected $obPlugin;
	protected $strApiKey;
	protected $intProfileId;
	protected $strModuleId;
	protected $strToken;

	/**
	 *	Constructor
	 */
	public function __construct($obPlugin) {
		$arProfile = $obPlugin->getProfileArray();
		$this->obPlugin = $obPlugin;
		$this->intProfileId = $arProfile['ID'];
		$this->strModuleId = $obPlugin->getModuleId();
		$this->strToken = $arProfile['CONNECT_CRED']['token'];
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

	public function isDebugMode(){
		return Log::getInstance($this->strModuleId)->isDebugMode();
	}

	/**
	 * Get access token
	 */
	public function getUrl() {
		return self::URL;
	}

	/**
	 *	Request wrapper
	 */
	public function request($method, $data=[], $user_token=false){
		$token = $this->strToken;
		if ($user_token) {
			$token = $user_token;
		}
		$params = [
			'METHOD' => 'POST',
			'CONTENT' => !empty($data) ? json_encode($data) : '',
			'HEADER_ADDITIONAL' => [
				'accept' => 'application/json',
				'x-auth-token' => $token,
			],
		];
		$result = $this->execute($method, null, $params);

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
		$strRequestUrl = $this->getUrl() . $strCommand;
		$strJson = HttpRequest::getHttpContent($strRequestUrl, $arParams);
		if ($strJson === false && static::getHeaders() === []) {
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
				'message' => 'Timeout on URL ' . $strRequestUrl,
				'code' => 'TIMEOUT',
			]]);
		}
		$arRequestHeaders = HttpRequest::getRequestHeaders();
		Log::getInstance($this->strModuleId, 'orders')->add('request: ' . print_r($arRequestHeaders, true), $this->intProfileId, true);
		if (strlen($strJson)){
			try {
				$arJson = Json::decode($strJson);
			}
			catch (\Exception $e) {
				$arJson['error']['message'] = $strJson;
			}
			if(is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors){
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
