<?
/**
 * Acrit Core: Kaspi.kz plugin
 * @documentation https://kaspi.kz/merchantcabinet/support/display/Support/XML
 */

namespace Acrit\Core\Export\Plugins;


use \Acrit\Core\Export\Filter,
	\Acrit\Core\Helper;


class KaspikzGeneral extends Kaspikz {
	
	const DATE_UPDATED = '2022-05-04';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'kaspikz.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'USD', 'EUR'];
	
	# Basic settings
	protected $bCategoriesExport = true;
	protected $bCurrenciesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 1;
	
	protected $arXmlMultiply = ['availability'];
	
	# Other export settings	
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		
		$arResult = [];
		
		# General
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@sku'] = ['FIELD' => 'ID'];
		$arResult['model'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['brand'] = ['FIELD' => 'IBLOCK__NAME'];
		
		/*
		$arResult['availabilities.availability@available'] = [
			'TYPE' => 'CONDITION',
			'CONDITIONS' => Filter::getConditionsJson($this->strModuleId, $intIBlockID, [ // optional
				[
					'FIELD' => 'CATALOG_QUANTITY',
					'LOGIC' => 'MORE',
					'VALUE' => '0',
				],
			]),
			'VALUE' => [
				[
					'CONST' => 'yes',
					'SUFFIX' => 'Y',
				],
				[
					'CONST' => 'no',
					'SUFFIX' => 'N',
				],
			],
		];
		*/
		
		//$arResult['availabilities.availability@storeId'] = ['CONST' => '1'];
		
		//$arResult['availabilities.availability@storeId'] = ['CONST' => ['1', '2'], 'MULTIPLE' => true];
		//$arResult['availabilities.availability'] = ['MULTIPLE' => true];
		/* $arResult['availabilities.availability@storeId'] = ['MULTIPLE' => true];
		$arResult['availabilities.availability@storeId'] = [];
		$arResult['availabilities.availability@storeId'][] = ['CONST' => '1'];
		$arResult['availabilities.availability@storeId'][] = ['CONST' => '2'];
		*/
		//$arResult['availabilities.availability@storeId'] = ['FIELD' => 'PROPERTY_MORE_PHOTO'];
		//$arResult['availabilities.availability@storeId'] = ['MULTIPLE' => true];
		//$arResult['availabilities.availability@storeId'] = ['FIELD' => ['CATALOG_PRICE_1', 'CATALOG_PRICE_2'], 'MULTIPLE' => true];
		//$arResult['availabilities.availability@storeId'] = ['FIELD' => ['CATALOG_STORE_AMOUNT_67', 'OFFER.CATALOG_STORE_AMOUNT_54'], 'MULTIPLE' => true];
		
		$arResult['availabilities'] = ['CONST' => '1'];
		
		$arResult['cityprices'] = ['MULTIPLE' => true];
		$arResult['cityprices.cityprice@cityId'] = ['CONST' => '1'];
		$arResult['cityprices.cityprice'] = ['FIELD' => 'CATALOG_PRICE_1'];
		
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
		$arResult['loanPeriod'] = [];
		
		#
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		
		$strXml .= '<kaspi_catalog date="string" xmlns="kaspiShopping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="kaspiShopping http://kaspi.kz/kaspishopping.xsd">'.static::EOL;
		
		$strXml .= '<company>'.htmlspecialcharsbx($this->arParams['COMPANY_NAME']).'</company>'.static::EOL;
		$strXml .= '<merchantid>'.htmlspecialcharsbx($this->arParams['COMPANY_ID']).'</merchantid>'.static::EOL;
		
		
		$strXml .= '<offers>'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		
		$strXml .= '</offers>'.static::EOL;
		$strXml .= '</kaspi_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('c'),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}	
	
	/**
	 *	Settings
	 */
	
	protected function onUpShowSettings(&$arSettings){		
		$arSettings['COMPANY_NAME'] = $this->includeHtml(__DIR__.'/include/settings/company_name.php');
		$arSettings['COMPANY_ID'] = $this->includeHtml(__DIR__.'/include/settings/company_id.php');
	}	

}

?>