<?php

/**
 * Class Manager
 * 
 * @package VKapi\Market
 */
class VKapi_Market_Manager_Demo
{
    /**
     * @var \VKapi\Market\Manager
     */
    private static $instance = \null;
    /**
     * @var bool - ������� �� ����� �����������
     */
    protected $bDebug = \null;
    /**
     * @var \VKapi\Market\Base
     */
    protected $oBase = \null;
    /**
     * @var \CIBlock
     */
    protected $oIblock = \null;
    /**
     * @var \CIBlockProperty
     */
    protected $oIblockProperty = \null;
    /**
     * @var \CIBlockElement
     */
    protected $oIblockElementOld = \null;
    /**
     * @var \Bitrix\Iblock\ElementTable
     */
    protected $oIblockElement = \null;
    /**
     * @var \CIBlockSection
     */
    protected $oIblockSectionOld = \null;
    /**
     * @var \Bitrix\Iblock\SectionElementTable
     */
    protected $oIblockElementSection = \null;
    /**
     * @var \Bitrix\Currency\CurrencyTable
     */
    protected $oCurrency = \null;
    /**
     * @var \VKapi\Market\ExportTable
     */
    protected $oExportTable = \null;
    /**
     * @var \VKapi\Market\Good\Reference\Export
     */
    protected $oGoodReferenceExport = \null;
    /**
     * @var \CFile
     */
    protected $oFile = \null;
    protected $oExportLog = \null;
    /**
     * @var \CCatalogGroup
     */
    protected $oCatalogGroup = \null;
    /**
     * @var \CCatalogStore
     */
    protected $oCatalogStore = \null;
    /**
     * @var \CPrice
     */
    protected $oPrice = \null;
    /**
     * @var \CCatalogProduct
     */
    protected $oProduct = \null;
    /**
     * @var \CCatalogDiscount
     */
    protected $oDiscount = \null;
    /**
     * @var array ������ ������������������ ����������� � ��
     */
    protected $arConnect = [];
    /**
     * @var array ������ � ��������� ��������
     */
    protected static $exportData = [];
    /**
     * @var int ����� ������ ����������
     */
    protected $timestart = 0;
    /**
     * @var int ����� �� ���������� ��������, ���.
     */
    protected $timeout = 45;
    /**
     * @var int ����� �� �������, ����� 85% �� �������, �� 512 ��
     */
    protected $memoryLimit = 456340275;
    private $bDemo = \null;
    private $bDemoExpired = \null;
    protected $arSiteSchema = [];
    protected $arSiteHost = [];
    private function __construct()
    {
        // ��������� �������� �� �������� ������
        $this->timestart = \time();
        $this->setTimeout($this->getParam('TIMEOUT', 45, ''));
        if (\preg_match('/^(\\d+)(.)$/', \ini_get('memory_limit'), $matches)) {
            if ($matches[2] == 'G' || $matches[2] == 'g') {
                $this->memoryLimit = $matches[1] * 1024 * 1024 * 1024;
            } elseif ($matches[2] == 'M' || $matches[2] == 'm') {
                $this->memoryLimit = $matches[1] * 1024 * 1024;
            } elseif ($matches[2] == 'K' || $matches[2] == 'k') {
                $this->memoryLimit = $matches[1] * 1024;
            }
            $this->memoryLimit = \round($this->memoryLimit * 0.85);
        }
    }
    private function __clone()
    {
    }
    /**
     * ������� ������ � 1 ����������
     * 
     * @return \VKapi\Market\Manager
     */
    public static function getInstance()
    {
        if (\is_null(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }
    /**
     * ���������� ������������� ������
     * 
     * @return string
     */
    public function getModuleId()
    {
        return 'vkapi.market';
    }
    /**
     * ������ ���� �� �������� ������ �� ����� �����
     * 
     * @param bool $bReturnAbsPath - ������� ���������� ����
     * @return string
     */
    public function getModulePath($bReturnAbsPath = \false)
    {
        return ($bReturnAbsPath ? \Bitrix\Main\Application::getDocumentRoot() : '') . \getLocalPath('modules/' . $this->getModuleId());
    }
    /**
     * ����� ������������ ������� ��� ����������� ����������
     * 
     * @param $eventName
     * @param $arData
     * @param false $returnAsArray - ������� ������ �������� � ���� ������ ��� ������������� � list()
     * @return mixed
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public function sendEvent($eventName, $arData, $returnAsArray = \false)
    {
        $event = new \Bitrix\Main\Event($this->getModuleId(), $eventName);
        $event->setParameters($arData);
        $event->send();
        $arKeys = \array_keys($arData);
        if ($event->getResults()) {
            foreach ($event->getResults() as $evenResult) {
                if ($evenResult->getType() == \Bitrix\Main\EventResult::SUCCESS) {
                    $arDataModified = $evenResult->getParameters();
                    $arModified = \array_intersect_key($arDataModified, $arData);
                    if (!empty($arModified)) {
                        $arData = \array_replace($arData, $arDataModified);
                    }
                }
            }
        }
        // ��� ������������� list()
        if ($returnAsArray) {
            $arResultValues = [];
            foreach ($arKeys as $key) {
                $arResultValues[] = $arData[$key];
            }
            return $arResultValues;
        }
        return $arData;
    }
    /**
     * ������� ����� ��� �������, ������� ����� ������������ �������
     * 
     * @return \VKapi\Market\Base
     */
    public function base()
    {
        if (\is_null($this->oBase)) {
            $this->oBase = new \VKapi\Market\Base($this->getModuleId());
        }
        return $this->oBase;
    }
    /**
     * @return \CFile
     */
    public function file()
    {
        if (\is_null($this->oFile)) {
            $this->oFile = new \CFile();
        }
        return $this->oFile;
    }
    /**
     * @return \CIBlock
     */
    public function iblock()
    {
        if (\is_null($this->oIblock) && $this->isInstalledIblockModule()) {
            $this->oIblock = new \CIBlock();
        }
        return $this->oIblock;
    }
    /**
     * ������ � �������� ��������
     * �������� ����:
     * + ID : int
     * + SITE_ID : string
     * + ACCOUNT_ID : int - ������������� ������������ ��������, �� ����� �������� ���������
     * + GROUP_ID : int - ������������� ������ � ���������
     * + GROUP_NAME : string - �������� ������ � ���������, ��� ������ � ������ ��������
     * + NAME : string
     * + ACTIVE : bool
     * + AUTO:bool
     * + CATALOG_ID:int
     * + ALBUMS:array
     * + PARAMS:array
     * 
     * @return \VKapi\Market\ExportTable
     */
    public function exportTable()
    {
        if (\is_null($this->oExportTable)) {
            $this->oExportTable = new \VKapi\Market\ExportTable();
        }
        return $this->oExportTable;
    }
    /**
     * @return \VKapi\Market\Good\Reference\Export
     */
    public function goodReferenceExport()
    {
        if (\is_null($this->oGoodReferenceExport)) {
            $this->oGoodReferenceExport = new \VKapi\Market\Good\Reference\Export();
        }
        return $this->oGoodReferenceExport;
    }
    /**
     * @return \CIBlockProperty
     */
    public function iblockProperty()
    {
        if (\is_null($this->oIblockProperty) && $this->isInstalledIblockModule()) {
            $this->oIblockProperty = new \CIBlockProperty();
        }
        return $this->oIblockProperty;
    }
    /**
     * @return \Bitrix\Iblock\ElementTable
     */
    public function iblockElement()
    {
        if (\is_null($this->oIblockElement) && $this->isInstalledIblockModule()) {
            $this->oIblockElement = new \Bitrix\Iblock\ElementTable();
        }
        return $this->oIblockElement;
    }
    /**
     * @return \CIBlockElement
     */
    public function iblockElementOld()
    {
        if (\is_null($this->oIblockElementOld) && $this->isInstalledIblockModule()) {
            $this->oIblockElementOld = new \CIBlockElement();
        }
        return $this->oIblockElementOld;
    }
    /**
     * @return \CIBlockSection
     */
    public function iblockSectionOld()
    {
        if (\is_null($this->oIblockSectionOld) && $this->isInstalledIblockModule()) {
            $this->oIblockSectionOld = new \CIBlockSection();
        }
        return $this->oIblockSectionOld;
    }
    /**
     * @return \Bitrix\Iblock\SectionElementTable
     */
    public function iblockElementSection()
    {
        if (\is_null($this->oIblockElementSection) && $this->isInstalledIblockModule()) {
            $this->oIblockElementSection = new \Bitrix\Iblock\SectionElementTable();
        }
        return $this->oIblockElementSection;
    }
    /**
     * @return \Bitrix\Currency\CurrencyTable
     */
    public function currency()
    {
        if (\is_null($this->oCurrency) && $this->isInstalledCurrencyModule()) {
            $this->oCurrency = new \Bitrix\Currency\CurrencyTable();
        }
        return $this->oCurrency;
    }
    /**
     * @return \Bitrix\Catalog\PriceTable|\Bitrix\Catalog\Model\Price
     */
    public function catalogPrice()
    {
        if (\is_null($this->oPrice) && $this->isInstalledCatalogModule()) {
            $this->oPrice = new \Bitrix\Catalog\Model\Price();
        }
        return $this->oPrice;
    }
    /**
     * @return \CCatalogGroup
     */
    public function catalogPriceGroup()
    {
        if (\is_null($this->oCatalogGroup) && $this->isInstalledCatalogModule()) {
            $this->oCatalogGroup = new \CCatalogGroup();
        }
        return $this->oCatalogGroup;
    }
    /**
     * @return \CCatalogStore
     */
    public function catalogStore()
    {
        if (\is_null($this->oCatalogStore) && $this->isInstalledCatalogModule() && \class_exists('\\CCatalogStore')) {
            $this->oCatalogStore = new \CCatalogStore();
        }
        return $this->oCatalogStore;
    }
    /**
     * @return \CCatalogProduct
     */
    public function catalogProduct()
    {
        if (\is_null($this->oProduct) && $this->isInstalledCatalogModule()) {
            $this->oProduct = new \CCatalogProduct();
        }
        return $this->oProduct;
    }
    /**
     * @return \CCatalogDiscount
     */
    public function catalogDiscount()
    {
        if (\is_null($this->oDiscount) && $this->isInstalledCatalogModule()) {
            $this->oDiscount = new \CCatalogDiscount();
        }
        return $this->oDiscount;
    }
    /**
     * 
     * @param $name
     * @param null $arReplace
     * @return mixed|string
     */
    public function getMsg($name, $arReplace = \null)
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.' . $name, $arReplace);
    }
    /**
     * ������ �������������� ���������
     * 
     * @param $code
     * @param array $arReplace
     * @return mixed|string
     */
    public function getMessage($name, $arReplace = \null)
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.' . $name, $arReplace);
    }
    /**
     * @param $name
     * @param string $default_value
     * @param null $siteId
     * 
     * @return string
     */
    public function getParam($name, $default_value = '', $siteId = \null)
    {
        return $this->base()->getParam($name, $default_value, $siteId);
    }
    /**
     * �������������� ������ � camelCase
     * 
     * @param $arData
     * @return array
     */
    public function toJsDataFormat($arData)
    {
        return $this->getCamelCaseKeys($this->getToLowerKeys($arData));
    }
    /**
     * ����������� ������ � ������� ������ ��� js
     * {id : name, ...} -> [{id:id, name : name}, ...]
     * 
     * @param array $arList
     * @return array
     */
    public function listToJsFormat($arList)
    {
        $arReturn = [];
        foreach ($arList as $id => $name) {
            $arReturn[] = ['id' => $id, 'name' => $name];
        }
        return $arReturn;
    }
    /**
     * ��������� ����� ������� � ������ camelCase �� camel_case
     * 
     * @param $ar
     * @return array
     */
    public function getCamelCaseKeys($ar)
    {
        if (\is_array($ar)) {
            $arNew = [];
            foreach ($ar as $key => $val) {
                $key = \preg_replace_callback('/_([a-z]{1})/', function ($matches) {
                    return \mb_strtoupper($matches[1]);
                }, $key);
                if (\is_array($val)) {
                    $arNew[$key] = $this->getCamelCaseKeys($val);
                } else {
                    $arNew[$key] = $val;
                }
            }
            unset($val);
            return $arNew;
        }
        return $ar;
    }
    /**
     * ��������� ����� ������� � ������ �������
     * 
     * @param $ar
     * @return array
     */
    public final function getToLowerKeys($ar)
    {
        if (\is_array($ar)) {
            $ar = \array_change_key_case($ar, \CASE_LOWER);
            foreach ($ar as $key => &$val) {
                $val = $this->getToLowerKeys($val);
            }
            unset($val);
        }
        return $ar;
    }
    /**
     * ����������� ������ � js �� ��������
     */
    public function showAdminPageCssJs()
    {
        global $APPLICATION;
        \CJSCore::Init(['jquery2', 'date']);
        \Bitrix\Main\UI\Extension::load(["ui.vue", "ui.vue.vuex"]);
        $messages = \Bitrix\Main\Localization\Loc::loadLanguageFile($this->getModulePath(\true) . '/admin.js.php');
        // �������� ���������
        \Bitrix\Main\Page\Asset::getInstance()->addString('<script type="text/javascript" data-module="' . $this->getModuleId() . '" >BX.message(' . \Bitrix\Main\Web\Json::encode($messages) . ');</script>', \false, \Bitrix\Main\Page\AssetLocation::AFTER_JS);
        if ($this->isDevelopment()) {
            \Bitrix\Main\Page\Asset::getInstance()->addString('<link  data-module="' . $this->getModuleId() . '" rel="stylesheet" type="text/css" href="' . $this->getConfiguration('css') . '">', \false, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);
            \Bitrix\Main\Page\Asset::getInstance()->addString('<script type="text/javascript" data-module="' . $this->getModuleId() . '" src="' . $this->getConfiguration('js') . '"></script>', \false, \Bitrix\Main\Page\AssetLocation::AFTER_JS);
        } else {
            \Bitrix\Main\Page\Asset::getInstance()->addString('<link  data-module="' . $this->getModuleId() . '" rel="stylesheet" type="text/css" href="' . \CUtil::GetAdditionalFileURL('/bitrix/js/' . $this->getModuleId() . '/app.css') . '">', \false, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);
            \Bitrix\Main\Page\Asset::getInstance()->addString('<script type="text/javascript" data-module="' . $this->getModuleId() . '" src="' . \CUtil::GetAdditionalFileURL('/bitrix/js/' . $this->getModuleId() . '/app.js') . '"></script>', \false, \Bitrix\Main\Page\AssetLocation::AFTER_JS);
        }
    }
    /**
     * ������ ������ � �������������, ��� �������� ����������� ��������� �� .settings.php
     * 
     * @param string $name - �������� ���������
     * @return mixed
     */
    public function getConfiguration($name = \null)
    {
        static $value;
        if (!isset($value)) {
            $value = (array) \Bitrix\Main\Config\Configuration::getValue($this->getModuleId());
        }
        if (!\is_null($name)) {
            return isset($value[$name]) ? $value[$name] : \null;
        }
        return $value;
    }
    /**
     * ��������� ����� � ������� �������� ������, ����� �� ������������ .settings.php
     * 
     * @return null
     */
    public function isDevelopment()
    {
        return $this->getConfiguration('mode') == 'development';
    }
    /**
     * ������ ������� �����������
     * 
     * @return int
     */
    public function getLogLevel()
    {
        static $logLevel;
        if (!isset($logLevel)) {
            $logLevel = $this->getParam('DEBUG', \VKapi\Market\Export\Log::LEVEL_NONE, '');
        }
        return $logLevel;
    }
    /**
     * ������ ���������� ���������� ����� ��������� � API
     * @return mixed
     */
    public function getConnectInterval()
    {
        return \max(500, \min(1500, $this->getParam('CONNECT_INTERVAL', 500, '')));
    }
    /**
     * ��������� ���������� �� ��������� ��������,
     * ��� �������� ����� �������� ����� �������� ��� ������ ����� � 1�
     * @return bool
     */
    public function isDisabledUpdatePicture()
    {
        return $this->getParam('DISABLE_UPDATE_PICTURE', 'N', '') == 'Y';
    }
    /**
     * �������� �� ������������� ������
     * 
     * @return bool
     */
    public function isEnabledProxy()
    {
        return $this->getParam('ENABLE_PROXY', 'N', '') == 'Y';
    }
    /**
     * ���� ������ �������
     * 
     * @return string
     */
    public function getProxyHost()
    {
        return $this->getParam('PROXY_HOST', '', '');
    }
    /**
     * ���� ������ �������
     * 
     * @return string
     */
    public function getProxyPort()
    {
        return $this->getParam('PROXY_PORT', '', '');
    }
    /**
     * ������������ ������ �������
     * 
     * @return string
     */
    public function getProxyUser()
    {
        return $this->getParam('PROXY_USER', '', '');
    }
    /**
     * ������ ������������ ������ �������
     * 
     * @return string
     */
    public function getProxyPass()
    {
        return $this->getParam('PROXY_PASS', '', '');
    }
    /**
     * ������ ����������� ����� �������� ������ � ���������
     * 
     * @return string
     */
    public function getDescriptionLengthLimit()
    {
        return \max(10, $this->getParam('DESCRIPTION_LENGTH_LIMIT', 5000, ''));
    }
    /**
     * ������ ����������� �� ���������� ������� � �����
     * 
     * @return string
     */
    public function getExportPackLimit()
    {
        return \max(1, \min(25, $this->getParam('ADD_TO_VK_PACK_LENGTH', 1, '')));
    }
    /**
     * ������ ������ � utm ������� ��� ���������� ������ �� �����
     * @return string
     */
    public function getUrlUtm()
    {
        return $this->getParam('URL_UTM', '', '');
    }
    /**
     * �������� ����� � ��������� cp1251
     * 
     * @param $text
     * @param $limit
     * @return string
     */
    public function truncateTextVK($text, $limit)
    {
        $result = '';
        do {
            if (\strlen($text) <= $limit) {
                $result = $text;
                break;
            }
            $length = \mb_strlen($text);
            $count = 3;
            for ($i = 0; $i < $length; $i++) {
                $count += \strlen($text[$i]);
                if ($count > $limit) {
                    break;
                }
                $result .= $text[$i];
            }
            $result = \trim($result, ' -') . '...';
        } while (\false);
        return $result;
    }
    /**
     * �������� �����
     * 
     * @param $text
     * @param $limit
     * @return string
     */
    public function truncateText($text, $limit)
    {
        $result = $text;
        $len = \strlen($text);
        if ($len > $limit) {
            $arChar = \preg_split('/(?<!^)(?!$)/' . ($this->base()->isUtf() ? 'u' : ''), $text);
            $result = '';
            foreach ($arChar as $char) {
                if (\strlen($result . $char) > $limit - 3) {
                    break;
                }
                $result .= $char;
            }
            $result = \trim($result, ' -') . '...';
        }
        return $result;
    }
    /**
     * �������� ����� ��� ������������� � execute � ������� json
     * 
     * @param $text
     * @param $limit
     * @return string
     */
    public function truncateTextForJson($text, $limit)
    {
        $result = $text;
        if (\strlen($this->jsonStringReplace($text)) > $limit) {
            $arChar = \preg_split('/(?<!^)(?!$)/' . ($this->base()->isUtf() ? 'u' : ''), $text);
            $result = '';
            foreach ($arChar as $char) {
                if (\strlen($this->jsonStringReplace($result . $char)) > $limit - 3) {
                    break;
                }
                $result .= $char;
            }
            $result = \trim($result, ' -') . '...';
        }
        return $result;
    }
    /**
     * ���������� ������ � ���� ����, ��� ������� �� ����� �������� �����
     * @param $str
     * @return array|string|string[]
     */
    public function prepareVkString($str)
    {
        /**
        * " � 6 ��������
        * & � 5 ��������
        * \ � 6 ��������
        * ' � 5 ��������
        * > � < � �� 4 �������
        * , � ! � 5 ��������
        * $ � 6 ��������
        * � � 7 ��������
        */
        static $arReplace, $arKeys, $arValues;
        if (!isset($arReplace)) {
            $arReplace = [];
            $arReplace['"'] = \str_repeat('*', 6);
            $arReplace['&'] = \str_repeat('*', 5);
            $arReplace['\\'] = \str_repeat('*', 6);
            $arReplace["'"] = \str_repeat('*', 5);
            $arReplace[">"] = \str_repeat('*', 4);
            $arReplace["<"] = \str_repeat('*', 4);
            $arReplace[","] = \str_repeat('*', 5);
            $arReplace["!"] = \str_repeat('*', 5);
            $arReplace["\$"] = \str_repeat('*', 6);
            $arReplace["�"] = \str_repeat('*', 7);
            $arKeys = \array_keys($arReplace);
            $arValues = \array_values($arReplace);
        }
        return \str_replace($arKeys, $arValues, $str);
    }
    /**
     * ������ �������������� ������ �� �������������� ���� � �������, �������� - ���.
     * 
     * @param $formatedPrice - �������� 1 500 ���.
     * @return mixed|string
     */
    public function getFormatedCurrencyByFormatedPrice($formatedPrice)
    {
        $arParts = \explode(' ', $formatedPrice);
        if (\count($arParts) > 1 && $this->isInstalledCatalogModule()) {
            return \end($arParts);
        }
        return $this->getMessage('PRICE_CURRENCY_SHORT_FORMAT');
    }
    /**
     * ������ ������������� ���� ��� ��, ��� ������, �������� 1020.30
     * 
     * @param $price
     * @return mixed|string
     */
    public function getPreparedPrice($price)
    {
        $price = \str_replace([' '], [''], $price);
        $price = \number_format($price, 2, '.', '');
        return (float) $price;
    }
    /**
     * ������ http:// ��� https://
     * 
     * @param $siteId
     * @return mixed
     */
    public function getSiteSchema($siteId)
    {
        if (!isset($this->arSiteSchema[$siteId])) {
            $this->arSiteSchema[$siteId] = $this->getParam('URL_HTTPS', 'N', $siteId) == 'N' ? 'http://' : 'https://';
        }
        return $this->arSiteSchema[$siteId];
    }
    /**
     * ������ �������� ��� �����
     * 
     * @param $siteId
     * @return mixed
     */
    public function getSiteHost($siteId)
    {
        if (!isset($this->arSiteHost[$siteId])) {
            $oSite = new \CSite();
            $by = 'sort';
            $order = 'asc';
            $dbr = $oSite->GetList($by, $order, ['ID' => $siteId]);
            if ($ar = $dbr->Fetch()) {
                $this->arSiteHost[$siteId] = $ar;
            }
        }
        return $this->arSiteHost[$siteId]['SERVER_NAME'];
    }
    /**
     * ������ ������ ������ [id => name, ...]
     * 
     * @return array
     */
    public function getSiteList()
    {
        static $arSite;
        if (!isset($arSite)) {
            $arSite = [];
            $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
            while ($ar = $dbr->Fetch()) {
                $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
            }
        }
        return $arSite;
    }
    /**
     * �������� ������ ������ ��� ������������� � SelectBoxFromArray
     * 
     * @return array
     */
    public function getSiteSelectList()
    {
        static $arSites;
        if (!isset($arSites)) {
            $arSites = ['REFERENCE_ID' => [''], 'REFERENCE' => [$this->getMessage('NO_SELECT')]];
            $arSite = $this->getSiteList();
            foreach ($arSite as $siteId => $siteName) {
                $arSites['REFERENCE_ID'][] = $siteId;
                $arSites['REFERENCE'][] = $siteName;
            }
        }
        return $arSites;
    }
    /**
     * ������ ������ ���������� [id => name, ...]
     * 
     * @return array
     */
    public function getIblockList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            $arItems = $this->getIblockItems();
            foreach ($arItems as $item) {
                $arReturn[$item['ID']] = $item['NAME'];
            }
        }
        return $arReturn;
    }
    /**
     * ������� ������ �� ����� ��������
     * 
     * @param $errors
     */
    public function showErrors($errors)
    {
        if ($errors && \is_array($errors)) {
            $arStr = [];
            foreach ($errors as $error) {
                $arStr[] = $error->getMessage();
            }
            \CAdminMessage::ShowMessage(\implode('<br />', $arStr));
        }
    }
    /**
     * ��������� ���������� �� ������ ��������
     * 
     * @return bool
     */
    public function isInstalledCatalogModule()
    {
        static $bResult;
        if (!isset($bResult)) {
            $bResult = \false;
            try {
                if (\Bitrix\Main\Loader::includeModule('catalog')) {
                    $bResult = \true;
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $bResult;
    }
    /**
     * �������� ���������� �� ������ ����������
     * 
     * @return bool
     */
    public function isInstalledIblockModule()
    {
        static $bResult;
        if (!isset($bResult)) {
            $bResult = \false;
            try {
                if (\Bitrix\Main\Loader::includeModule('iblock')) {
                    $bResult = \true;
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $bResult;
    }
    /**
     * �������� ���������� �� ������ ������� ������
     * 
     * @return bool
     */
    public function isInstalledHighloadBlockModule()
    {
        static $bResult;
        if (!isset($bResult)) {
            $bResult = \false;
            try {
                if (\Bitrix\Main\Loader::includeModule('highloadblock')) {
                    $bResult = \true;
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $bResult;
    }
    /**
     * ������ ����� ������������ ��� ������ � ��� ��������
     * @param $tableName
     * @return \Bitrix\Highloadblock\DataManager|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getHighloadBlockClassByTableName($tableName)
    {
        static $arClassList;
        $tableName = (string) $tableName;
        if (!isset($arClassList[$tableName])) {
            $arClassList[$tableName] = \null;
            if ($this->isInstalledHighloadBlockModule()) {
                // ������� ������� ���������� � ��� �� ���� ������
                $dbr = \Bitrix\Highloadblock\HighloadBlockTable::getList(['select' => ['*'], 'order' => ['NAME' => 'ASC'], 'filter' => ['TABLE_NAME' => $tableName], 'limit' => 1]);
                if ($arHLBlock = $dbr->fetch()) {
                    // ����� ���������������� ����� ��������
                    $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
                    $strEntityDataClass = $obEntity->getDataClass();
                    $arClassList[$tableName] = new $strEntityDataClass();
                }
            }
        }
        return $arClassList[$tableName];
    }
    /**
     * �������� ���������� �� ������ �����
     * 
     * @return bool
     */
    public function isInstalledCurrencyModule()
    {
        static $bResult;
        if (!isset($bResult)) {
            $bResult = \false;
            try {
                if (\Bitrix\Main\Loader::includeModule('currency')) {
                    $bResult = \true;
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $bResult;
    }
    /**
     * �������� - �������� � ���� �� ������
     * 
     * @return bool
     */
    public final function isDemo()
    {
        if (\is_null($this->bDemo)) {
            $this->checkDemo();
        }
        return $this->bDemo;
    }
    /**
     * �������� - �� ���������� �� ���� ������
     * 
     * @return bool
     */
    public final function isExpired()
    {
        if (\is_null($this->bDemoExpired)) {
            $this->checkDemo();
        }
        return $this->bDemoExpired;
    }
    /**
     * �������� ����
     */
    public final function checkDemo()
    {
        $module = new \CModule();
        if ($module->IncludeModuleEx('vkapi.market') == \constant('MODULE_NOT_FOUND')) {
            $this->bDemo = \false;
            $this->bDemoExpired = \false;
        } elseif ($module->IncludeModuleEx('vkapi.market') == \constant('MODULE_DEMO')) {
            $this->bDemo = \true;
            $this->bDemoExpired = \false;
        } elseif ($module->IncludeModuleEx('vkapi.market') == \constant('MODULE_DEMO_EXPIRED')) {
            $this->bDemo = \true;
            $this->bDemoExpired = \true;
        }
    }
    /**
 * ���� ���������
 */
    public function showAdminPageMessages()
    {
        if ($this->isDemo()) {
            echo '<div class="ap - vkapi_market - notice - box" >' . $this->getMsg('DEMO_NOTICE') . '</div>';
        }
        if ($this->isExpired()) {
            echo '<div class="ap - vkapi_market - notice - box expired" >' . $this->getMsg('DEMO_EXPIRED_NOTICE') . '</div>';
        }
        // �������� ����������� �� ���������� UI
        if (!\Bitrix\Main\Loader::includeModule('ui')) {
            echo '<div class="ap - vkapi_market - notice - box" >' . $this->getMsg('MODULE_UI_IS_NOT_INSTALLED') . '</div>';
        }
    }
    /**
     * ��������� �� ������, ����� ������ ������� �� ��������� �����
     */
    public function showAutoExportError()
    {
        $oVkParam = \VKapi\Market\Param::getInstance();
        if ($oVkParam->get('AUTO_EXPORT_STOP', 'N') != 'N') {
            echo '<div class="vkapi__market__admin__notice " >' . $this->getMsg('STATUS_AUTO_EXPORT_STOP_Y') . '</div>';
        }
    }
    /**
     * ����� ��������� ������������
     */
    public function resetAutoExportState($exportId)
    {
        $exportId = \intval($exportId);
        $oExportItem = new \VKapi\Market\Export\Item($exportId);
        $oExportItem->load();
        $oAlbumExport = new \VKapi\Market\Album\Export($oExportItem);
        $oGoodExport = new \VKapi\Market\Good\Export($oExportItem);
        $oPropertyExport = new \VKapi\Market\Property\Export($oExportItem);
        $oAlbumExport->state()->clean();
        $oGoodExport->state()->clean();
        $oPropertyExport->state()->clean();
        $oState = new \VKapi\Market\State('auto_' . $exportId);
        $oState->clean();
    }
    /**
     * ����� ��������� �� ���� ���������
     */
    public function resetAllState()
    {
        $oState = new \VKapi\Market\State('auto');
        \Bitrix\Main\IO\Directory::deleteDirectory($oState->getBaseDirectory());
    }
    /**
     * ���������� ������ ���������� ��������
     * 
     * @param $id
     * @return mixed
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getExportData($id)
    {
        if (!isset(self::$exportData[$id])) {
            $dbr = $this->exportTable()->getById($id);
            if ($ar = $dbr->fetch()) {
                self::$exportData[$id] = $ar;
            } else {
                self::$exportData[$id] = \null;
            }
        }
        return self::$exportData[$id];
    }
    /**
     * ���������� ������ ���������� ��� ��������� � ��
     * 
     * @param $accountId - ������������� ������������ ��������
     * @return \VKapi\Market\Connect|null
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getConnection($accountId)
    {
        // ������������� �����������
        if (!isset($this->arConnect[$accountId])) {
            $this->arConnect[$accountId] = \null;
            $conn = new \VKapi\Market\Connect();
            $resConnect = $conn->initAccountId($accountId);
            if ($resConnect->isSuccess()) {
                $this->arConnect[$accountId] = $conn;
            }
        }
        return $this->arConnect[$accountId];
    }
    /**
     * ������ ���� �� �������� ���������, ��� ��������� ������� �����
     */
    public function getPreviewPicturePath()
    {
        // �������� �������� c ������ ������
        if (!\file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/vkapi.market/preview/good.jpeg')) {
            \CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/upload/vkapi.market/preview/');
            @\copy($this->getModulePath(\true) . '/img/good.jpeg', $_SERVER['DOCUMENT_ROOT'] . '/upload/vkapi.market/preview/good.jpeg');
        }
        return '/upload/vkapi.market/preview/good.jpeg';
    }
    /**
     * ������������ ��������������� ������� �����
     * 
     * @param $wID
     * @param $wPosition
     * @param $wOpacity
     * @param $wKoef
     * @return array
     */
    public function getPreviewWatermark($wID, $wPosition, $wOpacity, $wKoef)
    {
        $srcSource = $this->getPreviewPicturePath();
        $ar = $this->file()->GetFileArray($wID);
        if (\file_exists($_SERVER['DOCUMENT_ROOT'] . $ar['SRC'])) {
            $arImageFilter = ["name" => "watermark", "position" => \in_array($wPosition, ['TL', 'TC', 'TR', 'ML', 'MC', 'MR', 'BL', 'BC', 'BR']) ? \strtolower($wPosition) : 'mc', "size" => "real", 'type' => 'image', 'alpha_level' => \abs(100 - $wOpacity), 'file' => $_SERVER['DOCUMENT_ROOT'] . $ar['SRC']];
            // �������
            if ($wPosition == 'FILL') {
                $arImageFilter['position'] = 'tl';
                $arImageFilter['fill'] = 'repeat';
            } else {
                // ���������
                $arImageFilter['size'] = 'big';
                $arImageFilter['fill'] = 'resize';
                $arImageFilter['coefficient'] = $wKoef;
            }
            \CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/upload/vkapi.market/tmp/');
            $srcDest = '/upload/vkapi.market/tmp/' . $wID . '_good.jpeg';
            $ss = $_SERVER['DOCUMENT_ROOT'] . $srcSource;
            $sd = $_SERVER['DOCUMENT_ROOT'] . $srcDest;
            $this->file()->ResizeImageFile($ss, $sd, [], \BX_RESIZE_IMAGE_PROPORTIONAL_ALT, $arImageFilter, 100);
            unset($arImageFilter, $srcSource, $ar, $wPosition, $wOpacity, $wKoef, $ss, $sd);
            return ['src' => $srcDest];
        }
        return ['src' => $srcSource];
    }
    /**
     * ������ ������ ������� ����� � ��������� ������
     * 
     * @return array
     */
    public function getMeasureList()
    {
        static $arReturn;
        if ($this->isInstalledCatalogModule()) {
            $dbr = \Bitrix\Catalog\MeasureTable::getList([]);
            while ($ar = $dbr->fetch()) {
                $ar['TITLE'] = $ar['MEASURE_TITLE'] = \CCatalogMeasureClassifier::getMeasureTitle($ar['CODE'], 'SYMBOL_RUS');
                $arReturn[$ar['ID']] = $ar;
            }
        }
        return $arReturn;
    }
    /**
     * ������ �������� ������� ���������
     * 
     * @param $id
     * @return mixed
     */
    public function getMeasureName($id)
    {
        $arNames = $this->getMeasureList();
        return isset($arNames[$id]) ? $arNames[$id]['TITLE'] : $id;
    }
    /**
     * ������ ������ ������� ����� � ��������� ������
     * 
     * @return array
     */
    public function getProductNameBaseList()
    {
        return ['PRODUCT_NAME' => $this->getMessage('PRODUCT_NAME'), 'PRODUCT_SEO_TITLE' => $this->getMessage('PRODUCT_SEO_TITLE'), 'PRODUCT_SEO_META_TITLE' => $this->getMessage('PRODUCT_SEO_META_TITLE')];
    }
    /**
     * ������ ������ ������� ����� � ��������� ��������� �����������
     * 
     * @return array
     */
    public function getOfferNameBaseList()
    {
        return ['OFFER_NAME' => $this->getMessage('OFFER_NAME'), 'OFFER_SEO_TITLE' => $this->getMessage('OFFER_SEO_TITLE'), 'OFFER_SEO_META_TITLE' => $this->getMessage('OFFER_SEO_META_TITLE')];
    }
    /**
     * ������ ������ ����� ������ ��� ������������� ��� �������
     * 
     * @return array
     */
    public function getProductSkuBaseList()
    {
        return ['PRODUCT_ID' => $this->getMessage('PRODUCT_ID'), 'PRODUCT_CODE' => $this->getMessage('PRODUCT_CODE'), 'PRODUCT_XML_ID' => $this->getMessage('PRODUCT_XML_ID'), 'PRODUCT_EXTERNAL_ID' => $this->getMessage('PRODUCT_EXTERNAL_ID')];
    }
    /**
     * ������ ������ ����� ��������� ����������� ��� ������������� ��� �������
     * 
     * @return array
     */
    public function getOfferSkuBaseList()
    {
        return ['OFFER_ID' => $this->getMessage('OFFER_ID'), 'OFFER_CODE' => $this->getMessage('OFFER_CODE'), 'OFFER_XML_ID' => $this->getMessage('OFFER_XML_ID'), 'OFFER_EXTERNAL_ID' => $this->getMessage('OFFER_EXTERNAL_ID')];
    }
    /**
     * ������ ������ ������� ����� � ���������� ��� ������
     * 
     * @return array
     */
    public function getProductPictureBaseList()
    {
        return ['PRODUCT_PREVIEW_PICTURE' => $this->getMessage('PRODUCT_PREVIEW_PICTURE'), 'PRODUCT_DETAIL_PICTURE' => $this->getMessage('PRODUCT_DETAIL_PICTURE')];
    }
    /**
     * ������ ������ ������� ����� � ���������� ��� ������
     * 
     * @return array
     */
    public function getOfferPictureBaseList()
    {
        return ['OFFER_PREVIEW_PICTURE' => $this->getMessage('OFFER_PREVIEW_PICTURE'), 'OFFER_DETAIL_PICTURE' => $this->getMessage('OFFER_DETAIL_PICTURE')];
    }
    /**
     * �����������, ���������� ��� �������� �������
     * 
     * @return array
     */
    public function getBaseTemplatePlaceholderList()
    {
        $arResult = ['BR' => $this->getMessage('TEMPLATE_PLACEHOLDER.BR'), 'EMPTY' => $this->getMessage('TEMPLATE_PLACEHOLDER.EMPTY'), 'PRICE' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRICE'), 'PRICE_FORMAT' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRICE_FORMAT'), 'PRICE_OLD' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRICE_OLD'), 'PRICE_OLD_FORMAT' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRICE_OLD_FORMAT'), 'DISCOUNT_PRICE' => $this->getMessage('TEMPLATE_PLACEHOLDER.DISCOUNT_PRICE'), 'DISCOUNT_PRICE_FORMAT' => $this->getMessage('TEMPLATE_PLACEHOLDER.DISCOUNT_PRICE_FORMAT'), 'DISCOUNT_PERCENT' => $this->getMessage('TEMPLATE_PLACEHOLDER.DISCOUNT_PERCENT'), 'DISCOUNT_PERCENT_FORMAT' => $this->getMessage('TEMPLATE_PLACEHOLDER.DISCOUNT_PERCENT_FORMAT'), 'PRODUCT_DETAIL_PAGE_URL' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_DETAIL_PAGE_URL')];
        if ($this->isInstalledCatalogModule()) {
            $arResult['CATALOG_QUANTITY'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_QUANTITY');
            $arResult['CATALOG_AVAILABLE'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_AVAILABLE');
            $arResult['CATALOG_WEIGHT'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_WEIGHT');
            $arResult['CATALOG_WIDTH'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_WIDTH');
            $arResult['CATALOG_HEIGHT'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_HEIGHT');
            $arResult['CATALOG_LENGTH'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_LENGTH');
            $arResult['CATALOG_MEASURE_NAME'] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_MEASURE_NAME');
            // ������ ---
            $arStores = $this->getStoreList();
            foreach ($arStores as $storeId => $storeName) {
                $arResult['CATALOG_STORE_' . $storeId] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_STORE', ['#SKLAD#' => $storeName]);
            }
            // ���� -----
            $arKeys = ['CURRENCY', 'PRICE', 'PRICE_OLD', 'DISCOUNT_PRICE', 'DISCOUNT_PERCENT', 'PRICE_FORMAT', 'PRICE_OLD_FORMAT', 'DISCOUNT_PRICE_FORMAT', 'DISCOUNT_PERCENT_FORMAT'];
            $arPrices = $this->getPriceList();
            foreach ($arPrices as $priceId => $priceName) {
                foreach ($arKeys as $key) {
                    $arResult['CATALOG_GROUP_' . $priceId . '_' . $key] = $this->getMessage('TEMPLATE_PLACEHOLDER.CATALOG_GROUP_' . $key, ['#PRICE#' => $priceName]);
                }
            }
        }
        return $arResult;
    }
    /**
     * �����������, ���������� ��� �������� ��� ������
     * 
     * @return array
     */
    public function getProductTemplatePlaceholderList()
    {
        return ['PRODUCT_ID' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_ID'), 'PRODUCT_XML_ID' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_XML_ID'), 'PRODUCT_NAME' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_NAME'), 'PRODUCT_CODE' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_CODE'), 'PRODUCT_PREVIEW_TEXT' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_PREVIEW_TEXT'), 'PRODUCT_DETAIL_TEXT' => $this->getMessage('TEMPLATE_PLACEHOLDER.PRODUCT_DETAIL_TEXT')];
    }
    /**
     * �����������, ���������� ��� �������� ��� ��������� �����������
     * 
     * @return array
     */
    public function getOfferTemplatePlaceholderList()
    {
        return ['OFFER_ID' => $this->getMessage('TEMPLATE_PLACEHOLDER.OFFER_ID'), 'OFFER_XML_ID' => $this->getMessage('TEMPLATE_PLACEHOLDER.OFFER_XML_ID'), 'OFFER_NAME' => $this->getMessage('TEMPLATE_PLACEHOLDER.OFFER_NAME'), 'OFFER_CODE' => $this->getMessage('TEMPLATE_PLACEHOLDER.OFFER_CODE'), 'OFFER_PREVIEW_TEXT' => $this->getMessage('TEMPLATE_PLACEHOLDER.OFFER_PREVIEW_TEXT'), 'OFFER_DETAIL_TEXT' => $this->getMessage('TEMPLATE_PLACEHOLDER.OFFER_DETAIL_TEXT')];
    }
    /**
     * ������ ������ �����
     * 
     * @return array
     */
    public function getCurrencyList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            if (!$this->isInstalledCurrencyModule()) {
                return $arReturn;
            }
            try {
                // �������� ��������� ---
                $dbr = $this->currency()->getList(['order' => ['FULL_NAME' => 'ASC'], 'select' => ['*', 'FULL_NAME' => 'CURRENT_LANG_FORMAT.FULL_NAME']]);
                while ($ar = $dbr->fetch()) {
                    $arReturn[$ar['CURRENCY']] = $ar['FULL_NAME'] . ' [' . $ar['CURRENCY'] . ']';
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $arReturn;
    }
    /**
     * �������� ����� ��� js
     * 
     * @return array
     */
    public function getCurrencyForJs()
    {
        $arReturn = [];
        return $this->listToJsFormat($this->getCurrencyList());
    }
    /**
     * ������ ������ ��� {id:name, ...}
     * 
     * @return array
     */
    public function getPriceList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            if (!$this->isInstalledCatalogModule()) {
                return $arReturn;
            }
            try {
                // �������� ��������� ---
                $dbr = $this->catalogPriceGroup()->getList(['NAME' => 'ASC']);
                while ($ar = $dbr->fetch()) {
                    $arReturn[$ar['ID']] = $ar['NAME_LANG'] . ' (' . $ar['NAME'] . ') [' . $ar['ID'] . ']';
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $arReturn;
    }
    /**
     * �������� ����� ��� js
     * 
     * @return array
     */
    public function getPricesForJs()
    {
        return $this->listToJsFormat($this->getPriceList());
    }
    /**
     * ������ ������ ����� ������������� {id:name, ...}
     * 
     * @return array
     */
    public function getUserGroupList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            $dbr = \Bitrix\Main\GroupTable::getList(['order' => ['NAME' => 'ASC']]);
            while ($ar = $dbr->fetch()) {
                $arReturn[$ar['ID']] = $ar['NAME'] . ' (' . $ar['STRING_ID'] . ') [' . $ar['ID'] . ']';
            }
        }
        return $arReturn;
    }
    /**
     * �������� ����� ��� js
     * 
     * @return array
     */
    public function getUserGroupListForJs()
    {
        return $this->listToJsFormat($this->getUserGroupList());
    }
    /**
     * ������ ������ �������, {id:name, ...}
     * 
     * @return array
     */
    public function getStoreList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            if (!$this->isInstalledCatalogModule() || \is_null($this->catalogStore())) {
                return $arReturn;
            }
            try {
                // �������� ��������� ---
                $dbr = $this->catalogStore()->getList(['NAME' => 'ASC'], ['ACTIVE' => 'Y'], \false, \false, ['TITLE', 'ID']);
                while ($ar = $dbr->fetch()) {
                    $arReturn[$ar['ID']] = $ar['TITLE'] . ' [' . $ar['ID'] . ']';
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $arReturn;
    }
    /**
     * ������ ������ ����������
     * 
     * @return array
     */
    public function getIblockItems()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            // �������� ��������� ---
            $dbrIblock = $this->iblock()->GetList(['NAME' => 'ASC']);
            while ($arIblock = $dbrIblock->fetch()) {
                $arReturn[$arIblock['ID']] = [
                    'ID' => $arIblock['ID'],
                    'CODE' => $arIblock['CODE'],
                    'SITE_ID' => $arIblock['LID'],
                    'NAME' => $arIblock['NAME'] . ' [' . $arIblock['ID'] . ']',
                    'IS_CATALOG' => \false,
                    //�������� �� ���������
                    'OFFER_IBLOCK_ID' => \false,
                    //id  ��������� � ��������� �������������
                    'LINK_PROPERTY_ID' => \false,
                ];
                // �������� �������� � �������
                if ($this->isInstalledCatalogModule()) {
                    $arInfo = \CCatalogSKU::GetInfoByProductIBlock($arIblock['ID']);
                    if (\is_array($arInfo)) {
                        $arReturn[$arIblock['ID']]['IS_CATALOG'] = \true;
                        $arReturn[$arIblock['ID']]['OFFER_IBLOCK_ID'] = $arInfo['IBLOCK_ID'];
                        $arReturn[$arIblock['ID']]['LINK_PROPERTY_ID'] = $arInfo['SKU_PROPERTY_ID'];
                    }
                }
            }
        }
        return $arReturn;
    }
    /**
     * �������� ���������� ��� js
     * 
     * @return array
     */
    public function getIblockForJs()
    {
        return $this->toJsDataFormat(\array_values($this->getIblockItems()));
    }
    /**
     * ������ �������� ��������� �� ��� ��������������
     * 
     * @param $id
     * @return array|null
     */
    public function getIblockById($id)
    {
        $id = \intval($id);
        $arItems = $this->getIblockItems();
        if (isset($arItems[$id])) {
            return $arItems[$id];
        }
        return \null;
    }
    /**
     * �������� ������� ��������� ��� js
     * 
     * @return array
     */
    public function getIblockPropertyForJs()
    {
        $arReturn = [];
        // �������� ��������� ---
        $dbr = $this->iblockProperty()->GetList(['NAME' => 'ASC']);
        while ($ar = $dbr->fetch()) {
            $arReturn[$ar['ID']] = ['ID' => $ar['ID'], 'CODE' => $ar['CODE'], 'NAME' => $ar['NAME'] . ' (' . $ar['CODE'] . ') [' . $ar['ID'] . ']', 'IBLOCK_ID' => $ar['IBLOCK_ID'], 'PROPERTY_TYPE' => $ar['PROPERTY_TYPE'], 'USER_TYPE' => $ar['USER_TYPE']];
        }
        $arReturn = \array_values($arReturn);
        return $this->toJsDataFormat($arReturn);
    }
    /**
     * ����������� ������ ������ �� ���� {key: value, ...} � [{id:key, name:value},...]
     * ��� ���� �������� ����� � ������ camelCase
     * 
     * @param $arList - ������ ����������� {key:value, ...}
     * @return array
     */
    public function getPreparedListForJs($arList)
    {
        $arReturn = [];
        foreach ($arList as $key => $value) {
            $arReturn[] = ['id' => $key, 'name' => $value];
        }
        return $this->toJsDataFormat($arReturn);
    }
    /**
     * ���������� ajax �������� ��� ���������������� �������
     * 
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function adminPageAjaxHandler()
    {
        $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();
        try {
            $app = \Bitrix\Main\Application::getInstance();
            $req = $app->getContext()->getRequest();
            // ��� ��� ���� ������ ���������� � ��������� ����� ��� ajax ��������,
            // � ������� ��� ���������� ����������� ������ ��� ������� ���������
            // ����� �������� ��� ������ �� �����
            // �������� ���� �������
            if (!$this->base()->canActionRight('R')) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('AJAX.ACCESS_DENIED'), 'ACCESS_DENIED');
            }
            switch ($req->get('method')) {
                // ������ ������ ��������� ��� �������� ���� ������
                case 'getIblockPropertyEnumList':
                    if (!(int) $req->get('propertyId')) {
                        throw new \VKapi\Market\Exception\BaseException($this->getMessage('AJAX.ERROR.NOT_FOUND_PROPERTY_ID'), 'NOT_FOUND_PROPERTY_ID');
                    }
                    $arItems = [];
                    if ($this->isInstalledIblockModule()) {
                        $dbrEnum = \CIBlockPropertyEnum::GetList(['VALUE' => 'ASC'], ['PROPERTY_ID' => (int) $req->get('propertyId')]);
                        while ($arEnum = $dbrEnum->fetch()) {
                            $arItems[$arEnum['ID']] = $arEnum['VALUE'] . ' [' . $arEnum['ID'] . ']';
                        }
                    }
                    $oJsonResponse->setResponse(['items' => $this->base()->getToLowerKeys(\VKapi\Market\Condition\Control\Select::prepareValues($arItems))]);
                    break;
                // ������ ������ ��������� ��� �������� ���� ����������
                case 'getHighloadBlockValueList':
                    $tableName = (string) $req->get('tableName');
                    if (!$tableName) {
                        throw new \VKapi\Market\Exception\BaseException($this->getMessage('AJAX.ERROR.NOT_FOUND_TABLE_NAME'), 'NOT_FOUND_TABLE_NAME');
                    }
                    $arItems = [];
                    if ($this->isInstalledHighloadBlockModule()) {
                        $table = $this->getHighloadBlockClassByTableName($tableName);
                        try {
                            $dbrRows = $table->getList(['order' => ['UF_NAME' => 'asc']]);
                            while ($arRow = $dbrRows->fetch()) {
                                $key = $arRow['UF_XML_ID'] ?? $arRow['ID'];
                                $arItems[$key] = ($arRow['UF_NAME'] ?? '') . ' [' . $arRow['ID'] . ']';
                            }
                        } catch (\Exception $e) {
                            throw new \VKapi\Market\Exception\BaseException($e->getMessage(), $e->getCode(), [], $e);
                        }
                    }
                    $oJsonResponse->setResponse(['items' => $this->base()->getToLowerKeys(\VKapi\Market\Condition\Control\Select::prepareValues($arItems))]);
                    break;
                case 'previewInVk':
                    $oExportItem = new \VKapi\Market\Export\Item();
                    $oGoodExport = new \VKapi\Market\Good\Export($oExportItem);
                    $result = $oGoodExport->getPreviewForVk(!!$req->getPost('isOffer'));
                    if (!$result->isSuccess()) {
                        $oJsonResponse->setErrorFromResult($result);
                        break;
                    }
                    $oJsonResponse->setResponse($this->base()->getToLowerKeys($result->getData()));
                    break;
                default:
                    throw new \VKapi\Market\Exception\BaseException($this->getMessage('AJAX.METHOD_NOT_FOUND'), 'METHOD_NOT_FOUND');
            }
        } catch (\Throwable $ex) {
            $oJsonResponse->setException($ex);
        }
        $oJsonResponse->output();
    }
    /**
     * ��������� ������ �� ���������� �������� � ���.
     * 
     * @param int $sec
     */
    public function setTimeout($sec = 45)
    {
        $this->timeout = \intval($sec);
    }
    /**
     * �������� �������� �� ����� �� �������
     * 
     * @return bool
     */
    public function isTimeout()
    {
        return \time() > $this->timestart + $this->timeout || $this->isOverMemoryLimit();
    }
    /**
     * �������� ���� �� ��� ����� ��� ���������� ��������
     * 
     * @return bool
     */
    public function hasTime()
    {
        return !$this->isTimeout();
    }
    /**
     * ������� �� ��������� �� ����� �� ������
     * 
     * @return bool
     */
    public function isOverMemoryLimit()
    {
        return \memory_get_usage() > $this->memoryLimit;
    }
    /**
     * �������� ������������ �� � ���������� ����� �� �������
     */
    public function checkTime()
    {
        if ($this->isTimeout()) {
            throw new \VKapi\Market\Exception\TimeoutException();
        }
    }
    /**
     * ������ ������� ���������� �� ������ ������ � ����������
     * 
     * @param $state - ���������, {count:0, offset:0, ...}
     * @return false|float|int
     */
    public function getPercentByState($state)
    {
        return $this->getPercentByOffsetCount($state['offset'], $state['count']);
    }
    /**
     * ������ ������� ����������
     * 
     * @param $offset
     * @param $count
     * @return false|float|int
     */
    public function getPercentByOffsetCount($offset, $count)
    {
        if ($offset && $count) {
            return \floor(100 * ($offset / $count));
        }
        return 0;
    }
    /**
     * ������ ��� ������ �������� ������ � ��
     * 
     * @param $exportId
     * @return \VKapi\Market\Result
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function agentExportToVk($exportId)
    {
        $exportId = \intval($exportId);
        $result = new \VKapi\Market\Result();
        $oState = new \VKapi\Market\State('auto_' . $exportId);
        $oExportItem = new \VKapi\Market\Export\Item($exportId);
        $oExportItem->load();
        $oAlbumExport = new \VKapi\Market\Album\Export($oExportItem);
        $oGoodExport = new \VKapi\Market\Good\Export($oExportItem);
        $oPropertyExport = new \VKapi\Market\Property\Export($oExportItem);
        $arSteps = [1 => ['name' => $this->getMessage('EXPORT.STEP1'), 'percent' => 0, 'items' => []], 2 => ['name' => $this->getMessage('EXPORT.STEP2'), 'percent' => 0, 'items' => []], 3 => ['name' => $this->getMessage('EXPORT.STEP3'), 'percent' => 0, 'items' => []]];
        // ���������� ������� ���������
        $stateData = \array_merge(['step' => 1, 'complete' => \false, 'steps' => $arSteps], $oState->get());
        // ����� ������ � �������� ��������
        if ($stateData['complete']) {
            $stateData = ['step' => 1, 'complete' => \false, 'steps' => $arSteps, 'dateTimeStopFormat' => '', 'dateTimeStartFormat' => \date('d.m.Y H:i:s')];
            $oAlbumExport->state()->clean();
            $oGoodExport->state()->clean();
        }
        try {
            // ������������ ��������������� �������� --
            if ($stateData['step'] == 1) {
                $resultExportAlbum = $oAlbumExport->exportRun();
                $resultExportAlbumData = $resultExportAlbum->getData();
                if (isset($resultExportAlbumData['steps'])) {
                    $stateData['steps'][1]['percent'] = \round(\array_sum(\array_column($resultExportAlbumData['steps'], 'percent')) / \count($resultExportAlbumData['steps']));
                    $stateData['steps'][1]['items'] = $resultExportAlbumData['steps'];
                    if ($resultExportAlbumData['complete']) {
                        $stateData['step']++;
                    }
                }
                if (!$resultExportAlbum->isSuccess()) {
                    $resultExportAlbum->getFirstError()->setMore('state', $stateData);
                    $result->setError($resultExportAlbum->getFirstError());
                }
            } elseif ($stateData['step'] == 2) {
                $oPropertyExport->exportRun();
                $stateData['steps'][2]['items'] = $oPropertyExport->getSteps();
                $stateData['steps'][2]['percent'] = $oPropertyExport->getPercent();
                if ($oPropertyExport->isComplete()) {
                    $stateData['step']++;
                }
            } elseif ($stateData['step'] == 3) {
                $resultExportGoods = $oGoodExport->exportRun();
                $resultExportGoodsData = $resultExportGoods->getData();
                if (isset($resultExportGoodsData['steps'])) {
                    $stateData['steps'][3]['percent'] = \round(\array_sum(\array_column($resultExportGoodsData['steps'], 'percent')) / \count($resultExportGoodsData['steps']));
                    $stateData['steps'][3]['items'] = $resultExportGoodsData['steps'];
                    if ($resultExportGoodsData['complete']) {
                        // �������� ���������
                        $stateData['complete'] = \true;
                        $stateData['dateTimeStopFormat'] = \date('d.m.Y H:i:s');
                    }
                }
                if (!$resultExportGoods->isSuccess()) {
                    $resultExportGoods->getFirstError()->setMore('state', $stateData);
                    $result->setError($resultExportGoods->getFirstError());
                }
            }
        } catch (\BaseException $ex) {
        }
        $result->setData('state', $stateData);
        // ��������� ���������
        $oState->set($stateData)->save();
        if ($ex instanceof \Throwable) {
            throw $ex;
        }
        return $result;
    }
    /**
     * ������ ��������� ������� �� ������
     * 
     * @param int[] $arList
     * @param $lastValue
     * @return int|null
     */
    public function getNextItem($arList, $lastValue)
    {
        if (empty($arList)) {
            return \null;
        }
        $pos = \array_search($lastValue, $arList);
        if ($pos === \false) {
            return \reset($arList);
        }
        $arListOrder = \array_merge(\array_slice($arList, $pos), \array_slice($arList, 0, $pos));
        return \next($arListOrder);
    }
    /**
     * ������ ����� �������������� ��� json
     * @param $str
     * @return string|string[]|null
     */
    public function jsonStringReplace($str)
    {
        $str = \str_replace(['\\', "'", '"', "\r\n", "\n"], ['\\\\', "\\'", '\\"', '\\n', '\\n'], $str);
        // ���������� \ �� �����
        $str = \preg_replace('/([\\\\]+)$/', '\\1\\1', $str);
        return $str;
    }
    /**
     * ����������� ������ � ����� ��� �������� � �� �������
     * 
     * @param $arFields
     * @return string
     */
    public function toJsonString($arFields)
    {
        $arReturn = [];
        foreach ($arFields as $key => $value) {
            $valueString = '';
            if (\is_array($value)) {
                $valueString = $this->toJsonString($value);
            } elseif (\is_int($value) || \is_float($value)) {
                $valueString = $value;
            } else {
                $valueString = '"' . $this->jsonStringReplace($value) . '"';
            }
            if (\is_numeric($key)) {
                $arReturn[] = $valueString;
            } else {
                $arReturn[] = '"' . \str_replace('"', '\\"', $key) . '":' . $valueString;
            }
        }
        return $this->isAssocArray($arFields) ? '{
        ' . \implode(',', $arReturn) . '}' : '[' . \implode(',', $arReturn) . ']';
    }
    /**
     * ������ ������ � json ������� ��� VK � ��������� cp1251
     * 
     * @param $arFields
     * @return array|bool|\SplFixedArray|string
     */
    public function toVkJsonString($arFields)
    {
        $string = $this->toJsonString($arFields);
        if ($this->base()->isUtf()) {
            return $string;
        }
        return \Bitrix\Main\Text\Encoding::convertEncoding($string, 'WINDOWS - 1251', 'UTF - 8');
    }
    public function isAssocArray($array)
    {
        $keys = \array_keys($array);
        return \array_keys($keys) !== $keys;
    }
    /**
     * �������� ���������� ������� ������ ��� ������
     * @param $exportId
     * @param $elementId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function checkExportConditionsForElementId($exportId, $elementId)
    {
        $exportId = \intval($exportId);
        $productId = \intval($elementId);
        $offerId = \intval($elementId);
        $oExportItem = new \VKapi\Market\Export\Item($exportId);
        $oExportItem->load();
        // ����� ��������
        $arExport = $oExportItem->getData();
        $oCondition = new \VKapi\Market\Condition\Manager();
        // ��������� ������� �� ����� �������� ������������
        $arElement = [];
        $bOffer = \false;
        if ($this->isInstalledCatalogModule()) {
            // ��������� � ������ ������� �������� �����������
            $offersExist = \CCatalogSKU::getExistOffers($productId);
            // �������� ������� ��������
            $arHasParent = \CCatalogSku::GetProductInfo($productId);
            // ��������� ����������
            if (\is_array($offersExist) && isset($offersExist[$productId]) && $offersExist[$productId] === \true) {
                // �������� ������
                $bOffer = \true;
                $offerId = 0;
                $arElementAll = $oCondition->getPreparedElementFieldsById([$productId], \false, $oExportItem->getProductPriceUserGroupIds(), $oExportItem->getSiteId());
                if (\is_array($arElementAll) && isset($arElementAll[$productId])) {
                    $arElement = $arElementAll[$productId];
                }
                // ������� ������ ���������� ����
                $resultOffers = \CCatalogSKU::getOffersList($productId);
                if (\is_array($resultOffers) && isset($resultOffers[$productId]) && !empty($resultOffers[$productId])) {
                    $arOfferIdList = \array_keys($resultOffers[$productId]);
                    $offerId = $arOfferIdList[0];
                }
            } elseif (\is_array($arHasParent) && isset($arHasParent['ID'])) {
                $productId = $arHasParent['ID'];
                $bOffer = \true;
                $arElementAll = $oCondition->getPreparedElementFieldsById([$productId], \false, $oExportItem->getProductPriceUserGroupIds(), $oExportItem->getSiteId());
                if (\is_array($arElementAll) && isset($arElementAll[$productId])) {
                    $arElement = $arElementAll[$productId];
                }
            }
        }
        // ����� �����
        $arOffer = \null;
        $arOfferSource = \null;
        $arOfferAll = $oCondition->getPreparedElementFieldsById([$offerId], $bOffer, $bOffer ? $oExportItem->getOfferPriceUserGroupIds() : $oExportItem->getProductPriceUserGroupIds(), $oExportItem->getSiteId());
        if (isset($arOfferAll[$offerId])) {
            $arOfferSource = $arOfferAll[$offerId];
            $arOffer = $arOfferSource;
            if ($bOffer && !empty($arElement)) {
                $arOffer = \array_replace($arElement, $arOffer);
            }
        }
        $arReturn = ['productId' => $productId, 'offerId' => $offerId, 'valid' => \false, 'condition' => \null, 'export' => $arExport, 'product' => $arElement, 'offer' => $arOfferSource, 'arItem' => $arOffer];
        if (!\is_null($arOffer)) {
            $arReturn['valid'] = $oCondition->isMatchCondition($arExport['PARAMS']['CONDITIONS'], $arOffer);
            $arReturn['condition'] = $oCondition->getEval($arExport['PARAMS']['CONDITIONS']);
        }
        return $arReturn;
    }
    /**
     * �������� ���������� �� ������ ��������-��������
     * 
     * @return bool
     */
    public function isInstalledSaleModule()
    {
        static $bResult;
        if (!isset($bResult)) {
            $bResult = \false;
            try {
                if (\Bitrix\Main\Loader::includeModule('sale')) {
                    $bResult = \true;
                }
            } catch (\Exception $e) {
                // ..
            }
        }
        return $bResult;
    }
    public function getSaleStatusList()
    {
        static $arReturn;
        if (empty($arReturn)) {
            $arReturn = [];
            if (!$this->isInstalledSaleModule()) {
                return $arReturn;
            }
            $registry = \Bitrix\Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
            $statusClaccName = $registry->getOrderStatusClassName();
            $dbrSaleStatus = $statusClaccName::getList(['order' => ['NAME' => 'ASC'], 'filter' => ['=TYPE' => $statusClaccName::TYPE, '=Bitrix\\Sale\\Internals\\StatusLangTable:STATUS.LID' => \LANGUAGE_ID], 'select' => ['ID', 'NAME' => 'Bitrix\\Sale\\Internals\\StatusLangTable:STATUS.NAME']]);
            while ($arSaleStatus = $dbrSaleStatus->Fetch()) {
                $arReturn[$arSaleStatus['ID']] = $arSaleStatus['NAME'];
            }
            // $deliveryStatusClaccName = $registry->getDeliveryStatusClassName();
            // $dbrShipmentStatus = $deliveryStatusClaccName::getList([
            // 'select' => ['ID', 'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME'],
            // 'filter' => [
            // '=TYPE' => $deliveryStatusClaccName::TYPE,
            // '=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID
            // ],
            // 'order' => ['NAME' => 'ASC'],
            // ]);
            // while ($arShipmentStatusItem = $dbrShipmentStatus->fetch()) {
            // $arReturn[$arShipmentStatusItem['ID']] = $arShipmentStatusItem['NAME'];
            // }
        }
        return $arReturn;
    }
    /**
     * ������ ������ � ���������� �������� ������ ��� ������������� � SelectBox
     * @return array
     */
    public function getSaleStatusSelect()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = ['REFERENCE' => [$this->getMessage('NOT_SELECTED')], 'REFERENCE_ID' => ['']];
            $ar = $this->getSaleStatusList();
            foreach ($ar as $id => $name) {
                $arReturn['REFERENCE'][] = $name . ' [' . $id . ']';
                $arReturn['REFERENCE_ID'][] = $id;
            }
        }
        return $arReturn;
    }
    /**
     * ������ ����� ������������
     * @return array
     */
    public function getSalePersonaleTypeList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            if (!$this->isInstalledSaleModule()) {
                return $arReturn;
            }
            $registry = \Bitrix\Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
            $personalTypeClassName = $registry->getPersonTypeClassName();
            $dbr = $personalTypeClassName::getList(['order' => ['NAME' => 'ASC']]);
            while ($ar = $dbr->fetch()) {
                $arReturn[$ar['ID']] = $ar['NAME'] . ' [' . $ar['ID'] . '] (' . $ar['LID'] . ')';
            }
        }
        return $arReturn;
    }
    /**
     * ������ ����� ������������
     * @return array
     */
    public function getSalePersonaleTypeSelect()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = ['REFERENCE' => [$this->getMessage('NOT_SELECTED')], 'REFERENCE_ID' => ['']];
            $ar = $this->getSalePersonaleTypeList();
            foreach ($ar as $id => $name) {
                $arReturn['REFERENCE'][] = $name;
                $arReturn['REFERENCE_ID'][] = $id;
            }
        }
        return $arReturn;
    }
    /**
     * ������ �������� ������
     * @return array
     */
    public function getSalePaymentIdsSelect()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = ['REFERENCE' => [$this->getMessage('NOT_SELECTED')], 'REFERENCE_ID' => ['']];
            if (!$this->isInstalledSaleModule()) {
                return $arReturn;
            }
            $dbr = \Bitrix\Sale\PaySystem\Manager::getList(['order' => ['NAME' => 'ASC']]);
            while ($ar = $dbr->fetch()) {
                $arReturn['REFERENCE'][] = $ar['NAME'] . ' [' . $ar['ID'] . ']';
                $arReturn['REFERENCE_ID'][] = $ar['ID'];
            }
        }
        return $arReturn;
    }
    /**
     * ������ �������� ������
     * @return array
     */
    public function getSaleDeliveryIdsSelect()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = ['REFERENCE' => [$this->getMessage('NOT_SELECTED')], 'REFERENCE_ID' => ['']];
            if (!$this->isInstalledSaleModule()) {
                return $arReturn;
            }
            $array = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
            $arParentIds = \array_column($array, 'PARENT_ID', 'PARENT_ID');
            $arParentIds = \array_diff(\array_map('intval', \array_keys($arParentIds)), [0]);
            $arParentIds = \array_values(\array_unique($arParentIds));
            $arItems = [];
            foreach ($array as $item) {
                if (\in_array($item['ID'], $arParentIds)) {
                    continue;
                }
                $name = '';
                if (!empty($item['PARENT_ID'])) {
                    $name .= $array[$item['PARENT_ID']]['NAME'] . ' [' . $item['PARENT_ID'] . ']';
                    $name .= ' - ';
                    $name .= $item['NAME'];
                    $name .= ' [' . $item['ID'] . ']';
                } else {
                    $name .= $item['NAME'] . ' [' . $item['ID'] . '] ';
                }
                $arItems[$item['ID']] = $name;
            }
            \asort($arItems);
            $arReturn['REFERENCE'] = \array_merge($arReturn['REFERENCE'], \array_values($arItems));
            $arReturn['REFERENCE_ID'] = \array_merge($arReturn['REFERENCE_ID'], \array_keys($arItems));
        }
        return $arReturn;
    }
    /**
     * ������ ������� ������
     * @return array
     */
    public function getSalePropertiesSelect()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = ['REFERENCE' => [$this->getMessage('NOT_SELECTED')], 'REFERENCE_ID' => ['']];
            if (!$this->isInstalledSaleModule()) {
                return $arReturn;
            }
            $arPersonalTypes = $this->getSalePersonaleTypeList();
            $registry = \Bitrix\Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
            /**
             * @var $propertyClassName \Bitrix\Sale\Property
             */
            $propertyClassName = $registry->getPropertyClassName();
            $dbrOrderProp = $propertyClassName::getList(['order' => ["NAME" => "ASC", "PERSON_TYPE_ID" => "ASC"]]);
            while ($arOrderProp = $dbrOrderProp->fetch()) {
                if (!\in_array($arOrderProp['TYPE'], ['STRING', 'TEXT', 'NUMBER'])) {
                    continue;
                }
                $arReturn['REFERENCE'][] = \sprintf(' % s [%s] (%s) - %s', $arOrderProp['NAME'], $arOrderProp['ID'], $arOrderProp['CODE'], $arPersonalTypes[$arOrderProp['PERSON_TYPE_ID']]);
                $arReturn['REFERENCE_ID'][] = $arOrderProp['ID'];
            }
        }
        return $arReturn;
    }
}
?>