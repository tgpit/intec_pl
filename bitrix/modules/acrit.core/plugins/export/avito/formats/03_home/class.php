<?

/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/dlya_doma_i_dachi
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Xml,
    \Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoHome extends Avito {

   CONST DATE_UPDATED = '2021-06-29';

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
      return parent::getCode() . '_HOME';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME').static::outdatedGetNameSuffix();
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'avito_home.xml';
   }

   /**
    * 	Get adailable fields for current plugin
    */
   public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
      $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
      #
      $arResult[] = new Field(array(
          'CODE' => 'CONDITION',
          'DISPLAY_CODE' => 'Condition',
          'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
          'SORT' => 360,
          'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
          'REQUIRED' => true,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'GOODS_TYPE',
          'DISPLAY_CODE' => 'GoodsType',
          'NAME' => static::getMessage('FIELD_GOODS_TYPE_NAME'),
          'SORT' => 1010,
          'DESCRIPTION' => static::getMessage('FIELD_GOODS_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'GOODS_SUB_TYPE',
          'DISPLAY_CODE' => 'GoodsSubType',
          'NAME' => static::getMessage('FIELD_GOODS_SUB_TYPE_NAME'),
          'SORT' => 1011,
          'DESCRIPTION' => static::getMessage('FIELD_GOODS_SUB_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AD_TYPE',
          'DISPLAY_CODE' => 'AdType',
          'NAME' => static::getMessage('FIELD_AD_TYPE_NAME'),
          'SORT' => 1020,
          'DESCRIPTION' => static::getMessage('FIELD_AD_TYPE_DESC'),
          'REQUIRED' => true,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AVAILABILITY',
          'DISPLAY_CODE' => 'Availability',
          'NAME' => static::getMessage('FIELD_AVAILABILITY_NAME'),
          'SORT' => 1030,
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
				'CODE' => 'PRICE_TYPE',
				'DISPLAY_CODE' => 'PriceType',
				'NAME' => static::getMessage('FIELD_PRICE_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PRICE_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BAG_VALUE',
				'DISPLAY_CODE' => 'BagValue',
				'NAME' => static::getMessage('FIELD_BAG_VALUE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BAG_VALUE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BAG_UNITS',
				'DISPLAY_CODE' => 'BagUnits',
				'NAME' => static::getMessage('FIELD_BAG_UNITS_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BAG_UNITS_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'LUMBER_TYPE',
				'DISPLAY_CODE' => 'LumberType',
				'NAME' => static::getMessage('FIELD_LUMBER_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_LUMBER_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'TYPE_OF_WOOD',
				'DISPLAY_CODE' => 'TypeOfWood',
				'NAME' => static::getMessage('FIELD_TYPE_OF_WOOD_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_TYPE_OF_WOOD_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'EDGE_TYPE',
				'DISPLAY_CODE' => 'EdgeType',
				'NAME' => static::getMessage('FIELD_EDGE_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_EDGE_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'SORT_OF_WOOD',
				'DISPLAY_CODE' => 'SortOfWood',
				'NAME' => static::getMessage('FIELD_SORT_OF_WOOD_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_SORT_OF_WOOD_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'MOISTURE_CONTENT',
				'DISPLAY_CODE' => 'MoistureContent',
				'NAME' => static::getMessage('FIELD_MOISTURE_CONTENT_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_MOISTURE_CONTENT_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PROCESSING',
				'DISPLAY_CODE' => 'Processing',
				'NAME' => static::getMessage('FIELD_PROCESSING_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PROCESSING_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PURPOSE',
				'DISPLAY_CODE' => 'Purpose',
				'NAME' => static::getMessage('FIELD_PURPOSE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PURPOSE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'IS_PROFILED',
				'DISPLAY_CODE' => 'IsProfiled',
				'NAME' => static::getMessage('FIELD_IS_PROFILED_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_IS_PROFILED_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'TYPE_OF_STRUCTURE',
				'DISPLAY_CODE' => 'TypeOfStructure',
				'NAME' => static::getMessage('FIELD_TYPE_OF_STRUCTURE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_TYPE_OF_STRUCTURE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'LADDER_TYPE',
				'DISPLAY_CODE' => 'LadderType',
				'NAME' => static::getMessage('FIELD_LADDER_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_LADDER_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PILE_TYPE',
				'DISPLAY_CODE' => 'PileType',
				'NAME' => static::getMessage('FIELD_PILE_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PILE_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PILE_MATERIAL',
				'DISPLAY_CODE' => 'PileMaterial',
				'NAME' => static::getMessage('FIELD_PILE_MATERIAL_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PILE_MATERIAL_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'FENCE_TYPE',
				'DISPLAY_CODE' => 'FenceType',
				'NAME' => static::getMessage('FIELD_FENCE_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_FENCE_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'RCPRODUCT',
				'DISPLAY_CODE' => 'RCProduct',
				'NAME' => static::getMessage('FIELD_RCPRODUCT_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_RCPRODUCT_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'RCSLAB_TYPE',
				'DISPLAY_CODE' => 'RCSlabType',
				'NAME' => static::getMessage('FIELD_RCSLAB_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_RCSLAB_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'RCWALL_TYPE',
				'DISPLAY_CODE' => 'RCWallType',
				'NAME' => static::getMessage('FIELD_RCWALL_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_RCWALL_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'RCELEMENT_TYPE',
				'DISPLAY_CODE' => 'RCElementType',
				'NAME' => static::getMessage('FIELD_RCELEMENT_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_RCELEMENT_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PRODUCT_TYPE',
				'DISPLAY_CODE' => 'ProductType',
				'NAME' => static::getMessage('FIELD_PRODUCT_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_PRODUCT_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BULK_MATERIAL_TYPE',
				'DISPLAY_CODE' => 'BulkMaterialType',
				'NAME' => static::getMessage('FIELD_BULK_MATERIAL_TYPE_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BULK_MATERIAL_TYPE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'HEIGHT',
				'DISPLAY_CODE' => 'Height',
				'NAME' => static::getMessage('FIELD_HEIGHT_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_HEIGHT_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'WIDTH',
				'DISPLAY_CODE' => 'Width',
				'NAME' => static::getMessage('FIELD_WIDTH_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_WIDTH_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'LENGTH',
				'DISPLAY_CODE' => 'Length',
				'NAME' => static::getMessage('FIELD_LENGTH_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_LENGTH_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'DIAMETER',
				'DISPLAY_CODE' => 'Diameter',
				'NAME' => static::getMessage('FIELD_DIAMETER_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_DIAMETER_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'BRAND',
				'DISPLAY_CODE' => 'Brand',
				'NAME' => static::getMessage('FIELD_BRAND_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_BRAND_DESC'),
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
      if (!Helper::isEmpty($arFields['AUCTION_PRICE']))
         $arXmlTags['AuctionPrice'] = Xml::addTag($arFields['AUCTION_PRICE']);
      if (!Helper::isEmpty($arFields['AUCTION_PRICE_LAST_DATE']))
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
      #
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
      if (!Helper::isEmpty($arFields['GOODS_TYPE']))
         $arXmlTags['GoodsType'] = Xml::addTag($arFields['GOODS_TYPE']);
      if (!Helper::isEmpty($arFields['GOODS_SUB_TYPE']))
         $arXmlTags['GoodsSubType'] = Xml::addTag($arFields['GOODS_SUB_TYPE']);
      if (!Helper::isEmpty($arFields['AD_TYPE']))
         $arXmlTags['AdType'] = Xml::addTag($arFields['AD_TYPE']);
      if (!Helper::isEmpty($arFields['AVAILABILITY']))
         $arXmlTags['Availability'] = Xml::addTag($arFields['AVAILABILITY']);
			#
			if(!Helper::isEmpty($arFields['PRICE_TYPE']))
				$arXmlTags['PriceType'] = Xml::addTag($arFields['PRICE_TYPE']);
			if(!Helper::isEmpty($arFields['BAG_VALUE']))
				$arXmlTags['BagValue'] = Xml::addTag($arFields['BAG_VALUE']);
			if(!Helper::isEmpty($arFields['BAG_UNITS']))
				$arXmlTags['BagUnits'] = Xml::addTag($arFields['BAG_UNITS']);
			if(!Helper::isEmpty($arFields['LUMBER_TYPE']))
				$arXmlTags['LumberType'] = Xml::addTag($arFields['LUMBER_TYPE']);
			if(!Helper::isEmpty($arFields['TYPE_OF_WOOD']))
				$arXmlTags['TypeOfWood'] = Xml::addTag($arFields['TYPE_OF_WOOD']);
			if(!Helper::isEmpty($arFields['EDGE_TYPE']))
				$arXmlTags['EdgeType'] = Xml::addTag($arFields['EDGE_TYPE']);
			if(!Helper::isEmpty($arFields['SORT_OF_WOOD']))
				$arXmlTags['SortOfWood'] = Xml::addTag($arFields['SORT_OF_WOOD']);
			if(!Helper::isEmpty($arFields['MOISTURE_CONTENT']))
				$arXmlTags['MoistureContent'] = Xml::addTag($arFields['MOISTURE_CONTENT']);
			if(!Helper::isEmpty($arFields['PROCESSING']))
				$arXmlTags['Processing'] = Xml::addTag($arFields['PROCESSING']);
			if(!Helper::isEmpty($arFields['PURPOSE']))
				$arXmlTags['Purpose'] = Xml::addTag($arFields['PURPOSE']);
			if(!Helper::isEmpty($arFields['IS_PROFILED']))
				$arXmlTags['IsProfiled'] = Xml::addTag($arFields['IS_PROFILED']);
			if(!Helper::isEmpty($arFields['TYPE_OF_STRUCTURE']))
				$arXmlTags['TypeOfStructure'] = Xml::addTag($arFields['TYPE_OF_STRUCTURE']);
			if(!Helper::isEmpty($arFields['LADDER_TYPE']))
				$arXmlTags['LadderType'] = Xml::addTag($arFields['LADDER_TYPE']);
			if(!Helper::isEmpty($arFields['PILE_TYPE']))
				$arXmlTags['PileType'] = Xml::addTag($arFields['PILE_TYPE']);
			if(!Helper::isEmpty($arFields['PILE_MATERIAL']))
				$arXmlTags['PileMaterial'] = Xml::addTag($arFields['PILE_MATERIAL']);
			if(!Helper::isEmpty($arFields['FENCE_TYPE']))
				$arXmlTags['FenceType'] = Xml::addTag($arFields['FENCE_TYPE']);
			if(!Helper::isEmpty($arFields['RCPRODUCT']))
				$arXmlTags['RCProduct'] = Xml::addTag($arFields['RCPRODUCT']);
			if(!Helper::isEmpty($arFields['RCSLAB_TYPE']))
				$arXmlTags['RCSlabType'] = Xml::addTag($arFields['RCSLAB_TYPE']);
			if(!Helper::isEmpty($arFields['RCWALL_TYPE']))
				$arXmlTags['RCWallType'] = Xml::addTag($arFields['RCWALL_TYPE']);
			if(!Helper::isEmpty($arFields['RCELEMENT_TYPE']))
				$arXmlTags['RCElementType'] = Xml::addTag($arFields['RCELEMENT_TYPE']);
			if(!Helper::isEmpty($arFields['PRODUCT_TYPE']))
				$arXmlTags['ProductType'] = Xml::addTag($arFields['PRODUCT_TYPE']);
			if(!Helper::isEmpty($arFields['BULK_MATERIAL_TYPE']))
				$arXmlTags['BulkMaterialType'] = Xml::addTag($arFields['BULK_MATERIAL_TYPE']);
			if(!Helper::isEmpty($arFields['HEIGHT']))
				$arXmlTags['Height'] = Xml::addTag($arFields['HEIGHT']);
			if(!Helper::isEmpty($arFields['WIDTH']))
				$arXmlTags['Width'] = Xml::addTag($arFields['WIDTH']);
			if(!Helper::isEmpty($arFields['LENGTH']))
				$arXmlTags['Length'] = Xml::addTag($arFields['LENGTH']);
			if(!Helper::isEmpty($arFields['DIAMETER']))
				$arXmlTags['Diameter'] = Xml::addTag($arFields['DIAMETER']);
			if(!Helper::isEmpty($arFields['BRAND']))
				$arXmlTags['Brand'] = Xml::addTag($arFields['BRAND']);
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