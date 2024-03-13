<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/realty/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoRealty extends Avito {
	
	CONST DATE_UPDATED = '2021-03-05';
	
	CONST CATEGORIES_PARSE_NODE = ''; // ???

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
		return parent::getCode().'_REALTY';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME').static::outdatedGetNameSuffix();
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_realty.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$this->modifyField($arResult, 'PRICE', array(
			'REQUIRED' => true,
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'STREET',
			'DISPLAY_CODE' => 'Street',
			'NAME' => static::getMessage('FIELD_STREET_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_STREET_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_STREET',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => '256',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DISTANCE_TO_CITY',
			'DISPLAY_CODE' => 'DistanceToCity',
			'NAME' => static::getMessage('FIELD_DISTANCE_TO_CITY_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_DISTANCE_TO_CITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DIRECTION_ROAD',
			'DISPLAY_CODE' => 'DirectionRoad',
			'NAME' => static::getMessage('FIELD_DIRECTION_ROAD_NAME'),
			'SORT' => 590,
			'DESCRIPTION' => static::getMessage('FIELD_DIRECTION_ROAD_DESC'),
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'OPERATION_TYPE',
			'DISPLAY_CODE' => 'OperationType',
			'NAME' => static::getMessage('FIELD_OPERATION_TYPE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_OPERATION_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'COUNTRY',
			'DISPLAY_CODE' => 'Country',
			'NAME' => static::getMessage('FIELD_COUNTRY_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_COUNTRY_DESC'),
		));
		$this->modifyField($arResult, 'TITLE', array(
			'SORT' => 1020,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_TYPE',
			'DISPLAY_CODE' => 'PriceType',
			'NAME' => static::getMessage('FIELD_PRICE_TYPE_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ROOMS',
			'DISPLAY_CODE' => 'Rooms',
			'NAME' => static::getMessage('FIELD_ROOMS_NAME'),
			'SORT' => 1040,
			'DESCRIPTION' => static::getMessage('FIELD_ROOMS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SQUARE',
			'DISPLAY_CODE' => 'Square',
			'NAME' => static::getMessage('FIELD_SQUARE_NAME'),
			'SORT' => 1050,
			'DESCRIPTION' => static::getMessage('FIELD_SQUARE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'KITCHEN_SPACE',
			'DISPLAY_CODE' => 'KitchenSpace',
			'NAME' => static::getMessage('FIELD_KITCHEN_SPACE_NAME'),
			'SORT' => 1060,
			'DESCRIPTION' => static::getMessage('FIELD_KITCHEN_SPACE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LIVING_SPACE',
			'DISPLAY_CODE' => 'LivingSpace',
			'NAME' => static::getMessage('FIELD_LIVING_SPACE_NAME'),
			'SORT' => 1070,
			'DESCRIPTION' => static::getMessage('FIELD_LIVING_SPACE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LAND_AREA',
			'DISPLAY_CODE' => 'LandArea',
			'NAME' => static::getMessage('FIELD_LAND_AREA_NAME'),
			'SORT' => 1080,
			'DESCRIPTION' => static::getMessage('FIELD_LAND_AREA_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FLOOR',
			'DISPLAY_CODE' => 'Floor',
			'NAME' => static::getMessage('FIELD_FLOOR_NAME'),
			'SORT' => 1090,
			'DESCRIPTION' => static::getMessage('FIELD_FLOOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FLOORS',
			'DISPLAY_CODE' => 'Floors',
			'NAME' => static::getMessage('FIELD_FLOORS_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_FLOORS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'HOUSE_TYPE',
			'DISPLAY_CODE' => 'HouseType',
			'NAME' => static::getMessage('FIELD_HOUSE_TYPE_NAME'),
			'SORT' => 1110,
			'DESCRIPTION' => static::getMessage('FIELD_HOUSE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WALLS_TYPE',
			'DISPLAY_CODE' => 'WallsType',
			'NAME' => static::getMessage('FIELD_WALLS_TYPE_NAME'),
			'SORT' => 1120,
			'DESCRIPTION' => static::getMessage('FIELD_WALLS_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MARKET_TYPE',
			'DISPLAY_CODE' => 'MarketType',
			'NAME' => static::getMessage('FIELD_MARKET_TYPE_NAME'),
			'SORT' => 1130,
			'DESCRIPTION' => static::getMessage('FIELD_MARKET_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'NEW_DEVELOPMENT_ID',
			'DISPLAY_CODE' => 'NewDevelopmentId',
			'NAME' => static::getMessage('FIELD_NEW_DEVELOPMENT_ID_NAME'),
			'SORT' => 1140,
			'DESCRIPTION' => static::getMessage('FIELD_NEW_DEVELOPMENT_ID_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PROPERTY_RIGHTS',
			'DISPLAY_CODE' => 'PropertyRights',
			'NAME' => static::getMessage('FIELD_PROPERTY_RIGHTS_NAME'),
			'SORT' => 1150,
			'DESCRIPTION' => static::getMessage('FIELD_PROPERTY_RIGHTS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OBJECT_TYPE',
			'DISPLAY_CODE' => 'ObjectType',
			'NAME' => static::getMessage('FIELD_OBJECT_TYPE_NAME'),
			'SORT' => 1160,
			'DESCRIPTION' => static::getMessage('FIELD_OBJECT_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ADDITIONAL_OBJECT_TYPES',
			'DISPLAY_CODE' => 'AdditionalObjectTypes',
			'NAME' => static::getMessage('FIELD_ADDITIONAL_OBJECT_TYPES_NAME'),
			'SORT' => 1162,
			'DESCRIPTION' => static::getMessage('FIELD_ADDITIONAL_OBJECT_TYPES_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OBJECT_SUBTYPE',
			'DISPLAY_CODE' => 'ObjectSubtype',
			'NAME' => static::getMessage('FIELD_OBJECT_SUBTYPE_NAME'),
			'SORT' => 1170,
			'DESCRIPTION' => static::getMessage('FIELD_OBJECT_SUBTYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SECURED',
			'DISPLAY_CODE' => 'Secured',
			'NAME' => static::getMessage('FIELD_SECURED_NAME'),
			'SORT' => 1180,
			'DESCRIPTION' => static::getMessage('FIELD_SECURED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BUILDING_CLASS',
			'DISPLAY_CODE' => 'BuildingClass',
			'NAME' => static::getMessage('FIELD_BUILDING_CLASS_NAME'),
			'SORT' => 1190,
			'DESCRIPTION' => static::getMessage('FIELD_BUILDING_CLASS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CADASTRAL_NUMBER',
			'DISPLAY_CODE' => 'CadastralNumber',
			'NAME' => static::getMessage('FIELD_CADASTRAL_NUMBER_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_CADASTRAL_NUMBER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DECORATION',
			'DISPLAY_CODE' => 'Decoration',
			'NAME' => static::getMessage('FIELD_DECORATION_NAME'),
			'SORT' => 1204,
			'DESCRIPTION' => static::getMessage('FIELD_DECORATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SAFE_DEMONSTRATION',
			'DISPLAY_CODE' => 'SafeDemonstration',
			'NAME' => static::getMessage('FIELD_SAFE_DEMONSTRATION_NAME'),
			'SORT' => 1205,
			'DESCRIPTION' => static::getMessage('FIELD_SAFE_DEMONSTRATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'APARTMENT_NUMBER',
			'DISPLAY_CODE' => 'ApartmentNumber',
			'NAME' => static::getMessage('FIELD_APARTMENT_NUMBER_NAME'),
			'SORT' => 1206,
			'DESCRIPTION' => static::getMessage('FIELD_APARTMENT_NUMBER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STATUS',
			'DISPLAY_CODE' => 'Status',
			'NAME' => static::getMessage('FIELD_STATUS_NAME'),
			'SORT' => 1207,
			'DESCRIPTION' => static::getMessage('FIELD_STATUS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BALCONY_OR_LOGGIA',
			'DISPLAY_CODE' => 'BalconyOrLoggia',
			'NAME' => static::getMessage('FIELD_BALCONY_OR_LOGGIA_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_BALCONY_OR_LOGGIA_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BALCONY_OR_LOGGIA_MULTI',
			'DISPLAY_CODE' => 'BalconyOrLoggiaMulti',
			'NAME' => static::getMessage('FIELD_BALCONY_OR_LOGGIA_MULTI_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_BALCONY_OR_LOGGIA_MULTI_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VIEW_FROM_WINDOWS',
			'DISPLAY_CODE' => 'ViewFromWindows',
			'NAME' => static::getMessage('FIELD_VIEW_FROM_WINDOWS_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_VIEW_FROM_WINDOWS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BUILT_YEAR',
			'DISPLAY_CODE' => 'BuiltYear',
			'NAME' => static::getMessage('FIELD_BUILT_YEAR_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_BUILT_YEAR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PASSENGER_ELEVATOR',
			'DISPLAY_CODE' => 'PassengerElevator',
			'NAME' => static::getMessage('FIELD_PASSENGER_ELEVATOR_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_PASSENGER_ELEVATOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FREIGHT_ELEVATOR',
			'DISPLAY_CODE' => 'FreightElevator',
			'NAME' => static::getMessage('FIELD_FREIGHT_ELEVATOR_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_FREIGHT_ELEVATOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'IN_HOUSE',
			'DISPLAY_CODE' => 'InHouse',
			'NAME' => static::getMessage('FIELD_IN_HOUSE_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_IN_HOUSE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COURTYARD',
			'DISPLAY_CODE' => 'Courtyard',
			'NAME' => static::getMessage('FIELD_COURTYARD_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_COURTYARD_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARKING',
			'DISPLAY_CODE' => 'Parking',
			'NAME' => static::getMessage('FIELD_PARKING_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_PARKING_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CEILING_HEIGHT',
			'DISPLAY_CODE' => 'CeilingHeight',
			'NAME' => static::getMessage('FIELD_CEILING_HEIGHT_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_CEILING_HEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENOVATION',
			'DISPLAY_CODE' => 'Renovation',
			'NAME' => static::getMessage('FIELD_RENOVATION_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_RENOVATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BATHROOM',
			'DISPLAY_CODE' => 'Bathroom',
			'NAME' => static::getMessage('FIELD_BATHROOM_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_BATHROOM_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BATHROOM_MULTI',
			'DISPLAY_CODE' => 'BathroomMulti',
			'NAME' => static::getMessage('FIELD_BATHROOM_MULTI_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_BATHROOM_MULTI_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SSADDITIONALLY',
			'DISPLAY_CODE' => 'SSAdditionally',
			'NAME' => static::getMessage('FIELD_SSADDITIONALLY_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_SSADDITIONALLY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'NDADDITIONALLY',
			'DISPLAY_CODE' => 'NDAdditionally',
			'NAME' => static::getMessage('FIELD_NDADDITIONALLY_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_NDADDITIONALLY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DEALTYPE',
			'DISPLAY_CODE' => 'DealType',
			'NAME' => static::getMessage('FIELD_DEALTYPE_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_DEALTYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ROOMTYPE',
			'DISPLAY_CODE' => 'RoomType',
			'NAME' => static::getMessage('FIELD_ROOMTYPE_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_ROOMTYPE_DESC'),
		));
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1220,
				'NAME' => static::getMessage('HEADER_LEASE'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_TYPE',
			'DISPLAY_CODE' => 'LeaseType',
			'NAME' => static::getMessage('FIELD_LEASE_TYPE_NAME'),
			'SORT' => 1221,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_BEDS',
			'DISPLAY_CODE' => 'LeaseBeds',
			'NAME' => static::getMessage('FIELD_LEASE_BEDS_NAME'),
			'SORT' => 1222,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_BEDS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_SLEEPING_PLACES',
			'DISPLAY_CODE' => 'LeaseSleepingPlaces',
			'NAME' => static::getMessage('FIELD_LEASE_SLEEPING_PLACES_NAME'),
			'SORT' => 1230,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_SLEEPING_PLACES_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_MULTIMEDIA',
			'DISPLAY_CODE' => 'LeaseMultimedia',
			'NAME' => static::getMessage('FIELD_LEASE_MULTIMEDIA_NAME'),
			'SORT' => 1240,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_MULTIMEDIA_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_APPLIANCES',
			'DISPLAY_CODE' => 'LeaseAppliances',
			'NAME' => static::getMessage('FIELD_LEASE_APPLIANCES_NAME'),
			'SORT' => 1250,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_APPLIANCES_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_COMFORT',
			'DISPLAY_CODE' => 'LeaseComfort',
			'NAME' => static::getMessage('FIELD_LEASE_COMFORT_NAME'),
			'SORT' => 1260,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_COMFORT_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_ADDITIONALLY',
			'DISPLAY_CODE' => 'LeaseAdditionally',
			'NAME' => static::getMessage('FIELD_LEASE_ADDITIONALLY_NAME'),
			'SORT' => 1270,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_COMMISSION_SIZE',
			'DISPLAY_CODE' => 'LeaseCommissionSize',
			'NAME' => static::getMessage('FIELD_LEASE_COMMISSION_SIZE_NAME'),
			'SORT' => 1280,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_COMMISSION_SIZE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_DEPOSIT',
			'DISPLAY_CODE' => 'LeaseDeposit',
			'NAME' => static::getMessage('FIELD_LEASE_DEPOSIT_NAME'),
			'SORT' => 1290,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_DEPOSIT_DESC'),
		));
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 5000,
				'NAME' => static::getMessage('HEADER_ADDITIONAL'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'LAND_ADDITIONALLY',
			'DISPLAY_CODE' => 'LandAdditionally',
			'NAME' => static::getMessage('FIELD_LAND_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_LAND_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'LAND_STATUS',
			'DISPLAY_CODE' => 'LandStatus',
			'NAME' => static::getMessage('FIELD_LAND_STATUS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_LAND_STATUS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'REPAIR_ADDITIONALLY',
			'DISPLAY_CODE' => 'RepairAdditionally',
			'NAME' => static::getMessage('FIELD_REPAIR_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_REPAIR_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'FURNITURE',
			'DISPLAY_CODE' => 'Furniture',
			'NAME' => static::getMessage('FIELD_FURNITURE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FURNITURE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENOVATION_PROGRAM',
			'DISPLAY_CODE' => 'RenovationProgram',
			'NAME' => static::getMessage('FIELD_RENOVATION_PROGRAM_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_RENOVATION_PROGRAM_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'HOUSE_ADDITIONALLY',
			'DISPLAY_CODE' => 'HouseAdditionally',
			'NAME' => static::getMessage('FIELD_HOUSE_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_HOUSE_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'HOUSE_SERVICES',
			'DISPLAY_CODE' => 'HouseServices',
			'NAME' => static::getMessage('FIELD_HOUSE_SERVICES_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_HOUSE_SERVICES_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARKING_TYPE',
			'DISPLAY_CODE' => 'ParkingType',
			'NAME' => static::getMessage('FIELD_PARKING_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_PARKING_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARKING_ADDITIONALLY',
			'DISPLAY_CODE' => 'ParkingAdditionally',
			'NAME' => static::getMessage('FIELD_PARKING_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_PARKING_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSPORT_ACCESSIBILITY',
			'DISPLAY_CODE' => 'TransportAccessibility',
			'NAME' => static::getMessage('FIELD_TRANSPORT_ACCESSIBILITY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSPORT_ACCESSIBILITY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'INFRASTRUCTURE',
			'DISPLAY_CODE' => 'Infrastructure',
			'NAME' => static::getMessage('FIELD_INFRASTRUCTURE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_INFRASTRUCTURE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'SALE_METHOD',
			'DISPLAY_CODE' => 'SaleMethod',
			'NAME' => static::getMessage('FIELD_SALE_METHOD_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SALE_METHOD_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SALE_OPTIONS',
			'DISPLAY_CODE' => 'SaleOptions',
			'NAME' => static::getMessage('FIELD_SALE_OPTIONS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SALE_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'PREMISES_TYPE',
			'DISPLAY_CODE' => 'PremisesType',
			'NAME' => static::getMessage('FIELD_PREMISES_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_PREMISES_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENTRANCE',
			'DISPLAY_CODE' => 'Entrance',
			'NAME' => static::getMessage('FIELD_ENTRANCE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ENTRANCE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENTRANCE_ADDITIONALLY',
			'DISPLAY_CODE' => 'EntranceAdditionally',
			'NAME' => static::getMessage('FIELD_ENTRANCE_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ENTRANCE_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'FLOOR_ADDITIONALLY',
			'DISPLAY_CODE' => 'FloorAdditionally',
			'NAME' => static::getMessage('FIELD_FLOOR_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FLOOR_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'LAYOUT',
			'DISPLAY_CODE' => 'Layout',
			'NAME' => static::getMessage('FIELD_LAYOUT_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_LAYOUT_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'POWER_GRID_CAPACITY',
			'DISPLAY_CODE' => 'PowerGridCapacity',
			'NAME' => static::getMessage('FIELD_POWER_GRID_CAPACITY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_GRID_CAPACITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'POWER_GRID_ADDITIONALLY',
			'DISPLAY_CODE' => 'PowerGridAdditionally',
			'NAME' => static::getMessage('FIELD_POWER_GRID_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_GRID_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'HEATING',
			'DISPLAY_CODE' => 'Heating',
			'NAME' => static::getMessage('FIELD_HEATING_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_HEATING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'READINESS_STATUS',
			'DISPLAY_CODE' => 'ReadinessStatus',
			'NAME' => static::getMessage('FIELD_READINESS_STATUS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_READINESS_STATUS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BUILDING_TYPE',
			'DISPLAY_CODE' => 'BuildingType',
			'NAME' => static::getMessage('FIELD_BUILDING_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_BUILDING_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DISTANCE_FROM_ROAD',
			'DISPLAY_CODE' => 'DistanceFromRoad',
			'NAME' => static::getMessage('FIELD_DISTANCE_FROM_ROAD_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_DISTANCE_FROM_ROAD_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARKING_SPACES',
			'DISPLAY_CODE' => 'ParkingSpaces',
			'NAME' => static::getMessage('FIELD_PARKING_SPACES_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_PARKING_SPACES_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSACTION_TYPE',
			'DISPLAY_CODE' => 'TransactionType',
			'NAME' => static::getMessage('FIELD_TRANSACTION_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSACTION_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CURRENT_TENANTS',
			'DISPLAY_CODE' => 'CurrentTenants',
			'NAME' => static::getMessage('FIELD_CURRENT_TENANTS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENT_TENANTS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOREIGN_REALTY_COMPLETION_YEAR',
			'DISPLAY_CODE' => 'ForeignRealtyCompletionYear',
			'NAME' => static::getMessage('FIELD_FOREIGN_REALTY_COMPLETION_YEAR_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FOREIGN_REALTY_COMPLETION_YEAR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BATHROOM_COUNT',
			'DISPLAY_CODE' => 'BathroomCount',
			'NAME' => static::getMessage('FIELD_BATHROOM_COUNT_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_BATHROOM_COUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ELEVATOR',
			'DISPLAY_CODE' => 'Elevator',
			'NAME' => static::getMessage('FIELD_ELEVATOR_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ELEVATOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOREIGN_REALTY_ADDITIONALLY',
			'DISPLAY_CODE' => 'ForeignRealtyAdditionally',
			'NAME' => static::getMessage('FIELD_FOREIGN_REALTY_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FOREIGN_REALTY_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'AIRPORT_DISTANCE',
			'DISPLAY_CODE' => 'AirportDistance',
			'NAME' => static::getMessage('FIELD_AIRPORT_DISTANCE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_AIRPORT_DISTANCE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WATER_DISTANCE',
			'DISPLAY_CODE' => 'WaterDistance',
			'NAME' => static::getMessage('FIELD_WATER_DISTANCE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_WATER_DISTANCE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RESIDENCE_AFTER_DEAL',
			'DISPLAY_CODE' => 'ResidenceAfterDeal',
			'NAME' => static::getMessage('FIELD_RESIDENCE_AFTER_DEAL_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_RESIDENCE_AFTER_DEAL_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOREIGN_REALTY_SALE_OPTIONS',
			'DISPLAY_CODE' => 'ForeignRealtySaleOptions',
			'NAME' => static::getMessage('FIELD_FOREIGN_REALTY_SALE_OPTIONS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FOREIGN_REALTY_SALE_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'INVESTMENT',
			'DISPLAY_CODE' => 'Investment',
			'NAME' => static::getMessage('FIELD_INVESTMENT_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_INVESTMENT_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE',
			'DISPLAY_CODE' => 'ForeignRealtyCommercialObjectType',
			'NAME' => static::getMessage('FIELD_FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOREIGN_REALTY_LAND_STATUS',
			'DISPLAY_CODE' => 'ForeignRealtyLandStatus',
			'NAME' => static::getMessage('FIELD_FOREIGN_REALTY_LAND_STATUS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FOREIGN_REALTY_LAND_STATUS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOREIGN_CITY',
			'DISPLAY_CODE' => 'ForeignCity',
			'NAME' => static::getMessage('FIELD_FOREIGN_CITY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_FOREIGN_CITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHAREHOLDER_FIRST_NAME',
			'DISPLAY_CODE' => 'ShareholderFirstName',
			'NAME' => static::getMessage('FIELD_SHAREHOLDER_FIRST_NAME_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SHAREHOLDER_FIRST_NAME_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHAREHOLDER_LAST_NAME',
			'DISPLAY_CODE' => 'ShareholderLastName',
			'NAME' => static::getMessage('FIELD_SHAREHOLDER_LAST_NAME_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SHAREHOLDER_LAST_NAME_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHAREHOLDER_PATRONYMIC',
			'DISPLAY_CODE' => 'ShareholderPatronymic',
			'NAME' => static::getMessage('FIELD_SHAREHOLDER_PATRONYMIC_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SHAREHOLDER_PATRONYMIC_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHAREHOLDER_INN',
			'DISPLAY_CODE' => 'ShareholderINN',
			'NAME' => static::getMessage('FIELD_SHAREHOLDER_INN_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SHAREHOLDER_INN_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CURRENCY_PRICE',
			'DISPLAY_CODE' => 'CurrencyPrice',
			'NAME' => static::getMessage('FIELD_CURRENCY_PRICE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_PRICE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CURRENCY',
			'DISPLAY_CODE' => 'Currency',
			'NAME' => static::getMessage('FIELD_CURRENCY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RESIDENCE_TYPE',
			'DISPLAY_CODE' => 'ResidenceType',
			'NAME' => static::getMessage('FIELD_RESIDENCE_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_RESIDENCE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ROOM_LOCATION_TYPE',
			'DISPLAY_CODE' => 'RoomLocationType',
			'NAME' => static::getMessage('FIELD_ROOM_LOCATION_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_ROOM_LOCATION_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BED_LOCATION_TYPE',
			'DISPLAY_CODE' => 'BedLocationType',
			'NAME' => static::getMessage('FIELD_BED_LOCATION_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_BED_LOCATION_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_COMFORT_MULTI',
			'DISPLAY_CODE' => 'LeaseComfortMulti',
			'NAME' => static::getMessage('FIELD_LEASE_COMFORT_MULTI_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_COMFORT_MULTI_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_PRICE_OPTIONS',
			'DISPLAY_CODE' => 'LeasePriceOptions',
			'NAME' => static::getMessage('FIELD_LEASE_PRICE_OPTIONS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_PRICE_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'SQUARE_ADDITIONALLY',
			'DISPLAY_CODE' => 'SquareAdditionally',
			'NAME' => static::getMessage('FIELD_SQUARE_ADDITIONALLY_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SQUARE_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENTAL_TYPE',
			'DISPLAY_CODE' => 'RentalType',
			'NAME' => static::getMessage('FIELD_RENTAL_TYPE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_RENTAL_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENTAL_HOLIDAYS',
			'DISPLAY_CODE' => 'RentalHolidays',
			'NAME' => static::getMessage('FIELD_RENTAL_HOLIDAYS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_RENTAL_HOLIDAYS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENTAL_MINIMUM_PERIOD',
			'DISPLAY_CODE' => 'RentalMinimumPeriod',
			'NAME' => static::getMessage('FIELD_RENTAL_MINIMUM_PERIOD_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_RENTAL_MINIMUM_PERIOD_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_DEPOSIT_PRICE',
			'DISPLAY_CODE' => 'LeaseDepositPrice',
			'NAME' => static::getMessage('FIELD_LEASE_DEPOSIT_PRICE_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_DEPOSIT_PRICE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CHILDREN_ALLOWED',
			'DISPLAY_CODE' => 'ChildrenAllowed',
			'NAME' => static::getMessage('FIELD_CHILDREN_ALLOWED_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_CHILDREN_ALLOWED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PETS_ALLOWED',
			'DISPLAY_CODE' => 'PetsAllowed',
			'NAME' => static::getMessage('FIELD_PETS_ALLOWED_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_PETS_ALLOWED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SMOKING_ALLOWED',
			'DISPLAY_CODE' => 'SmokingAllowed',
			'NAME' => static::getMessage('FIELD_SMOKING_ALLOWED_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_SMOKING_ALLOWED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARTIES_ALLOWED',
			'DISPLAY_CODE' => 'PartiesAllowed',
			'NAME' => static::getMessage('FIELD_PARTIES_ALLOWED_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_PARTIES_ALLOWED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DOCUMENTS',
			'DISPLAY_CODE' => 'Documents',
			'NAME' => static::getMessage('FIELD_DOCUMENTS_NAME'),
			'SORT' => 5000,
			'DESCRIPTION' => static::getMessage('FIELD_DOCUMENTS_DESC'),
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
		if(!Helper::isEmpty($arFields['IMAGE_URLS']))
			$arXmlTags['ImageUrls'] = Xml::addTag($arFields['IMAGE_URLS']);
		if(!Helper::isEmpty($arFields['IMAGE_NAMES']))
			$arXmlTags['ImageNames'] = Xml::addTag($arFields['IMAGE_NAMES']);
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
		if(!Helper::isEmpty($arFields['STREET']))
			$arXmlTags['Street'] = Xml::addTag($arFields['STREET']);
		if(!Helper::isEmpty($arFields['LATITUDE']))
			$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
		if(!Helper::isEmpty($arFields['LONGITUDE']))
			$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
		if(!Helper::isEmpty($arFields['DISTANCE_TO_CITY']))
			$arXmlTags['DistanceToCity'] = Xml::addTag($arFields['DISTANCE_TO_CITY']);
		if(!Helper::isEmpty($arFields['DIRECTION_ROAD']))
			$arXmlTags['DirectionRoad'] = Xml::addTag($arFields['DIRECTION_ROAD']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['OPERATION_TYPE']))
			$arXmlTags['OperationType'] = Xml::addTag($arFields['OPERATION_TYPE']);
		if(!Helper::isEmpty($arFields['COUNTRY']))
			$arXmlTags['Country'] = Xml::addTag($arFields['COUNTRY']);
		if(!Helper::isEmpty($arFields['PRICE_TYPE']))
			$arXmlTags['PriceType'] = Xml::addTag($arFields['PRICE_TYPE']);
		if(!Helper::isEmpty($arFields['ROOMS']))
			$arXmlTags['Rooms'] = Xml::addTag($arFields['ROOMS']);
		if(!Helper::isEmpty($arFields['SQUARE']))
			$arXmlTags['Square'] = Xml::addTag($arFields['SQUARE']);
		if(!Helper::isEmpty($arFields['KITCHEN_SPACE']))
			$arXmlTags['KitchenSpace'] = Xml::addTag($arFields['KITCHEN_SPACE']);
		if(!Helper::isEmpty($arFields['LIVING_SPACE']))
			$arXmlTags['LivingSpace'] = Xml::addTag($arFields['LIVING_SPACE']);
		if(!Helper::isEmpty($arFields['LAND_AREA']))
			$arXmlTags['LandArea'] = Xml::addTag($arFields['LAND_AREA']);
		if(!Helper::isEmpty($arFields['FLOOR']))
			$arXmlTags['Floor'] = Xml::addTag($arFields['FLOOR']);
		if(!Helper::isEmpty($arFields['FLOORS']))
			$arXmlTags['Floors'] = Xml::addTag($arFields['FLOORS']);
		if(!Helper::isEmpty($arFields['HOUSE_TYPE']))
			$arXmlTags['HouseType'] = Xml::addTag($arFields['HOUSE_TYPE']);
		if(!Helper::isEmpty($arFields['WALLS_TYPE']))
			$arXmlTags['WallsType'] = Xml::addTag($arFields['WALLS_TYPE']);
		if(!Helper::isEmpty($arFields['MARKET_TYPE']))
			$arXmlTags['MarketType'] = Xml::addTag($arFields['MARKET_TYPE']);
		if(!Helper::isEmpty($arFields['NEW_DEVELOPMENT_ID']))
			$arXmlTags['NewDevelopmentId'] = Xml::addTag($arFields['NEW_DEVELOPMENT_ID']);
		if(!Helper::isEmpty($arFields['PROPERTY_RIGHTS']))
			$arXmlTags['PropertyRights'] = Xml::addTag($arFields['PROPERTY_RIGHTS']);
		if(!Helper::isEmpty($arFields['OBJECT_TYPE']))
			$arXmlTags['ObjectType'] = Xml::addTag($arFields['OBJECT_TYPE']);
		if(!Helper::isEmpty($arFields['ADDITIONAL_OBJECT_TYPES']))
			$arXmlTags['AdditionalObjectTypes'] = Xml::addTagWithSubtags($arFields['ADDITIONAL_OBJECT_TYPES'], 'option');
		if(!Helper::isEmpty($arFields['OBJECT_SUBTYPE']))
			$arXmlTags['ObjectSubtype'] = Xml::addTag($arFields['OBJECT_SUBTYPE']);
		if(!Helper::isEmpty($arFields['SECURED']))
			$arXmlTags['Secured'] = Xml::addTag($arFields['SECURED']);
		if(!Helper::isEmpty($arFields['BUILDING_CLASS']))
			$arXmlTags['BuildingClass'] = Xml::addTag($arFields['BUILDING_CLASS']);
		if(!Helper::isEmpty($arFields['CADASTRAL_NUMBER']))
			$arXmlTags['CadastralNumber'] = Xml::addTag($arFields['CADASTRAL_NUMBER']);
		if(!Helper::isEmpty($arFields['DECORATION']))
			$arXmlTags['Decoration'] = Xml::addTag($arFields['DECORATION']);
		if(!Helper::isEmpty($arFields['SAFE_DEMONSTRATION']))
			$arXmlTags['SafeDemonstration'] = Xml::addTag($arFields['SAFE_DEMONSTRATION']);
		if(!Helper::isEmpty($arFields['APARTMENT_NUMBER']))
			$arXmlTags['ApartmentNumber'] = Xml::addTag($arFields['APARTMENT_NUMBER']);
		if(!Helper::isEmpty($arFields['STATUS']))
			$arXmlTags['Status'] = Xml::addTag($arFields['STATUS']);
		if(!Helper::isEmpty($arFields['BALCONY_OR_LOGGIA']))
			$arXmlTags['BalconyOrLoggia'] = Xml::addTag($arFields['BALCONY_OR_LOGGIA']);
		if(!Helper::isEmpty($arFields['BALCONY_OR_LOGGIA_MULTI']))
			$arXmlTags['BalconyOrLoggiaMulti'] = Xml::addTagWithSubtags($arFields['BALCONY_OR_LOGGIA_MULTI'], 'option');
		if(!Helper::isEmpty($arFields['VIEW_FROM_WINDOWS']))
			$arXmlTags['ViewFromWindows'] = Xml::addTagWithSubtags($arFields['VIEW_FROM_WINDOWS'], 'option');
		if(!Helper::isEmpty($arFields['BUILT_YEAR']))
			$arXmlTags['BuiltYear'] = Xml::addTag($arFields['BUILT_YEAR']);
		if(!Helper::isEmpty($arFields['PASSENGER_ELEVATOR']))
			$arXmlTags['PassengerElevator'] = Xml::addTag($arFields['PASSENGER_ELEVATOR']);
		if(!Helper::isEmpty($arFields['FREIGHT_ELEVATOR']))
			$arXmlTags['FreightElevator'] = Xml::addTag($arFields['FREIGHT_ELEVATOR']);
		if(!Helper::isEmpty($arFields['IN_HOUSE']))
			$arXmlTags['InHouse'] = Xml::addTagWithSubtags($arFields['IN_HOUSE'], 'option');
		if(!Helper::isEmpty($arFields['COURTYARD']))
			$arXmlTags['Courtyard'] = Xml::addTagWithSubtags($arFields['COURTYARD'], 'option');
		if(!Helper::isEmpty($arFields['PARKING']))
			$arXmlTags['Parking'] = Xml::addTagWithSubtags($arFields['PARKING'], 'option');
		if(!Helper::isEmpty($arFields['CEILING_HEIGHT']))
			$arXmlTags['CeilingHeight'] = Xml::addTag($arFields['CEILING_HEIGHT']);
		if(!Helper::isEmpty($arFields['RENOVATION']))
			$arXmlTags['Renovation'] = Xml::addTag($arFields['RENOVATION']);
		if(!Helper::isEmpty($arFields['BATHROOM']))
			$arXmlTags['Bathroom'] = Xml::addTag($arFields['BATHROOM']);
		if(!Helper::isEmpty($arFields['BATHROOM_MULTI']))
			$arXmlTags['BathroomMulti'] = Xml::addTagWithSubtags($arFields['BATHROOM_MULTI'], 'option');
		if(!Helper::isEmpty($arFields['SSADDITIONALLY']))
			$arXmlTags['SSAdditionally'] = Xml::addTag($arFields['SSADDITIONALLY']);
		if(!Helper::isEmpty($arFields['NDADDITIONALLY']))
			$arXmlTags['NDAdditionally'] = Xml::addTag($arFields['NDADDITIONALLY']);
		if(!Helper::isEmpty($arFields['DEALTYPE']))
			$arXmlTags['DealType'] = Xml::addTag($arFields['DEALTYPE']);
		if(!Helper::isEmpty($arFields['ROOMTYPE']))
			$arXmlTags['RoomType'] = Xml::addTag($arFields['ROOMTYPE']);
		#
		if(!Helper::isEmpty($arFields['LEASE_TYPE']))
			$arXmlTags['LeaseType'] = Xml::addTag($arFields['LEASE_TYPE']);
		if(!Helper::isEmpty($arFields['LEASE_BEDS']))
			$arXmlTags['LeaseBeds'] = Xml::addTag($arFields['LEASE_BEDS']);
		if(!Helper::isEmpty($arFields['LEASE_SLEEPING_PLACES']))
			$arXmlTags['LeaseSleepingPlaces'] = Xml::addTag($arFields['LEASE_SLEEPING_PLACES']);
		if(!Helper::isEmpty($arFields['LEASE_MULTIMEDIA']))
			$arXmlTags['LeaseMultimedia'] = Xml::addTagWithSubtags($arFields['LEASE_MULTIMEDIA'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_APPLIANCES']))
			$arXmlTags['LeaseAppliances'] = Xml::addTagWithSubtags($arFields['LEASE_APPLIANCES'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_COMFORT']))
			$arXmlTags['LeaseComfort'] = Xml::addTagWithSubtags($arFields['LEASE_COMFORT'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_ADDITIONALLY']))
			$arXmlTags['LeaseAdditionally'] = Xml::addTagWithSubtags($arFields['LEASE_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_COMMISSIONSIZE']))
			$arXmlTags['LeaseCommissionSize'] = Xml::addTag($arFields['LEASE_COMMISSIONSIZE']);
		if(!Helper::isEmpty($arFields['LEASE_DEPOSIT']))
			$arXmlTags['LeaseDeposit'] = Xml::addTag($arFields['LEASE_DEPOSIT']);
		#
		if(!Helper::isEmpty($arFields['LAND_ADDITIONALLY']))
			$arXmlTags['LandAdditionally'] = Xml::addTagWithSubtags($arFields['LAND_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['LAND_STATUS']))
			$arXmlTags['LandStatus'] = Xml::addTag($arFields['LAND_STATUS']);
		if(!Helper::isEmpty($arFields['REPAIR_ADDITIONALLY']))
			$arXmlTags['RepairAdditionally'] = Xml::addTagWithSubtags($arFields['REPAIR_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['FURNITURE']))
			$arXmlTags['Furniture'] = Xml::addTagWithSubtags($arFields['FURNITURE'], 'option');
		if(!Helper::isEmpty($arFields['RENOVATION_PROGRAM']))
			$arXmlTags['RenovationProgram'] = Xml::addTagWithSubtags($arFields['RENOVATION_PROGRAM'], 'option');
		if(!Helper::isEmpty($arFields['HOUSE_ADDITIONALLY']))
			$arXmlTags['HouseAdditionally'] = Xml::addTagWithSubtags($arFields['HOUSE_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['HOUSE_SERVICES']))
			$arXmlTags['HouseServices'] = Xml::addTagWithSubtags($arFields['HOUSE_SERVICES'], 'option');
		if(!Helper::isEmpty($arFields['PARKING_TYPE']))
			$arXmlTags['ParkingType'] = Xml::addTag($arFields['PARKING_TYPE']);
		if(!Helper::isEmpty($arFields['PARKING_ADDITIONALLY']))
			$arXmlTags['ParkingAdditionally'] = Xml::addTagWithSubtags($arFields['PARKING_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['TRANSPORT_ACCESSIBILITY']))
			$arXmlTags['TransportAccessibility'] = Xml::addTagWithSubtags($arFields['TRANSPORT_ACCESSIBILITY'], 'option');
		if(!Helper::isEmpty($arFields['INFRASTRUCTURE']))
			$arXmlTags['Infrastructure'] = Xml::addTagWithSubtags($arFields['INFRASTRUCTURE'], 'option');
		if(!Helper::isEmpty($arFields['SALE_METHOD']))
			$arXmlTags['SaleMethod'] = Xml::addTag($arFields['SALE_METHOD']);
		if(!Helper::isEmpty($arFields['SALE_OPTIONS']))
			$arXmlTags['SaleOptions'] = Xml::addTagWithSubtags($arFields['SALE_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['PREMISES_TYPE']))
			$arXmlTags['PremisesType'] = Xml::addTag($arFields['PREMISES_TYPE']);
		if(!Helper::isEmpty($arFields['ENTRANCE']))
			$arXmlTags['Entrance'] = Xml::addTag($arFields['ENTRANCE']);
		if(!Helper::isEmpty($arFields['ENTRANCE_ADDITIONALLY']))
			$arXmlTags['EntranceAdditionally'] = Xml::addTagWithSubtags($arFields['ENTRANCE_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['FLOOR_ADDITIONALLY']))
			$arXmlTags['FloorAdditionally'] = Xml::addTagWithSubtags($arFields['FLOOR_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['LAYOUT']))
			$arXmlTags['Layout'] = Xml::addTagWithSubtags($arFields['LAYOUT'], 'option');
		if(!Helper::isEmpty($arFields['POWER_GRID_CAPACITY']))
			$arXmlTags['PowerGridCapacity'] = Xml::addTag($arFields['POWER_GRID_CAPACITY']);
		if(!Helper::isEmpty($arFields['POWER_GRID_ADDITIONALLY']))
			$arXmlTags['PowerGridAdditionally'] = Xml::addTagWithSubtags($arFields['POWER_GRID_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['HEATING']))
			$arXmlTags['Heating'] = Xml::addTag($arFields['HEATING']);
		if(!Helper::isEmpty($arFields['READINESS_STATUS']))
			$arXmlTags['ReadinessStatus'] = Xml::addTag($arFields['READINESS_STATUS']);
		if(!Helper::isEmpty($arFields['BUILDING_TYPE']))
			$arXmlTags['BuildingType'] = Xml::addTag($arFields['BUILDING_TYPE']);
		if(!Helper::isEmpty($arFields['DISTANCE_FROM_ROAD']))
			$arXmlTags['DistanceFromRoad'] = Xml::addTag($arFields['DISTANCE_FROM_ROAD']);
		if(!Helper::isEmpty($arFields['PARKING_SPACES']))
			$arXmlTags['ParkingSpaces'] = Xml::addTag($arFields['PARKING_SPACES']);
		if(!Helper::isEmpty($arFields['TRANSACTION_TYPE']))
			$arXmlTags['TransactionType'] = Xml::addTag($arFields['TRANSACTION_TYPE']);
		if(!Helper::isEmpty($arFields['CURRENT_TENANTS']))
			$arXmlTags['CurrentTenants'] = Xml::addTagWithSubtags($arFields['CURRENT_TENANTS'], 'option');
		if(!Helper::isEmpty($arFields['FOREIGN_REALTY_COMPLETION_YEAR']))
			$arXmlTags['ForeignRealtyCompletionYear'] = Xml::addTag($arFields['FOREIGN_REALTY_COMPLETION_YEAR']);
		if(!Helper::isEmpty($arFields['BATHROOM_COUNT']))
			$arXmlTags['BathroomCount'] = Xml::addTag($arFields['BATHROOM_COUNT']);
		if(!Helper::isEmpty($arFields['ELEVATOR']))
			$arXmlTags['Elevator'] = Xml::addTag($arFields['ELEVATOR']);
		if(!Helper::isEmpty($arFields['FOREIGN_REALTY_ADDITIONALLY']))
			$arXmlTags['ForeignRealtyAdditionally'] = Xml::addTagWithSubtags($arFields['FOREIGN_REALTY_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['AIRPORT_DISTANCE']))
			$arXmlTags['AirportDistance'] = Xml::addTag($arFields['AIRPORT_DISTANCE']);
		if(!Helper::isEmpty($arFields['WATER_DISTANCE']))
			$arXmlTags['WaterDistance'] = Xml::addTag($arFields['WATER_DISTANCE']);
		if(!Helper::isEmpty($arFields['RESIDENCE_AFTER_DEAL']))
			$arXmlTags['ResidenceAfterDeal'] = Xml::addTag($arFields['RESIDENCE_AFTER_DEAL']);
		if(!Helper::isEmpty($arFields['FOREIGN_REALTY_SALE_OPTIONS']))
			$arXmlTags['ForeignRealtySaleOptions'] = Xml::addTagWithSubtags($arFields['FOREIGN_REALTY_SALE_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['INVESTMENT']))
			$arXmlTags['Investment'] = Xml::addTagWithSubtags($arFields['INVESTMENT'], 'option');
		if(!Helper::isEmpty($arFields['FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE']))
			$arXmlTags['ForeignRealtyCommercialObjectType'] = Xml::addTag($arFields['FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE']);
		if(!Helper::isEmpty($arFields['FOREIGN_REALTY_LAND_STATUS']))
			$arXmlTags['ForeignRealtyLandStatus'] = Xml::addTagWithSubtags($arFields['FOREIGN_REALTY_LAND_STATUS'], 'option');
		if(!Helper::isEmpty($arFields['FOREIGN_CITY']))
			$arXmlTags['ForeignCity'] = Xml::addTag($arFields['FOREIGN_CITY']);
		if(!Helper::isEmpty($arFields['SHAREHOLDER_FIRST_NAME']))
			$arXmlTags['ShareholderFirstName'] = Xml::addTag($arFields['SHAREHOLDER_FIRST_NAME']);
		if(!Helper::isEmpty($arFields['SHAREHOLDER_LAST_NAME']))
			$arXmlTags['ShareholderLastName'] = Xml::addTag($arFields['SHAREHOLDER_LAST_NAME']);
		if(!Helper::isEmpty($arFields['SHAREHOLDER_PATRONYMIC']))
			$arXmlTags['ShareholderPatronymic'] = Xml::addTag($arFields['SHAREHOLDER_PATRONYMIC']);
		if(!Helper::isEmpty($arFields['SHAREHOLDER_INN']))
			$arXmlTags['ShareholderINN'] = Xml::addTag($arFields['SHAREHOLDER_INN']);
		if(!Helper::isEmpty($arFields['CURRENCY_PRICE']))
			$arXmlTags['CurrencyPrice'] = Xml::addTag($arFields['CURRENCY_PRICE']);
		if(!Helper::isEmpty($arFields['CURRENCY']))
			$arXmlTags['Currency'] = Xml::addTag($arFields['CURRENCY']);
		if(!Helper::isEmpty($arFields['RESIDENCE_TYPE']))
			$arXmlTags['ResidenceType'] = Xml::addTag($arFields['RESIDENCE_TYPE']);
		if(!Helper::isEmpty($arFields['ROOM_LOCATION_TYPE']))
			$arXmlTags['RoomLocationType'] = Xml::addTag($arFields['ROOM_LOCATION_TYPE']);
		if(!Helper::isEmpty($arFields['BED_LOCATION_TYPE']))
			$arXmlTags['BedLocationType'] = Xml::addTag($arFields['BED_LOCATION_TYPE']);
		if(!Helper::isEmpty($arFields['LEASE_COMFORT_MULTI']))
			$arXmlTags['LeaseComfortMulti'] = Xml::addTagWithSubtags($arFields['LEASE_COMFORT_MULTI'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_PRICE_OPTIONS']))
			$arXmlTags['LeasePriceOptions'] = Xml::addTagWithSubtags($arFields['LEASE_PRICE_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['SQUARE_ADDITIONALLY']))
			$arXmlTags['SquareAdditionally'] = Xml::addTagWithSubtags($arFields['SQUARE_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['RENTAL_TYPE']))
			$arXmlTags['RentalType'] = Xml::addTag($arFields['RENTAL_TYPE']);
		if(!Helper::isEmpty($arFields['RENTAL_HOLIDAYS']))
			$arXmlTags['RentalHolidays'] = Xml::addTag($arFields['RENTAL_HOLIDAYS']);
		if(!Helper::isEmpty($arFields['RENTAL_MINIMUM_PERIOD']))
			$arXmlTags['RentalMinimumPeriod'] = Xml::addTag($arFields['RENTAL_MINIMUM_PERIOD']);
		if(!Helper::isEmpty($arFields['LEASE_DEPOSIT_PRICE']))
			$arXmlTags['LeaseDepositPrice'] = Xml::addTag($arFields['LEASE_DEPOSIT_PRICE']);
		if(!Helper::isEmpty($arFields['CHILDREN_ALLOWED']))
			$arXmlTags['ChildrenAllowed'] = Xml::addTag($arFields['CHILDREN_ALLOWED']);
		if(!Helper::isEmpty($arFields['PETS_ALLOWED']))
			$arXmlTags['PetsAllowed'] = Xml::addTag($arFields['PETS_ALLOWED']);
		if(!Helper::isEmpty($arFields['SMOKING_ALLOWED']))
			$arXmlTags['SmokingAllowed'] = Xml::addTag($arFields['SMOKING_ALLOWED']);
		if(!Helper::isEmpty($arFields['PARTIES_ALLOWED']))
			$arXmlTags['PartiesAllowed'] = Xml::addTag($arFields['PARTIES_ALLOWED']);
		if(!Helper::isEmpty($arFields['DOCUMENTS']))
			$arXmlTags['Documents'] = Xml::addTag($arFields['DOCUMENTS']);
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