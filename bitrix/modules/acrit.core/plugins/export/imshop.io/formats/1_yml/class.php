<?
/**
 * Acrit Core: Yandex.Market YML for FBY, FBY+, FBS
 * @documentation https://yandex.ru/support/marketplace/catalog/yml-requirements.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter;

class ImshopIoYml extends ImshopIo {
	
	const DATE_UPDATED = '2024-02-29';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'imshop.io.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'EUR', 'UAH', 'BYN', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = true;
	protected $bCurrenciesExport = false;
	protected $bCategoriesList = true;
	protected $strCategoriesUrl = 'http://download.cdn.yandex.net/market/market_categories.xls';
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	protected $arXmlMultiply = ['badge', 'video', 'file'];
	
	# Other export settings
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['@available'] = ['CONST' => 'true'];
		$arResult['@publishedOn'] = [];
		$arResult['@itemPreviewUrl'] = [];
		$arResult['@fraction'] = ['CONST' => '1'];
		$arResult['@group_id'] = ['FIELD' => 'ID'];
		$arResult['@uuid'] = [];
		$arResult['@sizeGridImage'] = [];
		#
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE', 'MULTIPLE' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y'], 'REQUIRED' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['sort'] = [];
		$arResult['guid'] = [];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['vendorCode'] = ['FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTNUMBER', 'PROPERTY_ARTICLE'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['model'] = [];
		$arResult['typePrefix'] = [];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		$arResult['googleProductCategory'] = [];
		$arResult['picture'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true, 'REQUIRED' => true];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true, 'REQUIRED' => true];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['oldprice@silver'] = ['IS_PRICE' => true];
		$arResult['oldprice@gold'] = ['IS_PRICE' => true];
		$arResult['oldprice@platina'] = ['IS_PRICE' => true];
		$arResult['oldprice@vip'] = ['IS_PRICE' => true];
		$arResult['priceByCard'] = ['IS_PRICE' => true];
		$arResult['priceUnits'] = [];
		$arResult['saleEndsDateIso'] = [];
		$arResult['retailPrice'] = ['IS_PRICE' => true];
		$arResult['deferPrice'] = ['IS_PRICE' => true];
		$arResult['priceLabel'] = [];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'], 'PARAMS' => ['HTMLSPECIALCHARS' => 'cdata']];
		$arResult['rec'] = ['MULTIPLE' => true];
		$arResult['rec@title'] = ['MULTIPLE' => true];
		$arResult['maxPromocodeDiscountPercent'] = ['MULTIPLE' => true];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_YANDEX'];
		$arResult['useBonuses'] = ['CONST' => 'false'];
		$arResult['overSized'] = ['CONST' => 'false'];
		$arResult['preorder'] = ['CONST' => 'false'];
		$arResult['badge'] = ['MULTIPLE' => true];
		$arResult['badge@textColor'] = ['MULTIPLE' => true];
		$arResult['badge@bgColor'] = ['MULTIPLE' => true];
		$arResult['badge@link'] = ['MULTIPLE' => true];
		$arResult['badge@position'] = ['MULTIPLE' => true];
		$arResult['badge@picture'] = ['MULTIPLE' => true];
		$arResult['badge@type'] = ['MULTIPLE' => true];
		$arResult['video'] = ['MULTIPLE' => true];
		$arResult['video@main'] = ['MULTIPLE' => true];
		$arResult['video@title'] = ['MULTIPLE' => true];
		$arResult['video@image'] = ['MULTIPLE' => true];
		$arResult['file'] = ['MULTIPLE' => true];
		$arResult['file@title'] = ['MULTIPLE' => true];
		$arResult['file@size'] = ['MULTIPLE' => true];
		$arResult['file@icon'] = ['MULTIPLE' => true];
		$arResult['file@url'] = ['MULTIPLE' => true];
		$arResult['rating'] = [];
		$arResult['reviews_count'] = [];
		$arResult['sizeGridPageUrl'] = [];
		$arResult['othercolors'] = [];
		$arResult['parentVendorCode'] = [];
		#
		return $arResult;
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
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		$date = date('Y-m-d H:i');
		# Replace macros
		$arReplace = [			
			'#XML_DATE#' => $date,
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
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
		$mDataMore = [
			'SKU_ID' =>  $arFields['@id'],
		];
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

}

?>