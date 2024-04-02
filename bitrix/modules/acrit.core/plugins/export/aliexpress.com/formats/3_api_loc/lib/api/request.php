<?
/**
 * Acrit Core: Aliexpress plugin API Local (Russian)
 * @documentation https://business.aliexpress.ru/docs/category/open-api
 */

namespace Acrit\Core\Export\Plugins\AliHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;
use PhpOffice\PhpSpreadsheet\Exception;

Helper::loadMessages(__FILE__);

class Request {
	const URL = 'https://openapi.aliexpress.ru/api/v1/';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

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
		$this->strToken = $arProfile['PARAMS']['TOKEN'];
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
		return $this->obPlugin->addToLog($strMessage, $bDebug);
	}

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
		return self::URL;
	}

	/**
	 *	Request wrapper
	 */
	public function request($method, $data=[], $user_token=false, $req_method=self::METHOD_POST, $skip_errors=false){
		$token = $this->strToken;
		if ($user_token) {
			$token = $user_token;
		}
		$strContent = !empty($data) ? Json::encode($data) : '';
		$params = [
			'METHOD' => $req_method,
			'CONTENT' => $strContent,
			'HEADER_ADDITIONAL' => [
				'accept' => 'application/json',
				'x-auth-token' => $token,
			],
			'SKIP_ERRORS' => $skip_errors,
		];
		$result = $this->execute($method, null, $params);
//		if (strpos($result['message'], 'Unauthenticated') === 0) {
//			$token = $this->updateToken();
//			$params['HEADER_ADDITIONAL']['Authorization'] = 'Bearer ' . $token;
//			$result = $this->execute($method, null, $params);
//		}
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
		$strUrl = $this->getUrl() . $strCommand;
		$strJson = HttpRequest::getHttpContent($strUrl, $arParams);
		if ($strJson === false && static::getHeaders() === []) {
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
				'message' => 'Timeout on URL '.$this->getUrl() . $strCommand,
				'code' => 'TIMEOUT',
			]]);
		}
		if (strlen($strJson)) {
			try {
				$arJson = Json::decode($strJson);
				if (!is_array($arJson)) {
					$arJson = $strJson;
				}
			}
			catch (\Exception $e) {
				$arJson['error']['message'] = $strJson;
			}
			if (is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors){
				$this->obPlugin->addToLog([
					'RESPONSE' => $arJson['error'],
					'STATUS' => HttpRequest::getCode(),
					'URL' => $strUrl,
					'COMMAND' => $strCommand,
					'CONTENT' => $arParams['CONTENT'],
				]);
			}
			else {
				$this->obPlugin->addToLog([
					'RESPONSE' => $arJson['error'],
					'STATUS' => HttpRequest::getCode(),
					'URL' => $strUrl,
					'COMMAND' => $strCommand,
					'CONTENT' => $arParams['CONTENT'],
				], true);
			}
			return $arJson;
		}
		else{
			$this->addToLog('$strJson is empty: '.[
				'URL' => $strUrl,
				'CONTENT' => $arParams['CONTENT'],
				'STATUS' => HttpRequest::getCode(),
				'HEADERS' => HttpRequest::getRequestHeaders(),
			]);
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
