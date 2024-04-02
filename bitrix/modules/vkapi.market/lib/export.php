<?php

namespace VKapi\Market;

use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Class Export - подготавливает раздлинчые данные для вывода и использования,
 * списки свойств, списки шаблонов автозамен, и тп
 * Выводит блок для ручного экмпорта
 * 
 * @package VKapi\Market
 */
class Export
{
    /**
     * @var Export
     */
    private static $instance = null;
    protected $oOption = null;
    protected $oTable = null;
    protected $oElement = false;
    protected $oProperty = false;
    protected $oPropertyEnum = false;
    protected $arProps = [];
    public function __construct()
    {
        $this->oTable = new \VKapi\Market\ExportTable();
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $this->oIblock = new \CIBlock();
            $this->oProperty = new \CIBlockProperty();
            $this->oPropertyEnum = new \CIBlockPropertyEnum();
        }
    }
    /**
     * @return \VKapi\Market\Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * Вернет объект клсааса для рабоыт с таблицей
     * 
     * @return \VKapi\Market\ExportTable
     */
    public function getTable()
    {
        return $this->oTable;
    }
    /**
     * Языкозависимое сообщение
     * 
     * @param $name
     * @param array $arReplace
     * @return mixed|string
     */
    public function getMessage($name, $arReplace = [])
    {
        return \VKapi\Market\Manager::getInstance()->getMessage('EXPORT.' . $name, $arReplace);
    }
    /**
     * Вернет массив с описание выгрузок, для показа в html посредствам SelectBoxFromArray()
     * 
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getSelectList()
    {
        static $arExportList;
        if (!isset($arExportList)) {
            $arExportList = ['REFERENCE_ID' => [''], 'REFERENCE' => [$this->getMessage('NO_SELECT')]];
            $dbrExport = $this->getTable()->getList(['filter' => ['ACTIVE' => true, 'AUTO' => false]]);
            while ($arExport = $dbrExport->Fetch()) {
                $arExportList['REFERENCE_ID'][] = $arExport['ID'];
                $arExportList['REFERENCE'][] = '[' . $arExport['ID'] . '] ' . '[' . $arExport['GROUP_ID'] . '] ' . $arExport['GROUP_NAME'];
            }
        }
        return $arExportList;
    }
    /**
     * Вернет массив активных выгрузок для использвоания в js
     * 
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getItemsForJs()
    {
        static $arExportList;
        if (!isset($arExportList)) {
            $arExportList = [];
            $dbrExport = $this->getTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['ACTIVE' => true]]);
            while ($arExport = $dbrExport->Fetch()) {
                $arExportList[] = ['ID' => $arExport['ID'], 'NAME' => $arExport['NAME'], 'GROUP_ID' => $arExport['GROUP_ID'], 'GROUP_NAME' => $arExport["GROUP_NAME"]];
            }
        }
        return \VKapi\Market\Manager::getInstance()->toJsDataFormat($arExportList);
    }
    /**
     * Выводит блок ручного экспорта
     * 
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function showExportBlockByHand()
    {
        \CUtil::InitJSCore('jquery');
        $rand = \Bitrix\Main\Security\Random::getString(10);
        $container = 'vkapi-market-hand-export--' . $rand;
        // размещаем блок
        echo '<div class="vkapi-market-hand-export" id="' . $container . '"></div>';
        // формируем даныне
        $arData = ['items' => $this->getItemsForJs()];
        // размещаем js
        ?>
        <script type="text/javascript" class="vkapi-market-data">
            (function () {
                var params = <?php 
        echo \Bitrix\Main\Web\Json::encode($arData);
        ?>;
                window.VKapiMarketHandExportJs = window.VKapiMarketHandExportJs || {};
                window.VKapiMarketHandExportJs['<?php 
        echo $container;
        ?>'] = new VKapiMarketHandExport('<?php 
        echo $container;
        ?>', params);
            })();
        </script>
        <?php 
    }
    /**
     * Разбирает данные из пост запроса и формирует описание выгрузки
     * 
     * @return \VKapi\Market\Result
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function parseExportDataFromPostData()
    {
        $result = new \VKapi\Market\Result();
        $result->setData('FIELDS', []);
        $req = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $oManager = \VKapi\Market\Manager::getInstance();
        $oConnect = new \VKapi\Market\Connect();
        $oCondition = new \VKapi\Market\Condition\Manager();
        $oCondition->addCondition(new \VKapi\Market\Condition\Group());
        $oCondition->addCondition(new \VKapi\Market\Condition\CatalogField());
        $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementFieldBase());
        $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementField());
        $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementProperty());
        $oPhoto = new \VKapi\Market\Export\Photo();
        $arSiteList = $oManager->getSiteList();
        $arAccounts = $oConnect->getAccountList();
        $arIblockItems = $oManager->getIblockItems();
        // положение водного знака
        $arWatermarkPosition = $oPhoto->getWatermarkPositionSelectList();
        // прозрачность водного знака
        $arWatermarkOpactity = $oPhoto->getWatermarkOpacitySelectList();
        // коэффициент
        $arWatermarkKoef = $oPhoto->getWatermarkKoefficientSelectList();
        $arFields = [];
        // данные о существующей выгрузке -----
        $arResult = [];
        if ((int) $req->get('ID') > 0 || (int) $req->get('COPY_ID') > 0) {
            $dbrExport = $this->getTable()->getList(['filter' => ['ID' => (int) $req->get('ID') ?: (int) $req->get('COPY_ID')]]);
            if ($ar = $dbrExport->fetch()) {
                $arResult = $ar;
            }
        }
        do {
            $arFields['NAME'] = htmlspecialchars(trim($req->getPost('NAME')));
            if (strlen(trim($req->getPost('NAME'))) <= 0) {
                $result->addError($this->getMessage('ERROR.FILED.NAME'));
                break;
            }
            if (strlen(trim($req->getPost('SITE_ID'))) <= 0 || !isset($arSiteList[$req->getPost('SITE_ID')])) {
                $result->addError($this->getMessage('ERROR.FILED.SITE_ID'));
                break;
            }
            if (strlen(trim($req->getPost('ACCOUNT_ID'))) <= 0 || !isset($arAccounts[$req->getPost('ACCOUNT_ID')])) {
                $result->addError($this->getMessage('ERROR.FILED.ACCOUNT_ID'));
                break;
            }
            if (strlen(trim($req->getPost('GROUP_ID'))) <= 0) {
                $result->addError($this->getMessage('ERROR.FILED.GROUP_ID'));
                break;
            }
            if (strlen(trim($req->getPost('GROUP_NAME'))) <= 0) {
                $result->addError($this->getMessage('ERROR.FILED.GROUP_NAME'));
                break;
            }
            $arFields['SITE_ID'] = trim($req->getPost('SITE_ID'));
            $arFields['ACCOUNT_ID'] = trim($req->getPost('ACCOUNT_ID'));
            $arFields['GROUP_NAME'] = htmlspecialchars(trim($req->getPost('GROUP_NAME')));
            $arFields['GROUP_ID'] = trim($req->getPost('GROUP_ID'));
            $arFields['CATALOG_ID'] = intval($req->getPost('CATALOG_IBLOCK_ID'));
            if (!isset($arIblockItems[$arFields['CATALOG_ID']])) {
                $result->addError($this->getMessage('ERROR.FILED.CATALOG_IBLOCK_ID'));
                break;
            }
            if ((int) $req->getPost('CATEGORY_ID') <= 0) {
                $result->addError($this->getMessage('ERROR.FILED.CATEGORY_ID'));
                break;
            }
            $arFields['ACTIVE'] = $req->getPost('ACTIVE') == 'Y';
            $arFields['AUTO'] = $req->getPost('AUTO') == 'Y';
            $watermarkId = (int) $arResult['PARAMS']['WATERMARK'];
            // удаление водного знака
            if (!!$req->getPost('WATERMARK_del') && $req->getPost('WATERMARK_del') == 'Y' && (int) $arResult['PARAMS']['WATERMARK']) {
                \CFile::Delete((int) $arResult['PARAMS']['WATERMARK']);
                $watermarkId = 0;
            }
            // добавление нового знака ------------------------------------------------------
            if ((int) $_FILES['WATERMARK']['size'] && (int) $req->get('ID')) {
                $arType = ['image/png' => 'png', 'image/jpeg' => 'jpeg', 'image/jpg' => 'jpg', 'image/gif' => 'gif'];
                $arFile = $_FILES["WATERMARK"];
                $arFile["MODULE_ID"] = "vkapi.market";
                $arFile["name"] = "watermark" . (int) $req->get('ID') . '.' . $arType[$arFile['type']];
                $watermarkId = \CFile::SaveFile($arFile, "vkapi.market/wm/");
            }
            // подборки ---------------------------------------------------------------------
            $arFields['ALBUMS'] = $req->getPost('ALBUMS');
            // переворачиваем альбомы, чтобы в выгрузке они выглядили в том же порядке что и в вк 1,2,3,...
            $arFields['ALBUMS'] = (array) $arFields['ALBUMS'];
            $arFields['PARAMS'] = ['CONDITIONS' => $oCondition->parse(), 'CATEGORY_ID' => (int) $req->getPost('CATEGORY_ID'), 'CURRENCY_ID' => trim($req->getPost('CURRENCY_ID')), 'CATALOG_IBLOCK_ID' => trim($req->getPost('CATALOG_IBLOCK_ID')), 'OFFER_IBLOCK_ID' => trim($req->getPost('OFFER_IBLOCK_ID')), 'LINK_PROPERTY_ID' => trim($req->getPost('LINK_PROPERTY_ID')), 'DESCRIPTION_DELETE' => array_intersect((array) $req->getPost('DESCRIPTION_DELETE'), ['LINK', 'IMG', 'TABLE']), 'IMAGE_TO_SQUARE' => $req->getPost('IMAGE_TO_SQUARE') ? 1 : 0, 'DISABLED_OLD_ALBUM_DELETING' => $req->getPost('DISABLED_OLD_ALBUM_DELETING') ? 1 : 0, 'DISABLED_OLD_ITEM_DELETING' => $req->getPost('DISABLED_OLD_ITEM_DELETING') ? 1 : 0, 'EXTENDED_GOODS' => $req->getPost('EXTENDED_GOODS') ? 1 : 0, 'OFFER_COMBINE' => $req->getPost('OFFER_COMBINE') ? 1 : 0, 'PRODUCT_PRICE' => trim($req->getPost('PRODUCT_PRICE')), 'PRODUCT_PRICE_GROUPS' => empty((array) $req->getPost('PRODUCT_PRICE_GROUPS')) ? [2] : (array) $req->getPost('PRODUCT_PRICE_GROUPS'), 'PRODUCT_PRICE_OLD' => trim($req->getPost('PRODUCT_PRICE_OLD')), 'PRODUCT_NAME' => trim($req->getPost('PRODUCT_NAME')), 'PRODUCT_WEIGHT' => trim($req->getPost('PRODUCT_WEIGHT')), 'PRODUCT_LENGTH' => trim($req->getPost('PRODUCT_LENGTH')), 'PRODUCT_HEIGHT' => trim($req->getPost('PRODUCT_HEIGHT')), 'PRODUCT_WIDTH' => trim($req->getPost('PRODUCT_WIDTH')), 'PRODUCT_QUANTITY' => trim($req->getPost('PRODUCT_QUANTITY')), 'PRODUCT_PICTURE' => trim($req->getPost('PRODUCT_PICTURE')), 'PRODUCT_PICTURE_MORE' => trim($req->getPost('PRODUCT_PICTURE_MORE')), 'PRODUCT_SKU' => trim($req->getPost('PRODUCT_SKU')), 'OFFER_PRICE' => trim($req->getPost('OFFER_PRICE')), 'OFFER_PRICE_GROUPS' => empty((array) $req->getPost('OFFER_PRICE_GROUPS')) ? [2] : (array) $req->getPost('OFFER_PRICE_GROUPS'), 'OFFER_PRICE_OLD' => trim($req->getPost('OFFER_PRICE_OLD')), 'OFFER_NAME' => trim($req->getPost('OFFER_NAME')), 'OFFER_WEIGHT' => trim($req->getPost('OFFER_WEIGHT')), 'OFFER_LENGTH' => trim($req->getPost('OFFER_LENGTH')), 'OFFER_HEIGHT' => trim($req->getPost('OFFER_HEIGHT')), 'OFFER_WIDTH' => trim($req->getPost('OFFER_WIDTH')), 'OFFER_QUANTITY' => trim($req->getPost('OFFER_QUANTITY')), 'OFFER_PICTURE' => trim($req->getPost('OFFER_PICTURE')), 'OFFER_PICTURE_MORE' => trim($req->getPost('OFFER_PICTURE_MORE')), 'OFFER_SKU' => trim($req->getPost('OFFER_SKU')), 'PRODUCT_DEFAULT_TEXT' => $req->getPost('PRODUCT_DEFAULT_TEXT'), 'PRODUCT_TEMPLATE' => $req->getPost('PRODUCT_TEMPLATE'), 'OFFER_DEFAULT_TEXT' => $req->getPost('OFFER_DEFAULT_TEXT'), 'OFFER_TEMPLATE' => $req->getPost('OFFER_TEMPLATE'), 'OFFER_TEMPLATE_BEFORE' => $req->getPost('OFFER_TEMPLATE_BEFORE'), 'OFFER_TEMPLATE_AFTER' => $req->getPost('OFFER_TEMPLATE_AFTER'), 'PREVIEW_IN_VK_PRODUCT_ID' => intval($req->getPost('PREVIEW_IN_VK_PRODUCT_ID')), 'PREVIEW_IN_VK_PRODUCT_NAME' => trim($req->getPost('PREVIEW_IN_VK_PRODUCT_NAME')), 'PREVIEW_IN_VK_OFFER_ID' => intval($req->getPost('PREVIEW_IN_VK_OFFER_ID')), 'PREVIEW_IN_VK_OFFER_NAME' => trim($req->getPost('PREVIEW_IN_VK_OFFER_NAME')), 'WATERMARK' => $watermarkId, 'WATERMARK_POSITION' => in_array($req->getPost('WATERMARK_POSITION'), $arWatermarkPosition['REFERENCE_ID']) ? $req->getPost('WATERMARK_POSITION') : '', 'WATERMARK_OPACITY' => in_array($req->getPost('WATERMARK_OPACITY'), $arWatermarkOpactity['REFERENCE_ID']) ? $req->getPost('WATERMARK_OPACITY') : '', 'WATERMARK_COEFFICIENT' => in_array($req->getPost('WATERMARK_COEFFICIENT'), $arWatermarkKoef['REFERENCE_ID']) ? $req->getPost('WATERMARK_COEFFICIENT') : ''];
            if (is_array($req->getPost('PROPERTIES'))) {
                $arFields['PARAMS']['PROPERTIES'] = $req->getPost('PROPERTIES');
            }
            // проверяем какой катлог выбран
            $arIblock = $oManager->getIblockById($arFields['PARAMS']['CATALOG_IBLOCK_ID']);
            if ($arIblock) {
                if ($arIblock['IS_CATALOG']) {
                    $arFields['PARAMS']['OFFER_IBLOCK_ID'] = $arIblock['OFFER_IBLOCK_ID'];
                    $arFields['PARAMS']['LINK_PROPERTY_ID'] = $arIblock['LINK_PROPERTY_ID'];
                }
            } else {
                $arFields['PARAMS']['CATALOG_IBLOCK_ID'] = '';
                $arFields['PARAMS']['OFFER_IBLOCK_ID'] = '';
                $arFields['PARAMS']['LINK_PROPERTY_ID'] = '';
            }
            // //првоеряем заполненость полей описывающих вариант с торговыми предложенями ---------
            $arFieldsCheck = ['PRODUCT_PRICE', 'PRODUCT_NAME', 'PRODUCT_PICTURE'];
            foreach ($arFieldsCheck as $fieldCode) {
                if (empty($arFields['PARAMS'][$fieldCode])) {
                    $result->addError($this->getMessage('ERROR.FILED.' . $fieldCode));
                    break 2;
                }
            }
            if ($arFields['PARAMS']['LINK_PROPERTY_ID']) {
                $arFieldsCheck = ['OFFER_PRICE', 'OFFER_NAME', 'OFFER_PICTURE'];
                foreach ($arFieldsCheck as $fieldCode) {
                    if (empty($arFields['PARAMS'][$fieldCode])) {
                        $result->addError($this->getMessage('ERROR.FILED.' . $fieldCode));
                        break 2;
                    }
                }
            }
            $result->setData('FIELDS', $arFields);
        } while (false);
        return $result;
    }
}
?>