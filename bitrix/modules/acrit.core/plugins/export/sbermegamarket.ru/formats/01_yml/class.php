<?
/**
 * Acrit Core: SberMegaMarket
 * @documentation https://conf.goods.ru/merchant-api/1-vvedenie/1-1-tovarnyj-fid
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter;

class SberMegaMarketRuYml extends SberMegaMarketRu {
	
	const DATE_UPDATED = '2023-02-07';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'sbermegamarket.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8, self::CP1251];
	protected $strFileExt = 'yml';
	protected $arSupportedCurrencies = ['RUB'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCurrenciesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	protected $arXmlMultiply = ['shipment-options.option@days', 'outlets.outlet@id'];
	
	# Other export settings
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['@available'] = [
			'TYPE' => 'CONDITION',
			'CONDITIONS' => $this->getFieldFilter($intIBlockID, [
				'FIELD' => 'CATALOG_QUANTITY',
				'LOGIC' => 'MORE',
				'VALUE' => '0',
			]),
			'REQUIRED' => true,
			'DEFAULT_VALUE' => [
				[
					'TYPE' => 'CONST',
					'CONST' => 'true',
					'SUFFIX' => 'Y',
				],
				[
					'TYPE' => 'CONST',
					'CONST' => 'false',
					'SUFFIX' => 'N',
				],
			],
		];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['name'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true, 'REQUIRED' => true];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		$arResult['picture'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_YANDEX'];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_YANDEX'];
		$arResult['shipment-options.option@days'] = ['CONST' => '1', 'MULTIPLE' => true];
		$arResult['shipment-options.option@order-before'] = ['CONST' => '15', 'MULTIPLE' => true];
		$arResult['vendor'] = ['FIELD' => ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF', 'PROPERTY_MANUFACTURER']];
		$arResult['vendorCode'] = ['FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTNUMBER', 'PROPERTY_ARTICLE'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['model'] = ['FIELD' => 'PROPERTY_MODEL'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'FIELD_PARAMS' => ['ENTITY_DECODE' => 'Y', 'HTML2TEXT' => 'Y', 'HTML2TEXT_mode' => 'simple']];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['outlets.outlet@id'] = ['CONST' => '1', 'MULTIPLE' => true];
		$arResult['outlets.outlet@instock'] = ['FIELD' => 'CATALOG_STORE_AMOUNT_1', 'MULTIPLE' => true];
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
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_DATE#' => date('Y-m-d H:i'),
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
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
		if($arFields['oldprice'] <= $arFields['price'] * 1.05){
			unset($arXmlTags['oldprice'], $arFields['oldprice']);
		}
		if(is_array($arParams = $arXmlTags['param'])){
			unset($arXmlTags['param']);
			$arXmlTags['param'] = $arParams;
		}
	}

}

?>