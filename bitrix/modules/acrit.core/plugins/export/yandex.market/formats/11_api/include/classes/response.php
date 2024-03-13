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
	\Acrit\Core\HttpRequest;

Helper::loadMessages();

class Response {

	private $strUrl = ''; // private string
	private $strMethod = 'GET'; # HTTP method (GET or POST) // private string
	private $strResponse = ''; // private string
	private $arJsonResult = []; // private array
	private $arRequestHeaders = []; // private array
	private $arResponseHeaders = []; // private array
	private $arErrors = []; # Array os strings // private array
	private $intStatus = 0; # Http status code (200, 404, 500, ... ) // private int

	public function __construct(){
		//
	}
	
	public function setUrl(string $strUrl){
		$this->strUrl = $strUrl;
	}
	
	public function getUrl(){
		return $this->strUrl;
	}
	
	public function setMethod(string $strMethod){
		if(in_array(toUpper($strMethod), ['GET', 'POST'])){
			$this->strMethod = $strMethod;
		}
	}
	
	public function getMethod(){
		return $this->strMethod;
	}
	
	public function setResponse(string $strResponse){
		$this->strResponse = $strResponse;
		try{
			$this->setJsonResult(Json::decode($strResponse));
		}
		catch(\Throwable $obError){
			$this->addError($obError->getMessage());
		}
	}

	public function getResponse(){
		if(!Helper::isUtf()){
			return Helper::convertEncodingFrom($this->strResponse, 'UTF-8');
		}
		return $this->strResponse;
	}
	
	public function setJsonResult(array $arJsonResult){
		$this->arJsonResult = $arJsonResult;
	}
	
	public function getJsonResult(){
		return $this->arJsonResult;
	}
	
	public function setRequestHeaders(array $arRequestHeaders){
		$this->arRequestHeaders = $arRequestHeaders;
	}
	
	public function getRequestHeaders(){
		return $this->arRequestHeaders;
	}
	
	public function setResponseHeaders(array $arResponseHeaders){
		$arResponseHeadersTmp = [];
		foreach($arResponseHeaders as $arHeader){
			foreach($arHeader['values'] as $arHeaderValue){
				$arResponseHeadersTmp[] = sprintf('%s: %s', $arHeader['name'], $arHeaderValue);
			}
		}
		$this->arResponseHeaders = $arResponseHeadersTmp;
	}
	
	public function getResponseHeaders(){
		return $this->arResponseHeaders;
	}

	public function setErrors(array $arErrors){
		$this->arErrors = $arErrors;
	}

	public function addError(string $strError){
		$this->arErrors[] = $strError;
	}

	public function getErrors(){
		return $this->arErrors;
	}
	
	public function setStatus(int $intStatus){
		$this->intStatus = $intStatus;
	}
	
	public function getStatus(){
		return $this->intStatus;
	}

}

