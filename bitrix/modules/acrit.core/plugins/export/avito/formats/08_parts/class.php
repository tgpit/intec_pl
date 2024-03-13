<?

/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/zapchasti_i_aksessuary
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Xml,
    \Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoParts extends Avito {

   CONST DATE_UPDATED = '2021-08-05';

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
      return parent::getCode() . '_PARTS';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME').static::outdatedGetNameSuffix();
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'avito_parts.xml';
   }

   /**
    * 	Get adailable fields for current plugin
    */
   public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
      $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
      #
			$arResult[] = new Field(array(
				'CODE' => 'DISPLAY_AREAS',
				'DISPLAY_CODE' => 'DisplayAreas',
				'NAME' => static::getMessage('FIELD_DISPLAY_AREAS_NAME'),
				'SORT' => 350,
				'DESCRIPTION' => static::getMessage('FIELD_DISPLAY_AREAS_DESC'),
				'MULTIPLE' => true,
				'PARAMS' => array(
					'MULTIPLE' => 'multiple',
				),
			));
      $arResult[] = new Field(array(
          'CODE' => 'CONDITION',
          'DISPLAY_CODE' => 'Condition',
          'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
          'SORT' => 360,
          'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
          'REQUIRED' => true,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'OEM',
          'DISPLAY_CODE' => 'OEM',
          'NAME' => static::getMessage('FIELD_OEM_NAME'),
          'SORT' => 362,
          'DESCRIPTION' => static::getMessage('FIELD_OEM_DESC'),
          'REQUIRED' => false,
      ));
      $this->modifyField($arResult, 'CATEGORY', array(
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TYPE_ID',
          'DISPLAY_CODE' => 'TypeId',
          'NAME' => static::getMessage('FIELD_TYPE_ID_NAME'),
          'SORT' => 1000,
          'DESCRIPTION' => static::getMessage('FIELD_TYPE_ID_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AD_TYPE',
          'DISPLAY_CODE' => 'AdType',
          'NAME' => static::getMessage('FIELD_AD_TYPE_NAME'),
          'SORT' => 1010,
          'DESCRIPTION' => static::getMessage('FIELD_AD_TYPE_DESC'),
      ));
      if ($bAdmin) {
         $arResult[] = new Field(array(
             'SORT' => 1020,
             'NAME' => static::getMessage('HEADER_TIRES'),
             'IS_HEADER' => true,
         ));
      }
      $arResult[] = new Field(array(
          'CODE' => 'RIM_DIAMETER',
          'DISPLAY_CODE' => 'RimDiameter',
          'NAME' => static::getMessage('FIELD_RIM_DIAMETER_NAME'),
          'SORT' => 1030,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_DIAMETER_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TIRE_TYPE',
          'DISPLAY_CODE' => 'TireType',
          'NAME' => static::getMessage('FIELD_TIRE_TYPE_NAME'),
          'SORT' => 1040,
          'DESCRIPTION' => static::getMessage('FIELD_TIRE_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'WHEEL_AXLE',
          'DISPLAY_CODE' => 'WheelAxle',
          'NAME' => static::getMessage('FIELD_WHEEL_AXLE_NAME'),
          'SORT' => 1050,
          'DESCRIPTION' => static::getMessage('FIELD_WHEEL_AXLE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_TYPE',
          'DISPLAY_CODE' => 'RimType',
          'NAME' => static::getMessage('FIELD_RIM_TYPE_NAME'),
          'SORT' => 1060,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TIRE_SECTION_WIDTH',
          'DISPLAY_CODE' => 'TireSectionWidth',
          'NAME' => static::getMessage('FIELD_TIRE_SECTION_WIDTH_NAME'),
          'SORT' => 1070,
          'DESCRIPTION' => static::getMessage('FIELD_TIRE_SECTION_WIDTH_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TIRE_ASPECT_RATIO',
          'DISPLAY_CODE' => 'TireAspectRatio',
          'NAME' => static::getMessage('FIELD_TIRE_ASPECT_RATIO_NAME'),
          'SORT' => 1080,
          'DESCRIPTION' => static::getMessage('FIELD_TIRE_ASPECT_RATIO_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_WIDTH',
          'DISPLAY_CODE' => 'RimWidth',
          'NAME' => static::getMessage('FIELD_RIM_WIDTH_NAME'),
          'SORT' => 1090,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_WIDTH_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_BOLTS',
          'DISPLAY_CODE' => 'RimBolts',
          'NAME' => static::getMessage('FIELD_RIM_BOLTS_NAME'),
          'SORT' => 1100,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_BOLTS_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_BOLTS_DIAMETER',
          'DISPLAY_CODE' => 'RimBoltsDiameter',
          'NAME' => static::getMessage('FIELD_RIM_BOLTS_DIAMETER_NAME'),
          'SORT' => 1110,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_BOLTS_DIAMETER_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_OFFSET',
          'DISPLAY_CODE' => 'RimOffset',
          'NAME' => static::getMessage('FIELD_RIM_OFFSET_NAME'),
          'SORT' => 1120,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_OFFSET_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RUNFLAT',
          'DISPLAY_CODE' => 'RunFlat',
          'NAME' => static::getMessage('FIELD_RUNFLAT_NAME'),
          'SORT' => 1121,
          'DESCRIPTION' => static::getMessage('FIELD_RUNFLAT_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'HOMOLOGATION',
          'DISPLAY_CODE' => 'Homologation',
          'NAME' => static::getMessage('FIELD_HOMOLOGATION_NAME'),
          'SORT' => 1122,
          'DESCRIPTION' => static::getMessage('FIELD_HOMOLOGATION_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'MODEL',
          'DISPLAY_CODE' => 'Model',
          'NAME' => static::getMessage('FIELD_MODEL_NAME'),
          'SORT' => 1123,
          'DESCRIPTION' => static::getMessage('FIELD_MODEL_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'SPEED_INDEX',
          'DISPLAY_CODE' => 'SpeedIndex',
          'NAME' => static::getMessage('FIELD_SPEED_INDEX_NAME'),
          'SORT' => 1123,
          'DESCRIPTION' => static::getMessage('FIELD_SPEED_INDEX_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'LOAD_INDEX',
          'DISPLAY_CODE' => 'LoadIndex',
          'NAME' => static::getMessage('FIELD_LOAD_INDEX_NAME'),
          'SORT' => 1124,
          'DESCRIPTION' => static::getMessage('FIELD_LOAD_INDEX_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'BRAND',
          'DISPLAY_CODE' => 'Brand',
          'NAME' => static::getMessage('FIELD_BRAND_NAME'),
          'SORT' => 1130,
          'DESCRIPTION' => static::getMessage('FIELD_BRAND_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'ORIGINALITY',
          'DISPLAY_CODE' => 'Originality',
          'NAME' => static::getMessage('FIELD_ORIGINALITY_NAME'),
          'SORT' => 1131,
          'DESCRIPTION' => static::getMessage('FIELD_ORIGINALITY_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'ORIGINAL_OEM',
          'DISPLAY_CODE' => 'OriginalOEM',
          'NAME' => static::getMessage('FIELD_ORIGINAL_OEM_NAME'),
          'SORT' => 1132,
          'DESCRIPTION' => static::getMessage('FIELD_ORIGINAL_OEM_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'ORIGINAL_VENDOR',
          'DISPLAY_CODE' => 'OriginalVendor',
          'NAME' => static::getMessage('FIELD_ORIGINAL_VENDOR_NAME'),
          'SORT' => 1133,
          'DESCRIPTION' => static::getMessage('FIELD_ORIGINAL_VENDOR_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RESIDUAL_TREAD',
          'DISPLAY_CODE' => 'ResidualTread',
          'NAME' => static::getMessage('FIELD_RESIDUAL_TREAD_NAME'),
          'SORT' => 1134,
          'DESCRIPTION' => static::getMessage('FIELD_RESIDUAL_TREAD_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AVAILABILITY',
          'DISPLAY_CODE' => 'Availability',
          'NAME' => static::getMessage('FIELD_AVAILABILITY_NAME'),
          'SORT' => 1140,
          'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DESC'),
					'DEFAULT_VALUE' => [
						array(
							'TYPE' => 'CONST',
							'CONST' => static::getMessage('FIELD_AVAILABILITY_IN'),
						),
					],
      ));
			#
			$arResult[] = new Field(array(
				'CODE' => 'GOODS_TYPE',
				'DISPLAY_CODE' => 'GoodsType',
				'NAME' => static::getMessage('FIELD_GOODS_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_GOODS_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PRODUCT_TYPE',
				'DISPLAY_CODE' => 'ProductType',
				'NAME' => static::getMessage('FIELD_PRODUCT_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PRODUCT_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'DEVICE_TYPE',
				'DISPLAY_CODE' => 'DeviceType',
				'NAME' => static::getMessage('FIELD_DEVICE_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_DEVICE_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'SPARE_PART_TYPE',
				'DISPLAY_CODE' => 'SparePartType',
				'NAME' => static::getMessage('FIELD_SPARE_PART_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_SPARE_PART_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'TECHNIC_SPARE_PART_TYPE',
				'DISPLAY_CODE' => 'TechnicSparePartType',
				'NAME' => static::getMessage('FIELD_TECHNIC_SPARE_PART_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_TECHNIC_SPARE_PART_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'ENGINE_SPARE_PART_TYPE',
				'DISPLAY_CODE' => 'EngineSparePartType',
				'NAME' => static::getMessage('FIELD_ENGINE_SPARE_PART_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_ENGINE_SPARE_PART_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BODY_SPARE_PART_TYPE',
				'DISPLAY_CODE' => 'BodySparePartType',
				'NAME' => static::getMessage('FIELD_BODY_SPARE_PART_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BODY_SPARE_PART_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'TECHNIC',
				'DISPLAY_CODE' => 'Technic',
				'NAME' => static::getMessage('FIELD_TECHNIC_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_TECHNIC_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'MAKE',
				'DISPLAY_CODE' => 'Make',
				'NAME' => static::getMessage('FIELD_MAKE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_MAKE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'GENERATION',
				'DISPLAY_CODE' => 'Generation',
				'NAME' => static::getMessage('FIELD_GENERATION_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_GENERATION_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'MODIFICATION',
				'DISPLAY_CODE' => 'Modification',
				'NAME' => static::getMessage('FIELD_MODIFICATION_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_MODIFICATION_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BODY_TYPE',
				'DISPLAY_CODE' => 'BodyType',
				'NAME' => static::getMessage('FIELD_BODY_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BODY_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'DOORS',
				'DISPLAY_CODE' => 'Doors',
				'NAME' => static::getMessage('FIELD_DOORS_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_DOORS_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'DIFFERENT_WIDTH_TIRES',
				'DISPLAY_CODE' => 'DifferentWidthTires',
				'NAME' => static::getMessage('FIELD_DIFFERENT_WIDTH_TIRES_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_DIFFERENT_WIDTH_TIRES_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BACK_RIM_DIAMETER',
				'DISPLAY_CODE' => 'BackRimDiameter',
				'NAME' => static::getMessage('FIELD_BACK_RIM_DIAMETER_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BACK_RIM_DIAMETER_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BACK_TIRE_SECTION_WIDTH',
				'DISPLAY_CODE' => 'BackTireSectionWidth',
				'NAME' => static::getMessage('FIELD_BACK_TIRE_SECTION_WIDTH_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BACK_TIRE_SECTION_WIDTH_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BACK_TIRE_ASPECT_RATIO',
				'DISPLAY_CODE' => 'BackTireAspectRatio',
				'NAME' => static::getMessage('FIELD_BACK_TIRE_ASPECT_RATIO_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BACK_TIRE_ASPECT_RATIO_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'RIM_DIA',
				'DISPLAY_CODE' => 'RimDIA',
				'NAME' => static::getMessage('FIELD_RIM_DIA_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_RIM_DIA_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'QUANTITY',
				'DISPLAY_CODE' => 'Quantity',
				'NAME' => static::getMessage('FIELD_QUANTITY_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_QUANTITY_DESC'),
			));
      #
      $this->sortFields($arResult);
      return $arResult;
   }

   /**
    * 	Process single element (generate XML)
    * 	@return array
    */
   public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
      $intProfileID = $arProfile['ID'];
      $intElementID = $arElement['ID'];
      # Build XML
      $arXmlTags = array(
          'Id' => array('#' => $arFields['ID']),
      );
      if (!Helper::isEmpty($arFields['DATE_BEGIN']))
         $arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
      if (!Helper::isEmpty($arFields['DATE_END']))
         $arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
      if (!Helper::isEmpty($arFields['LISTING_FEE']))
         $arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
      if (!Helper::isEmpty($arFields['AD_STATUS']))
         $arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
      if (!Helper::isEmpty($arFields['AVITO_ID']))
         $arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
			if(!Helper::isEmpty($arFields['AUCTION_PRICE']))
					$arXmlTags['AuctionPrice'] = Xml::addTag($arFields['AUCTION_PRICE']);
			if(!Helper::isEmpty($arFields['AUCTION_PRICE_LAST_DATE']))
					$arXmlTags['AuctionPriceLastDate'] = Xml::addTag($arFields['AUCTION_PRICE_LAST_DATE']);
      #
      if (!Helper::isEmpty($arFields['ALLOW_EMAIL']))
         $arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
			if(!Helper::isEmpty($arFields['EMAIL']))
				$arXmlTags['Email'] = Xml::addTag($arFields['EMAIL']);
      if (!Helper::isEmpty($arFields['MANAGER_NAME']))
         $arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
      if (!Helper::isEmpty($arFields['CONTACT_PHONE']))
         $arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
			if(!Helper::isEmpty($arFields['CONTACT_METHOD']))
				$arXmlTags['ContactMethod'] = Xml::addTag($arFields['CONTACT_METHOD']);
      #
      if (!Helper::isEmpty($arFields['DESCRIPTION']))
         $arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
      if (!Helper::isEmpty($arFields['IMAGES']))
         $arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
      if (!Helper::isEmpty($arFields['VIDEO_URL']))
         $arXmlTags['VideoURL'] = Xml::addTag($arFields['VIDEO_URL']);
      if (!Helper::isEmpty($arFields['TITLE']))
         $arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
      if (!Helper::isEmpty($arFields['PRICE']))
         $arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
      if (!Helper::isEmpty($arFields['CONDITION']))
         $arXmlTags['Condition'] = Xml::addTag($arFields['CONDITION']);
      if (!Helper::isEmpty($arFields['OEM']))
         $arXmlTags['OEM'] = Xml::addTag($arFields['OEM']);
      #
			if(!Helper::isEmpty($arFields['LATITUDE']) || !Helper::isEmpty($arFields['LONGITUDE'])) {
				$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
				$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
			}
			if(!Helper::isEmpty($arFields['DISPLAY_AREAS']))
				$arXmlTags['DisplayAreas'] = Xml::addTagWithSubtags($arFields['DISPLAY_AREAS'], 'Area');
      if (!Helper::isEmpty($arFields['ADDRESS']))
         $arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
      if (!Helper::isEmpty($arFields['REGION']))
         $arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
      if (!Helper::isEmpty($arFields['CITY']))
         $arXmlTags['City'] = Xml::addTag($arFields['CITY']);
      if (!Helper::isEmpty($arFields['SUBWAY']))
         $arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
      if (!Helper::isEmpty($arFields['DISTRICT']))
         $arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
      #
      if (!Helper::isEmpty($arFields['CATEGORY']))
         $arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
      if (!Helper::isEmpty($arFields['TYPE_ID']))
         $arXmlTags['TypeId'] = Xml::addTag($arFields['TYPE_ID']);
      if (!Helper::isEmpty($arFields['AD_TYPE']))
         $arXmlTags['AdType'] = Xml::addTag($arFields['AD_TYPE']);
      #
      if (!Helper::isEmpty($arFields['RIM_DIAMETER']))
         $arXmlTags['RimDiameter'] = Xml::addTag($arFields['RIM_DIAMETER']);
      if (!Helper::isEmpty($arFields['TIRE_TYPE']))
         $arXmlTags['TireType'] = Xml::addTag($arFields['TIRE_TYPE']);
      if (!Helper::isEmpty($arFields['WHEEL_AXLE']))
         $arXmlTags['WheelAxle'] = Xml::addTag($arFields['WHEEL_AXLE']);
      if (!Helper::isEmpty($arFields['RIM_TYPE']))
         $arXmlTags['RimType'] = Xml::addTag($arFields['RIM_TYPE']);
      if (!Helper::isEmpty($arFields['TIRE_SECTION_WIDTH']))
         $arXmlTags['TireSectionWidth'] = Xml::addTag($arFields['TIRE_SECTION_WIDTH']);
      if (!Helper::isEmpty($arFields['TIRE_ASPECT_RATIO']))
         $arXmlTags['TireAspectRatio'] = Xml::addTag($arFields['TIRE_ASPECT_RATIO']);
      if (!Helper::isEmpty($arFields['RIM_WIDTH']))
         $arXmlTags['RimWidth'] = Xml::addTag($arFields['RIM_WIDTH']);
      if (!Helper::isEmpty($arFields['RIM_BOLTS']))
         $arXmlTags['RimBolts'] = Xml::addTag($arFields['RIM_BOLTS']);
      if (!Helper::isEmpty($arFields['RIM_BOLTS_DIAMETER']))
         $arXmlTags['RimBoltsDiameter'] = Xml::addTag($arFields['RIM_BOLTS_DIAMETER']);
      if (!Helper::isEmpty($arFields['RIM_OFFSET']))
         $arXmlTags['RimOffset'] = Xml::addTag($arFields['RIM_OFFSET']);
      if (!Helper::isEmpty($arFields['RUNFLAT']))
         $arXmlTags['RunFlat'] = Xml::addTag($arFields['RUNFLAT']);
      if (!Helper::isEmpty($arFields['HOMOLOGATION']))
         $arXmlTags['Homologation'] = Xml::addTag($arFields['HOMOLOGATION']);
      if (!Helper::isEmpty($arFields['MODEL']))
         $arXmlTags['Model'] = Xml::addTag($arFields['MODEL']);
      if (!Helper::isEmpty($arFields['SPEED_INDEX']))
         $arXmlTags['SpeedIndex'] = Xml::addTag($arFields['SPEED_INDEX']);
      if (!Helper::isEmpty($arFields['LOAD_INDEX']))
         $arXmlTags['LoadIndex'] = Xml::addTag($arFields['LOAD_INDEX']);
      if (!Helper::isEmpty($arFields['RESIDUAL_TREAD']))
         $arXmlTags['ResidualTread'] = Xml::addTag($arFields['RESIDUAL_TREAD']);
      if (!Helper::isEmpty($arFields['BRAND']))
         $arXmlTags['Brand'] = Xml::addTag($arFields['BRAND']);
      if (!Helper::isEmpty($arFields['ORIGINALITY']))
         $arXmlTags['Originality'] = Xml::addTag($arFields['ORIGINALITY']);
      if (!Helper::isEmpty($arFields['ORIGINAL_OEM']))
         $arXmlTags['OriginalOEM'] = Xml::addTag($arFields['ORIGINAL_OEM']);
      if (!Helper::isEmpty($arFields['ORIGINAL_VENDOR']))
         $arXmlTags['OriginalVendor'] = Xml::addTag($arFields['ORIGINAL_VENDOR']);
      if (!Helper::isEmpty($arFields['AVAILABILITY']))
         $arXmlTags['Availability'] = Xml::addTag($arFields['AVAILABILITY']);
			#
			if(!Helper::isEmpty($arFields['GOODS_TYPE']))
				$arXmlTags['GoodsType'] = Xml::addTag($arFields['GOODS_TYPE']);
			if(!Helper::isEmpty($arFields['PRODUCT_TYPE']))
				$arXmlTags['ProductType'] = Xml::addTag($arFields['PRODUCT_TYPE']);
			if(!Helper::isEmpty($arFields['DEVICE_TYPE']))
				$arXmlTags['DeviceType'] = Xml::addTag($arFields['DEVICE_TYPE']);
			if(!Helper::isEmpty($arFields['SPARE_PART_TYPE']))
				$arXmlTags['SparePartType'] = Xml::addTag($arFields['SPARE_PART_TYPE']);
			if(!Helper::isEmpty($arFields['TECHNIC_SPARE_PART_TYPE']))
				$arXmlTags['TechnicSparePartType'] = Xml::addTag($arFields['TECHNIC_SPARE_PART_TYPE']);
			if(!Helper::isEmpty($arFields['ENGINE_SPARE_PART_TYPE']))
				$arXmlTags['EngineSparePartType'] = Xml::addTag($arFields['ENGINE_SPARE_PART_TYPE']);
			if(!Helper::isEmpty($arFields['BODY_SPARE_PART_TYPE']))
				$arXmlTags['BodySparePartType'] = Xml::addTag($arFields['BODY_SPARE_PART_TYPE']);
			if(!Helper::isEmpty($arFields['TECHNIC']))
				$arXmlTags['Technic'] = Xml::addTag($arFields['TECHNIC']);
			if(!Helper::isEmpty($arFields['MAKE']))
				$arXmlTags['Make'] = Xml::addTag($arFields['MAKE']);
			if(!Helper::isEmpty($arFields['GENERATION']))
				$arXmlTags['Generation'] = Xml::addTag($arFields['GENERATION']);
			if(!Helper::isEmpty($arFields['MODIFICATION']))
				$arXmlTags['Modification'] = Xml::addTag($arFields['MODIFICATION']);
			if(!Helper::isEmpty($arFields['BODY_TYPE']))
				$arXmlTags['BodyType'] = Xml::addTag($arFields['BODY_TYPE']);
			if(!Helper::isEmpty($arFields['DOORS']))
				$arXmlTags['Doors'] = Xml::addTag($arFields['DOORS']);
			if(!Helper::isEmpty($arFields['DIFFERENT_WIDTH_TIRES']))
				$arXmlTags['DifferentWidthTires'] = Xml::addTag($arFields['DIFFERENT_WIDTH_TIRES']);
			if(!Helper::isEmpty($arFields['BACK_RIM_DIAMETER']))
				$arXmlTags['BackRimDiameter'] = Xml::addTag($arFields['BACK_RIM_DIAMETER']);
			if(!Helper::isEmpty($arFields['BACK_TIRE_SECTION_WIDTH']))
				$arXmlTags['BackTireSectionWidth'] = Xml::addTag($arFields['BACK_TIRE_SECTION_WIDTH']);
			if(!Helper::isEmpty($arFields['BACK_TIRE_ASPECT_RATIO']))
				$arXmlTags['BackTireAspectRatio'] = Xml::addTag($arFields['BACK_TIRE_ASPECT_RATIO']);
			if(!Helper::isEmpty($arFields['RIM_DIA']))
				$arXmlTags['RimDIA'] = Xml::addTag($arFields['RIM_DIA']);
			if(!Helper::isEmpty($arFields['QUANTITY']))
				$arXmlTags['Quantity'] = Xml::addTag($arFields['QUANTITY']);
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