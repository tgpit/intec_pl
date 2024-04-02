<?php

namespace VKapi\Market;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Class Manager
 * @package VKapi\Market
 */
final class Manager extends \VKapi_Market_Manager_Demo
{
    /**
 * ���������� ����� ������� ������������ �������� ������
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param string $tempalte - ������ ��������, �� �������� ����������� �������� ������
 * @param array $arData - {key:value, } ������, ��� ����������
 * @param array $arPlaceholders - ������ �������� ������������� ��� ������
 */
    public const EVENT_ON_BEFORE_PRODUCT_DESCRIPTION = 'onBeforeProductDescription';
    /**
 * ���������� ����� ������� ������������ (������������� ����� - ��� �����������) ��������� �������� �������� ������ � ��������� �������������
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param string $tempalte - ������ ��������, �� �������� ����������� �������� ������
 * @param array $arData - {key:value, } ������, ��� ����������
 * @param array $arPlaceholders - ������ �������� ������������� ��� ������
 * @param array $arOffer - ������ ����������� �������� ����������� ������
 */
    public const EVENT_ON_BEFORE_OFFER_DESCRIPTION = 'onBeforeOffersDescription';
    /**
 * ���������� ����� ������� ������������ ������ �������� ������ � ��������� �������������
 * ��� ������� ������ � ���������� ����������� �������� �����������
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param string $tempalte - ������ ��������, �� �������� ����������� �������� ������
 * @param array $arData - {key:value, } ������, ��� ����������
 * @param array $arPlaceholders - ������ �������� ������������� ��� ������
 * @param array $arOfferList - ������ �������� �������� �����������
 */
    public const EVENT_ON_BEFORE_OFFER_DESCRIPTION_BEFORE = 'onBeforeOffersDescriptionBefore';
    /**
 * ���������� ����� ������� ������������ ��������� �������� ������ � ��������� �������������
 * ��� ������� ������ � ���������� ����������� �������� �����������
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param string $tempalte - ������ ��������, �� �������� ����������� �������� ������
 * @param array $arData - {key:value, } ������, ��� ����������
 * @param array $arPlaceholders - ������ �������� ������������� ��� ������
 * @param array $arOfferList - ������ �������� �������� �����������
 */
    public const EVENT_ON_BEFORE_OFFER_DESCRIPTION_AFTER = 'onBeforeOffersDescriptionAfter';
    /**
 * ���������� ����� ���������� ����� ����������� ����� ������� ����� ����� �������� � �� �� API ��� �������� ������,
 * ������ ��� ������� ����� ������������ ����� �������� �������� ����� ���� ����������� ����� - ����, �����, �������� � ��
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param array $arFields - {key:value, } �������������� ���� �� ����������� �����
 * @param array $arProduct - {key:value, } ������ ����������� ������ � ��������, �� ������ �������� ����������� �������� ��� ��
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - ������ ������, ����������� �������� ������ � ���������� ��� �������, ��� ������� � ������� ������, ������������ � �������������
 */
    public const EVENT_ON_AFTER_PREPARE_FIELDS_VK_FROM_PRODUCT = 'onAfterPrepareFieldsVkFromProduct';
    /**
 * ���������� ����� ���������� ����� ����������� ����� ������� ����� ����� �������� � �� �� API ��� ������ �������� �������� �����������,
 * ������ ��� ������� ����� ������������ ����� �������� �������� ����� ���� ����������� ����� - ����, �����, �������� � ��
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param array $arFields - {key:value, } �������������� ���� �� ����������� �����
 * @param array $arProduct - {key:value, } ������ ����������� ������ � ��������, �� ������ �������� ����������� �������� ��� ��
 * @param array $arOffer - ��
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - ������ ������, ����������� �������� ������ � ���������� ��� �������, ��� ������� � ������� ������, ������������ � �������������
 */
    public const EVENT_ON_AFTER_PREPARE_FIELDS_VK_FROM_OFFER = 'onAfterPrepareFieldsVkFromOffer';
    /**
 * ���������� ����� ���������� ����� ����������� �����, �� ������ ������������ �������� ������ ��� ��
 * ������ ��� ������� ����� ������������ ����� �������� �������� ������������ ��������� �������� ��� ����������� �������� � ��
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param array $arProduct - {key:value, } ������ ����������� ������ � ��������, �� ������ �������� ����������� �������� ��� ��
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - ������ ������, ����������� �������� ������ � ���������� ��� �������, ��� ������� � ������� ������, ������������ � �������������
 * �������� ������ ������ � ������ ������� - new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['arProduct' => $arProduct]);
 */
    public const EVENT_ON_AFTER_PREPARE_PRODUCT_DATA = 'onAfterPrepareProductData';
    /**
 * ���������� ����� ���������� ����� ����������� �����, �� ������ ������������ �������� ������ ��� ��
 * ������ ��� ������� ����� ������������ ����� �������� �������� ������������ ��������� �������� ��� ����������� �������� � ��
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param array $arProduct - {key:value, } ������ ����������� ������ � ��������, �� ������ �������� ����������� �������� ��� ��
 * @param array $arOffer - {key:value, } ������ ����������� �������� ����������� � ��������, �� ������ �������� ����������� �������� ��� ��
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - ������ ������, ����������� �������� ������ � ���������� ��� �������, ��� ������� � ������� ������, ������������ � �������������
 * �������� ������ ������ � ������ ������� - new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['arOffer' => $arOffer]);
 */
    public const EVENT_ON_AFTER_PREPARE_OFFER_DATA = 'onAfterPrepareOfferData';
    /**
 * ���������� ����� ��������� ������ � ��������, ����� ��� ��� ���� ���������, ��������, �������� ������ ���������
 * @param \Bitrix\Sale\Order $order - ������ ������
 * @param \VKapi\Market\Sale\Order\Item $item - ������ ������ ������ ��, ���������� ���� ������ �� �������������� � ������������ ������ � �������
 */
    public const EVENT_ON_BEFORE_ORDER_CREATE = 'onBeforeOrderCreate';
    /**
 * ���������� ����� ����������� ��������� ������ � ��������, ����� �� �� ������ ����� ������ �� callback ��� ��� ������ ������� ������
 * @param \Bitrix\Sale\Order $order - ������ ������
 * @param \VKapi\Market\Sale\Order\Item $item - ������ ������ ������ ��, ���������� ���� ������ �� �������������� � ������������ ������ � �������
 */
    public const EVENT_ON_BEFORE_ORDER_UPDATE = 'onBeforeOrderUpdate';
    /**
 * ���������� ����� �������� ������ ��������
 * @param \Bitrix\Sale\Order $order - ������ ������
 * @param \VKapi\Market\Sale\Order\Item $item - ������ ������ ������ ��, ���������� ���� ������ �� �������������� � ������������ ������ � �������
 */
    public const EVENT_ON_AFTER_ORDER_CREATE = 'onAfterOrderCreate';
    /**
 * ���������� ����� ������� ������������ �������� ������
 * @param array $arExportData - ������ ����������� ��������� ��������
 * @param string $filter - ������, ������ ������ \Bitrix\Main\ORM\Query\Filter\ConditionTree ��� ������� � \Bitrix\Iblock\ElementTable::getCount, \Bitrix\Iblock\ElementTable::getList
 * �������� ������ ������ � ������ ������� - new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['filter' => $filter]);
 */
    public const EVENT_ON_GET_FILTER_FOR_PREPARE_LIST = 'onGetFilterForPrepareList';
}
?>