<?
/**
 * Acrit Core: Wildberries
 * @documentation https://openapi.wb.ru/
 */

namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\Response;

Helper::loadMessages();

class Api {

	# Documentation [at 2022-09-13]: https://openapi.wb.ru/

	protected const SERVER_URL = 'https://suppliers-api.wildberries.ru';
	protected const ATTEMPT_COUNT = 5;

	protected $intProfileId;
	protected $strModuleId;
	protected $arMethods = []; // protected array
	protected $strAuthorizationKey = ''; // protected string
	protected $obHttp = null;
	protected $obResult = null;

	public function __construct($intProfileId, $strModuleId, string $strAuthorizationKey){
		$this->intProfileId = $intProfileId;
		$this->strModuleId = $strModuleId;
		$this->strAuthorizationKey = $strAuthorizationKey;
		$this->arMethods = $this->getMethods();
	}

	/**
	 * Return array of supported methods.
	 * Supported keys for each method:
	 * 		METHOD ['GET' or 'POST']
	 * 		SERVER ['https://suppliers-api.wildberries.ru']
	 * 		ARGMAP - key/value (local/real) ['SEARCH_TEXT' => 'query', 'CATEGORY_NAME' => 'objectName', 'COUNT' => 'top']
	 * 			LOCAL is this: SEARCH_TEXT, CATEGORY_NAME, COUNT
	 */
	public function getMethods(){
		return [
			# --- CONTENT --- #
			# Цвет
			'/content/v1/directory/colors' => [
				'METHOD' => 'GET',
				'ADDITIONAL_SEARCH' => true,
			],
			# Пол
			'/content/v1/directory/kinds' => [
				'METHOD' => 'GET',
				'ADDITIONAL_SEARCH' => true,
			],
			# Страна Производства
			'/content/v1/directory/countries' => [
				'METHOD' => 'GET',
				'ADDITIONAL_SEARCH' => true,
			],
			# Коллекция
			'/content/v1/directory/collections' => [
				'METHOD' => 'GET',
				'ARGMAP' => ['SEARCH_TEXT' => 'pattern', 'COUNT' => 'top'],
			],
			# Сезон
			'/content/v1/directory/seasons' => [
				'METHOD' => 'GET',
				'ADDITIONAL_SEARCH' => true,
			],
			# Комплектация
			'/content/v1/directory/contents' => [
				'METHOD' => 'GET',
				'ARGMAP' => ['SEARCH_TEXT' => 'pattern', 'COUNT' => 'top'],
			],
			# Состав
			'/content/v1/directory/consists' => [
				'METHOD' => 'GET',
				'ARGMAP' => ['SEARCH_TEXT' => 'pattern', 'COUNT' => 'top'],
			],
			# Бренд
			'/content/v1/directory/brands' => [
				'METHOD' => 'GET',
				'ARGMAP' => ['SEARCH_TEXT' => 'pattern', 'COUNT' => 'top'],
			],
			# ТНВЭД код
			'/content/v1/directory/tnved' => [
				'METHOD' => 'GET',
				'ARGMAP' => ['CATEGORY_NAME' => 'objectName', 'SEARCH_TEXT' => 'tnvedsLike'],
			],
			# Категория товаров
			'/content/v1/object/all' => [
				'METHOD' => 'GET',
			],
			# Родительские категории товаров
			'/content/v1/object/parent/all' => [
				'METHOD' => 'GET',
			],
			# Характеристики для создания КТ по всем подкатегориям
			'/content/v1/object/characteristics/list/filter' => [
				'METHOD' => 'GET',
			],
			# Характеристики для создания КТ для категории товара
			'/content/v1/object/characteristics/{objectName}' => [
				'METHOD' => 'GET',
			],
			# --- VIEWER --- #
			# Список НМ
			'/content/v1/cards/list' => [
				'METHOD' => 'POST',
			],
			# Список несозданных НМ с ошибками
			'/content/v1/cards/error/list' => [
				'METHOD' => 'GET',
			],
			# Получение КТ по вендор кодам (артикулам)
			'/content/v1/cards/filter' => [
				'METHOD' => 'POST',
			],
			# Генерация баркодов
			'/content/v1/barcodes' => [
				'METHOD' => 'POST',
			],
			# --- SOURCE --- #
			# Редактирование КТ
			'/content/v1/cards/update' => [
				'METHOD' => 'POST',
			],
			# Создание КТ
			'/content/v1/cards/upload' => [
				'METHOD' => 'POST',
			],
			# Добавление НМ к КТ
			'/content/v1/cards/upload/add' => [
				'METHOD' => 'POST',
			],
			# --- MediaService --- #
			# Изменение медиа контента КТ
			'/content/v1/media/save' => [
				'METHOD' => 'POST',
			],
			# Добавление медиа контента в КТ
			'/content/v1/media/file' => [
				'METHOD' => 'POST',
			],
			# Остатки
			'/api/v2/stocks' => [
				'METHOD' => 'POST',
			],
			'/api/v3/stocks/{warehouse}' => [
				'METHOD' => 'PUT',
				'CALLBACK' => function($strMethod, &$strUrl, &$arData){
					$strUrl = str_replace('{warehouse}', $arData['warehouse'], $strUrl);
					$arData = $arData['data'];
				},
			],
			# Цены
			'/public/api/v1/prices' => [
				'METHOD' => 'POST',
			],
			# NEW
			'/content/v1/cards/cursor/list' => [
				'METHOD' => 'POST',
			],
			'/public/api/v1/info' => [
				'METHOD' => 'GET',
			],
			'/api/v2/warehouses' => [
				'METHOD' => 'GET',
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
			if(!in_array($this->obResult->getStatus(), [408, 500, 502, 503, 504])){
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
		if(!is_a($this->obResult, '\Acrit\Core\Export\Plugins\WildberriesV4Helpers\Response')){
			$this->obResult = new Response();
		}
		return $this->obResult;
	}
	
	public function executeInternal(string $strApiMethod, array $arData=[], array $arParams=[]):Response{
		$obResult = new Response();
		if($arMethod = $this->arMethods[$strApiMethod]){
			$bPost = toUpper($arMethod['METHOD']) == 'POST';
			$bPut = toUpper($arMethod['METHOD']) == 'PUT';
			$strServerUrl = isset($arMethod[1]) && Helper::strlen($arMethod['SERVER'])
				? $arMethod['SERVER'] : static::SERVER_URL;
			$strRequestUrl = $strServerUrl.$strApiMethod;
			# Prepare
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->disableSslVerification();
			$obHttp->setHeader('Authorization', $this->strAuthorizationKey);
			$obHttp->setHeader('User-agent', 'curl/7.29.0');
			# Custom token (for check)
			if(Helper::strlen($arParams['AUTH_TOKEN'])){
				$obHttp->setHeader('Authorization', $arParams['AUTH_TOKEN']);
			}
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
			if(is_callable($arMethod['CALLBACK'])){
				call_user_func_array($arMethod['CALLBACK'], [$strApiMethod, &$strRequestUrl, &$arData]);
			}
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
