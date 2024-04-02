<?php

namespace VKapi\Market\Export;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
/**
 * ����� ��� ������� ������ � ���������� ���������, ��������� �����, ���������, ������� ������ ������� � ��
 */
class Item
{
    private $exportId = null;
    private $previewMode = false;
    private $arExportData = null;
    private $oConnection = null;
    public function __construct($exportId = 0)
    {
        $this->exportId = intval($exportId);
    }
    public function getId()
    {
        return (int) $this->exportId;
    }
    public function getMessage($name, $arReplace = [])
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.EXPORT.ITEM.' . $name, $arReplace);
    }
    public function load()
    {
        if (is_null($this->arExportData)) {
            $arExport = \VKapi\Market\ExportTable::getById($this->getId())->fetch();
            if (!$arExport) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_EXPORT_NOT_FOUND'), 'ERROR_EXPORT_NOT_FOUND');
            }
            $this->arExportData = $arExport;
        }
    }
    /**
 * ������ ��� ������ ����������� ��������
 * @return array|null
 */
    public function getData()
    {
        return $this->arExportData;
    }
    /**
 * ��������� ����� ����� ����� ����������� ��������, ������������
 * ��� ������������ ������������� �� �������� �������������� ��������
 */
    public function setData($arExportData)
    {
        $this->arExportData = $arExportData;
    }
    /**
 * ������ ������ ��� �������� � ��
 * @return \VKapi\Market\Connect
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \VKapi\Market\Exception\BaseException
 */
    public function connection()
    {
        if (is_null($this->oConnection)) {
            $this->oConnection = new \VKapi\Market\Connect();
            $result = $this->oConnection->initAccountId($this->getAccountId());
            if (!$result->isSuccess()) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_INIT_CONNECTION', ['#MSG#' => $result->getFirstErrorMessage(), '#CODE#' => $result->getFirstErrorCode()]), 'ERROR_INIT_CONNECTION');
            }
        }
        return $this->oConnection;
    }
    /**
 * ������ ������ ��� �������� � ��
 * @return \VKapi\Market\Connect
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \VKapi\Market\Exception\BaseException
 */
    public function checkApiAccess()
    {
        $result = $this->connection()->method('market.get', ['owner_id' => '-' . $this->getGroupId(), 'offset' => 0, 'count' => 1]);
        if (!$result->isSuccess()) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_CHECK_CONNECTION_ACCESS', ['#MSG#' => $result->getFirstErrorMessage(), '#CODE#' => $result->getFirstErrorCode()]), 'ERROR_CHECK_CONNECTION_ACCESS');
        }
    }
    /**
 * ��������� ������ �������������, ����� �� ����������� ��������
 * @param $flag
 */
    public function setPreviewMode($flag)
    {
        $this->previewMode = (bool) $flag;
    }
    /**
 * �������� ������ �������������, ����� �� ����������� ��������
 * @return bool
 */
    public function isPreviewMode()
    {
        return $this->previewMode;
    }
    /**
 * 
 * @return int
 */
    public function getGroupId()
    {
        return (int) $this->arExportData['GROUP_ID'];
    }
    /**
 * ������� ������������� ����� ��� �������� ������ ��������
 * @return string
 */
    public function getSiteId()
    {
        return $this->arExportData['SITE_ID'];
    }
    /**
 * ������� ����������� ������������� �������� �����������
 * @return int
 */
    public function getAccountId()
    {
        return (int) $this->arExportData['ACCOUNT_ID'];
    }
    /**
 * �����n ������������� ��������� �� �� ���������
 * @return int
 */
    public function getCategoryId()
    {
        return (int) $this->arExportData['PARAMS']['CATEGORY_ID'];
    }
    /**
 * ������� �������������� ��������� �������� ��� �������� � ��
 * @return int[]
 */
    public function getAlbumIds()
    {
        $sliced = array_slice((array) $this->arExportData['ALBUMS'], 0, 2);
        if (\CModule::IncludeModuleEx("vka" . "pi.market") === constant("MODULE_DE" . "M" . "O")) {
            return $sliced;
        }
        return $this->arExportData['ALBUMS'];
    }
    /**
 * ������� ����� ������ ��� �������������� ��������, �������� ��� ��������� ������ � ��
 * @return string[]
 */
    public function getDescriptionDeleteRules()
    {
        return (array) $this->arExportData['PARAMS']['DESCRIPTION_DELETE'];
    }
    /**
 * ������ ID ����� ������� �����
 * @return int
 */
    public function getWatermark()
    {
        return (int) $this->arExportData['PARAMS']['WATERMARK'];
    }
    /**
 * ������ ������� ������������ ������� ����� �� 0 �� 100
 * @return int
 */
    public function getWatermarkOpacity()
    {
        return max(0, min(100, (int) $this->arExportData['PARAMS']['WATERMARK_OPACITY']));
    }
    /**
 * ������ ����������� ��������������� 0.1, 0.2, ..., 1
 * @return string
 */
    public function getWatermarkCoefficient()
    {
        return $this->arExportData['PARAMS']['WATERMARK_COEFFICIENT'];
    }
    /**
 * ������ ������� ������� �����, tl(top left), tc(top center), ...
 * @return string
 */
    public function getWatermarkPosition()
    {
        return $this->arExportData['PARAMS']['WATERMARK_POSITION'];
    }
    /**
 * ������ �������������� ������ ��� ��������
 * @return array|mixed
 */
    public function getPropertyIds()
    {
        if (isset($this->arExportData['PARAMS']['PROPERTIES'])) {
            return $this->arExportData['PARAMS']['PROPERTIES'];
        }
        return [];
    }
    /**
 * ������ �������������� ������ ��� �������������
 * @return int
 */
    public function getProductIdForPreview()
    {
        return (int) $this->arExportData['PARAMS']['PREVIEW_IN_VK_PRODUCT_ID'];
    }
    /**
 * ������ �������������� �� ��� �������������
 * @return int
 */
    public function getOfferIdForPreview()
    {
        return (int) $this->arExportData['PARAMS']['PREVIEW_IN_VK_OFFER_ID'];
    }
    /**
 * ������ �������������� ��������� �������
 * @return int
 */
    public function getProductIblockId()
    {
        return (int) $this->arExportData['PARAMS']['CATALOG_IBLOCK_ID'];
    }
    /**
 * ������ �������������� ��������� ��
 * @return int
 */
    public function getOfferIblockId()
    {
        return (int) $this->arExportData['PARAMS']['OFFER_IBLOCK_ID'];
    }
    /**
 * ������ ������������� �������� �������� �������� �����������
 * 
 * @return int
 */
    public function getLinkPropertyId()
    {
        return (int) $this->arExportData['PARAMS']['LINK_PROPERTY_ID'];
    }
    /**
 * ������ ��� ������, �� ��������� ������ RUB
 * 
 * @return string
 */
    public function getCurrencyId()
    {
        return $this->arExportData['PARAMS']['CURRENCY_ID'] ?? 'RUB';
    }
    /**
 * ������ �������� ������� ������ �������
 * 
 * @return string
 */
    public function getConditions()
    {
        return $this->arExportData['PARAMS']['CONDITIONS'];
    }
    /**
 * ������ ���� �� �������� ����� �������� ������ ������
 * 
 * @return string
 */
    public function getProductName()
    {
        return $this->arExportData['PARAMS']['PRODUCT_NAME'];
    }
    /**
 * ������ ���� �� �������� ����� �������� ������
 * 
 * @return string
 */
    public function getProductPhoto()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PICTURE'];
    }
    /**
 * ������ ���� �� �������� ����� �������������� �������� ������
 * 
 * @return string
 */
    public function getProductMorePhoto()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PICTURE_MORE'];
    }
    /**
 * ������ ���� �� �������� ����� ���� ������
 * 
 * @return string
 */
    public function getProductPrice()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PRICE'];
    }
    /**
 * ������ ������ ����� ������������� ��� ������� ����������� ������ �� ����
 * 
 * @return int[]
 */
    public function getProductPriceUserGroupIds()
    {
        $ar = (array) $this->arExportData['PARAMS']['PRODUCT_PRICE_GROUPS'];
        if (empty($ar)) {
            $ar[] = 2;
        }
        return $ar;
    }
    /**
 * ������ ���� �� �������� ����� ������ ���� ������
 * 
 * @return string
 */
    public function getProductPriceOld()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PRICE_OLD'];
    }
    /**
 * ������ ���� �� �������� ����� ��� ������
 * 
 * @return string
 */
    public function getProductWeight()
    {
        return $this->arExportData['PARAMS']['PRODUCT_WEIGHT'];
    }
    /**
 * ������ ���� �� �������� ����� ���������� ������
 * 
 * @return string
 */
    public function getProductQuantity()
    {
        return $this->arExportData['PARAMS']['PRODUCT_QUANTITY'];
    }
    /**
 * ������ ���� �� �������� ����� ����� ������
 * 
 * @return string
 */
    public function getProductLength()
    {
        return $this->arExportData['PARAMS']['PRODUCT_LENGTH'];
    }
    /**
 * ������ ���� �� �������� ����� ������ ������
 * 
 * @return string
 */
    public function getProductHeight()
    {
        return $this->arExportData['PARAMS']['PRODUCT_HEIGHT'];
    }
    /**
 * ������ ���� �� �������� ����� ������ ������
 * 
 * @return string
 */
    public function getProductWidth()
    {
        return $this->arExportData['PARAMS']['PRODUCT_WIDTH'];
    }
    /**
 * ������ ���� �� �������� ����� ������� ������
 * 
 * @return string
 */
    public function getProductSku()
    {
        return $this->arExportData['PARAMS']['PRODUCT_SKU'];
    }
    /**
 * ������ ����� �� ��������� ��� �������� ������
 * 
 * @return string
 */
    public function getProductDefaultText()
    {
        return $this->arExportData['PARAMS']['PRODUCT_DEFAULT_TEXT'];
    }
    /**
 * ������ ������ �������� �������� ������
 * 
 * @return string
 */
    public function getProductTemplate()
    {
        return $this->arExportData['PARAMS']['PRODUCT_TEMPLATE'];
    }
    /**
 * ������ ���� �� �������� ����� �������� ��
 * 
 * @return string
 */
    public function getOfferName()
    {
        return $this->arExportData['PARAMS']['OFFER_NAME'];
    }
    /**
 * ������ ���� �� �������� ����� �������� ��������� �����������
 * 
 * @return string
 */
    public function getOfferPhoto()
    {
        return $this->arExportData['PARAMS']['OFFER_PICTURE'];
    }
    /**
 * ������ ���� �� �������� ����� �������������� �������� ��������� �����������
 * 
 * @return string
 */
    public function getOfferMorePhoto()
    {
        return $this->arExportData['PARAMS']['OFFER_PICTURE_MORE'];
    }
    /**
 * ������ ���� �� �������� ����� ���� ��������� �����������
 * 
 * @return string
 */
    public function getOfferPrice()
    {
        return $this->arExportData['PARAMS']['OFFER_PRICE'];
    }
    /**
 * ������ ������ ����� ������������� ��� ������� ����������� ������ �� ����
 * 
 * @return int[]
 */
    public function getOfferPriceUserGroupIds()
    {
        $ar = (array) $this->arExportData['PARAMS']['OFFER_PRICE_GROUPS'];
        if (empty($ar)) {
            $ar[] = 2;
        }
        return $ar;
    }
    /**
 * ������ ���� �� �������� ����� ������ ���� ��������� �����������
 * 
 * @return string
 */
    public function getOfferPriceOld()
    {
        return $this->arExportData['PARAMS']['OFFER_PRICE_OLD'];
    }
    /**
 * ������ ���� �� �������� ����� ��� ��
 * 
 * @return string
 */
    public function getOfferWeight()
    {
        return $this->arExportData['PARAMS']['OFFER_WEIGHT'];
    }
    /**
 * ������ ���� �� �������� ����� ����������
 * 
 * @return string
 */
    public function getOfferQuantity()
    {
        return $this->arExportData['PARAMS']['OFFER_QUANTITY'];
    }
    /**
 * ������ ���� �� �������� ����� ����� ��
 * 
 * @return string
 */
    public function getOfferLength()
    {
        return $this->arExportData['PARAMS']['OFFER_LENGTH'];
    }
    /**
 * ������ ���� �� �������� ����� ������ ��
 * 
 * @return string
 */
    public function getOfferHeight()
    {
        return $this->arExportData['PARAMS']['OFFER_HEIGHT'];
    }
    /**
 * ������ ���� �� �������� ����� ������ ��
 * 
 * @return string
 */
    public function getOfferWidth()
    {
        return $this->arExportData['PARAMS']['OFFER_WIDTH'];
    }
    /**
 * ������ ���� �� �������� ����� ������� ��
 * 
 * @return string
 */
    public function getOfferSku()
    {
        return $this->arExportData['PARAMS']['OFFER_SKU'];
    }
    /**
 * ������ ���� �� ��������� ��� �������� ��
 * 
 * @return string
 */
    public function getOfferDefaultText()
    {
        return $this->arExportData['PARAMS']['OFFER_DEFAULT_TEXT'];
    }
    /**
 * ������ ������ �������� ��
 * 
 * @return string
 */
    public function getOfferTemplate()
    {
        return (string) $this->arExportData['PARAMS']['OFFER_TEMPLATE'];
    }
    /**
 * ������ ������ �������� �� ����������� �����
 * 
 * @return string
 */
    public function getOfferTemplateBefore()
    {
        return (string) $this->arExportData['PARAMS']['OFFER_TEMPLATE_BEFORE'];
    }
    /**
 * ������ ������ �������� ����� ����������� �����
 * 
 * @return string
 */
    public function getOfferTemplateAfter()
    {
        return (string) $this->arExportData['PARAMS']['OFFER_TEMPLATE_AFTER'];
    }
    /**
 * �������� ��������� �� �������� ������ �������
 * @return bool
 */
    public function isDisabledOldItemDeleting()
    {
        return (bool) $this->arExportData['PARAMS']['DISABLED_OLD_ITEM_DELETING'];
    }
    /**
 * �������� ��������� �� �������� ������ ��������
 */
    public function isDisabledOldAlbumDeleting()
    {
        return (bool) $this->arExportData['PARAMS']['DISABLED_OLD_ALBUM_DELETING'];
    }
    /**
 * �������� �� ���������� �������� � ��������
 */
    public function isEnabledImageToSquare()
    {
        return (bool) $this->arExportData['PARAMS']['IMAGE_TO_SQUARE'];
    }
    /**
 * ������� �� ����� ����������� ������
 */
    public function isEnabledExtendedGoods()
    {
        return (bool) $this->arExportData['PARAMS']['EXTENDED_GOODS'];
    }
    /**
 * �������� �� ����������� ��
 */
    public function isEnabledOfferCombine()
    {
        return (bool) $this->arExportData['PARAMS']['OFFER_COMBINE'];
    }
    /**
 * ��������� �������� �� ����� � ���� ��������
 * 
 * @return bool
 */
    public function hasOffers()
    {
        return $this->getOfferIblockId() && $this->getLinkPropertyId();
    }
}
?>