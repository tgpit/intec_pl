<?
/**
 * Acrit core
 * @package acrit.core
 * @copyright 2018 Acrit
 */

namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Application,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

/**
 * Base interface for plugin
 */
abstract class Plugin {

	CONST TYPE_NATIVE = 'NATIVE';
	CONST TYPE_CUSTOM = 'CUSTOM';

	CONST OWN_CATEGORIES = false;

	CONST ARCHIVE_ZIP = 'ZIP';
	CONST ARCHIVE_TAG_GZ = 'TAR.GZ';

	const PROPS_AVAILABLE = ['STRING', 'LOCATION', 'ENUM', 'Y/N', 'DATE', 'FILE', 'NUMBER'];
	const SYNC_NONE = 0;
	const SYNC_STOC = 1;
	const SYNC_CTOS = 2;
	const SYNC_ALL = 3;
	const ADD_SYNC_TYPE_SINGLE = 1;
	const ADD_SYNC_TYPE_DUAL = 2;
	const ADD_SYNC_DEFAULT_PERIOD_1 = 10;
	const ADD_SYNC_DEFAULT_RANGE_1 = 60;
	const ADD_SYNC_DEFAULT_PERIOD_2 = 24;
	const ADD_SYNC_DEFAULT_RANGE_2 = 336;

	#
	protected static $strStaticModuleId = null;
	protected static $bSubclass = false;

	# ���� ������������ ���������� ������� � ������-��������
	CONST DATE_UPDATED = NULL;

	protected $strModuleId = null;
	protected $arEventHandlers = array();
	protected $intProfileId = null;
	protected $arProfile = array();
	protected $arParams = array(); // Ref to $this->arProfile['PARAMS']
	protected $arData = array();
	protected $arSession = array();
	protected $arDirections = [];

	protected $arFieldsCached = array();

	// Flags
	protected $isCountable = true;


	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		$this->includeClasses();
		$this->strModuleId = $strModuleId;
	}

	/**
	 * Get module id
	 */
	public function getModuleId() {
		return $this->strModuleId;
	}

	/**
	 *	Set static::$strModuleId (for static purposes)
	 */
	public static function setStaticModuleId($strModuleId){
		static::$strStaticModuleId = $strModuleId;
	}

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode(){
		return 'PLUGIN';
	}

	/**
	 * Get plugin short name
	 */
	abstract public static function getName();

	/**
	 * Get type of regular synchronization
	 */
	public static function getAddSyncType() {
		return self::ADD_SYNC_TYPE_SINGLE;
	}

	/**
	 * Get lang code for getMessage
	 */
	public static function getLangCode($code) {
		return 'ACRIT_ORDERS_PLUGIN_' . static::getCode() . '_' . $code;
	}

	/**
	 * Get plugin help link
	 */
	public static function getHelpLink(){
		return '';
	}

	/**
	 *	Is current plugin universal?
	 */
	public static function isUniversal(){

	}

	/**
	 * Is it possible to get the total number of items?
	 */
	public function isCountable(){
		return $this->isCountable;
	}

	/**
	 * Can it work with the goods
	 */
	public function hasProducts(){
		return true;
	}

	/**
	 * Get comment for the tab
	 */
	public function getTabComment($tab){
		return '';
	}

	/**
	 *	Is current plugin universal?
	 */
	public function getSettingsPeriodSyncBlock() {
		return '';
	}

	/**
	 * Get plugin description.
	 */
	public static function getDescription() {
		$strFile = static::getFolder().'/.description.php';
		if(is_file($strFile) && filesize($strFile)) {
			Loc::loadMessages($strFile);
			ob_start();
			require $strFile;
			$strResult = ob_get_clean();
			if(strlen(static::DATE_UPDATED)){
				$strResult .= PHP_EOL.Loc::getMessage('ACRIT_EXP_DATE_UPDATED', array('#DATE#' => static::DATE_UPDATED));
			}
			return trim($strResult);
		}
		return false;
	}

	/**
	 * Get plugin example
	 */
	public static function getExample() {
		/*
		$strFile = static::getFolder().'/.example.php';
		if(is_file($strFile) && filesize($strFile)) {
			Loc::loadMessages($strFile);
			ob_start();
			require $strFile;
			$strResult = ob_get_clean();
			return trim($strResult);
		}
		return false;
		*/
		$strHtml = '';
		if(static::isSubClass()){
			$obThis = new static(static::$strStaticModuleId);
			$arFormats = array();
			foreach($obThis->arSupportedFormats as $strFormat){
				$strFile = static::getFolder().'/.example.'.toLower($strFormat);
				if(is_file($strFile) && filesize($strFile)) {
					$arFormats[$strFormat] = file_get_contents($strFile);
				}
			}
			unset($obThis);
			ob_start();
			require __DIR__.'/../../include/orders/plugin_example/template.php';
			$strHtml = trim(ob_get_clean());
			if(!Helper::isUtf()){
				$strHtml = Helper::convertEncoding($strHtml, static::UTF8, static::CP1251);
			}
			unset($obThis, $arFormats);
		}
		return $strHtml;
	}

	/**
	 *	Is it subclass?
	 */
	public static function isSubclass(){
		return static::$bSubclass;
	}

	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return false;
	}

	/**
	 * Get lang message
	 */
	public static function getMessage($strLangKey, $arReplace=array()){
		$strPhrase = 'ACRIT_EXP_'.(static::getCode()).'_'.$strLangKey;
		$strMessage = Loc::getMessage($strPhrase, $arReplace);
		if(empty($strMessage)){
			$strClass = get_called_class();
			$arClasses = array($strClass);
			$i = 0;
			while(true){
				if (++$i > 10) break;
				$strClass = get_parent_class($strClass);
				if($strClass !== false) {
					$arClasses[] = $strClass;
				}
			}
			foreach($arClasses as $strClass){
				$strPhrase = 'ACRIT_EXP_'.($strClass::getCode()).'_'.$strLangKey;
				$strMessage = Loc::getMessage($strPhrase, $arReplace);
				if(!empty($strMessage)){
					break;
				}
			}
		}
		if(empty($strMessage)) {
			$strMessage = Loc::getMessage($strLangKey, $arReplace);
		}
		return $strMessage;
	}

	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		/*
		$arResult[] = array(
			'DIV' => 'mytab',
			'TAB' => 'My tab name',
			'TITLE' => 'My tab title',
			'SORT' => 5,
			'FILE' => __DIR__.'/tabs/mytab.php',
		);
		*/
		return $arResult;
	}

	/**
	 *	Get custom sub tabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		/*
		$arResult[] = array(
			'DIV' => 'mysubtab',
			'TAB' => 'My subtab name',
			'TITLE' => 'My subtab description',
			'SORT' => 5,
			'FILE' => __DIR__.'/subtabs/mysubtab.php',
		);
		*/
		return $arResult;
	}

	/**
	 *	Get folder of current test (child class)
	 */
	protected static function getFolder($strRelative=false){
		$strClassName = get_called_class();
		$obReflectionClass = new \ReflectionClass($strClassName);
		$strFileName = $obReflectionClass->getFileName();
		unset($obReflectionClass);
		$strResult = pathinfo($strFileName,PATHINFO_DIRNAME);
		if($strRelative) {
			$strResult = substr($strResult,strlen($_SERVER['DOCUMENT_ROOT']));
		}
		return $strResult;
	}

	/**
	 *	Custom ajax actions for plugins
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult){
		// nothing, code in plugin
	}

	/* *** */

	/**
	 *	Set profile array
	 */
	public function setProfileArray(array $arProfile){
		$this->arProfile = $arProfile;
		$this->arParams = &$this->arProfile['PARAMS'];
		$this->intProfileId = $arProfile['ID'];
	}

	/**
	 *	Get profile array
	 */
	public function getProfileArray(){
		return $this->arProfile;
	}

	/**
	 *	Get external store fields list
	 */
	public function getFields() {
		$list = [];
		$list[] = [
			'id' => 'products_list',
			'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_FIELDS_PRODUCTS_LIST'),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

    /**
     * Array of constants
     */
    public function getConstantsValue(){
        $arConstants = $this->arProfile['OTHER']['CONSTANTS']['LIST'];
        $list = [];
        foreach ( $arConstants as $key => $item) {
            $list[] = [
                'ID' => $key,
                'VALUE' => $item['VALUE'],
            ];
        }
        return $list;
    }

	/**
	 * Additional modifies for order array
	 */
	public function formatOrder($order) {
		// Format products list for order property
        foreach ( $this->getConstantsValue() as $item) {
            $value = [$item['VALUE']];
            if ( !Helper::isUtf() && $this->arProfile['CONNECT_DATA']['convert'] === 'Y') {
                $value =  Helper::convertEncoding($value, $strFrom='CP1251',  $strTo='UTF-8');
            }
            $order['FIELDS'][$item['ID']] = [
                'TYPE'  => 'STRING',
                'VALUE' => $value,
            ];
        }

		$rows = [];
		if (is_array($order['PRODUCTS'])) {
			foreach ($order['PRODUCTS'] as $product) {
				$row = '';
				$row .= $product['PRODUCT_NAME'] . "\n";
				$row .= Loc::getMessage('ACRIT_CRM_PLUGIN_FORMATORDER_PRODUCTS_CODE') . $product['PRODUCT_CODE'] . "\n";
				$row .= Loc::getMessage('ACRIT_CRM_PLUGIN_FORMATORDER_PRODUCTS_QUANTITY') . $product['QUANTITY'] . "\n";
				$row .= Loc::getMessage('ACRIT_CRM_PLUGIN_FORMATORDER_PRODUCTS_PRICE') . $product['PRICE'] . ' ' . $product['CURRENCY'];
				$rows[] = $row;
			}
		}
		$text = implode("\n\n", $rows);
		$order['FIELDS']['products_list'] = [
			'TYPE'  => 'STRING',
			'VALUE' => $text,
		];
		return $order;
	}

	/**
	 *	Set profile param ($arProfile['PARAMS']) and save it
	 */
	public function setProfileParam($arParams){
		return Helper::call($this->strModuleId, 'Profile', 'setParam', [$this->arProfile['ID'], $arParams]);
	}

	/**
	 *	Check plugin data
	return [
	['TITLE' => '213123123', 'MESSAGE' => 'My error!', 'IS_ERROR' => true],
	['TITLE' => '213123123', 'MESSAGE' => 'My note!'],
	];
	 */
	public function checkData(){
		$arMessages = [];
		# Check filename is unique
		$T = microtime(true);
		$strExportFile = Helper::path($this->arParams['EXPORT_FILE_NAME']);
		if(strlen($strExportFile)){
			$arConflictProfilesId = [];
			$arFilter = ['!ID' => $this->intProfileId];
			$arProfiles = Helper::call($this->strModuleId, 'ProfileTable', 'getProfiles', [$arFilter, [], false, false]);
			foreach($arProfiles as $arProfile){
				if(strlen($arProfile['PARAMS']['EXPORT_FILE_NAME'])){
					if(Helper::path($arProfile['PARAMS']['EXPORT_FILE_NAME']) == $strExportFile){
						$arConflictProfilesId[] = $arProfile['ID'];
					}
				}
			}
			if(!empty($arConflictProfilesId)){
				$arMessages[] = [
					'TITLE' => Helper::getMessage('ACRIT_EXP_PLUGIN_CHECK_DATA_CONFLICT_FILENAME_TITLE', [
						'#FILENAME#' => $this->arParams['EXPORT_FILE_NAME'],
					]),
					'MESSAGE' => Helper::getMessage('ACRIT_EXP_PLUGIN_CHECK_DATA_CONFLICT_FILENAME_DESC', [
						'#ID#' => implode(', ', $arConflictProfilesId),
					]),
					'IS_ERROR' => true,
				];
			}
		}
		#
		return $arMessages;
	}

	/**
	 *	Show messages in profile edit
	 */
	public function showMessages(){
		//
	}

	/**
	 *	Add message to log
	 */
	public function addToLog($strMessage, $bDebug=false){
		return Log::getInstance($this->strModuleId, 'orders')->add($strMessage, $this->intProfileId, $bDebug);
	}

	/**
	 *	Log and print message
	 */
	protected function logAndPrintError($strLangPhraseID, $intProfileID){
		$strMessage = static::getMessage($strLangPhraseID);
		if(strlen($strMessage)){
			Log::getInstance($this->strModuleId, 'orders')->add($strMessage, $intProfileID);
			print Helper::showError($strMessage);
		}
		unset($strMessage);
	}

	/**
	 *	Include selected plugin's file
	 */
	protected function includeFile($strFile, $callbackFile){
		global $APPLICATION;
		$strClass = get_called_class();
		$arFiles = array();
		while(true){
			$strPathAbsolute = $strClass::getFolder().'/'.$strFile;
			if(is_file($strPathAbsolute) && filesize($strPathAbsolute)>0){
				$arFiles[] = $strPathAbsolute;
			}
			$strClass = get_parent_class($strClass);
			if(!is_subclass_of($strClass, __CLASS__)){
				break;
			}
		}
		$arFiles = array_reverse($arFiles);
		$strResult = '';
		foreach($arFiles as $strFile){
			$strResult .= call_user_func_array($callbackFile, array($strFile, $bWrite));
		}
		return $strResult;
	}

	public function includeCss(){
		return $this->includeFile('style.css', function($strFile){
			return '<style>'.file_get_contents($strFile).'</style>';
		});
	}

	public function includeJs(){
		return $this->includeFile('script.js', function($strFile){
			return '<script>'.file_get_contents($strFile).'</script>';
		});
	}

	/**
	 *	Include classes
	 */
	public function includeClasses(){
		//
	}

	/**
	 *	Show plugin settings
	 */
	public function showSettings($arProfile){
		return '';
	}

    /**
     *	Show plugin special settings
     */
    public function showSpecial(){
        return '';
    }
    /**
     *	Show plugin feedback
     */

    public function feedBack(){
        return false;
    }

	/**
	 * Get id of products in marketplace
	 */
	public static function getIdField() {
		return [
			'id' => '',
			'name' => '',
		];
	}


	/**
	 *
	 * ORDERS INTEGRATION FUNCTIONS
	 *
	 */

    /**
     * Get message for ajax
     */
	public function getMess() {
	    $list = [
	        'getOrders' => Loc::getMessage('ACRIT_ORDER_GET_ORDERS'),
	        'confirmOrders' => Loc::getMessage('ACRIT_ORDER_CONFIRM_ORDERS'),
	        'emptyConfirm' => Loc::getMessage('ACRIT_ORDER_EMPTY_CONFIRM'),
	        'begDate' => Loc::getMessage('ACRIT_ORDER_BEG_DATE'),
	        'endDate' => Loc::getMessage('ACRIT_ORDER_END_DATE'),
	        'idBitrix' => Loc::getMessage('ACRIT_ORDER_ID_BITRIX'),
	        'numBitrix' => Loc::getMessage('ACRIT_ORDER_NUM_BITRIX'),
	        'summBitrix' => Loc::getMessage('ACRIT_ORDER_SUMM_BITRIX'),
	        'nuIm' => Loc::getMessage('ACRIT_ORDER_NUM_IM'),
	        'dateIm' => Loc::getMessage('ACRIT_ORDER_DATE_IM'),
	        'sumIm' => Loc::getMessage('ACRIT_ORDER_SUMM_IM'),
	        'confirm' => Loc::getMessage('ACRIT_ORDER_CONFIRM'),
	        'nameProd' => Loc::getMessage('ACRIT_ORDER_NAME_PROD'),
	        'idProd' => Loc::getMessage('ACRIT_ORDER_ID_PROD'),
	        'quantProd' => Loc::getMessage('ACRIT_ORDER_QUANT_PROD'),
	        'priceProd' => Loc::getMessage('ACRIT_ORDER_PRICE_PROD'),
	        'dataBitrix' => Loc::getMessage('ACRIT_ORDER_DATA_BITRIX'),
	        'dataMarket' => Loc::getMessage('ACRIT_ORDER_DATA_MARKET'),
        ];
	    return $list;
    }

	/**
	 * Get directions for synchronization
	 */
	public function getDirections() {
		$list = [];
		if (in_array(self::SYNC_NONE, $this->arDirections)) {
			$list[] = [
				'id' => self::SYNC_NONE,
				'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_DIRECTS_NONE'),
			];
		}
		if (in_array(self::SYNC_STOC, $this->arDirections)) {
			$list[] = [
				'id' => self::SYNC_STOC,
				'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_DIRECTS_STOC'),
			];
		}
		if (in_array(self::SYNC_CTOS, $this->arDirections)) {
			$list[] = [
				'id' => self::SYNC_CTOS,
				'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_DIRECTS_CTOS'),
			];
		}
		if (in_array(self::SYNC_ALL, $this->arDirections)) {
			$list[] = [
				'id' => self::SYNC_ALL,
				'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_DIRECTS_ALL'),
			];
		}
		return $list;
	}

	public function hasDirection($direction) {
		$result = in_array($direction, $this->arDirections);
		return $result;
	}

	/**
	 *	Regular synchronization interval modifications
	 */
	public function modifSyncInterval($sync_interval) {
		return $sync_interval;
	}

	/**
	 * Get option of profile
	 */
	public function getOption($name) {
		$value = false;
		if ($name && $this->arProfile['ID']) {
			$arProfile = Helper::call($this->strModuleId, 'OrdersProfiles', 'getProfiles', [$this->arProfile['ID']]);
			$arOptions = $arProfile['OPTIONS'];
			$value = $arOptions[$name];
		}
		return $value;
	}

	/**
	 * Save option of profile
	 */
	public function setOption($name, $value) {
		$result = true;
		if ($name && $this->arProfile['ID']) {
			$arProfile = Helper::call($this->strModuleId, 'OrdersProfiles', 'getProfiles', [$this->arProfile['ID']]);
			$arOptions = $arProfile['OPTIONS'];
			$arOptions[$name] = $value;
			$obResult = Helper::call($this->strModuleId, 'OrdersProfiles', 'update', [$this->arProfile['ID'], [
				'OPTIONS' => serialize($arOptions)
			]]);
		}
		return $result;
	}


	// --- EVENTS ---

	/**
	 * Before the profile saved
	 */
	public function eventPageEditBeforeSave($arFields) {
		return $arFields;
	}

	/**
	 * When get the profile
	 */
	public function eventPageEditGetProfile($arFields) {
		return $arFields;
	}
	
	/**
	 *	Include html item with outbut buffering (for eg, it can be used for settings)
	 */
	public function includeHtml($strFilename, $arParams=[]){
		ob_start();
		require $strFilename;
		return trim(ob_get_clean());
	}
}
