<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

/**
 * Class Filter
 * @package Acrit\Core\Export
 */

class Filter {
	
	const VALUE_SEPARATOR = '#|#'; // For filter values
	
	static $arAllLogicCache = array();
	
	protected $strModuleId;
	protected $intIBlockID;
	protected $intMainElementID;
	protected $intIBlockOffersID;
	protected $intIBlockParentID;
	protected $intOffersPropertyID;
	protected $intSkuPropertyID;
	protected $strJson;
	protected $arAvailableElementFields;
	protected $arAvailableOfferFields;
	protected $arAvailableParentFields;
	protected $strInputName;
	protected $arFilter;
	protected $bIncludeSubsections;
	
	public function __construct($strModuleId, $intIBlockID){
		$this->strModuleId = $strModuleId;
		$this->intIBlockID = $intIBlockID;
		#$this->arAvailableElementFields = ProfileIBlock::getAvailableElementFieldsPlain($intIBlockID);
		$this->arAvailableElementFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
		$this->bIncludeSubsections = false;
		$arCatalogArray = Helper::getCatalogArray($intIBlockID);
		if(is_array($arCatalogArray) && $arCatalogArray['OFFERS_IBLOCK_ID']) {
			$this->intIBlockOffersID = $arCatalogArray['OFFERS_IBLOCK_ID'];
			$this->intOffersPropertyID = $arCatalogArray['OFFERS_PROPERTY_ID'];
			$this->arAvailableOfferFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$arCatalogArray['OFFERS_IBLOCK_ID']]);
		}
		elseif(is_array($arCatalogArray) && $arCatalogArray['PRODUCT_IBLOCK_ID']){
			$this->intIBlockParentID = $arCatalogArray['PRODUCT_IBLOCK_ID'];
			$this->intSkuPropertyID = $arCatalogArray['SKU_PROPERTY_ID'];
			$this->arAvailableParentFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$arCatalogArray['PRODUCT_IBLOCK_ID']]);
		}
	}

	public function setMainElementID($intElementId){
		$this->intMainElementID = $intElementId;
	}

	public function getMainElementID(){
		return $this->intMainElementID;
	}
	
	public static function addJs(){
		ob_start();
		?>
		<script>
		BX.message({
			'ACRIT_EXP_CONDITIONS_VALUE_SEPARATOR': '<?=static::VALUE_SEPARATOR;?>',
			'ACRIT_EXP_CONDITIONS_POPUP_LOADING': '<?=Loc::getMessage('ACRIT_EXP_POPUP_LOADING');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SELECT_FIELD': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SELECT_FIELD');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SELECT_LOGIC': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SELECT_LOGIC');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SELECT_VALUE': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SELECT_VALUE');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SAVE': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SAVE');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_CANCEL': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_CANCEL');?>',
			//
			'ACRIT_EXP_CONDITIONS_ADD_ITEM': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ADD_ITEM');?>',
			'ACRIT_EXP_CONDITIONS_ADD_GROUP': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ADD_GROUP');?>',
			'ACRIT_EXP_CONDITIONS_ENTITY_FIELD': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ENTITY_FIELD');?>',
			'ACRIT_EXP_CONDITIONS_ENTITY_LOGIC': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ENTITY_LOGIC');?>',
			'ACRIT_EXP_CONDITIONS_ENTITY_VALUE': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ENTITY_VALUE');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_ALL': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_ALL');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_ANY': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_ANY');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_Y': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_Y');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_N': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_N');?>',
			'ACRIT_EXP_CONDITIONS_DELETE_ITEM': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_DELETE_ITEM');?>',
			'ACRIT_EXP_CONDITIONS_DELETE_GROUP': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_DELETE_GROUP');?>'
		});
		var acritFilterLang = {
			addItem: BX.message('ACRIT_EXP_CONDITIONS_ADD_ITEM'),
			addGroup: BX.message('ACRIT_EXP_CONDITIONS_ADD_GROUP'),
			//
			selectField: BX.message('ACRIT_EXP_CONDITIONS_ENTITY_FIELD'),
			selectLogic: BX.message('ACRIT_EXP_CONDITIONS_ENTITY_LOGIC'),
			selectValue: BX.message('ACRIT_EXP_CONDITIONS_ENTITY_VALUE'),
			//
			aggregatorAll: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_ALL'),
			aggregatorAny: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_ANY'),
			aggregatorY: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_Y'),
			aggregatorN: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_N'),
			//
			deleteItemConfirm: BX.message('ACRIT_EXP_CONDITIONS_DELETE_ITEM'),
			deleteGroupConfirm: BX.message('ACRIT_EXP_CONDITIONS_DELETE_GROUP')
		};
		</script>
		<?
		\Bitrix\Main\Page\Asset::GetInstance()->AddString(ob_get_clean());
	}
	
	/**
	 *	Display HTML
	 */
	public function show(){
		$strFilterUniqID = 'filter_'.uniqid().time();
		?>
		<div class="acrit-filter" id="<?=$strFilterUniqID;?>" data-role="filter" data-iblock-id="<?=$this->intIBlockID;?>"></div>
		<input type="hidden" name="<?=$this->strInputName;?>" value="<?=htmlspecialcharsbx($this->strJson);?>" id="<?=$strFilterUniqID;?>_input" />
		<script>
		// Main filter
		$('#<?=$strFilterUniqID;?>').acritFilter({
			lang: acritFilterLang,
			field: $('#<?=$strFilterUniqID;?>_input'),
			callbackClickEntity: AcritExpConditionsPopupCallbackClickEntity
		});
		</script>
		<?
	}
	
	/**
	 *	Set input name
	 */
	public function setInputName($strInputName){
		$this->strInputName = $strInputName;
	}
	
	/**
	 *	Set saved JSON
	 */
	public function setJson($strJson){
		$this->strJson = $strJson;
	}
	
	/**
	 *	Parse json
	 */
	public function getJsonArray(){
		$strJson = $this->strJson;
		if(!Helper::isUtf()){
			$strJson = Helper::convertEncoding($strJson, 'CP1251', 'UTF-8');
		}
		$arJsonResult = json_decode($strJson, true);
		if(!Helper::isUtf()){
			$arJsonResult = Helper::convertEncoding($arJsonResult, 'UTF-8', 'CP1251');
		}
		return $arJsonResult;
	}
	
	/**
	 *	Set include_subsections mode for filtering
	 */
	public function setIncludeSubsections($bIncludeSubsections){
		$this->bIncludeSubsections = $bIncludeSubsections;
	}
	
	/**
	 *	
	 */
	public static function getDatetimeFilterValues($bWithTime=true){
		$arResult = [
			'days' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_DAYS'),
			'months' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_MONTHS'),
			'years' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_YEARS'),
			'hours' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_HOURS'),
			'minutes' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_MINUTES'),
			'seconds' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_SECONDS'),
		];
		if(!$bWithTime){
			unset($arResult['hours'], $arResult['minutes'], $arResult['seconds']);
		}
		return $arResult;
	}
	
	/**
	 *	Parse datetime value
	 */
	public static function parseDatetimeValue($strValue, $strField, $bReturnArray=false, $strUserType=null, $bInFuture=false){
		if(preg_match('#^(\d+)([a-z]+)$#', $strValue, $arMatch)){
			$strDatetime = false;
			$strValue = $arMatch[1];
			$strType = $arMatch[2];
			if(strlen($strType)){
				$arTypes = static::getDatetimeFilterValues();
				if(isset($arTypes[$strType])){
					$obDate = new \Bitrix\Main\Type\DateTime();
					$strDiff = $bInFuture ? sprintf('+ %d %s', $strValue, $strType): sprintf('- %d %s', $strValue, $strType);
					$obDate->add($strDiff);
					if(Helper::isProperty($strField)){
						$strFormat = $strUserType == 'DateTime' ? 'Y-m-d H:i:s' : 'Y-m-d';
					}
					else{
						$strFormat = \Bitrix\Main\Type\DateTime::convertFormatToPhp(FORMAT_DATETIME);
					}
					$strDatetime = $obDate->format($strFormat);
				}
			}
			if($bReturnArray){
				return [
					$strDatetime,
					$strValue,
					$strType,
				];
			}
			else{
				return $strDatetime;
			}
		}
		return false;
	}
	
	/**
	 *	Get logic for values
	 */
	public static function getLogicAll($strType, $strUserType=false){
		if(is_array(static::$arAllLogicCache[$strType][$strUserType]) && !empty(static::$arAllLogicCache[$strType][$strUserType])){
			return static::$arAllLogicCache[$strType][$strUserType];
		}
		
		$arResult = array();
		
		$arResult = array(
			'EQUAL' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_EQUAL'),
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
					return static::buildFilterItem($strModuleId, array($strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
				},
			),
			'NOT_EQUAL' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_EQUAL'),
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
					return static::buildFilterItem($strModuleId, array('!'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
				},
			),
			'ISSET' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_ISSET'),
				'HIDE_VALUE' => true,
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
					return static::buildFilterItem($strModuleId, array('!'.$strField => false), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
				},
			),
			'NOT_ISSET' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_ISSET'),
				'HIDE_VALUE' => true,
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
					return static::buildFilterItem($strModuleId, array($strField => false), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
				},
			),
		);
		
		switch($strType){
			case 'S':
				if($strUserType=='_Checkbox' || $strUserType=='SASDCheckbox') {
					$arResult = array(
						'CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array($strField => 'Y'), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('!'.$strField => 'Y'), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						)
					);
				}
				elseif($strUserType=='directory') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				elseif($strUserType=='Date' || $strUserType=='DateTime') {
					$arResult = array_merge($arResult, array(
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strType, $strUserType){
								static::formatDateToFilterFormat($strValue, $strField, $strType, $strUserType);
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strType, $strUserType){
								static::formatDateToFilterFormat($strValue, $strField, $strType, $strUserType);
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strType, $strUserType){
								static::formatDateToFilterFormat($strValue, $strField, $strType, $strUserType);
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strType, $strUserType){
								static::formatDateToFilterFormat($strValue, $strField, $strType, $strUserType);
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'FOR_THE_LAST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_FOR_THE_LAST'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strUserType){
								$strValueParsed = static::parseDatetimeValue($strValue, $strField, false, $strUserType);
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValueParsed), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_FOR_THE_LAST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_FOR_THE_LAST'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strUserType){
								$strValueParsed = static::parseDatetimeValue($strValue, $strField, false, $strUserType);
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValueParsed), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'IN_FUTURE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_FUTURE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null)use($strUserType){
								$strValueParsed = static::parseDatetimeValue($strValue, $strField, false, $strUserType, true);
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValueParsed), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				elseif($strUserType=='_Currency') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				else {
					$arResult = array_merge($arResult, array(
						'EXACT' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_EXACT'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_EXACT' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_EXACT'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('!='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'SUBSTRING' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_SUBSTRING'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('%'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_SUBSTRING' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_SUBSTRING'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('!%'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'BEGINS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_BEGINS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array($strField => $strValue.'%'), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_BEGINS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_BEGINS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('!'.$strField => $strValue.'%'), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'ENDS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_ENDS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array($strField => '%'.$strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_ENDS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_ENDS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('!'.$strField => '%'.$strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LOGIC' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LOGIC'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('?'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_LOGIC' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_LOGIC'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('!?'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				break;
			case 'N':
				if($strUserType=='_ID_LIST') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				elseif($strUserType=='SASDCheckboxNum') {
					$arResult = array(
						'CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array($strField => 1), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array($strField => 2), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						)
					);
				}
				else {
					$arResult = array_merge($arResult, array(
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				if($strUserType == '_SectionId'){
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
							},
						),
					));
				}
				break;
			case 'L':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
						},
					),
				));
				break;
			case 'E':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
						},
					),
				));
				break;
			case 'G':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array($strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID);
						},
					),
				));
				break;
			case 'F':
				$arExclude = array('ISSET','NOT_ISSET');
				foreach($arResult as $key => $arItem){
					if(!in_array($key, $arExclude)){
						unset($arResult[$key]);
					}
				}
				break;
			case 'X':
				switch($strUserType){
					case '_OffersFlag':
						$arResult = array(
							'X_WITH_OFFERS' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_X_WITH_OFFERS'),
								'MULTIPLE' => true,
								'HIDE_VALUE' => true,
								'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false){
									if(!$bIsOffers && $intIBlockOffersID){
										return static::buildFilterItem($strModuleId, array(), $intIBlockID, true, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID, false);
									}
								},
							),
							'X_WITHOUT_OFFERS' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_X_WITHOUT_OFFERS'),
								'MULTIPLE' => true,
								'HIDE_VALUE' => true,
								'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false){
									if(!$bIsOffers && $intIBlockOffersID){
										return static::buildFilterItem($strModuleId, array(), $intIBlockID, true, $intIBlockOffersID, $intOffersPropertyID, $bIsParent, $intMainElementID, true);
									}
								},
							),
						);
						break;
				}
				break;
		}
		
		static::$arAllLogicCache[$strType][$strUserType] = $arResult;
		return $arResult;
	}
	
	/**
	 *	Get logic item
	 */
	public static function getLogicItem($strType, $strUserType, $strLogic){
		$arLogicAll = static::getLogicAll($strType, $strUserType);
		return $arLogicAll[$strLogic];
	}
	
	/**
	 *	Build PHP filter
	 */
	public function buildFilter(){
		$this->arFilter = array(
			'IBLOCK_ID' => $this->intIBlockID,
		);
		$arJson = $this->getJsonArray();
		$this->appendFilter($arJson, $this->arFilter);
		if($this->bIncludeSubsections){
			$this->arFilter = static::addFilterForSubsections($this->arFilter);
		}
		// Event handler
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBuildFilter') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$this->arFilter, $this));
		}
		return $this->arFilter;
	}
	
	/**
	 *	Transform filter for filtering also in subsections
	 */
	protected static function addFilterForSubsections($arFilter){
		$arResult = array();
		if(is_array($arFilter)){
			foreach($arFilter as $strKey => $mFilterItem){
				$strKeyCode = ltrim($strKey, '<=>!?%');
				if(is_array($mFilterItem) && $strKeyCode != 'IBLOCK_SECTION_ID'){
					$arResult[$strKey] = static::addFilterForSubsections($mFilterItem);
				}
				elseif($strKeyCode == 'IBLOCK_SECTION_ID'){
					$strOperation = substr($strKey, 0, strlen($strKey) - strlen($strKeyCode));
					$arResult[$strOperation.'SECTION_ID'] = $mFilterItem; // SECTION_ID in filter is right unlink IBLOCK_SECTION_ID
					$arResult['INCLUDE_SUBSECTIONS'] = 'Y';
				}
				else {
					$arResult[$strKey] = $mFilterItem;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Append filter (this function will work recursively)
	 */
	protected function appendFilter($arJsonItems, &$arFilter){
		if(is_array($arJsonItems)) {
			foreach($arJsonItems as $key => $arJsonItem){
				if($arJsonItem['type']=='group') {
					if(is_array($arJsonItem['items']) && !empty($arJsonItem['items'])) {
						$arSubFilter = array(
							'LOGIC' => $arJsonItem['aggregatorType']=='ANY' ? 'OR' : 'AND',
						);
						$this->appendFilter($arJsonItem['items'], $arSubFilter);
						if(!(count($arSubFilter)==1 && isset($arSubFilter['LOGIC']))){
							$arFilter[] = $arSubFilter;
						}
					}
				}
				elseif($arJsonItem['type']=='item'){
					$arFilterItem = array();
					$bIsOffers = $arJsonItem['iblockType']=='offers' ? true : false;
					$bIsParent = $arJsonItem['iblockType']=='parent' ? true : false;
					$strFieldProcess = $strFieldOriginal = $arJsonItem['field']['value'];
					$this->remapFilterField($strFieldProcess);
					if($bIsOffers) {
						$arField = $this->arAvailableOfferFields[$strFieldOriginal];
					}
					elseif($bIsParent) {
						$arField = $this->arAvailableParentFields[$strFieldOriginal];
					}
					else {
						$arField = $this->arAvailableElementFields[$strFieldOriginal];
					}
					if(is_array($arField)) {
						$strLogic = $arJsonItem['logic']['value'];
						$arLogic = static::getLogicItem($arField['TYPE'], $arField['USER_TYPE'], $strLogic);
						$strValue = $arJsonItem['value']['value'];
						if($arLogic['CALLBACK']) {
							$arFilterItem = call_user_func_array($arLogic['CALLBACK'], array(
								$this->strModuleId, $strFieldProcess, $strLogic, $strValue, $this->intIBlockID, $bIsOffers,
									$this->intIBlockOffersID, $this->intOffersPropertyID, $bIsParent, $this->intMainElementID
							));
						}
						if(is_array($arFilterItem) && !empty($arFilterItem)) {
							$arFilter[] = $arFilterItem;
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Change field code for correct use in API
	 */
	protected function remapFilterField(&$strField){
		if(preg_match('#CATALOG_PRICE_(\d+)__CURRENCY#i', $strField, $arMatch)){
			$strField = 'CATALOG_CURRENCY_'.$arMatch[1];
		}
		if(Helper::isCatalogNewFilter()){
			$arMap = [
				'CATALOG_QUANTITY' => 'QUANTITY',
				'CATALOG_QUANTITY_RESERVED' => 'QUANTITY_RESERVED',
				'CATALOG_AVAILABLE' => 'AVAILABLE',
				'CATALOG_WEIGHT' => 'WEIGHT',
				'CATALOG_LENGTH' => 'LENGTH',
				'CATALOG_WIDTH' => 'WIDTH',
				'CATALOG_HEIGHT' => 'HEIGHT',
				'CATALOG_VAT_ID' => 'VAT_ID',
				'CATALOG_VAT_INCLUDED' => 'VAT_INCLUDED',
				'CATALOG_PURCHASING_PRICE' => 'PURCHASING_PRICE',
				'CATALOG_PURCHASING_CURRENCY' => 'PURCHASING_CURRENCY',
				'CATALOG_MEASURE' => 'MEASURE',
				'CATALOG_QUANTITY_TRACE' => 'QUANTITY_TRACE_RAW',
				'CATALOG_CAN_BUY_ZERO' => 'CAN_BUY_ZERO_RAW',
				'CATALOG_SUBSCRIBE' => 'SUBSCRIBE_RAW',
				'CATALOG_TYPE' => 'TYPE',
				'CATALOG_BARCODE' => 'BARCODE',
				'CATALOG_BARCODE_MULTI' => 'BARCODE_MULTI',
				'CATALOG_MEASURE_ID' => 'MEASURE',
			];
			if(isset($arMap[$strField])){
				$strField = $arMap[$strField];
			}
		}
	}
	
	/**
	 *	Build filter item (this use in each logic item)
	 */
	protected static function buildFilterItem($strModuleId, $arItem, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bIsParent=false, $intMainElementID=null, $bNegation=false){
		$arResult = array();
		if($bIsOffers){
			$strKey = $bNegation ? '!ID' : 'ID';
			$arResult = array(
				$strKey => \CIBlockElement::SubQuery('PROPERTY_'.$intOffersPropertyID, array_merge($arItem, array(
					'IBLOCK_ID' => $intIBlockOffersID,
				))),
			);
		}
		elseif($bIsParent){
			if($intParentIBlockId = Helper::getCatalogArray($intIBlockID)['PRODUCT_IBLOCK_ID']){
				$strKey = ($bNegation ? '!' : '').'PROPERTY_CML2_LINK';
				$arMainElementFilter = [
					'IBLOCK_ID' => $intParentIBlockId,
					'ID' => $intMainElementID,
					$arItem,
				];
				$bSuccessMainElement = !!\CIBlockElement::getList([], $arMainElementFilter, false, false, ['ID'])->fetch();
				$arResult = [
					$strKey => $bSuccessMainElement ? $intMainElementID : -1,
				];
			}
		}
		else {
			static::convertFilterItemPropMultiple($arItem, $intIBlockID);
			$arResult = $arItem;
		}
		// Event handler
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers($strModuleId, 'OnBuildFilterItem') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arItem, $intIBlockID, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bNegation));
		}
		return $arResult;
	}

	/**
	 * When filtering by multiple property (PROPERTY_TYPE=L), negative filter does not work (for elements with several values selected). So, we have to use subquery
	 */
	protected static function convertFilterItemPropMultiple(&$arItem, $intIBlockID){
		if(is_array($arItem)){
			foreach($arItem as $strField => $mValue){
				if(preg_match('#^!PROPERTY_(.*?)$#', $strField, $arMatch)){
					$strProperty = $arMatch[1];
					$arQuery = [
						'filter' => [
							'IBLOCK_ID' => $intIBlockID,
							'=CODE' => $strProperty,
							'=PROPERTY_TYPE' => 'L',
							'=MULTIPLE' => 'Y',
						],
						'select' => ['ID'],
					];
					if($arProp = \Bitrix\IBlock\PropertyTable::getList($arQuery)->fetch()){
						unset($arItem[$strField]);
						$mValue = is_array($mValue) ? $mValue : [$mValue];
						foreach($mValue as $key => $intValue){
							if($intValue){
								$arItem[] = [
									'!ID' => \CIBlockElement::subQuery('ID', [
										'IBLOCK_ID' => $intIBlockID,
										'PROPERTY_'.$strProperty => $intValue,
									]),
								];
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Get conditions json
	 */
	public static function getConditionsJson($strModuleId, $intIBlockID, $arItems, $strType='ALL'){
		if(is_array($arItems)) {
			$arAvailableFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
			$arXmlItems = array();
			if(is_array($arItems) && isset($arItems['FIELD'])){
				$arItems = array($arItems);
			}
			foreach($arItems as $arItem){
				$strField = $arItem['FIELD'];
				$strLogic = $arItem['LOGIC'];
				$strValue = $arItem['VALUE'];
				$strTitle = $arItem['TITLE'];
				#
				$strValue = !is_null($strValue) ? $strValue : '';
				if(strlen($strValue) && !strlen($strTitle)){
					$strTitle = $strValue;
				}
				#
				$arField = $arAvailableFields[$strField];
				if(is_array($arField)){
					$arLogic = static::getLogicItem($arField['TYPE'], $arField['USER_TYPE'], $strLogic);
					if(is_array($arLogic)){
						$arXmlItems[] = array(
							'type' => 'item',
							'iblockType' => 'main',
							'field' => array(
								'name' => $arField['NAME'],
								'value' => $strField,
							),
							'logic' => array(
								'name' => $arLogic['NAME'],
								'value' => $strLogic,
								'hide' => $arLogic['HIDE_VALUE'] ? 'Y' : 'N',
							),
							'value' => array(
								'name' => $strTitle,
								'value' => $strValue,
							),
						);
					}
				}
			}
		}
		#
		$strType = in_array($strType, ['ANY', 'ALL']) ? $strType : 'ALL';
		$arFilterJson = array(
			array(
				'type' => 'group',
				'aggregatorType' => $strType,
				'items' => $arXmlItems,
			),
		);
		return Json::encode($arFilterJson);
	}
	
	/**
	 *	Just for conditions_value.php
	 */
	public static function getPropertyItems_L($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		$arFilter = array(
			'IBLOCK_ID' => $intFieldIBlockID,
			'PROPERTY_ID' => $arProperty['DATA']['ID'],
		);
		$resEnums = \CIBlockPropertyEnum::GetList(array('SORT'=>'ASC'), $arFilter);
		while($arEnum = $resEnums->getNext()){
			if(in_array($arEnum['ID'], $arValues)) {
				$arResult[$arEnum['ID']] = $arEnum['VALUE'].' ['.$arEnum['ID'].']';
			}
		}
		unset($arFilter, $resEnums, $arEnum);
		return $arResult;
	}
	public static function getPropertyItems_E($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = array(
				'ID' => $arValues,
			);
			$resItems = \CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, array('ID', 'NAME'));
			while($arItem = $resItems->GetNext()){
				$arResult[IntVal($arItem['ID'])] = $arItem['~NAME'].' ['.$arItem['ID'].']';
			}
		}
		unset($resItems, $arItem, $arFilter);
		return $arResult;
	}
	public static function getPropertyItems_G($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = array(
				'ID' => $arValues,
				'CHECK_PERMISSIONS' => 'N',
			);
			$resItems = \CIBlockSection::GetList(array('ID' => 'ASC'), $arFilter, false, array('ID', 'NAME'), false);
			while($arItem = $resItems->GetNext()){
				$arResult[IntVal($arItem['ID'])] = $arItem['~NAME'].' ['.$arItem['ID'].']';
			}
		}
		unset($resItems, $arItem, $arFilter);
		return $arResult;
	}
	public static function getPropertyItems_S_directory($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		$strHlTableName = $arProperty['DATA']['USER_TYPE_SETTINGS']['TABLE_NAME'];
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('highloadblock')) {
			if(strlen($strHlTableName)){
				$arFilter = array(
					'UF_XML_ID' => $arValues,
				);
				$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME'=>$strHlTableName)))->fetch();
				$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
				$strEntityDataClass = $obEntity->getDataClass();
				$resData = $strEntityDataClass::GetList(array(
					'filter' => $arFilter,
					'select' => array('ID', 'UF_NAME', 'UF_XML_ID'),
					'order' => array('ID' => 'ASC'),
				));
				while ($arItem = $resData->Fetch()) {
					$arResult[$arItem['UF_XML_ID']] = $arItem['UF_NAME'];
				}
			}
		}
		unset($strHlTableName, $arFilter, $arHLBlock, $obEntity, $strEntityDataClass, $resData, $arItem);
		return $arResult;
	}
	
	/**
	 *	Search sections
	 *	(because CIBlockSection does not support LOGIC OR)
	 */
	public static function searchSectionsByText($intIBlockID, $strSearch){
		$intIBlockID = IntVal($intIBlockID);
		$arWhere = array();
		if(strlen($strSearch)){
			$arWhere[] = "(BS.CODE IS NULL OR (BS.CODE LIKE '%{$strSearch}%'))";
			$arWhere[] = "(BS.NAME LIKE '%{$strSearch}%')";
			if(is_numeric($strSearch) && $strSearch > 0){
				$intID = IntVal($strSearch);
				$arWhere[] = "BS.ID = {$intID}";
			}
		}
		$strWhere = "(BS.IBLOCK_ID = '{$intIBlockID}')";
		if(!empty($arWhere)){
			$strWhere .= ' AND '.implode(' OR ', $arWhere);
		}
		$strSql = "
			SELECT
				DISTINCT BS.ID AS ID, BS.NAME AS NAME
			FROM
				b_iblock_section BS
			INNER JOIN
				b_iblock B ON BS.IBLOCK_ID = B.ID
			WHERE
				{$strWhere};
		";
		return $GLOBALS['DB']->query($strSql);
	}

	/**
	 * Convert date/datetime value from site-format (usually d.m.Y/d.m.Y H:i:s) to mysql-format (Y-m-d H:i:s)
	 */
	public static function formatDateToFilterFormat(&$strValue, $strField, $strType, $strUserType){
		if(Helper::isProperty($strField)){
			if($strUserType == 'DateTime' && $arValue = parseDateTime($strValue, FORMAT_DATETIME)){
				$strValue = date('Y-m-d H:i:s', mktime($arValue['HH'], $arValue['MI'], $arValue['SS'],
					$arValue['MM'], $arValue['DD'], $arValue['YYYY']));
			}
			elseif($strUserType == 'Date' && $arValue = parseDateTime($strValue, FORMAT_DATE)){
				$strValue = date('Y-m-d H:i:s', mktime(0, 0, 0, $arValue['MM'], $arValue['DD'], $arValue['YYYY']));
			}
		}
		# For fields leave format as site format
	}

	/**
	 * Get dispay name for filter item (not in list!)
	 */
	public static function getItemDisplayName($arItem){
		$strResult = $arItem['NAME'];
		$arInfo = [];
		if(Helper::strlen($arItem['ID'])){
			$arInfo[] = $arItem['ID'];
		}
		if(Helper::strlen($arItem['CODE'])){
			$arInfo[] = $arItem['CODE'];
		}
		if(!empty($arInfo)){
			$strResult .= sprintf(' [%s]', implode(', ', $arInfo));
		}
		return $strResult;
	}
	
}
