<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/mototsikly_i_mototehnika
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoMoto extends Avito {
	
	CONST DATE_UPDATED = '2021-03-05';

	protected static $bSubclass = true;
	
	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/* START OF BASE STATIC METHODS */
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_MOTO';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME').static::outdatedGetNameSuffix();
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_moto.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$this->modifyField($arResult, 'CATEGORY', array(
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONDITION',
			'DISPLAY_CODE' => 'Condition',
			'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
			'SORT' => 990,
			'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'VEHICLE_TYPE',
			'DISPLAY_CODE' => 'VehicleType',
			'NAME' => static::getMessage('FIELD_VEHICLE_TYPE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_VEHICLE_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'MOTO_TYPE',
			'DISPLAY_CODE' => 'MotoType',
			'NAME' => static::getMessage('FIELD_MOTO_TYPE_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_MOTO_TYPE_DESC'),
			'REQUIRED' => true,
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'VIN',
			'DISPLAY_CODE' => 'VIN',
			'NAME' => static::getMessage('FIELD_VIN_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_VIN_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'YEAR',
			'DISPLAY_CODE' => 'Year',
			'NAME' => static::getMessage('FIELD_YEAR_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_YEAR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'POWER',
			'DISPLAY_CODE' => 'Power',
			'NAME' => static::getMessage('FIELD_POWER_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_CAPACITY',
			'DISPLAY_CODE' => 'EngineCapacity',
			'NAME' => static::getMessage('FIELD_ENGINE_CAPACITY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_CAPACITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'KILOMETRAGE',
			'DISPLAY_CODE' => 'Kilometrage',
			'NAME' => static::getMessage('FIELD_KILOMETRAGE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_KILOMETRAGE_DESC'),
		));			$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY',
			'DISPLAY_CODE' => 'Availability',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TECHNICAL_PASSPORT',
			'DISPLAY_CODE' => 'TechnicalPassport',
			'NAME' => static::getMessage('FIELD_TECHNICAL_PASSPORT_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_TECHNICAL_PASSPORT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OWNERS',
			'DISPLAY_CODE' => 'Owners',
			'NAME' => static::getMessage('FIELD_OWNERS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_OWNERS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAKE',
			'DISPLAY_CODE' => 'Make',
			'NAME' => static::getMessage('FIELD_MAKE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_MAKE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODEL',
			'DISPLAY_CODE' => 'Model',
			'NAME' => static::getMessage('FIELD_MODEL_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_MODEL_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TYPE',
			'DISPLAY_CODE' => 'Type',
			'NAME' => static::getMessage('FIELD_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_TYPE',
			'DISPLAY_CODE' => 'EngineType',
			'NAME' => static::getMessage('FIELD_ENGINE_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FUEL_FEED',
			'DISPLAY_CODE' => 'FuelFeed',
			'NAME' => static::getMessage('FIELD_FUEL_FEED_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FUEL_FEED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DRIVE_TYPE',
			'DISPLAY_CODE' => 'DriveType',
			'NAME' => static::getMessage('FIELD_DRIVE_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_DRIVE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STROKE',
			'DISPLAY_CODE' => 'Stroke',
			'NAME' => static::getMessage('FIELD_STROKE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_STROKE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CYLINDERS',
			'DISPLAY_CODE' => 'Cylinders',
			'NAME' => static::getMessage('FIELD_CYLINDERS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CYLINDERS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSMISSION',
			'DISPLAY_CODE' => 'Transmission',
			'NAME' => static::getMessage('FIELD_TRANSMISSION_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSMISSION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'NUMBER_OF_GEARS',
			'DISPLAY_CODE' => 'NumberOfGears',
			'NAME' => static::getMessage('FIELD_NUMBER_OF_GEARS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_NUMBER_OF_GEARS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CYLINDERS_POSITION',
			'DISPLAY_CODE' => 'CylindersPosition',
			'NAME' => static::getMessage('FIELD_CYLINDERS_POSITION_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CYLINDERS_POSITION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_COOLING',
			'DISPLAY_CODE' => 'EngineCooling',
			'NAME' => static::getMessage('FIELD_ENGINE_COOLING_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_COOLING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TOP_SPEED',
			'DISPLAY_CODE' => 'TopSpeed',
			'NAME' => static::getMessage('FIELD_TOP_SPEED_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_TOP_SPEED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BATTERY_CAPACITY',
			'DISPLAY_CODE' => 'BatteryCapacity',
			'NAME' => static::getMessage('FIELD_BATTERY_CAPACITY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_BATTERY_CAPACITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ELECTRIC_RANGE',
			'DISPLAY_CODE' => 'ElectricRange',
			'NAME' => static::getMessage('FIELD_ELECTRIC_RANGE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ELECTRIC_RANGE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CHARGING_TIME',
			'DISPLAY_CODE' => 'ChargingTime',
			'NAME' => static::getMessage('FIELD_CHARGING_TIME_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CHARGING_TIME_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ADDITIONAL_OPTIONS',
			'DISPLAY_CODE' => 'AdditionalOptions',
			'NAME' => static::getMessage('FIELD_ADDITIONAL_OPTIONS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ADDITIONAL_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		#
		$this->sortFields($arResult);
		return $arResult;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		# Build XML
		$arXmlTags = array(
			'Id' => array('#' => $arFields['ID']),
		);
		if(!Helper::isEmpty($arFields['DATE_BEGIN']))
			$arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
		if(!Helper::isEmpty($arFields['DATE_END']))
			$arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
		if(!Helper::isEmpty($arFields['LISTING_FEE']))
			$arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
		if(!Helper::isEmpty($arFields['AD_STATUS']))
			$arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
		if(!Helper::isEmpty($arFields['AVITO_ID']))
			$arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
		if(!Helper::isEmpty($arFields['AUCTION_PRICE']))
			$arXmlTags['AuctionPrice'] = Xml::addTag($arFields['AUCTION_PRICE']);
		if(!Helper::isEmpty($arFields['AUCTION_PRICE_LAST_DATE']))
			$arXmlTags['AuctionPriceLastDate'] = Xml::addTag($arFields['AUCTION_PRICE_LAST_DATE']);
		#
		if(!Helper::isEmpty($arFields['ALLOW_EMAIL']))
			$arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
		if(!Helper::isEmpty($arFields['EMAIL']))
			$arXmlTags['Email'] = Xml::addTag($arFields['EMAIL']);
		if(!Helper::isEmpty($arFields['MANAGER_NAME']))
			$arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
		if(!Helper::isEmpty($arFields['CONTACT_PHONE']))
			$arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
		if(!Helper::isEmpty($arFields['CONTACT_METHOD']))
			$arXmlTags['ContactMethod'] = Xml::addTag($arFields['CONTACT_METHOD']);
		#
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['IMAGES']))
			$arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
		if(!Helper::isEmpty($arFields['VIDEO_URL']))
			$arXmlTags['VideoURL'] = Xml::addTag($arFields['VIDEO_URL']);
		if(!Helper::isEmpty($arFields['TITLE']))
			$arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
		#
		if(!Helper::isEmpty($arFields['ADDRESS']))
			$arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
		if(!Helper::isEmpty($arFields['REGION']))
			$arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
		if(!Helper::isEmpty($arFields['CITY']))
			$arXmlTags['City'] = Xml::addTag($arFields['CITY']);
		if(!Helper::isEmpty($arFields['SUBWAY']))
			$arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
		if(!Helper::isEmpty($arFields['DISTRICT']))
			$arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
		if(!Helper::isEmpty($arFields['LATITUDE']))
			$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
		if(!Helper::isEmpty($arFields['LONGITUDE']))
			$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['CONDITION']))
			$arXmlTags['Condition'] = Xml::addTag($arFields['CONDITION']);
		if(!Helper::isEmpty($arFields['VEHICLE_TYPE']))
			$arXmlTags['VehicleType'] = Xml::addTag($arFields['VEHICLE_TYPE']);
		if(!Helper::isEmpty($arFields['MOTO_TYPE']))
			$arXmlTags['MotoType'] = Xml::addTag($arFields['MOTO_TYPE']);
		#
		if(!Helper::isEmpty($arFields['VIN']))
			$arXmlTags['VIN'] = Xml::addTag($arFields['VIN']);
		if(!Helper::isEmpty($arFields['YEAR']))
			$arXmlTags['Year'] = Xml::addTag($arFields['YEAR']);
		if(!Helper::isEmpty($arFields['POWER']))
			$arXmlTags['Power'] = Xml::addTag($arFields['POWER']);
		if(!Helper::isEmpty($arFields['ENGINE_CAPACITY']))
			$arXmlTags['EngineCapacity'] = Xml::addTag($arFields['ENGINE_CAPACITY']);
		if(!Helper::isEmpty($arFields['KILOMETRAGE']))
			$arXmlTags['Kilometrage'] = Xml::addTag($arFields['KILOMETRAGE']);
		#
		if(!Helper::isEmpty($arFields['AVAILABILITY']))
			$arXmlTags['Availability'] = Xml::addTag($arFields['AVAILABILITY']);
		if(!Helper::isEmpty($arFields['TECHNICAL_PASSPORT']))
			$arXmlTags['TechnicalPassport'] = Xml::addTag($arFields['TECHNICAL_PASSPORT']);
		if(!Helper::isEmpty($arFields['OWNERS']))
			$arXmlTags['Owners'] = Xml::addTag($arFields['OWNERS']);
		if(!Helper::isEmpty($arFields['MAKE']))
			$arXmlTags['Make'] = Xml::addTag($arFields['MAKE']);
		if(!Helper::isEmpty($arFields['MODEL']))
			$arXmlTags['Model'] = Xml::addTag($arFields['MODEL']);
		if(!Helper::isEmpty($arFields['TYPE']))
			$arXmlTags['Type'] = Xml::addTag($arFields['TYPE']);
		if(!Helper::isEmpty($arFields['ENGINE_TYPE']))
			$arXmlTags['EngineType'] = Xml::addTag($arFields['ENGINE_TYPE']);
		if(!Helper::isEmpty($arFields['FUEL_FEED']))
			$arXmlTags['FuelFeed'] = Xml::addTag($arFields['FUEL_FEED']);
		if(!Helper::isEmpty($arFields['DRIVE_TYPE']))
			$arXmlTags['DriveType'] = Xml::addTag($arFields['DRIVE_TYPE']);
		if(!Helper::isEmpty($arFields['STROKE']))
			$arXmlTags['Stroke'] = Xml::addTag($arFields['STROKE']);
		if(!Helper::isEmpty($arFields['CYLINDERS']))
			$arXmlTags['Cylinders'] = Xml::addTag($arFields['CYLINDERS']);
		if(!Helper::isEmpty($arFields['TRANSMISSION']))
			$arXmlTags['Transmission'] = Xml::addTag($arFields['TRANSMISSION']);
		if(!Helper::isEmpty($arFields['NUMBER_OF_GEARS']))
			$arXmlTags['NumberOfGears'] = Xml::addTag($arFields['NUMBER_OF_GEARS']);
		if(!Helper::isEmpty($arFields['CYLINDERS_POSITION']))
			$arXmlTags['CylindersPosition'] = Xml::addTag($arFields['CYLINDERS_POSITION']);
		if(!Helper::isEmpty($arFields['ENGINE_COOLING']))
			$arXmlTags['EngineCooling'] = Xml::addTag($arFields['ENGINE_COOLING']);
		if(!Helper::isEmpty($arFields['TOP_SPEED']))
			$arXmlTags['TopSpeed'] = Xml::addTag($arFields['TOP_SPEED']);
		if(!Helper::isEmpty($arFields['BATTERY_CAPACITY']))
			$arXmlTags['BatteryCapacity'] = Xml::addTag($arFields['BATTERY_CAPACITY']);
		if(!Helper::isEmpty($arFields['ELECTRIC_RANGE']))
			$arXmlTags['ElectricRange'] = Xml::addTag($arFields['ELECTRIC_RANGE']);
		if(!Helper::isEmpty($arFields['CHARGING_TIME']))
			$arXmlTags['ChargingTime'] = Xml::addTag($arFields['CHARGING_TIME']);
		if(!Helper::isEmpty($arFields['ADDITIONAL_OPTIONS']))
			$arXmlTags['AdditionalOptions'] = Xml::addTagWithSubtags($arFields['ADDITIONAL_OPTIONS'], 'option');
		# build XML
		$arXml = array(
			'Ad' => array(
				'#' => $arXmlTags,
			),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		$strXml = Xml::arrayToXml($arXml);
		# build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => $strXml,
			'CURRENCY' => '',
			'SECTION_ID' => static::getElement_SectionID($intProfileID, $arElement),
			'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
			'DATA_MORE' => array(),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# after..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}
	
	/**
	 *	Show outdated notice
	 */
	public function showMessages(){
		print static::outdatedGetMessage();
	}
	
}

?>