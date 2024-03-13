<?
/**
 * Acrit Core: Yandex education plugin
 * @documentation https://yandex.ru/support/webmaster/search-appearance/education.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

class YandexWebmasterEducation extends \Acrit\Core\Export\UniversalPlugin {
	
	const DATE_UPDATED = '2023-06-26';

	const XML_PARAM_TEMP = '__param__';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'yandex_education.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'EUR', 'UAH', 'KZT', 'BYN'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesStrict = true;
	protected $bCurrenciesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	// protected $arXmlMultiply = ['room-space.value', 'location.metro.name'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# General
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL', 'REQUIRED' => true];
		$arResult['_category'] = ['FIELD' => ''];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1', 'REQUIRED' => true];
		$arResult['currencyId'] = ['CONST' => 'RUR', 'REQUIRED' => true];
		$arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'REQUIRED' => true, 'FIELD_PARAMS' => ['HTML2TEXT' => 'Y', 'HTML2TEXT_mode' => 'simple']];

		# Additional
		$arResult['HEADER_ADDITIONAL'] = [];
		$arAvailableParams = [
			'additional_category' => [],
			'content_url' => ['MULTIPLE' => true],
			'discount_price' => [],
			'discount_last_date' => [],
			'subscription_price' => [],
			'installment_payment' => [],
			'installment_payment@unit' => [],
			'monthly_price' => [],
			'monthly_discount_price' => [],
			'monthly_discount_last_date' => [],
			'nearest_date' => [],
			'duration' => [],
			'duration@unit' => [],
			'plan' => ['CDATA' => true, 'MULTIPLE' => true],
			'plan@unit' => ['MULTIPLE' => true],
			'plan@hours' => ['MULTIPLE' => true],
			'learning_format' => [],
			'has_video_lessons' => [],
			'has_text_lessons' => [],
			'has_webinars' => [],
			'has_homework' => [],
			'has_simulators' => [],
			'has_community' => [],
			'difficulty' => [],
			'training_type' => [],
			'has_free_part' => [],
			'has_employment' => [],
			'learning_result' => [],
			'hours_per_week' => [],
			'classes' => [],
		];
		foreach($arAvailableParams as $strParam => $arParam){
			$strField = sprintf('%s.%s', static::XML_PARAM_TEMP, $strParam);
			$arParam['NAME'] = $this->getXmlParamNameByCode($strParam);
			$arParam['DESCRIPTION'] = $this->getXmlParamHintByCode($strParam);
			if(strpos($strParam, '@') === false){
				$arParam['DISPLAY_CODE'] = sprintf('<param name="%s">', htmlspecialcharsbx($arParam['NAME']));
			}
			else{
				$arParamExploded = explode('@', $strParam);
				$strParam1 = reset($arParamExploded);
				$strParam2 = end($arParamExploded);
				$arParam['DISPLAY_CODE'] = sprintf('<param name="%s" %s="...">',
					htmlspecialcharsbx($this->getXmlParamNameByCode($strParam1)), $strParam2);
			}
			$arResult[$strField] = $arParam;
		}
		#
		return $arResult;
	}

	/**
	 * Get lang-phrase for param name
	 */
	protected function getXmlParamNameByCode($strParam){
		return static::getMessage('PARAM_NAME_'.$strParam);
	}

	/**
	 * Get lang-phrase for param hint
	 */
	protected function getXmlParamHintByCode($strParam){
		return static::getMessage('PARAM_HINT_'.$strParam);
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['YML_NAME'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/name.php'),
			'SORT' => 100,
		];
		$arSettings['YML_COMPANY'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/company.php'),
			'SORT' => 110,
		];
		$arSettings['YML_EMAIL'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/email.php'),
			'SORT' => 120,
		];
		$arSettings['YML_PICTURE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/picture.php'),
			'SORT' => 130,
		];
		$arSettings['YML_DESCRIPTION'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/description.php'),
			'SORT' => 140,
		];
	}
	
	/**
	 *	Get all categories
	 */
	public function getCategoriesList($intProfileId){
		$arCategories = [];
		$strFile = __DIR__.'/education_rubricator.xml';
		if(Helper::strlen($strFileContent = file_get_contents($strFile))){
			$arXml = \Acrit\Core\Xml::xmlToArray($strFileContent);
			if(isset($arXml['categories']['#']['category']) && is_array($arXml['categories']['#']['category'])){
				foreach($arXml['categories']['#']['category'] as $arCategory){
					$arCategories[$arCategory['@']['id']] = [
						'ID' => $arCategory['@']['id'],
						'PARENT_ID' => $arCategory['@']['parentId'] ?? null,
						'NAME' => $arCategory['#'],
					];
				}
			}
		}
		#
		$arResult = [];
		foreach($arCategories as $arCategory){
			if(isset($arCategory['PARENT_ID'])){
				$arParent = $arCategories[$arCategory['PARENT_ID']];
				$arResult[$arCategory['ID']] = sprintf('[%d] %s / %s', $arCategory['ID'], $arParent['NAME'], $arCategory['NAME']);
			}
			else{
				$arResult[$arCategory['ID']] = sprintf('[%d] %s', $arCategory['ID'], $arCategory['NAME']);
			}
			
		}
		return $arResult;
	}
	
	/**
	 *	Step: check
	 */
	// public function stepCheck($intProfileID, $arData){
	// 	$this->arData['SESSION']['CATEGORIES'] = ['!!! OK !!!'];
	// 	return parent::stepCheck($intProfileID, $arData);
	// }

	protected function onUpBuildXml(&$arXmlTags, &$arXmlAttr, &$strXmlItem, &$arElement, &$arFields, &$arElementSections, &$mDataMore){
		# Obtain category
		$this->obtainCategoryId($arXmlTags, $arFields, $this->getMainIBlockId($arElement['IBLOCK_ID']), $arElementSections);
		# RUB -> RUR
		if(isset($arXmlTags['currencyId'][0]['#']) && $arXmlTags['currencyId'][0]['#'] == 'RUB'){
			$arXmlTags['currencyId'][0]['#'] = 'RUR';
		}
		# Custom params
		if(!isset($arXmlTags['param']) || !is_array($arXmlTags['param'])){
			$arXmlTags['param'] = [];
		}
		if(isset($arXmlTags['__param__'])){
			$arCustomParams = [];
			$intPlanIndex = 1;
			if(is_array($arXmlTags['__param__'])){
				foreach($arXmlTags['__param__'] as $arParamGroupItem){
					if(isset($arParamGroupItem['#']) && is_array($arParamGroupItem['#'])){
						foreach($arParamGroupItem['#'] as $strParam => $arParams){
							foreach($arParams as $arParamItem){
								if(!isset($arParamItem['@']) || !is_array($arParamItem['@'])){
									$arParamItem['@'] = [];
								}
								$arParamItem['@'] = array_merge([
									'name' => $this->getXmlParamNameByCode($strParam),
								], $arParamItem['@']);
								if($strParam == 'plan'){
									$arParamItem['@']['order'] = $intPlanIndex++;
								}
								$arCustomParams[] = $arParamItem;
							}
						}
					}
				}
			}
			if(!empty($arCustomParams)){
				$arXmlTags['param'] = array_merge($arCustomParams, $arXmlTags['param']);
			}
			unset($arXmlTags['__param__']);
		}
	}

	/**
	 * 
	 */
	protected function obtainCategoryId(&$arXmlTags, &$arFields, $intIBlockId, $arSections){
		$strCategoryName = null;
		if(isset($arFields['_category']) && Helper::strlen($arFields['_category'])){
			$strCategoryName = $arFields['_category'];
			if(isset($arXmlTags['_category'])){
				unset($arXmlTags['_category']);
			}
		}
		else{
			if($intSectionId = is_array($arSections) && !empty($arSections) ? reset($arSections) : null){
				static $arRedefinitionsCache = [];
				$strCacheKey = sprintf('%d_%d', $this->intProfileId, $intIBlockId);
				if(!isset($arRedefinitionsCache[$strCacheKey])){
					$arRedefinitionsAll = Helper::call($this->strModuleId,
						'CategoryRedefinition', 'getForProfile', [$this->intProfileId, $intIBlockId]);
					if(is_array($arRedefinitionsAll)){
						$arRedefinitionsCache[$strCacheKey] = $arRedefinitionsAll;
					}
				}
				$strCategoryName = $arRedefinitionsCache[$strCacheKey][$intSectionId] ?? null;
			}
		}
		if(is_string($strCategoryName) && Helper::strlen($strCategoryName)){
			$intCategoryId = null;
			if(is_numeric($strCategoryName)){ // If category='123', use '123'
				$intCategoryId = $strCategoryName;
			}
			elseif(preg_match('#\[(\d+)\]#', $strCategoryName, $arMatch)){ // If category='[123] category name', use '123'
				$intCategoryId = $arMatch[1];
			}
			else{ // If category='category name', use categories list
				static $arCategoriesList;
				if(is_null($arCategoriesList)){
					$arCategoriesList = array_map(function($item){
						return preg_replace('#^\[\d+\]\s*#', '', $item);
					}, $this->getCategoriesList($this->intProfileId));
				}
				if($intCategoriesListKey = array_search($strCategoryName, $arCategoriesList)){
					$intCategoryId = $intCategoriesListKey;
				}
			}
			if($intCategoryId){
				$arXmlTags = array_merge([
					'categoryId' => \Acrit\Core\Xml::addTag([$intCategoryId]),
				], $arXmlTags);
			}
		}
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#" standalone="yes"?>'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<name>#YML_NAME#</name>'.static::EOL;
		$strXml .= '		<company>#YML_COMPANY#</company>'.static::EOL;
		$strXml .= '		<url>#YML_URL#</url>'.static::EOL;
		$strXml .= '		<email>#YML_EMAIL#</email>'.static::EOL;
		$strXml .= '		<picture>#YML_PICTURE#</picture>'.static::EOL;
		$strXml .= '		<description>#YML_DESCRIPTION#</description>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		#$strXml .= '		<sets>#SETS#</sets>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => toLower($this->arParams['ENCODING']),
			'#XML_DATE#' => date('Y-m-d H:i'), # 2018-07-17 12:10
			#
			'#YML_NAME#' => htmlspecialcharsbx($this->arParams['YML_NAME']),
			'#YML_COMPANY#' => htmlspecialcharsbx($this->arParams['YML_COMPANY']),
			'#YML_URL#' => Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y'),
			'#YML_EMAIL#' => htmlspecialcharsbx($this->arParams['YML_EMAIL']),
			'#YML_PICTURE#' => Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y', $this->arParams['YML_PICTURE']),
			'#YML_DESCRIPTION#' => htmlspecialcharsbx($this->arParams['YML_DESCRIPTION']),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

	/**
	 * Convert RUB to RUR in <currencies>
	 */
	protected function onUpGetXmlCurrencies(&$arCurrencies){
		$arCurrenciesTmp = [];
		foreach($arCurrencies as $strCurrency => $arCurrency){
			if($strCurrency == 'RUB'){
				$strCurrency = $arCurrency['CURRENCY'] = 'RUR';
			}
			$arCurrenciesTmp[$strCurrency] = $arCurrency;
		}
		$arCurrencies = $arCurrenciesTmp;
	}

}

?>