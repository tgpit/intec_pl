<?
/**
 * Acrit Core: Kaspi.kz plugin
 * @documentation https://kaspi.kz/merchantcabinet/support/display/Support/XML
 */

namespace Acrit\Core\Export\Plugins;


use \Acrit\Core\Export\Filter,
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Plugins\KaspiKzHelpers\CategoryTable as Category,
	\Acrit\Core\Export\Plugins\KaspiKzHelpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\KaspiKzHelpers\AttributeValueTable as AttributeValue,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\Field\ValueBase,
	\Bitrix\Main\Context;


class KaspikzGeneralApi extends Kaspikz {
	
	const DATE_UPDATED = '2022-06-07';
	const ATTRIBUTE_ID = 'attribute_%s_%s';
	
	const ATTR_ID_IMAGE = 4194;
	const ATTR_ID_IMAGES = 4195;
	const ATTR_ID_YOUTUBE_CODE = 4074;
	const ATTR_ID_JSON_RICH_CONTENT = 11254;
	
	protected static $bSubclass = true;
	protected $cache_dir = '/upload/acrit.core/.tmp/kaspi.kz/api/';
	
	# General	
	protected $strDefaultFilename = '';	
	protected $arSupportedFormats = ['JSON']; // Формат выгрузки - JSON
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'USD', 'EUR'];
	
	# Basic settings
	protected $bApi = true; // Выгружаем не в файл, а по АПИ
	protected $bCategoriesExport = true; // Нужно чтобы в целом была возможность работать с категориями, хотя категории отдельно не выгружаются	
	//protected $bCategoriesList = true; // В плагине доступен список категорий, необходимо для работы со списком категорий	
	//protected $bCategoriesUpdate = true; // Разрешаем обновлять категории	
	protected $bCategoriesStrict = true; // Важно указывать только категории сервиса
	protected $bCategoryCustomName = true; // Добавляем возможность использовать значение «Использовать поля товаров» в опции «Источник названий категорий»

	protected $bCurrenciesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 1;
	
	protected $arXmlMultiply = ['availability'];
	
	protected $test_count = 0;
	protected $api_categories = [];
	protected $arUsedCategories = [];
	protected $api_attribute_values = [];
	protected $api_attribute_upload_result = [];
	protected $arRedefinitions = [];
	
	# Misc
	protected $strCategoryLevelDelimiter = ' / '; // Символ(ы) для разделения категорий разных уровней (пример: Авто / Оборудование / Магнитолы)
	
	# Other export settings	
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockId){
		
		$arResult = [];
		
		# General
		$arResult['HEADER_GENERAL'] = [];
		$arResult['sku'] = ['FIELD' => 'ID'];
		$arResult['title'] = ['FIELD' => 'NAME'];
		$arResult['brand'] = ['FIELD' => 'IBLOCK__NAME'];
		$arResult['category'] = ['FIELD' => 'IBLOCK__NAME'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT'];
		$arResult['attributes'] = ['FIELD' => ['CATALOG_WEIGHT', 'CATALOG_BARCODE', 'PROPERTY_COLOR'], 'MULTIPLE' => true];
		$arResult['images'] = ['FIELD' => 'PROPERTY_MORE_PHOTO'];
		$arData = $this->getDataForFields($intIBlockId);
		
		foreach($arData as $arCategory){
			$arResult['HEADER_CATEGORY_'.$arCategory['CATEGORY_ID']] = [
				'NAME' => $this->formatCategoryName($arCategory['CATEGORY_ID'], $arCategory['NAME']),
				'NORMAL_CASE' => true,
			];
			foreach($arCategory['ATTRIBUTES'] as $arAttribute){
				$strAttributeId = sprintf(static::ATTRIBUTE_ID, $arCategory['CATEGORY_ID'], $arAttribute['ATTRIBUTE_ID']);
				
				$arField = [
					'NAME' => $arAttribute['NAME'],
					'DISPLAY_CODE' => 'attribute_'.$arAttribute['ATTRIBUTE_ID'],
					'DESCRIPTION' => $this->descriptionToHint($arAttribute['DESCRIPTION']),
					'REQUIRED' => count($arData) == 1 && $arAttribute['IS_REQUIRED'] == 'Y' 
						|| $arAttribute['FORCE_REQUIRED'] == 'Y',
					'MULTIPLE' => false,
					'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
					'PARAMS' => [],
				];
				if($arAttribute['DICTIONARY_ID']){
					$arField['ALLOWED_VALUES_CUSTOM'] = true;
				}
				$this->guessDefaultValue($arField, $arAttribute);
				$arResult[$strAttributeId] = $arField;
			}
		}		
		
		return $arResult;
	}
	
	/**
	 *	Format category name with ID
	 */
	protected function formatCategoryName($intCategoryId, $strCategoryName=null){
		if(!strlen($strCategoryName) && $intCategoryId > 0){
			$strCategoryName = $this->getCategoryName($intCategoryId);
		}
		if(!is_numeric($intCategoryId) || !$intCategoryId){
			return $strCategoryName;
		}
		return sprintf('[%d] %s', $intCategoryId, $strCategoryName);
	}
	
	/**
	 *	Try to guess default value
	 */
	protected function guessDefaultValue(&$arField, $arAttribute){
		if($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_IMAGE){ // Izobrazhenie
			$arField['FIELD'] = ['DETAIL_PICTURE'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_IMAGES){ // Izobrazheniya
			$arField['FIELD'] = ['PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS', 'PROPERTY_PICS_NEWS'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'space'];
			$arField['PARAMS'] = ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'space'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_YOUTUBE_CODE){ // YouTube
			$arField['FIELD'] = ['PROPERTY_YOUTUBE', 'PROPERTY_VIDEO'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_JSON_RICH_CONTENT){ // RichContent
			$arField['FIELD'] = [''];
			$arField['FIELD_PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
			$arField['PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_BRAND')){
			$arField['FIELD'] = ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_GROUP')){
			$arField['FIELD'] = ['ID'];
		}
	}
	
	/**
	 *	Convert simple description text to hint
	 */
	protected function descriptionToHint($strDescription){
		$strDescription = htmlspecialcharsbx($strDescription);
		$strPattern = '#((http|https|ftp|ftps)://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(\/\S*)?)#';
		$strReplace = '<a href="$1" target="_blank">$1</a>';
		$strDescription = preg_replace($strPattern, $strReplace, $strDescription);
		$strDescription = nl2br($strDescription);
		return $strDescription;
	}
	
	/**
	 *	Get used categories from redefinitions
	 */
	protected function getUsedCategories($intIBlockId=null, $bJustIds=false){
		$arResult = [];
		$arIBlockParams = $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS'];		
		if($arIBlockParams['CATEGORIES_ALTERNATIVE'] == 'Y'){
			if(is_array($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'])){
				foreach($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'] as $intCategoryId){
					if(is_numeric($intCategoryId)){
						$arResult[$intCategoryId] = $this->getCategoryName($intCategoryId);
					}
				}
			}
		}
		else{
			$arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', 
				[$this->intProfileId, $intIBlockId]);
			$arSelectedCategories = Exporter::getInvolvedSectionsID($intIBlockId, 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_ID'], 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_MODE']);
			$arUsedCategories = array_intersect_key($arRedefinitions, array_flip($arSelectedCategories));
			foreach($arUsedCategories as $strCategoryName){
				if(strlen($strCategoryName)){
					$strCategoryId = $this->parseCategoryId($strCategoryName);
					$arResult[$strCategoryId] = $strCategoryName;
				}
			}
		}
		if($bJustIds){
			$arResult = array_keys($arResult);
		}		
		return $arResult;
	}
	
	/**
	 *	Prepare fields data for getUniversalFields()
	 */
	protected function getDataForFields($intIBlockId){
		$arResult = [];
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
			? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		$arUsedCategories = $this->getUsedCategories($intMainIBlockId);
		$arUsedCategoriesId = array_keys($arUsedCategories);
		$arNotRequiredAttributes = $this->arProfile['IBLOCKS'][$intMainIBlockId]['PARAMS']['ATTRIBUTES_CANCEL_REQUIRED'];
		$arNotRequiredAttributes = Helper::explodeValues($arNotRequiredAttributes);
		$arNotRequiredAttributes = array_filter($arNotRequiredAttributes);
		if(!empty($arUsedCategoriesId)){
			$resCategories = Category::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'NAME'],
			]);
			while($arCategory = $resCategories->fetch()){
				$arCategory['ATTRIBUTES'] = [];
				$arResult[$arCategory['CATEGORY_ID']] = $arCategory;
			}			
			$resAttributes = Attribute::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'ATTRIBUTE_ID', 'DICTIONARY_ID', 'NAME', 'DESCRIPTION', 'TYPE', 
					'IS_COLLECTION', 'IS_REQUIRED', 'GROUP_ID', 'GROUP_NAME'],
			]);				
			while($arAttribute = $resAttributes->fetch()){
				if(in_array($arAttribute['ATTRIBUTE_ID'], $arNotRequiredAttributes)){
					$arAttribute['IS_REQUIRED'] = 'N';
				}
				$arResult[$arAttribute['CATEGORY_ID']]['ATTRIBUTES'][$arAttribute['ATTRIBUTE_ID']] = $arAttribute;
			}			
		}
		# Group
		if(!empty($arResult) && $this->isAttributesGrouped($intMainIBlockId)){
			
			# Find common attributes
			$arCommonAttributes = [];
			foreach($arResult as $arGroup){
				$arCommonAttributes[] = $arGroup['ATTRIBUTES'];
			}
			if(count($arCommonAttributes) > 1){
				$arCommonAttributes = call_user_func_array('array_intersect_key', $arCommonAttributes);
			}
			foreach($arCommonAttributes as $intAttrId => $arAttr){
				if(!$this->isAttributeDictionaryCommon($intAttrId)){
					unset($arCommonAttributes[$intAttrId]);
				}
			}
			# Check required
			foreach($arCommonAttributes as $intAttrId => $arAttr){
				foreach($arResult as $arGroup){
					if($arGroup['ATTRIBUTES'][$intAttrId]['IS_REQUIRED'] == 'Y'){
						$arCommonAttributes[$intAttrId]['IS_REQUIRED'] = 'Y';
						$arCommonAttributes[$intAttrId]['FORCE_REQUIRED'] = 'Y';
					}
				}
			}
			# Remove common attributes from categories
			if(!empty($arCommonAttributes)){
				foreach($arResult as $intGroupId => $arGroup){
					foreach($arCommonAttributes as $intAttrId => $arAttr){
						if(isset($arGroup['ATTRIBUTES'][$intAttrId])){
							unset($arResult[$intGroupId]['ATTRIBUTES'][$intAttrId]);
						}
					}
				}
			}
			# Create new virtual category
			if(!empty($arCommonAttributes)){
				$arResult = array_merge([
					static::GROUPED_CODE => [
						'ID' => false,
						'CATEGORY_ID' => static::GROUPED_CODE,
						'NAME' => static::getMessage('GROUPED_ATTRIBUTES_HEADER'),
						'ATTRIBUTES' => $arCommonAttributes,
					],
				], $arResult);
			}			
		}
		return $arResult;
	}
	
	/**
	 *	Check if attributes are grouped for iblock
	 */
	protected function isAttributesGrouped($intIBlockId){
		return $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS']['GROUP_ATTRIBUTES'] == 'Y';
	}

	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		
	}
	
	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		$mResult = Exporter::RESULT_ERROR;
		if($this->bCron){
			do{
				$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
			}
			while($mResult === Exporter::RESULT_CONTINUE);
		}
		else{
			$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
		}
		return $mResult;
	}
	
	/**
	 *	1) получение по api списка доступных для выгрузки категорий
	 *	2) получение по api списка доступных для каждой выгружаемой и сопоставленной
	 *	категории атрибутов
	 *	3) получение по api списка доступных значений для каждого атрибута
	 */
	protected function kaspiApi_getApiDataBeforeUpload() {
		$this->kaspiApi_getAvailableUploadCategories();
		$this->kaspiApi_getAvailableUploadAttributes();
		$this->kaspiApi_getAvailableUploadAttributeValues();
		
	}
	
	/**
	 *	"[17034083] Category 1 / Category 2 / Product name" => 17034083, &name => "Category 1 / Category 2 / Product name"
	 */
	protected function parseCategoryId(&$strCategoryName){
		if(strlen($strCategoryName) && preg_match('#^\[(\d+)\][\s]*(.*?)$#', $strCategoryName, $arMatch)){
			$strCategoryName = $arMatch[2];
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	3) получение по api списка доступных значений для каждого атрибута
	 */
	protected function kaspiApi_getAvailableUploadAttributeValues() {
		$api_attribute_values_cache_file = $_SERVER["DOCUMENT_ROOT"].$this->cache_dir.'api_attribute_values.txt';
		$this->api_attribute_values = array();
		$request = Context::getCurrent()->getRequest();
		$profile_id = $this->intProfileId;
		if ( !$this->arParams['USE_CACHE'] )
		{
			// HTTP-запрос
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->setHeaders([
			  'Accept: application/json',
			  'X-Auth-Token' => $this->arParams['AUTH_TOKEN'],
			]);			
			foreach ( $this->api_attributes as $cat => $cat_fields )
			{
				$cat_id = $this->getApiCategoryIdByCode($cat);
				foreach ( $cat_fields as $atr_i => $atr_fields )
				{
					$atr_id = $atr_i + 1;
					if ( $atr_fields['type'] == 'enum' )
					{
						$c = urlencode($cat);
						$attr = urlencode($atr_fields['code']);
						
						$strResponse = $obHttp->get(
							'https://kaspi.kz/shop/api/products/classification/attribute/values?c='.$c.'&a='.$attr);
						
						$this->api_attribute_values[$cat][$atr_fields['code']] = json_decode($strResponse, true);
						
						foreach ( $this->api_attribute_values[$cat][$atr_fields['code']] as $atr_val_i => $atr_val_fields )
						{
							$val_id = $atr_i + 1;
							$atr_value_id = $atr_val_i + 1;
							
							$arFields = [
								'PROFILE_ID' => $profile_id,
								'CATEGORY_ID' => $cat_id,
								'ATTRIBUTE_ID' => $atr_id,
								'ATTRIBUTE_CODE' => $atr_fields['code'],
								'DICTIONARY_ID' => 1,
								'VALUE_ID' => $atr_value_id,
								'VALUE' => $atr_val_fields['name'],
								'CODE' => $atr_val_fields['code'],
								'SESSION_ID' => session_id(),
								'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
							];
							
							$arFilter = [
								'PROFILE_ID' => $arFields['PROFILE_ID'],
								'CATEGORY_ID' => $arFields['CATEGORY_ID'],
								'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
								'VALUE_ID' => $arFields['VALUE_ID'],
								//'ATTRIBUTE_CODE' => $arFields['ATTRIBUTE_CODE'],
							];
							$resDBItem = AttributeValue::getList(['filter' => $arFilter, 'select' => ['ID']]);
							if($arDbItem = $resDBItem->fetch()){
								AttributeValue::update($arDbItem['ID'], $arFields);
							}
							else{
								AttributeValue::add($arFields);
							}
						}
					}
				}
			}
			//$this->addToLog($this->api_attribute_values);
		}
		else {
			$this->api_attribute_values = array();
			
			$resDBItem = AttributeValue::getList(['filter' => [], 'select' => ['ID', 'CODE', 'VALUE', 'ATTRIBUTE_CODE']]);
			
			foreach ( $this->api_attributes as $cat => $cat_fields )
			{				
				$cat_code = $this->getApiCategoryCodeById($cat);
				foreach ( $cat_fields as $atr_i => $atr_fields )
				{
					while ( $arDbItem = $resDBItem->fetch() )
					{
						$rec = array('code' => $arDbItem['CODE'], 'name' => $arDbItem['VALUE']);
						$this->api_attribute_values[$cat_code][$arDbItem['ATTRIBUTE_CODE']][] = $rec;
					}
				}
			}
		}	
	}	
	
	/**
	 *	получение code api категории по её name
	 *	
	 */
	protected function getApiCategoryCodeByName($category_name)
	{		
		foreach ( $this->arRedefinitions as $r_i => $r_val )
		{
			$category_hierarchy_data = explode(' / ', $r_val);
			
			foreach ( $this->api_categories as $c_i => $c_values )
			{
				//$category_hierarchy_data_2 = explode(' ', $category_hierarchy_data[2]);
				$category_hierarchy_data_2 = $category_hierarchy_data[0];
				$category_hierarchy_data_3 = explode('] ', $category_hierarchy_data_2);
				
				//if ( stripos($c_values['title'], $category_hierarchy_data_2[0]) !== false )
				if ( stripos($c_values['title'], $category_hierarchy_data_3[1]) !== false )
				{
					return $c_values['code'];
				}
			}
		}
		return '';
	}
	
	/**
	 *	получение id api категории по её code
	 *	
	 */
	protected function getApiCategoryIdByCode($category_code)
	{		
		foreach ( $this->api_categories as $c_i => $c_values )
		{
			if ( $c_values['code'] == $category_code )
			{
				$cat_id = $c_i + 1;
				return $cat_id;
			}
		}
		
		return '';
	}
	
	/**
	 *	получение code api категории по её id
	 *	
	 */
	protected function getApiCategoryCodeById($category_id)
	{		
		foreach ( $this->api_categories as $c_i => $c_values )
		{
			$cat_id = $c_i + 1;
			if ( $cat_id == $category_id )
			{				
				return $c_values['code'];
			}
		}
		
		return '';
	}
	
	/**
	 *	получение id api атрибута по его code
	 *	
	 */
	protected function getApiAttributeIdByCode($category_code, $attribute_code)
	{		
		foreach ( $this->api_attributes[$category_code] as $c_i => $c_values )
		{
			if ( $c_values['code'] == $attribute_code )
			{
				$attr_id = $c_i + 1;
				return $attr_id;
			}
		}
		
		return '';
	}
	
	/**
	 *	2) получение по api списка доступных для каждой выгружаемой и сопоставленной
	 *	категории атрибутов
	 */
	protected function kaspiApi_getAvailableUploadAttributes() {
		$api_attributes_cache_file = $_SERVER["DOCUMENT_ROOT"].$this->cache_dir.'api_attributes.txt';
		
		$arUsedCategories = array();
		foreach ( $this->arProfile['IBLOCKS'] as $intIBlockId => $block_data )
		{
			$arUsedCategories[$intIBlockId] = $this->getUsedCategories($intIBlockId, true);
		}
		$request = Context::getCurrent()->getRequest();
		$profile_id = $this->intProfileId;
		if ( !$this->arParams['USE_CACHE'] )
		{
			// HTTP-запрос
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->setHeaders([
			  'Accept: application/json',
			  'X-Auth-Token' => $this->arParams['AUTH_TOKEN'],
			]);
			
			$arCategoryAttributesRequested = array();
			$this->api_attributes = array();
			
			foreach ( $this->arProfile['IBLOCKS'] as $intIBlockId => $block_data )
			{
				if ( $arUsedCategories[$intIBlockId] !== '' )
				{
					$cat_id = $arUsedCategories[$intIBlockId][0] - 1;
					$cat_code = $this->api_categories[$cat_id]['code'];
					
					$i = urlencode($cat_code);
					
					$strResponse = $obHttp->get(
						'https://kaspi.kz/shop/api/products/classification/attributes?c='.$i);
				
					$this->api_attributes[$cat_code] = json_decode($strResponse, true);
					$arCategoryAttributesRequested[$c_i] = true;
					
					foreach ( $this->api_attributes[$cat_code] as $atr_index => $atr_fields )
					{
						$atr_id = $atr_index + 1;
						$multivalued = 'N';
						if ( $atr_fields['multiValued'] )
							$multivalued = 'Y';
						$mandatory = 'N';
						
						$delete_no_mandatory_field = false;
						
						if ( $atr_fields['mandatory'] )
							$mandatory = 'Y';
						else {
							$delete_no_mandatory_field = true;
						}
						$arFields = [
							'PROFILE_ID' => $profile_id,
							'CATEGORY_ID' => $cat_id,
							'ATTRIBUTE_ID' => $atr_id,
							'DICTIONARY_ID' => 1,
							'NAME' => $atr_fields['code'],
							'CODE' => $atr_fields['code'],
							'DESCRIPTION' => '',
							'TYPE' => $atr_fields['type'],
							'MULTIVALUED' => $multivalued,
							'IS_COLLECTION' => 'N',
							'IS_REQUIRED' => $mandatory,
							'SESSION_ID' => session_id(),
							'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
						];
						
						$arFilter = [
							'PROFILE_ID' => $arFields['PROFILE_ID'],
							'CATEGORY_ID' => $arFields['CATEGORY_ID'],
							'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
						];
						$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => ['ID']]);
						//Attribute::delete($arDbItem['ID']); //test
						/*
						if($arDbItem = $resDBItem->fetch()){
							if ( $delete_no_mandatory_field )
								Attribute::delete($arDbItem['ID']);
							else	
								Attribute::update($arDbItem['ID'], $arFields);
						}
						else{
							if ( !$delete_no_mandatory_field )
								Attribute::add($arFields);
						}
						*/
					}
				}
			}
		}
		else {			
			$this->api_attributes = array();
			
			$resDBItem = Attribute::getList(['filter' => [], 'select' => ['ID', 'CODE', 'TYPE', 'MULTIVALUED', 'IS_REQUIRED']]);
			
			foreach ( $this->arProfile['IBLOCKS'] as $intIBlockId => $block_data )
			{
				if ( $arUsedCategories[$intIBlockId] !== '' )
				{
					$cat_id = $arUsedCategories[$intIBlockId][0] - 1;
					
					while ( $arDbItem = $resDBItem->fetch() )
					{
						$rec = array('code' => $arDbItem['CODE'], 'type' => $arDbItem['TYPE'], 'multiValued' => $arDbItem['MULTIVALUED'], 'mandatory' => $arDbItem['IS_REQUIRED']);
						$this->api_attributes[$cat_id][] = $rec;
					}
				}
			}
		}
	}
	
	/**
	 *	1) получение по api списка доступных для выгрузки категорий
	 */
	protected function kaspiApi_getAvailableUploadCategories() {
		$api_categories_cache_file = $_SERVER["DOCUMENT_ROOT"].$this->cache_dir.'api_categories.txt';
		$strResponse = '';
		
		if ( !$this->arParams['USE_CACHE'] )
		{
			// HTTP-запрос
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->setHeaders([
			  'Accept: application/json',
			  'X-Auth-Token' => $this->arParams['AUTH_TOKEN'],
			]);
			
			$strResponse = $obHttp->get(
				'https://kaspi.kz/shop/api/products/classification/categories');
			$this->api_categories = json_decode($strResponse, true);

			foreach( $this->api_categories as $cat_index => $cat_data){
				$category_id = $cat_index + 1;
				$arFields = [
					'CATEGORY_ID' => $category_id,
					'NAME' => $cat_data['title'],
					'CODE' => $cat_data['code'],
					'SESSION_ID' => session_id(),
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
				];
				$arFilter = [
					'CATEGORY_ID' => $arFields['CATEGORY_ID'],
				];
				$resDBItem = Category::getList(['filter' => $arFilter, 'select' => ['ID']]);
				if($arDbItem = $resDBItem->fetch()){
					Category::update($arDbItem['ID'], $arFields);
				}
				else{
					Category::add($arFields);
				}
			}
		}
		else 
		{
			$resDBItem = Category::getList(['filter' => [], 'select' => ['ID', 'CATEGORY_ID', 'NAME', 'CODE']]);
			$this->api_categories = array();
			
			while ( $arDbItem = $resDBItem->fetch() )
			{
				$rec = array('code' => $arDbItem['CODE'], 'title' => $arDbItem['NAME']);
				$id = $arDbItem['CATEGORY_ID'] - 2;
				$this->api_categories[$id] = $rec;
			}
		}
	}
	
	/**
	 *	4) выполнение выгрузки
	 */
	protected function kaspiApi_DoAttributeUpload($arJsonItems){
		$api_attribute_upload_result_cache_file = $_SERVER["DOCUMENT_ROOT"].$this->cache_dir.'api_attribute_upload_result.txt';
		$strResponse = '';
		
		$arJson = array();
		$elements = array();
		$element_attributes = array();
		$element_index = 0;
		//$this->addToLog($this->api_attributes);
		foreach($arJsonItems as $arItemIndex => $arItem)
		{
			$element = array();
			
			foreach( $arItem as $arItem_field => $arItem_field_value )
			{				
				//if ( stripos($arItem_field, 'attribute__') !== false )
				if ( stripos($arItem_field, 'attribute_') !== false )
				{
					//$attr_index_pre = explode('__', $arItem_field);
					$attr_index_pre = explode('_', $arItem_field);
					$attr_index = $attr_index_pre[2] - 1;
					$attribute_code = $this->api_attributes[$arItem['category_code']][$attr_index]['code'];
					$element_attributes[$arItemIndex][] = array('code' => $attribute_code, 'value' => $arItem_field_value);					
					//$this->addToLog($this->api_attributes[$arItem['category_code']]);	
					/*
					$this->addToLog($attribute_code);
					$this->addToLog('attr_index');
					$this->addToLog($attr_index);
					*/
				}
				else {
					if ( stripos($arItem_field, 'attributes') !== false )
					{
						
					}
					else {
						if ( stripos($arItem_field, 'category_code') !== false )
						{
							
						}
						else {
							$element[$arItem_field] = $arItem_field_value;
						}
					}
				}
			}
			
			$elements[] = $element;
			$element_index++;
		}
		
		$element_index = 0;
		foreach($arJsonItems as $arItemIndex => $arItem)
		{
			foreach( $arItem as $arItem_field => $arItem_field_value )
			{
				if ( stripos($arItem_field, 'attribute__') !== false )
				{
					
				}
				else {
					if ( stripos($arItem_field, 'attributes') !== false )
					{
						$elements[$element_index][$arItem_field] = $element_attributes[$arItemIndex];
					}
					else {
						if ( stripos($arItem_field, 'category_code') !== false )
						{
							
						}
						else {
							if ( stripos($arItem_field, 'category') !== false )
							{								
								$arJsonItems[$arItemIndex]['category'] = $arJsonItems[$arItemIndex]['category_code'];
								$elements[$element_index][$arItem_field] = $arJsonItems[$arItemIndex]['category_code'];
							}
						}
					}
				}
			}
			$arJson[] = $elements[$element_index];
			$element_index++;
		}
		if ( !$this->arParams['USE_CACHE'] )
		{
			// HTTP-запрос
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			
			$this->addToLog($arJson);

			$strJson = \Bitrix\Main\Web\Json::encode($arJson);
			
			$obHttp->setHeader('Content-Type', 'text/plain');
$obHttp->setHeader('Accept', 'application/json');
$obHttp->setHeader('Content-Length', mb_strlen($strJson));
$obHttp->setHeader('X-Auth-Token', $this->arParams['AUTH_TOKEN']);
			
			//выгрузка товаров (Загрузка характеристик - https://kaspi.kz/merchantcabinet/support/pages/viewpage.action?pageId=22645486)
			$post_data = $strJson;
			
			
			$strResponse = $obHttp->post(
				'https://kaspi.kz/shop/api/products/import', $post_data);
			
				
			$h = fopen($api_attribute_upload_result_cache_file, 'wb');
			fwrite($h, $strResponse);
			fclose($h);
			
		}
		else {
			$strResponse = file_get_contents($api_attribute_upload_result_cache_file);
		}
		
		$this->api_attribute_upload_result = json_decode($strResponse, true);
		$this->addToLog($strResponse);
	}
	
	/**
	 *	Export data by API (one step)
	 */
	protected function stepExport_ExportApi_Step(&$arSession, $arStep){
		$this->kaspiApi_getApiDataBeforeUpload();
		
		$this->arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', 
			[$this->intProfileId, $intIBlockId]);
		$bExported = false;
		$arItems = $this->getExportDataItems(null, null);
		$obDatabase = \Bitrix\Main\Application::getConnection();
		if(!empty($arItems)){
			$arJsonItems = [];
			$arDataMore = [];
			
			foreach($arItems as $arItem){
				$arEncodedItem = Json::decode($arItem['DATA']);
				$arJsonItems[$arItem['ELEMENT_ID']] = $arEncodedItem;
				$arDataMore[$arItem['ELEMENT_ID']] = unserialize($arItem['DATA_MORE']);
				$category_code = $this->getApiCategoryCodeByName($arEncodedItem['category']);
				$arJsonItems[$arItem['ELEMENT_ID']]['category_code'] = $category_code;
				//$this->addToLog('category_code');
				//$this->addToLog($arJsonItems[$arItem['ELEMENT_ID']]['category_code']);
			}
			$arItemsId = array_column($arJsonItems, 'offer_id');
			
			/*
			алгоритм выгрузки:
			
			1) получение по api списка доступных для выгрузки категорий
			1.2) сопоставление имеющихся на сайте категорий с доступными для выгрузки

			2) получение по api списка доступных для каждой выгружаемой и сопоставленной
			категории атрибутов
			2.2) сопоставление имеющихся атрибутов с доступными

			3) получение по api списка доступных значений для каждого атрибута
			3.2) сопоставление имеющихся значений атрибутов с доступными

			4) выполнение выгрузки

			5) проверка результата выгрузки средствами api

			6) ручная проверка результатов выгрузки
			*/
		}
		$this->kaspiApi_DoAttributeUpload($arJsonItems);
	}
	
	/**
	 *	Correct value types (for example, "vat": 0 => "vat": "0")
	 */
	protected function correctFieldTypes(&$arItem, $arElement, $arFields, $arElementSections, $arDataMore){
		$arStringFields = ['offer_id', 'price', 'old_price', 'premium_price', 'vat'];
		foreach($arStringFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = strVal($arItem[$strKey]);
			}
		}
		$arFloatFields = ['depth', 'height', 'weight', 'width'];
		foreach($arFloatFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = floatVal($arItem[$strKey]);
			}
		}
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$this->test_count++;
		$arIBlockParams = $this->getIBlockParams($arElement['IBLOCK_ID']);
		# Correct int/string types for height, depth, price, vat, ...
		$this->correctFieldTypes($arItem, $arElement, $arFields, $arElementSections, $arDataMore);
		#
		$arDataMore = [
			'OFFER_ID' => $arItem['offer_id'],
			'STOCK' => null, // General stock
		];
		# Transfer stock from main data to DATA_MORE
		if(is_numeric($arFields['stock'])){
			$arDataMore['STOCK'] = intVal($arFields['stock']);
		}
		unset($arItem['stock'], $arFields['stock']);
		$arUsedCategories = $this->getUsedCategories();
		
		foreach($arItem as $key => $value){
			if ( $key == 'sku')
			{
				//$arItem[$key] = 'B4BKM0153\001\AW2122_'.$value.' / '.$value;				
				$arItem[$key] = $value;
				//$sku_new_val = intval($value + time()) % 1111000000;
				//$arItem[$key] = $sku_new_val;
			}
		
			if ( $key == 'images')
			{
				$images_recs = explode(",", $value);
				$new_value = array();
				foreach ( $images_recs as $img )
				{
					$img_rec = array('url' => $img);
					$new_value[] = $img_rec;
				}
				$arItem[$key] = $new_value;
			}
			if ( $key == 'category')
			{
				//$this->addToLog('category: '.$arItem['category']);
			}
			if ( isset($arUsedCategories[$arElementSections[0]]['NAME']) && $arUsedCategories[$arElementSections[0]]['NAME'] != '' )
				$arItem['category'] = $arUsedCategories[$arElementSections[0]]['NAME'];
			if ( $key == 'attributes')
			{
				//$this->addToLog($arItem);
				//$this->addToLog($arFields);
				//$this->addToLog($arItem);
				//$category_code = $this->getApiCategoryCodeByName($arItem['category']);
				//$this->addToLog($arItem['title'].' '.$category_code);
				/*
				$new_value = array();
				foreach ( $value as $val_key => $val_value )
				{
					$attribute_rec = array('code' => 'CATALOG_PROP_'.$val_key, 'value' => $val_value);
					$new_value[] = $attribute_rec;
				}				
				$arItem[$key] = $new_value;
				*/
			}
			
		}
	}	
	
	/**
	 *	Settings
	 */
	
	protected function onUpShowSettings(&$arSettings){
		$arSettings['AUTH_TOKEN'] = $this->includeHtml(__DIR__.'/include/settings/auth_token.php');
		$arSettings['COMPANY_NAME'] = $this->includeHtml(__DIR__.'/include/settings/company_name.php');
		$arSettings['COMPANY_ID'] = $this->includeHtml(__DIR__.'/include/settings/company_id.php');
		$arSettings['USE_CACHE'] = $this->includeHtml(__DIR__.'/include/settings/use_cache.php');
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockID, $arIBlockParams){
		return $this->includeHtml(__DIR__.'/include/attribute_update/settings.php', [
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'category_attributes_update': {
				$this->ajaxUpdateCategories($arParams, $arJsonResult);
				break;
			}
		}
	}
	
	/**
	 *	Update category attributes and dictionaries
	 */	 
	protected function ajaxUpdateCategories($arParams, &$arJsonResult){
		$arSession = &$_SESSION['ACRIT_EXP_KASPIKZ_CAT_ATTR_UPDATE'];
		$arPost = &$arParams['POST'];
		$bStart = false;
		if($arPost['start'] == 'Y'){
			$bStart = true;
			$arJsonResult['Action'] = 'Start';
			$arSession = [
				'ID' => session_id(),
				'CATEGORIES' => $this->getUsedCategories($arPost['iblock_id'], true),
				'ATTRIBUTES' => [],
				'COUNT' => 0,
				'INDEX' => 0,
				'CATEGORY_ID' => false,
				'CATEGORY_NAME' => false,
				'ATTRIBUTE_ID' => false,
				'ATTRIBUTE_NAME' => false,
				'SUB_INDEX' => 0,
				'FORCED' => $arPost['force'] == 'Y',
				'JUST_ATTR' => $arPost['just_attr'] == 'Y',
				#'SUB_COUNT' => 0, // Unfortunately, ozon does not provide this information :(
			];
			
			$arSession['COUNT'] = count($arSession['CATEGORIES']);
			$arJsonResult['Continue'] = true;

			$this->kaspiApi_getApiDataBeforeUpload();
			$arUpdateResult = $this->updateAttributeValues($arSession['ID']);
			$intCategoryId = 1;
			$arAttributes = $this->updateCategoryAttributes($intCategoryId, $arSession['ID']);
		}
		else{
			
		}
	}
	
	/**
	 *	Update dictionary
	 *	@return true if process is not finished (by has_next)
	 */
	protected function updateAttributeValues($strSessionId){
		$arResult = [
			'LAST_ID' => false,
			'CONTINUE' => false,
			'COUNT_SUCCESS' => 0,
		];
		if ( isset($this->api_attributes) )
		{
			$attr_count = count($this->api_attributes);
			if ( $attr_count > 0 )
			{
				$strSaveData = '';
				//\Bitrix\Main\Application::getConnection()->startTransaction();
				$test_1 = array();
				foreach ($this->api_attributes as $arCatItemCode => $arItem){
					foreach ($arItem as $arItemIndex => $arItemFields){
						$api_cat_id = $this->getApiCategoryIdByCode($arCatItemCode);
						$api_attr_id = $this->getApiAttributeIdByCode($arCatItemCode, $arItemFields['code']);
						$arFields = [
							'CATEGORY_ID' => $api_cat_id,
							'ATTRIBUTE_ID' => $api_attr_id,
							'DICTIONARY_ID' => 1,
							'VALUE_ID' => 1,
							'VALUE' => '',
							'SESSION_ID' => $strSessionId,
							'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
						];
						$arFilter = [
							'CATEGORY_ID' => $arFields['CATEGORY_ID'],
							'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
							'DICTIONARY_ID' => 1,
							'VALUE_ID' => 1,
						];
						$resDBItem = AttributeValue::getList(['filter' => $arFilter, 'select' => ['ID', 'VALUE']]);
						$arDbItem = $resDBItem->fetch();
						//$test_1[] = $arDbItem;
						if($arDbItem){
							AttributeValue::update($arDbItem['ID'], $arFields);
						}
						else{
							AttributeValue::add($arFields);
						}
						
						$arResult['LAST_ID'] = $arItem['id'];
						$arResult['COUNT_SUCCESS']++;
					}
				}
			}
		}
		
	}
	
	/**
	 *	Update attributes for categories
	 */
	protected function updateCategoryAttributes($arCategoryId, $strSessionId){
		if(!is_array($arCategoryId)){
			$arCategoryId = [$arCategoryId];
		}
		else{
			$arCategoryId = array_values($arCategoryId);
		}
		$arJsonRequest = [
			'attribute_type' => 'ALL',
			'category_id' => $arCategoryId,
			'language' => 'DEFAULT',
		];
		$arJsonResponse = [];
		$arJsonResponse['result'] = $this->api_attributes;
		$test_1 = [];
		if(is_array($arJsonResponse['result']) && !empty($arJsonResponse['result'])){
			$arResult = [];
			foreach($arJsonResponse['result'] as $arCategory){
				foreach($arCategory as $a_i => $arItem){
					$api_cat_id = -1;
					$api_attr_id = -1;
					foreach ($this->api_attributes as $arCatItemCode => $arItem_at){
						foreach ($arItem_at as $arItemIndex => $arItemFields){
							if ( $arItemFields['code'] == $arItem['code'] )
							{
								$api_cat_id = $this->getApiCategoryIdByCode($arCatItemCode);
								$api_attr_id = $this->getApiAttributeIdByCode($arCatItemCode, $arItem['code']);
								break 2;
							}
						}
					}
					$delete_no_mandatory_field = false;
					if ( $arItem['mandatory'] )
					{
						//
					}
					else {
						$delete_no_mandatory_field = true;
					}
					$arFields = [
						'CATEGORY_ID' => $api_cat_id,
						'ATTRIBUTE_ID' => $api_attr_id,
						'DICTIONARY_ID' => 1,
						'NAME' => $arItem['code'],
						'DESCRIPTION' => $arItem['code'],
						'TYPE' => $arItem['type'],
						'IS_COLLECTION' => 'N',
						'IS_REQUIRED' => 'Y',
						'GROUP_ID' => 1,
						'GROUP_NAME' => 1,
						'SESSION_ID' => $strSessionId,
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $api_cat_id,
						'ATTRIBUTE_ID' => $api_attr_id,
					];
					$arSelect = [
						'ID',
						'NAME',
						'LAST_VALUES_COUNT',
						'LAST_VALUES_DATETIME',
					];
					$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
					
					if ($arDbItem = $resDBItem->fetch()){
						if ( $delete_no_mandatory_field )
							Attribute::delete($arDbItem['ID']);
					}
					
					if($arDbItem = $resDBItem->fetch()){
						$test_1[] = $arDbItem;
						Attribute::update($arDbItem['ID'], $arFields);
						$arFields['LAST_VALUES_COUNT'] = $arDbItem['LAST_VALUES_COUNT'];
						if(is_object($arDbItem['LAST_VALUES_DATETIME'])){
							$bActual = microtime(true) - $arDbItem['LAST_VALUES_DATETIME']->getTimestamp() < static::CACHE_VALID_TIME;
							if($bActual){
								unset($arFields);
							}
						}
					}
					else{
						if ( !$delete_no_mandatory_field )
							Attribute::add($arFields);
					}
					
					if($arFields){
						$arResult[$arItem['id']] = $arFields;
					}
				}
			}
			Attribute::deleteByFilter([
				'CATEGORY_ID' => $arCategoryId,
				'!SESSION_ID' => $strSessionId,
			]);
			unset($arJsonResponse['result']);
			return $arResult;
		}
		else{
			$this->addToLog(static::getMessage('ERROR_UPDATE_ATTRIBUTES', ['#CATEGORY_ID#' => implode(', ', $arCategoryId), '#ERROR#' => print_r($arJsonResponse, true)]));
			return false;
		}		
		return false;
	}
	
	/**
	 *	Get saved categories, if not exists - download it
	 */
	public function getCategoriesList($intProfileId){
		$intLastUpdateTime = $this->getCategoriesDate();
		if(!$intLastUpdateTime || $intLastUpdateTime <= time() - 24*60*60){
			$this->updateCategories($this->intProfileId);
		}
		$arResult = [];
		$resCategories = Category::getList(['order' => ['NAME' => 'ASC'], 'select' => ['CATEGORY_ID', 'NAME']]);
		while($arCategory = $resCategories->fetch()){
			$arResult[$arCategory['CATEGORY_ID']] = $arCategory['NAME'];
		}
		// Sort categories, but numerical (such a "18+") in the end of the list
		uasort($arResult, function($a, $b){
			if(is_numeric(substr($a, 0, 1)) xor is_numeric(substr($b, 0, 1))){
				return strnatcmp($a, $b) * -1;
			}
			return strnatcmp($a, $b);
		});
		array_walk($arResult, function(&$value, $key) {
			$value = $this->formatCategoryName($key, $value);
		});
		unset($resCategories, $arCategory);
		return $arResult;
	}
	
	/**
	 *	Convert categories tree to plain list (recursively)
	 */
	protected function processUpdatedCategory($arCategoriesCurrent, $strSessionId, $arName=[], $bRecurred=false){
		if(is_array($arCategoriesCurrent)){
			$cat_index = 0;
			foreach($arCategoriesCurrent as $arCategory){
				$category_id = $cat_index + 1;
				$arNameChain = array_merge($arName, [$category_id => $arCategory['title']]);
				if(!empty($arCategory['children'])){
					$this->processUpdatedCategory($arCategory['children'], $strSessionId, $arNameChain, true);
				}
				else{
					$arFields = [
						'CATEGORY_ID' => $category_id,
						'NAME' => $arCategory['title'],
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					];
					$resDBItem = Category::getList(['filter' => $arFilter, 'select' => ['ID']]);
					if($arDbItem = $resDBItem->fetch()){
						Category::update($arDbItem['ID'], $arFields);
					}
					else{
						Category::add($arFields);
					}
				}
				$cat_index++;
			}
		}
	}
	
	/**
	 *	Update categories from server using API
	 */
	public function updateCategories($intProfileId){
		$this->kaspiApi_getAvailableUploadCategories();
		$arJsonResponse = array();
		$arJsonResponse['result'] = $this->api_categories;
		
		$strSessionId = session_id();
		if ( true ) //test
		{
			$this->processUpdatedCategory($arJsonResponse['result'], $strSessionId);
		}
		else{
			$strLogMessage = static::getMessage('ERROR_CATEGORIES_EMPTY_ANSWER', ['#URL#' => $strCommand]);
			$this->addToLog($strLogMessage);
		}
		Category::deleteByFilter([
			'!SESSION_ID' => $strSessionId,
		]);
		return true;
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/attributevalue.php';
		require_once __DIR__.'/include/classes/category.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
}

?>