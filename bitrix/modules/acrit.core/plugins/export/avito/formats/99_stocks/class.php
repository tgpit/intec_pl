<?
/**
 * Acrit Core: Avito stocks
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin;

class AvitoStocks extends UniversalPlugin {
	
	const DATE_UPDATED = '2022-11-22';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'avito_stocks.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = [];
	
	# Basic settings
	protected $bAdditionalFields = false;
	protected $bCategoriesExport = false;
	protected $bCategoriesUpdate = false;
	protected $bCurrenciesExport = false;
	protected $bCategoriesList = false;
	
	# XML settings
	protected $strXmlItemElement = 'item';
	protected $intXmlDepthItems = 1;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['id'] = ['FIELD' => 'ID'];
		$arResult['avitoId'] = ['FIELD' => ''];
		$arResult['stock'] = ['FIELD' => 'CATALOG_QUANTITY'];
		return $arResult;
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

}

?>