<?

/**
 * Acrit Core: Orders integration plugin for Yandex Market
 * Documentation:
 */

namespace Acrit\Core\Orders\Plugins\YandexMarketApi;

use \Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;
use PhpOffice\PhpSpreadsheet\Exception;

Helper::loadMessages(__FILE__);

class Request
{

	const URL = 'https://api.partner.market.yandex.ru/v2/';
	const DATE_FORMAT = 'd-m-Y';

	protected $obPlugin;
	protected $strAccessToken;
	protected $intProfileId;
	protected $strModuleId;
	protected $strCampaignId;

	/**
	 * 	Constructor
	 */
	public function __construct($obPlugin)
	{
		$arProfile = $obPlugin->getProfileArray();
		$this->obPlugin = $obPlugin;
		$this->intProfileId = $arProfile['ID'];
		$this->strModuleId = $obPlugin->getModuleId();
		$this->strClientId = $arProfile['CONNECT_CRED']['oauth_client_id'];
		$this->strAccessToken = $arProfile['CONNECT_CRED']['token'];
		$this->strCampaignId = $arProfile['CONNECT_CRED']['campaignId'];
	}

	/**
	 * 	Wrapper for Loc::getMessage()
	 */
	public static function getMessage($strMessage, $arReplace = null)
	{
		static $strLang;
		$strFile = realpath(__DIR__ . '/../class.php');
		if (is_null($strLang))
		{
			\Acrit\Core\Export\Exporter::getLangPrefix($strFile, $strLang, $strHead, $strName, $strHint);
		}
		return Helper::getMessage($strLang . $strMessage, $arReplace);
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
	 * 	Request wrapper
	 */
	public function requestPut($method, $params = [], $data = [])
	{
		/*
		  $data = [
		  "meta" => [],
		  "data" => [
		  "token" => $this->strAccessToken,
		  ]
		  ];
		  $data['data'] = array_merge($data['data'], $params);
		  /* */
		$result = $this->execute($method, null, [
			'METHOD' => 'PUT',
			'CONTENT' => json_encode($data),
		]);
		
		return $result;
	}

	public function request($method, $params = [])
	{
		/*
		  $data = [
		  "meta" => [],
		  "data" => [
		  "token" => $this->strAccessToken,
		  ]
		  ];
		  $data['data'] = array_merge($data['data'], $params);
		  /* */
		$result = $this->execute($method, $params, [
			'METHOD' => 'GET',
				//'CONTENT' => json_encode($data),
		]);
		
		return $result;
	}

	/**
	 * 	Execute http-request
	 */
	public function execute($strCommand, $arFields = null, $arParams = [])
	{
		$bSkipErrors = false;
		if ($arParams['SKIP_ERRORS'])
		{
			$bSkipErrors = true;
			unset($arParams['SKIP_ERRORS']);
		}
		$arParams['HEADER'] = [
			'Content-Type' => 'application/json',
//			'Authorization' => 'OAuth oauth_token="' . $this->strAccessToken . '", oauth_client_id="' . $this->strClientId . '"',
			'Authorization' => 'Bearer ' . $this->strAccessToken,
		];
		
		if (is_array($arFields))
		{
			$strCommand .= '?' . http_build_query($arFields);
		} elseif (is_string($arFields))
		{
			$strCommand .= '?' . $arFields;
		}
		$arParams['TIMEOUT'] = 30;
		$strJson = HttpRequest::getHttpContent(static::URL . $strCommand, $arParams);
		Log::getInstance($this->strModuleId, 'orders')->add('$strJson', $this->intProfileId, true);
		Log::getInstance($this->strModuleId, 'orders')->add($strJson, $this->intProfileId, true);
		if ($strJson === false && static::getHeaders() === [])
		{
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
							'message' => 'Timeout on URL ' . static::URL . $strCommand,
							'code' => 'TIMEOUT',
			]]);
		}

		if (strlen($strJson))
		{
			$arJson = json_decode($strJson, true);
			
			if (!is_array($arJson))
			{
				$arJson = $strJson;
			}
			if (is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors)
			{
//				$strMessage = 'ERROR_GENERAL'.($this->isDebugMode() ? '_DEBUG' : '');
				$strMessage = 'ERROR_GENERAL';
				$strError = sprintf('%s [%s]', $arJson['error']['message'], $arJson['error']['code']);
				$strMessage = sprintf(static::getMessage($strMessage, [
							'#COMMAND#' => $strCommand,
							'#JSON#' => $arParams['CONTENT'],
							'#ERROR#' => $strError,
				]));
//				$this->addToLog($strMessage);
			}
			return $arJson;
		}
		$strMessage = 'ERROR_REQUEST' . ($this->isDebugMode() ? '_DEBUG' : '');
		$strMessage = sprintf(static::getMessage($strMessage, [
					'#COMMAND#' => $strCommand,
					'#JSON#' => $arParams['CONTENT'],
					'#RESPONSE#' => $strJson,
		]));
//		$this->addToLog($strMessage);
		usleep(100000);
		return false;
	}

	/**
	 * 	Get headers from last request
	 */
	public function getHeaders()
	{
		return HttpRequest::getHeaders();
	}

}
