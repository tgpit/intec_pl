<?php

namespace VKapi\Market\Sale\Order\Sync;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Manager;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\ORMException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ����� ��� ������� ������ � ����������� �������������, ��������� �������� � ��
 * @package VKapi\Market\Sale\Order\Sync
 */
class Item
{
    protected $syncId = 0;
    protected $arSync = null;
    /**
     * @param $sync - ������������� ������������� ��� ������ ����������� ��
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function __construct($sync)
    {
        if (is_array($sync)) {
            $this->arSync = $sync;
            $this->syncId = $sync['ID'];
        } else {
            $this->loadSyncData((int) $sync);
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
     * ��������� ������������� �� ID
     * @param $syncId
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function loadSyncData($syncId)
    {
        $this->syncId = intval($syncId);
        $ar = \VKapi\Market\Sale\Order\SyncTable::getById($syncId)->fetch();
        if (!$ar) {
            throw new \VKapi\Market\Exception\BaseException($this->manager()->getMessage('LIB.SALE.ORDER.SYNC.ITEM.SYNC_ID_NOT_FOUND', ['#ID#' => $syncId]), 'ERROR_SALE_ORDER_SYNC_ITEM_ID_NOT_FOUND');
        }
        $this->arSync = $ar;
    }
    public function isActive()
    {
        return (bool) $this->arSync['ACTIVE'];
    }
    /**
     * ������ ������������� �������������
     * @return int
     */
    public function getId()
    {
        return (int) $this->syncId;
    }
    /**
     * ������ ������������� �������� ������������ ��������
     * @return int
     */
    public function getAccountId()
    {
        return (int) $this->arSync['ACCOUNT_ID'];
    }
    /**
     * ������ ������������ ������
     * @return int
     */
    public function getGroupId()
    {
        return (int) $this->arSync['GROUP_ID'];
    }
    /**
     * �������� ������� �� ����� ������� �� ��
     * @return bool
     */
    public function isEventEnabled()
    {
        return (bool) $this->arSync['EVENT_ENABLED'];
    }
    /**
     * ������ ��������� ������ ������� �������� ������ � �������� �� ��
     * @return mixed
     */
    public function getEventSecret()
    {
        return $this->arSync['EVENT_SECRET'];
    }
    /**
     * ������ ��� ������������� ��� ������ �� ��� ���������� Callback API �������
     * @return mixed
     */
    public function getEventCode()
    {
        return $this->arSync['EVENT_CODE'];
    }
    /**
     * ������ ����� ������� ����������
     * @return string
     */
    public function getGroupAccessToken()
    {
        return $this->arSync['GROUP_ACCESS_TOKEN'];
    }
    /**
     * ������ ������������� ����� � �������� ����� ����������� ������
     * @return string
     */
    public function getSiteId()
    {
        return $this->arSync['SITE_ID'];
    }
    /**
     * ������ ������������� ����� � �������� ����� ����������� ������
     * @return int
     */
    public function getStartImportTimestamp()
    {
        return (int) ($this->arSync['PARAMS']['IMPORT_START_TIMESTAMP'] ?? 0);
    }
    /**
     * ������ ���������� ������������� ���������� �������, ����� ������ ����� ������ �������������
     * @return int
     */
    public function getImportLastCount()
    {
        return (int) ($this->arSync['PARAMS']['IMPORT_LAST_COUNT'] ?? 0);
    }
    /**
     * ������ ������������ ���� �����������
     * @return int
     */
    public function getPersonalTypeId()
    {
        return (int) $this->manager()->getParam('PERSONAL_TYPE', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ ������ �������� �� ���������. ���������
     * @return int
     */
    public function getDeliveryId()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ ������ �������� - ��������
     * @return int
     */
    public function getDeliveryIdCourier()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID_COURIER', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ ������ �������� - �����
     * @return int
     */
    public function getDeliveryIdPochta()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID_POCHTA', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ ������ �������� - ����� ����������
     * @return int
     */
    public function getDeliveryIdPoint()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID_POINT', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ �������� ������
     * @return int
     */
    public function getPaymentId()
    {
        return (int) $this->manager()->getParam('PAYMENT_ID', 0, $this->getSiteId());
    }
    /**
     * ������ �������� ������ ��� ���������� ��� ����������
     * @return int
     */
    public function getFioPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_FIO', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ �������� ������ ��� �������� ������ ��������� ����������
     * @return int
     */
    public function getPhonePropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_PHONE', 0, $this->getSiteId());
    }
    /**
     * ������ ������������ �������� ������ ��� ���������� ������ ��������
     * @return int
     */
    public function getAddressPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_ADDRESS', 0, $this->getSiteId());
    }
    /**
     * ������ ������������� �������� ������ ��� �������� �������������� ������ � ��
     * @return int
     */
    public function getVkOrderPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_VKORDER', 0, $this->getSiteId());
    }
    /**
     * ������ ������������� �������� ������ ��� �������� �������������� ������ � ��
     * @return int
     */
    public function getCommentForUserPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_COMMENT_FOR_USER', 0, $this->getSiteId());
    }
    /**
     * ������ ������������� �������� ������ ��� �������� ������
     * @return int
     */
    public function getWidthPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_WIDTH', 0, $this->getSiteId());
    }
    /**
     * ������ ������������� �������� ������ ��� �������� ������
     * @return int
     */
    public function getHeightPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_HEIGHT', 0, $this->getSiteId());
    }
    /**
     * ������ ������������� �������� ������ ��� �������� �����
     * @return int
     */
    public function getLengthPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_LENGTH', 0, $this->getSiteId());
    }
    /**
     * ������ ������������� �������� ������ ��� �������� ����
     * @return int
     */
    public function getWeightPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_WEIGHT', 0, $this->getSiteId());
    }
    /**
     * ������ ��� ������� ��������� ��� ����������� ������� � ��
     * @param $vkStatus
     * @return string
     */
    public function getStatusIdByVkStatus($vkStatus)
    {
        $vkStatus = (int) $vkStatus;
        switch ($vkStatus) {
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_NEW:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_AGREE:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_COLLECT:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_DELIVER:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_COMPLETED:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_CANCELED:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_RETURNED:
                return $this->manager()->getParam('STATUS_' . $vkStatus, '', $this->getSiteId());
        }
        return '';
    }
    /**
     * ������ ������������� ������ � �� �� ���������� ������������� �������
     * @param $orderStatusId
     * @return mixed|null
     */
    public function getVkStatusByStatusId($orderStatusId)
    {
        $arStatus = [];
        $arVkStatusList = [\VKapi\Market\Sale\Order\Item::VK_STATUS_NEW, \VKapi\Market\Sale\Order\Item::VK_STATUS_AGREE, \VKapi\Market\Sale\Order\Item::VK_STATUS_COLLECT, \VKapi\Market\Sale\Order\Item::VK_STATUS_DELIVER, \VKapi\Market\Sale\Order\Item::VK_STATUS_COMPLETED, \VKapi\Market\Sale\Order\Item::VK_STATUS_CANCELED, \VKapi\Market\Sale\Order\Item::VK_STATUS_RETURNED];
        foreach ($arVkStatusList as $vkStatusId) {
            $arStatus[$this->manager()->getParam('STATUS_' . $vkStatusId, '', $this->getSiteId())] = $vkStatusId;
        }
        if (!empty($arStatus[$orderStatusId])) {
            return $arStatus[$orderStatusId];
        }
        return null;
    }
}
?>