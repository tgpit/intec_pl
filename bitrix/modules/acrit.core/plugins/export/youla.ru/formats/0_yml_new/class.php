<?
/**
 * Acrit Core: Youla universal format
 * @documentation https://docs.google.com/document/d/1_zBRRCNoM7uxe6xPHn5ztTFi55ANqjKKDA3XM1MvLEc/edit
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin;

class YoulaYmlNew extends UniversalPlugin {
	
	const DATE_UPDATED = '2023-03-13';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	const CATEGORY_CHAIN_SEPARATOR = ' / ';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'youla.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB'];
	
	# Basic settings
	protected $bAdditionalFields = false;
	protected $bCategoriesExport = true;
	protected $bCategoriesStrict = true;
	protected $bCategoriesUpdate = false;
	protected $bHideCategoriesUpdateButton = true;
	protected $bCurrenciesExport = false;
	protected $bCategoriesList = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 1;

	# Misc
	protected $arCacheCategoriesPlain = [];
	// protected $obParsedown;
	// protected $arXmlMultiply = ['Images.Image@url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$arResult = [];

		#
		$intMainIBlockId = $this->getMainIBlockId($intIBlockId);

		# Get common fields
		$arResult['HEADER_COMMON'] = [];
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['address'] = ['CONST' => '', 'REQUIRED' => true];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'REQUIRED' => true];
		$arResult['phone'] = ['CONST' => '', 'REQUIRED' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
		$arResult['picture'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO'], 'MULTIPLE' => true, 'REQUIRED' => true];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'REQUIRED' => true];
		$arResult['managerName'] = ['CONST' => '', 'REQUIRED' => true];

		# Field for alternative categories
		$arResult['сategoryId'] = ['CONST' => ''];

		# Get category fields
		$arSetupCategories = $this->getSetupCategories($intMainIBlockId);
		$strCommonName = static::getMessage('GROUP_COMMON');
		foreach($arSetupCategories as $intCategoryId => $strCategoryName){
			$arResult['HEADER_'.$intCategoryId] = ['NAME' => $strCategoryName];
			if(!empty($arCategoryAttributes = $this->getYoulaSubcategoryAttributes($intCategoryId))){
				foreach($arCategoryAttributes as $strAttribute => $arAttribute){
					$strFieldCode = sprintf('%s_%s', $intCategoryId, $strAttribute);
					$arResult[$strFieldCode] = $this->buildField($arAttribute, $strAttribute);
				}
			}
		}

		#
		return $arResult;
	}

	/**
	 * Build one field from field json
	 */
	public function buildField($arJsonField, $strAttribute){
		$arResult = [
			'NAME' => $arJsonField['NAME'],
			'DISPLAY_CODE' => $strAttribute,
		];
		# Allowed values
		if(is_array($arJsonField['VALUES']) && !empty($arJsonField['VALUES'])){
			$arResult['ALLOWED_VALUES_CUSTOM'] = true;
			$arResult['ALLOWED_VALUES_CUSTOM_DATA'] = $arJsonField['VALUES'];
		}
		#
		return $arResult;
	}

	protected function guessAdType($arValues){
		$strResult = '';
		if(is_array($arValues)){
			foreach($arValues as $arValue){
				if(Helper::strpos($arValue['value'], static::getMessage('GUESS_AD_TYPE_FOR_SELL'))){
					$strResult = $arValue['value'];
				}
			}
		}
		return $strResult;
	}

	public function getDataDir(){
		return __DIR__.'/data';
	}

	public function getFileJson($strFile){
		$arResult = [];
		$strFilename = sprintf('%s/%s.json', $this->getDataDir(), $strFile);
		if(is_file($strFilename) && filesize($strFilename)){
			$arResult = Json::tryDecode(file_get_contents($strFilename));
		}
		return $arResult;
	}

	public function convertFromUtf8($strText){
		return Helper::convertEncodingFrom($strText, 'UTF_8');
	}

	/**
	 * 
	 */
	public function convertFromMarkdown($strText){
		if(!is_object($this->obParsedown)){
			if(is_file($strFile = Helper::root().'/bitrix/modules/acrit.core/include/parsedown/Parsedown.php')){
				require_once($strFile);
				if(class_exists('\Acrit\Core\Parsedown')){
					$this->obParsedown = new \Acrit\Core\Parsedown;
				}
			}
		}
		if(is_object($this->obParsedown)){
			$strText = $this->obParsedown->text($strText);
		}
		return $strText;
	}
	
	/**
	 * Transform some fields
	 */
	public function getFieldCode($arField){
		$strTag = $arField['tag'];
		$arMap = [
			'Images' => 'Images.Image@url',
		];
		if($arField['type'] == 'checkbox'){
			$arMap[$strTag] = sprintf('%s.Option', $strTag);
		}
		elseif($arField['type'] == 'range'){
			# ToDo!
		}
		#
		$strResult = $strTag;
		if(isset($arMap[$strTag])){
			$strResult = $arMap[$strTag];
		}
		return $strResult;
	}
	
	/**
	 *	Get saved categories, if not exists - download it
	 */
	public function getCategoriesList($intProfileId){
		$arResult = &$this->arCacheCategoriesPlain;
		if(!is_array($arResult) || empty($arResult)){
			$arResult = [];
			$this->getYoulaCategories($arResult, $this->getYoulaData());
			$arResult = array_map(function($arItem){
				return $arItem['NAME_FULL'];
			}, $arResult);
		}
		return $arResult;
	}

	/**
	 * Get Youla data (full tree)
	 */
	public function getYoulaData(){
		static $arCache = [];
		if(empty($arCache)){
			$arCache = $this->getFileJson('attributes');
			foreach($arCache as $intCategoryId => &$arCategory){
				$arCategory['NAME_FULL'] = sprintf('[%s] %s', $intCategoryId, $arCategory['NAME']);
				if(is_array($arCategory['CHILDREN'])){
					# Sort keys: NAME_FULL after NAME
					$arChildren = $arCategory['CHILDREN'];
					unset($arCategory['CHILDREN']);
					$arCategory['CHILDREN'] = $arChildren;
					unset($arChildren);
					# Process children
					foreach($arCategory['CHILDREN'] as $intSubcategoryId => &$arSubcategory){
						$arSubcategory['NAME_FULL'] = sprintf('[%s] %s', $intSubcategoryId,
							implode(static::CATEGORY_CHAIN_SEPARATOR, [
								$arCategory['NAME'],
								$arSubcategory['NAME'],
							]));
						if(is_array($arSubcategory['ATTRIBUTES'])){
							# Sort keys: NAME_FULL after NAME
							$arAttributes = $arSubcategory['ATTRIBUTES'];
							unset($arSubcategory['ATTRIBUTES']);
							$arSubcategory['ATTRIBUTES'] = $arAttributes;
							unset($arAttributes);
						}
					}
					unset($arSubcategory);
				}
			}
			unset($arCategory);
		}
		return $arCache;
	}

	/**
	 * Get Youla categories
	 */
	public function getYoulaCategories(&$arResult, $arData, $arChain=[]){
		if(is_array($arData)){
			uasort($arData, function($a, $b){
				return strcmp($a['NAME'], $b['NAME']);
			});
			foreach($arData as $intCategory => $arCategory){
				$arCategoryChain = array_merge($arChain, [$arCategory['NAME']]);
				if(!empty($arChain)){ # Check if subcategory. Skip first-level categories!
					$arResult[$intCategory] = $arCategory;
				}
				if(is_array($arCategory['CHILDREN'])){
					$this->getYoulaCategories($arResult, $arCategory['CHILDREN'], $arCategoryChain);
				}
			}
		}
	}

	/**
	 * Get Youla subcategory attributes
	 */
	public function getYoulaSubcategoryAttributes($intSubcategoryId){
		$arResult = [];
		foreach($this->getYoulaData() as $intCategoryId => $arCategory){
			if(is_array($arCategory['CHILDREN'])){
				if(isset($arCategory['CHILDREN'][$intSubcategoryId])){
					$arResult = $arCategory['CHILDREN'][$intSubcategoryId]['ATTRIBUTES'];
				}
			}
		}
		return $arResult;
	}

	/**
	 * Get Youla subcategory attributes
	 */
	public function getYoulaSubcategoryAttributeValues($intSubcategoryId, $strAttribute){
		$arResult = [];
		if(!empty($arAttributes = $this->getYoulaSubcategoryAttributes($intSubcategoryId))){
			if(is_array($arAttributes[$strAttribute]['VALUES'])){
				$arResult = $arAttributes[$strAttribute]['VALUES'];
			}
		}
		return $arResult;
	}

	/**
	 * Is alternative categories mode
	 */
	public function isAlternativeMode($intIBlockId){
		return $this->getIBlockParams($intIBlockId)['CATEGORIES_ALTERNATIVE'] == 'Y';
	}

	/**
	 * Get alternative categories list
	 */
	public function getAlternativeCategoryList($intIBlockId){
		$arResult = $this->getIBlockParams($intIBlockId)['CATEGORIES_ALTERNATIVE_LIST'];
		if(!is_array($arResult)){
			$arResult = [];
		}
		$arResult = array_filter($arResult);
		return $arResult;
	}

	/**
	 * Get category setup in profile
	 * @return array [id => name]
	 */
	protected function getSetupCategories($intIBlockId){
		$arResult = [];
		if($this->isAlternativeMode($intIBlockId)){
			foreach($this->getAlternativeCategoryList($intIBlockId) as $strCategoryId){
				$arResult[$strCategoryId] = $this->formatCategoryName($strCategoryId);
			}
		}
		else{
			$arRedefinitions = $this->call('CategoryRedefinition::getForProfile', [$this->intProfileId, $intIBlockId]);
			foreach($arRedefinitions as $strRedefinition){
				if(preg_match('#^\[(.*?)\]\s*(.*?)$#', $strRedefinition, $arMatch)){
					$arResult[$arMatch[1]] = $arMatch[2];
				}
			}
		}
		return $arResult;
	}

	/**
	 * Get category name by id
	 */
	public function formatCategoryName($strCategoryId){
		$strResult = $strCategoryId;
		$arCategories = $this->getCategoriesList($this->intProfileId);
		if(Helper::strlen($strCategory = $arCategories[$strCategoryId])){
			$strResult = $strCategory;
		}
		return $strResult;
	}

	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildXml(&$arXmlTags, &$arXmlAttr, &$strXmlItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$intIBlockId = $arElement['IBLOCK_ID'];
		$intMainIBlockId = $this->getMainIBlockId($intIBlockId);
		# Detect category name
		if($this->isAlternativeMode($intMainIBlockId)){
			$strCategoryName = $arFields['сategoryId'];
		}
		else{
			$strCategoryName = $this->getCategoryRedefinition($intMainIBlockId, $arElementSections);
		}
		unset($arXmlTags['сategoryId']);
		# Detect youlaCategoryId, youlaSubcategoryId
		$this->detectCategoryId($strCategoryName, $arXmlTags);
		# Put all attribute values to separated (by category) array
		$arAttributes = $this->separateAttributes($arElement['ID'], $arXmlTags);
		# Use just attributes from target catgory
		if(Helper::strlen($intCategoryId = $this->parseCategoryId($strCategoryName))){
			if(is_array($arAttributes[$intCategoryId]) && !empty($arAttributes[$intCategoryId])){
				$arXmlTags = array_merge($arXmlTags, $arAttributes[$intCategoryId]);
			}
		}
		else{
			$strError = static::getMessage('ERROR_EMPTY_CATEGORY_ID', ['#ELEMENT_ID#' => $arElement['ID']]);
			return ['ERRORS' => [$strError]];
		}
	}

	/**
	 * Parse category id from full name
	 */
	protected function parseCategoryId($strCategoryName){
		$strResult = '';
		$strCategoryName = trim($strCategoryName);
		if(is_numeric($strCategoryName)){
			$strResult = $strCategoryName;
		}
		else{
			if(preg_match('#\[\s*(\d+)\s*\]#', $strCategoryName, $arMatch)){
				$strResult = $arMatch[1];
			}
		}
		return $strResult;
	}

	/**
	 * Get Youla section name from redefinition
	 */
	protected function getCategoryRedefinition($intMainIBlockId, $mSectionId){
		$strResult = null;
		$arQuery = [
			'filter' => [
				'PROFILE_ID' => $this->intProfileId,
				'IBLOCK_ID' => $intMainIBlockId,
				'SECTION_ID' => $mSectionId,
			],
			'select' => ['SECTION_NAME'],
		];
		if($arRedefinition = $this->call('CategoryRedefinition::getList', [$arQuery])->fetch()){
			$strResult = $arRedefinition['SECTION_NAME'];
		}
		return $strResult;
	}

	/**
	 * Detect category id and subcategory id, add it to XML
	 */
	protected function detectCategoryId($strCategoryName, &$arXmlTags){
		if($intProductSubcategoryId = $this->parseCategoryId($strCategoryName)){
			foreach($this->getYoulaData() as $intCategoryId => $arCategory){
				if(is_array($arCategory['CHILDREN'])){
					foreach($arCategory['CHILDREN'] as $intSubcategoryId => $arSubcategory){
						if($intSubcategoryId == $intProductSubcategoryId){
							$arXmlTags = array_merge([
								'youlaCategoryId' => Xml::addTag([$intCategoryId]),
								'youlaSubcategoryId' => Xml::addTag([$intSubcategoryId]),
							], $arXmlTags);
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Separate attribute from other XML, transform values
	 */
	protected function separateAttributes($intElementId, &$arItem, $intCategoryId=null){
		$arResult = [];
		#
		$arFields = $this->getUniversalFields($this->intProfileId, $intIBlockId);
		#
		foreach($arItem as $strAttribute => $arItemXml){
			if(preg_match('#^(\d+)_(.*?)$#', $strAttribute, $arMatch)){
				if(is_array($arAllowedValues = $arFields[$strAttribute]['ALLOWED_VALUES_CUSTOM_DATA'])){
					$arItemXml[0]['#'] = trim($arItemXml[0]['#']);
					$value = &$arItemXml[0]['#'];
					if(is_numeric($value) && $value == intVal($value) && $value > 0){
						$arResult[$arMatch[1]][$arMatch[2]] = $arItemXml;
					}
					elseif(($intValueId = array_search($value, $arAllowedValues)) !== false){
						$value = $intValueId;
						$arResult[$arMatch[1]][$arMatch[2]] = $arItemXml;
					}
					else{
						$strError = sprintf(static::getMessage('ERROR_WRONG_ATTRIBUTE_VALUE', [
							'#ELEMENT_ID#' => $intElementId,
							'#VALUE#' => $value,
							'#ATTRIBUTE#' => $strAttribute,
						]));
						if($this->getPreviewMode()){
							Helper::P($strError);
						}
						else{
							$this->addToLog($strError);
						}
					}
				}
				else{
					$arResult[$arMatch[1]][$arMatch[2]] = $arItemXml;
				}
				unset($arItem[$strAttribute]);
			}
		}
		if(!is_null($intCategoryId)){
			$arResult = $arResult[$intCategoryId];
			if(!is_array($arResult)){
				$arResult = [];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#" ?>'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_DATE#' => date('Y-m-d H:i'),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'allowed_values_custom':
				$arJsonResult['HTML'] = $this->getAllowedValuesContent($arParams['GET']);
				break;
		}
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesContent($arGet){
		ob_start();
		$intIBlockId = $arGet['iblock_id'];
		$strField = $arGet['field'];
		require __DIR__.'/allowed_values/popup.php';
		return ob_get_clean();
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockId, $arIBlockParams){
		return $this->includeHtml(__DIR__.'/categories/settings.php', [
			'IBLOCK_ID' => $intIBlockId,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}

}

?>