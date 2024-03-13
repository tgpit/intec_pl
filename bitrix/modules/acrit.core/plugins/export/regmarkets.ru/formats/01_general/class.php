<?
/**
 * Acrit Core: Regmarkets.ru
 * @documentation https://regmarkets.ru/help/25984961/
 */	

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin;

class RegmarketsRuGeneral extends UniversalPlugin {
	
	const DATE_UPDATED = '2023-05-22';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	const EMAIL_DEFAULT = 'simonov@regmarkets.ru';
	const EMAIL_BCC = 'admin@acrit.ru';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'regmarkets.ru.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'UAH', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = true;
	protected $bCurrenciesExport = true;
	protected $bCategoriesList = true;
	protected $strCategoriesUrl = 'http://download.cdn.yandex.net/market/market_categories.xls';
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	protected $arXmlMultiply = [];
	
	# Other export settings
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y'], 'REQUIRED' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		$arResult['picture'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true, 'MAX_COUNT' => 10];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER', 'REQUIRED' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true, 'REQUIRED' => true];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_YANDEX'];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true, 'REQUIRED' => true];
		$arResult['count'] = ['FIELD' => ''];
		$arResult['manufacturer'] = ['FIELD' => ['PROPERTY_MANUFACTURER', 'PROPERTY_BRAND']];
		$arResult['country_of_origin'] = ['FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['vendorCode'] = ['FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTNUMBER', 'PROPERTY_ARTICLE'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'], 'PARAMS' => ['HTMLSPECIALCHARS' => 'cdata']];
		$arResult['certificate'] = ['CONST' => ''];
		$arResult['dimensions'] = ['CONST' => ['{=catalog.CATALOG_LENGTH} / 10', '{=catalog.CATALOG_WIDTH} / 10', '{=catalog.CATALOG_HEIGHT} / 10'], 'CONST_PARAMS' => ['MATH' => 'Y'], 'PARAMS' => ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'slash'], 'REQUIRED' => true];
		$arResult['weight'] = ['CONST' => '{=catalog.CATALOG_WEIGHT} / 1000', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		$arResult['cpa'] = ['CONST' => '1'];
		$arResult['adult'] = ['CONST' => ''];
		$arResult['age'] = ['CONST' => ''];
		$arResult['age@unit'] = ['CONST' => ''];
		#
		return $arResult;
	}

	/**
	 * Add settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['SHOP_NAME'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/shop_name.php'),
			'SORT' => 150,
		];
		$arSettings['SHOP_COMPANY'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/shop_company.php'),
			'SORT' => 151,
		];
		$arSettings['SEND_TO_EMAIL_HEADER'] = [
			'HTML' => Helper::showHeading(static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_HEADER'), true),
			'FULL' => true,
			'SORT' => 151,
		];
		$arSettings['SEND_TO_EMAIL'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/send_to_email.php'),
			'SORT' => 152,
		];
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<name>#SHOP_NAME#</name>'.static::EOL;
		$strXml .= '		<company>#SHOP_COMPANY#</company>'.static::EOL;
		$strXml .= '		<url>#SHOP_URL#</url>'.static::EOL;
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			// '#XML_DATE#' => date('Y-m-d H:i'),
			'#XML_DATE#' => date('c'),
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			#
			'#SHOP_NAME#' => $this->output(($this->arParams['SHOP_NAME'])),
			'#SHOP_COMPANY#' => $this->output(($this->arParams['SHOP_COMPANY'])),
			'#SHOP_URL#' => $this->output(Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y')),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

	/**
	 *	Handler on generate XML for single item
	 */
	protected function onUpBuildXml(&$arXmlTags, &$arXmlAttr, &$strXmlItem, &$arElement, &$arFields, &$arElementSections, &$mDataMore){
		if($arFields['oldprice'] <= $arFields['price']){
			unset($arXmlTags['oldprice'], $arFields['oldprice']);
		}
	}

	protected function processUpdatedCategories($strTmpFile){
		require_once(realpath(__DIR__.'/../../../../../include/php_excel_reader/excel_reader2.php'));
		$obExcelData = new \Spreadsheet_Excel_Reader($strTmpFile, false);
		$intRowCount = $obExcelData->rowcount();
		#
		$strCategories = '';
		for($intLine=0; $intLine<=$intRowCount; $intLine++) {
			$strCategories .= $obExcelData->val($intLine, 1)."\n";
		}
		@unlink($strTmpFile);
		if(Helper::strlen($strCategories)){
			if(!Helper::isUtf()){
				$strCategories = Helper::convertEncoding($strCategories, 'UTF-8', 'CP1251');
			}
			return $strCategories;
		}
		return false;
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'send_email':
				$arJsonResult['Success'] = false;
				if(is_file(Helper::root().($this->getExportFileName()))){
					$arJsonResult['Success'] = $this->sendEmail($arParams['POST']);
				}
				else{
					$arJsonResult['Message'] = 'No file';
				}
				break;
		}
	}

	/**
	 * Send email to regmarkets
	 */
	protected function sendEmail($arPost=null){
		$bSuccess = false;
		#
		$strReceiver = $arPost ? $arPost['email'] : $this->arParams['EMAIL']['RECEIVER'];
		$strSubject = $arPost ? $arPost['subject'] : $this->arParams['EMAIL']['SUBJECT'];
		$strSender = $arPost ? $arPost['sender'] : $this->arParams['EMAIL']['SENDER'];
		$strInn = $arPost ? $arPost['inn'] : $this->arParams['EMAIL']['INN'];
		$strFio = $arPost ? $arPost['fio'] : $this->arParams['EMAIL']['FIO'];
		$strPhone = $arPost ? $arPost['phone'] : $this->arParams['EMAIL']['PHONE'];
		#
		$arReceivers = array_values(array_filter(Helper::explodeValues($strReceiver)));
		if(empty($arReceivers)){
			$arReceivers[] = static::EMAIL_DEFAULT;
		}
		$strXmlFilename = $this->getExportFileName();
		$strXmlFilepath = Helper::root().$strXmlFilename;
		$strXmlUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y', $strXmlFilename);
		#
		foreach($arReceivers as $index => $strReceiver){
			$arEmail = [
				'TO' => $strReceiver,
				'SUBJECT' => $strSubject,
				'HEADER' => [],
				'CHARSET' => defined('BX_UTF') && BX_UTF === true ? 'UTF-8' : 'windows-1251',
				'CONTENT_TYPE' => 'html',
				'BODY' => static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_BODY', [
					'#INN#' => $strInn,
					'#FIO#' => $strFio,
					'#PHONE#' => $strPhone,
					'#FILE_URL#' => $strXmlUrl,
					'#FILE_TITLE#' => sprintf('%s (%s)', $strXmlUrl, \CFile::formatSize(filesize($strXmlFilepath))),
				]),
			];
			if($index == 0){
				$arEmail['HEADER']['Bcc'] = static::EMAIL_BCC;
				$arEmail['HEADER']['Cc'] = static::EMAIL_BCC;
			}
			if(Helper::strlen($strSender)){
				$arEmail['HEADER']['From'] = $strSender;
			}
			if(\Bitrix\Main\Mail\Mail::send($arEmail)){
				$this->addToLog(static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_LOG', ['#EMAIL#' => $strReceiver]));
				$bSuccess = true;
			}
		}
		return $bSuccess;
	}

	public function finish($intProfileId, $arSession, $arData){
		if($this->arParams['EMAIL']['AUTO'] == 'Y'){
			$this->sendEmail();
		}
	}

}

?>