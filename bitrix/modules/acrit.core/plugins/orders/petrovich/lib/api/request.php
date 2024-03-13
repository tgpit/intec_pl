<?
/**
 * Acrit Core:  Leroymerlin api
 * @documentation https://merchant.leroymerlin.ru/faq/api/information/
 */

namespace Acrit\Core\Orders\Plugins\PetrovichHelpers;

use
    \Bitrix\Main,
    \Bitrix\Main\Web\Uri,
    \Bitrix\Main\Web\HttpClient,
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;
use PhpOffice\PhpSpreadsheet\Exception;

Helper::loadMessages(__FILE__);

class Request {
	const URL = 'https://';

	protected $obPlugin;
//	protected $strApiKey;
	protected $intProfileId;
	protected $strModuleId;
//	protected $strToken;
//	protected $grantType;
	protected $userName;
	protected $password;
	protected $clientId;
	protected $clientSecret;

	/**
	 *	Constructor
	 */
	public function __construct($obPlugin) {
		$arProfile = $obPlugin->getProfileArray();
		$this->obPlugin = $obPlugin;
		$this->intProfileId = $arProfile['ID'];
		$this->strModuleId = $obPlugin->getModuleId();
//		$this->strApiKey = $arProfile['CONNECT_CRED']['api_key'];
//		$this->grantType = 'password';
		$this->userName = $arProfile['CONNECT_CRED']['username'];
		$this->password = $arProfile['CONNECT_CRED']['password'];
//		$this->clientId = $arProfile['CONNECT_CRED']['client_id'];
//		$this->clientSecret = $arProfile['CONNECT_CRED']['client_secret'];
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
	public function request($method, $data, $reqmethod, $jwt){
		$result = $this->execute($method, $data, $reqmethod, $jwt );
//        file_put_contents(__DIR__.'/result1.txt', var_export($result, true) );
		return $result;
	}

	/**
	 *	Execute http-request
	 */
    public function execute($strCommand, $data, $reqmethod, $jwt  ){
        $bSkipErrors = false;
        $httpClient = new HttpClient();
        $httpClient->setVersion("1.1");
        $ext_url = self::URL . $strCommand;
        $httpClient->setHeader('Content-Type', 'application/json', false);
//        $httpClient->setHeader('Content-Type', 'application/x-www-form-urlencoded');
//        $httpClient->setHeader('x-api-key', $token);
        if ($jwt) {
            $httpClient->setHeader('Authorization', $jwt );
        }
        file_put_contents(__DIR__.'/url.txt', $ext_url );
        $httpClient->query($reqmethod, $ext_url , json_encode($data));
        $strJson = $httpClient->getResult();

        if(strlen($strJson)){
            $arJson = Json::decode($strJson);
            if(is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors){
                $strMessage = 'ERROR_GENERAL';
                $strError = sprintf('%s [%s]', $arJson['error']['message'], $arJson['error']['code']);
                $strMessage = sprintf(static::getMessage($strMessage,  [
                    '#COMMAND#' => $strCommand,
                    '#JSON#' => $data,
                    '#ERROR#' => $strError,
                ]));
//				$this->addToLog($strMessage);
            }
            return $arJson;
        }
        $strMessage = 'ERROR_REQUEST'.($this->isDebugMode() ? '_DEBUG' : '');
        $strMessage = sprintf(static::getMessage($strMessage,  [
            '#COMMAND#' => $strCommand,
            '#JSON#' => $data,
            '#RESPONSE#' => $strJson,
        ]));
//		$this->addToLog($strMessage);
        usleep(500000);
        return false;
    }
	
	/**
	 *	Get headers from last request
	 */
	public function getHeaders(){
		return HttpRequest::getHeaders();
	}

}
