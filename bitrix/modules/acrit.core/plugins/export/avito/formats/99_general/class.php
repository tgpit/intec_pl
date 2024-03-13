<?
/**
 * Acrit Core: Avito universal format
 * @documentation https://www.avito.ru/autoload/documentation/templates/1202059
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin;

class AvitoGeneral extends UniversalPlugin {
	
	const DATE_UPDATED = '2023-03-06';
	
	const DATE_FORMAT = 'Y-m-d\TH:i:sP';

	const CATEGORY_CHAIN_SEPARATOR = ' / ';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'avito.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = [];
	
	# Basic settings
	protected $bAdditionalFields = false;
	protected $bCategoriesExport = true;
	protected $bCategoriesStrict = true;
	protected $bCategoriesUpdate = false;
	protected $bHideCategoriesUpdateButton = true;
	protected $bCurrenciesExport = false;
	protected $bCategoriesList = true;
	
	# XML settings
	protected $strXmlItemElement = 'Ad';
	protected $intXmlDepthItems = 1;

	# Misc
	protected $obParsedown;
	protected $arXmlMultiply = ['Images.Image@url'];
	protected $arCacheCategoriesPlain = [];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$arResult = [];
		#
		$intMainIBlockId = $this->getMainIBlockId($intIBlockId);
		# Get common fields
		if(!empty($arCommonFields = $this->getFileJson('common_fields'))){
			$arResult['HEADER_COMMON'] = ['NAME' => $this->convertFromUtf8($arCommonFields['name'])];
			foreach($arCommonFields['fields'] as $arField){
				$strFieldCode = $this->getFieldCode($arField);
				$arResult[$strFieldCode] = $this->buildField($arField);
			}
		}
		# Field for alternative categories
		$arResult['CategoryId'] = ['CONST' => ''];
		# Get category fields
		$arSetupCategories = $this->getSetupCategories($intMainIBlockId);
		$strCommonName = static::getMessage('GROUP_COMMON');
		foreach($arSetupCategories as $strCategoryId => $strCategoryName){
			if(!empty($arCategoryFields = $this->getFileJson($strCategoryId))){
				$arGroups = &$arCategoryFields['fields']['field_groups'];
				# First, use common fields
				foreach($arGroups as $key => $arGroup){
					if($arGroup['name'] == $strCommonName){
						foreach($arGroup['fields'] as $arField){
							$strFieldCode = $this->getFieldCode($arField);
							if(!isset($arResult[$strFieldCode])){
								$arResult[$strFieldCode] = $this->buildField($arField);
							}
						}
						unset($arGroups[$key]);
						break;
					}
				}
				# Second, use other fields
				$arResult['HEADER_'.$strCategoryId] = [
					'NAME' => $this->convertFromUtf8($strCategoryName),
				];
				if(Helper::strlen($strUrl = $arCategoryFields['fields']['file_template']['xml'])){
					$arResult['HEADER_'.$strCategoryId]['DESCRIPTION'] = static::getMessage('GROUP_XML_EXAMPLE', [
						'#URL#' => $strUrl,
					]);
				}
				foreach($arGroups as $key => $arGroup){
					foreach($arGroup['fields'] as $arField){
						$strFieldCode = sprintf('%s_%s', $strCategoryId, $this->getFieldCode($arField));
						$arResult[$strFieldCode] = $this->buildField($arField);
					}
				}
			}
		}
		#
		return $arResult;
	}

	/**
	 * Build one field from avito field json
	 */
	public function buildField($arJsonField){
		$strDescription = htmlspecialcharsbx($this->convertFromUtf8($arJsonField['description']));
		$strExample = htmlspecialcharsbx($this->convertFromUtf8($arJsonField['example']));
		$arResult = [
			'NAME' => static::getMessage('F_NAME_'.$arJsonField['tag']),
			'DESCRIPTION' => $this->convertFromMarkdown($strDescription),
			'CUSTOM_REQUIRED' => !!$arJsonField['required'],
			'AVITO_FIELD_JSON' => $arJsonField, # For debug
			'DISPLAY_CODE' => $arJsonField['tag'],
		];
		if(isset($arJsonField['dependency'])){
			$arResult['DESCRIPTION'] .= static::getMessage('DESCRIPTION_FIELD_DEPENDENCY', [
				'#TEXT#' => '<ul style="margin:0 0 0 20px;padding:0;"><li>'.implode('</li><li>', $arJsonField['dependency']).'</li></ul>',
			]);
		}
		if(Helper::strlen($arJsonField['example'])){
			$arResult['DESCRIPTION'] .= static::getMessage('DESCRIPTION_FIELD_EXAMPLE', [
				'#EXAMPLE#' => nl2br($strExample),
			]);
		}
		$arResult['DESCRIPTION'] .= static::getMessage('DESCRIPTION_FIELD_ID', [
			'#ID#' => $arJsonField['id'],
		]);
		$arResult['DESCRIPTION'] = sprintf('<div class="acrit_exp_avito_hint">%s</div>',
			str_replace('<a ', '<a target="_blank" ', $arResult['DESCRIPTION']));
		# Fill empty name
		if(!Helper::strlen($arResult['NAME']) && Helper::strlen($strDescription)){
			$arDescription = preg_split('#\s*\n+\s*#', $strDescription);
			# Remove "**Vnimanie** <Text>" or "**Vnimanie: <text>**"
			foreach($arDescription as $key => $value){
				if(Helper::strpos($value, '**') !== false){
					unset($arDescription[$key]);
				}
			}
			$strName = reset($arDescription);
			# Convert to markdown (some fields has markdown in begin of text: Latitude, Longitude, ...) and strip tags
			$strName = strip_tags($this->convertFromMarkdown($strName));
			# Remove extra text (before)
			$arEndAfterChars = ['»', '"'];
			foreach($arEndAfterChars as $strChar){
				$strName = str_replace($strChar, $strChar.'.', $strName);
			}
			# Remove extra text (after)
			$arEndChars = ['.', ',', '—', '---'];
			foreach($arEndChars as $strChar){
				if(($intPos = Helper::strpos($strName, $strChar)) > 0){
					$strName = Helper::substr($strName, 0, $intPos);
				}
			}
			$arResult['NAME'] = $strName;
		}
		# Allowed values
		if($arJsonField['type'] == 'select'){
			$arResult['ALLOWED_VALUES_CUSTOM'] = true;
			$arResult['ALLOWED_VALUES_CUSTOM_DATA'] = $arJsonField['values'];
			$arResult['ALLOWED_VALUES_CUSTOM_DATA_TITLE'] = $arJsonField['values_title'];
			$arResult['ALLOWED_VALUES_CUSTOM_LINK'] = $arJsonField['values_link'];
		}
		elseif($arJsonField['type'] == ''){
			$arResult['ALLOWED_VALUES_CUSTOM'] = true;
			$arResult['ALLOWED_VALUES_CUSTOM_DATA'] = $arJsonField['values'] ?? $arJsonField['values_by_group'];
			$arResult['ALLOWED_VALUES_CUSTOM_BY_GROUPS'] = true;
		}
		# Default from Avtio
		if(is_array($arJsonField['default'])){
			if(is_string($arJsonField['default']['value']) && Helper::strlen($arJsonField['default']['value'])){
				$arResult['CONST'] = $arJsonField['default']['value'];
			}
		}
		elseif(is_array($arJsonField['values']) && count($arJsonField['values']) === 1){
			if($arJsonField['type'] != 'checkbox'){
				$arResult['CONST'] = $arJsonField['values'][0]['value'];
			}
		}
		# Guess values and params
		switch($arJsonField['tag']){
			case 'Id':
				$arResult['FIELD'] = 'ID';
				break;
			case 'Images':
				$arResult['FIELD'] = ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO'];
				$arResult['MULTIPLE'] = true;
				break;
			case 'Title':
				$arResult['FIELD'] = ['NAME'];
				$arResult['FIELD_PARAMS'] = ['HTML2TEXT' => 'Y', 'HTML2TEXT_mode' => 'simple'];
				break;
			case 'Description':
				$arResult['FIELD'] = ['DETAIL_TEXT'];
				$arResult['CDATA'] = true;
				break;
			case 'Price':
				$arResult['FIELD'] = ['CATALOG_PRICE_1__WITH_DISCOUNT'];
				break;
			case 'ManagerName':
				$arResult['CONST'] = '';
				break;
			case 'ContactPhone':
				$arResult['CONST'] = '';
				break;
			case 'VideoURL':
				$arResult['FIELD'] = ['PROPERTY_VIDEO', 'PROPERTY_YOUTUBE', 'PROPERTY_VIDEO_YOUTUBE'];
				break;
			case 'Condition':
				$arResult['CONST'] = is_array($arJsonField['values']) && !empty($arJsonField['values']) ? $arJsonField['values'][0]['value'] : '';
				break;
			case 'AdType':
				$arResult['CONST'] = $this->guessAdType($arJsonField['values']);
				break;
			case 'Brand':
				$arResult['FIELD'] = 'PROPERTY_BRAND';
				break;
			case 'Color':
				$arResult['FIELD'] = 'PROPERTY_COLOR';
				break;
			case 'Size':
				$arResult['FIELD'] = 'PROPERTY_SIZE';
				break;
			# Card
			case 'Wheels':
				$arResult['PARAMS'] = ['ENTITY_DECODE' => 'Y', 'HTMLSPECIALCHARS' => 'skip'];
				break;
		}
		#
		if($arJsonField['type'] == 'checkbox'){
			$arResult['MULTIPLE'] = true;
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
			$arResult = array_map(function($arItem){
				return $arItem['name_full'];
			}, $this->getAvitoCategories());
		}
		return $arResult;
	}

	/**
	 * Get Avito categories
	 */
	public function getAvitoCategories(){
		static $arCache = [];
		if(empty($arCache)){
			$arJsonCategories = $this->getFileJson('categories');
			$this->getAvitoCategoriesPlain($arCache, $arJsonCategories);
		}
		return $arCache;
	}
	private function getAvitoCategoriesPlain(&$arResult, $arJsonCategories, $arChain=[]){
		if(is_array($arJsonCategories)){
			usort($arJsonCategories, function($a, $b){
				return strcmp($a['name'], $b['name']);
			});
			foreach($arJsonCategories as $arCategory){
				$arCategoryChain = array_merge($arChain, [$arCategory['name']]);
				if($arCategory['show_fields']){
					$arCategory['name_full'] = sprintf('[%s] %s', $arCategory['id'], implode(static::CATEGORY_CHAIN_SEPARATOR, $arCategoryChain));
					$arResult[$arCategory['id']] = $arCategory;
				}
				if(is_array($arCategory['nested'])){
					$this->getAvitoCategoriesPlain($arResult, $arCategory['nested'], $arCategoryChain);
				}
			}
		}
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
			$strCategoryName = $arFields['CategoryId'];
		}
		else{
			$strCategoryName = $this->getCategoryRedefinition($intMainIBlockId, $arElementSections);
		}
		# Put all attribute values to separated (by category) array
		$arAttributes = $this->separateAttributes($arXmlTags);
		# Use just attributes from target catgory
		if(Helper::strlen($strCategoryId = $this->parseCategoryId($strCategoryName))){
			$arXmlTags = array_merge($arXmlTags, $arAttributes[$strCategoryId]);
		}
		else{
			$strError = static::getMessage('ERROR_EMPTY_CATEGORY_ID', ['#ELEMENT_ID#' => $arElement['ID']]);
			return ['ERRORS' => [$strError]];
		}
		# Remove trash
		unset($arXmlTags['CategoryId']);
	}

	/**
	 * 
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
	 * Get avito section name from redefinition
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
	 * 
	 */
	protected function separateAttributes(&$arItem, $strCategoryId=null){
		$arResult = [];
		foreach($arItem as $key => $arItemXml){
			if(preg_match('#^(\d+)_(.*?)$#', $key, $arMatch)){
				$arResult[$arMatch[1]][$arMatch[2]] = $arItemXml;
				unset($arItem[$key]);
			}
		}
		if(!is_null($strCategoryId)){
			$arResult = $arResult[$strCategoryId];
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
		$strXml .= '<Ads formatVersion="3" target="Avito.ru">'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</Ads>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_DATE#' => date('Y-m-d\TH:i:s'),
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
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