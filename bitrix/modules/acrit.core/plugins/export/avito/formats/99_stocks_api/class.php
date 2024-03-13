<?
/**
 * Acrit Core: Avito stocks API
 * https://developers.avito.ru/api-catalog/stock-management/documentation
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin;

class AvitoStocksApi extends UniversalPlugin {
	
	const DATE_UPDATED = '2023-05-23';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	const API_URL = 'https://api.avito.ru';
	const BATCH_DEFAULT = 200;

	protected static $bSubclass = true;
	
	# General
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $arSupportedCurrencies = [];
	
	# Basic settings
	protected $bApi = true;
	protected $bAdditionalFields = false;
	protected $bCategoriesExport = false;
	protected $bCategoriesUpdate = false;
	protected $bCurrenciesExport = false;
	protected $bCategoriesList = false;

	#
	protected $strClientCredentialsToken = null;
	protected $strClientCredentialsType = null;
	protected $intClientCredentialsExpires = null;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['external_id'] = ['FIELD' => '', 'CUSTOM_REQUIRED' => true];
		$arResult['item_id'] = ['FIELD' => '', 'CUSTOM_REQUIRED' => true];
		$arResult['quantity'] = ['FIELD' => 'CATALOG_QUANTITY', 'REQUIRED' => true];
		return $arResult;
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
		$arSettings['OAUTH_CLIENT_CHECK'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/oauth_client_check.php'),
			'SORT' => 120,
		];
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'oauth_check':
				$this->ajaxOAuthCheck($arParams, $arJsonResult);
				break;
		}
	}

	/**
	 * 
	 */
	protected function ajaxOAuthCheck($arParams, &$arJsonResult){
		if($this->getClientCredentialsToken($arParams['GET']['client_id'], $arParams['GET']['client_secret_id'])){
			$arJsonResult['Success'] = true;
			$arJsonResult['Message'] = static::getMessage('CHECK_SUCCESS');
		}
		else{
			$arJsonResult['Success'] = false;
			$arJsonResult['Message'] = static::getMessage('CHECK_ERROR');
		}
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#" ?>'.static::EOL;
		$strXml .= '<items date="#XML_DATE#" formatVersion="1">'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</items>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_DATE#' => date('Y-m-d\TH:i:s'),
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 * 
	 */
	protected function getClientCredentialsToken($strClientId=null, $strClientSecretId=null){
		if(Helper::strlen($this->strClientCredentialsToken) && $this->intClientCredentialsExpires >= time() + 60){
			return $this->strClientCredentialsToken;
		}
		#
		$this->strClientCredentialsToken = null;
		$this->strClientCredentialsType = null;
		$this->intClientCredentialsExpires = null;
		#
		$obHttp = new \Bitrix\Main\Web\HttpClient();
		$obHttp->disableSslVerification();
		$arPost = [
			'grant_type' => 'client_credentials',
			'client_id' => $strClientId ?? $this->arParams['OAUTH_CLIENT_ID'],
			'client_secret' => $strClientSecretId ?? $this->arParams['OAUTH_CLIENT_SECRET_ID'],
		];
		$strJson = $obHttp->post(static::API_URL.'/token/', http_build_query($arPost));
		$arJson = [];
		try{
			$arJson = \Bitrix\Main\Web\Json::decode($strJson);
		}
		catch(\Throwable $obError){
			$this->addToLog(static::getMessage('ERROR_GET_TOKEN', ['#ERROR#' => $obError->getMessage()]));
		}
		if(Helper::strlen($arJson['access_token'])){
			$this->strClientCredentialsToken = $arJson['access_token'];
		}
		if(Helper::strlen($arJson['token_type'])){
			$this->strClientCredentialsType = $arJson['token_type'];
		}
		if(Helper::strlen($arJson['expires_in'])){
			$this->intClientCredentialsExpires = time() + $arJson['expires_in'];
		}
		if(!Helper::strlen($arJson['access_token'])){
			$this->addToLog(static::getMessage('ERROR_GET_TOKEN', ['#ERROR#' => $strJson]));
		}
		return Helper::strlen($this->strClientCredentialsToken) ? $this->strClientCredentialsToken : null;
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		if(Helper::strlen($arItem['external_id'])){
			$arItem['external_id'] = strVal($arItem['external_id']);
		}
		else{
			unset($arItem['external_id']);
		}
		if(isset($arItem['item_id'])){
			$arItem['item_id'] = intVal($arItem['item_id']);
		}
		if(isset($arItem['quantity'])){
			$arItem['quantity'] = intVal($arItem['quantity']);
		}
		if(!Helper::strlen($arItem['external_id']) && $arItem['item_id'] <= 0){
			$strError = static::getMessage('ERROR_ID_EMPTY', ['#ELEMENT_ID#' => $arElement['ID']]);
			$this->addToLog($strError);
			return [
				'ERRORS' => [$strError],
			];
		}
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
	 * Get batch size (items per one request)
	 */
	protected function getBatchSize(){
		return static::BATCH_DEFAULT;
	}

	/**
	 * 
	 */
	protected function exportItems(array $arItems){
		$arJsonItems = [];
		foreach($arItems as $key => $arItem){
			try{
				$arItemJson = Json::decode($arItem['DATA']);
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
			$arJsonItems[$arItem['ID']] = $arItemJson;
		}
		$strRequestUrl = static::API_URL.'/stock-management/1/stocks';
		#
		if($this->getClientCredentialsToken()){
			$arData = [
				'stocks' => array_values($arJsonItems),
			];
			$strJsonRequest = Json::encode($arData);
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->disableSslVerification();
			$obHttp->setHeader('Content-Type', 'application/json');
			$obHttp->setHeader('Content-Length', Helper::strlen($strJsonRequest));
			$obHttp->setHeader('Authorization', $this->strClientCredentialsType.' '.$this->strClientCredentialsToken);
			$bResult = $obHttp->query(\Bitrix\Main\Web\HttpClient::HTTP_PUT, $strRequestUrl, $strJsonRequest);
			$strJsonResponse = trim($obHttp->getResult());
			$arJsonResponse = [];
			try{
				$arJsonResponse = \Bitrix\Main\Web\Json::decode($strJsonResponse);
			}
			catch(\Throwable $obError){
				$this->addToLog(static::getMessage('ERROR_REQUEST_1', [
					'#STATUS#' => $obHttp->getStatus(),
					'#RESPONSE#' => $obError->getMessage(),
				]));
			}
			$this->addToLog([
				'URL' => $strRequestUrl,
				'METHOD' => 'PUT',
				'DATA' => $arData,
				'RESPONSE' => $strJsonResponse,
				'CODE' => $obHttp->getStatus(),
			], true);
			if($obHttp->getStatus() == 200){
				return Exporter::RESULT_SUCCESS;
			}
			else{
				$this->addToLog(static::getMessage('ERROR_REQUEST_2', [
					'#STATUS#' => $obHttp->getStatus(),
					'#RESPONSE#' => $strJsonResponse,
				]));
			}
		}
		else{
			$this->addToLog(static::getMessage('ERROR_AUTH'));
		}
		return Exporter::RESULT_ERROR;
	}

}

?>