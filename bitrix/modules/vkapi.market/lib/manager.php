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
 * вызывается перед началом формирования описания товара
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param string $tempalte - шаблон описания, из которого офрмируется описание товара
 * @param array $arData - {key:value, } данные, для автозамены
 * @param array $arPlaceholders - массив найденых плейсхолдеров для замены
 */
    public const EVENT_ON_BEFORE_PRODUCT_DESCRIPTION = 'onBeforeProductDescription';
    /**
 * вызывается перед началом формирования (повторяющейся части - при объединении) основного контента описания товара с торговыми предложениями
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param string $tempalte - шаблон описания, из которого офрмируется описание товара
 * @param array $arData - {key:value, } данные, для автозамены
 * @param array $arPlaceholders - массив найденых плейсхолдеров для замены
 * @param array $arOffer - массив описывающий торговое предложение твоара
 */
    public const EVENT_ON_BEFORE_OFFER_DESCRIPTION = 'onBeforeOffersDescription';
    /**
 * вызывается перед началом формирования начала описания товара с торговыми предложениями
 * при базовом режиме и включенном объединении торговых предложений
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param string $tempalte - шаблон описания, из которого офрмируется описание товара
 * @param array $arData - {key:value, } данные, для автозамены
 * @param array $arPlaceholders - массив найденых плейсхолдеров для замены
 * @param array $arOfferList - массив описаний торговых предложений
 */
    public const EVENT_ON_BEFORE_OFFER_DESCRIPTION_BEFORE = 'onBeforeOffersDescriptionBefore';
    /**
 * вызывается перед началом формирования окончания описания товара с торговыми предложениями
 * при базовом режиме и включенном объединении торговых предложений
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param string $tempalte - шаблон описания, из которого офрмируется описание товара
 * @param array $arData - {key:value, } данные, для автозамены
 * @param array $arPlaceholders - массив найденых плейсхолдеров для замены
 * @param array $arOfferList - массив описаний торговых предложений
 */
    public const EVENT_ON_BEFORE_OFFER_DESCRIPTION_AFTER = 'onBeforeOffersDescriptionAfter';
    /**
 * Вызывается после подготовки полей описывающих товар которые далее будут переданы в вк по API для простого товара,
 * именно это событие можно использовать чтобы поменять изменить любое поле описывающее товар - цена, текст, название и тп
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param array $arFields - {key:value, } подготовленные поля ВК описывающие товар
 * @param array $arProduct - {key:value, } массив описывающий товарв в битриксе, на основе которого формируется описание для вк
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - объект класса, формирующий описание товара и вызывающий это событие, для доступа к методам класса, использовать с осторожностью
 */
    public const EVENT_ON_AFTER_PREPARE_FIELDS_VK_FROM_PRODUCT = 'onAfterPrepareFieldsVkFromProduct';
    /**
 * Вызывается после подготовки полей описывающих товар которые далее будут переданы в вк по API для товара имеющего торговые предложения,
 * именно это событие можно использовать чтобы поменять изменить любое поле описывающее товар - цена, текст, название и тп
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param array $arFields - {key:value, } подготовленные поля ВК описывающие товар
 * @param array $arProduct - {key:value, } массив описывающий товарв в битриксе, на основе которого формируется описание для вк
 * @param array $arOffer - тп
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - объект класса, формирующий описание товара и вызывающий это событие, для доступа к методам класса, использовать с осторожностью
 */
    public const EVENT_ON_AFTER_PREPARE_FIELDS_VK_FROM_OFFER = 'onAfterPrepareFieldsVkFromOffer';
    /**
 * Вызывается после подготовки полей описывающих товар, ДО начала формирования описания товара для МЛ
 * именно это событие можно использовать чтобы поменять например идентфикатор локальной картинки для последующей выгрузки в вк
 * @param array $arExportData - массив описывающий параметры выгрузки
 * @param array $arProduct - {key:value, } массив описывающий товарв в битриксе, на основе которого формируется описание для вк
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - объект класса, формирующий описание товара и вызывающий это событие, для доступа к методам класса, использовать с осторожностью
 * Вернется должен объект с новыми данными - new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['arProduct' => $arProduct]);
 */
    public const EVENT_ON_AFTER_PREPARE_PRODUCT_DATA = 'onAfterPrepareProductData';
    /**
 * Вызывается после подготовки полей описывающих товар, ДО начала формирования описания товара для МЛ
 * именно это событие можно использовать чтобы поменять например идентфикатор локальной картинки для последующей выгрузки в вк
 * @param array $arExportData - массив описывающий параметры выгрузки
 * @param array $arProduct - {key:value, } массив описывающий товарв в битриксе, на основе которого формируется описание для вк
 * @param array $arOffer - {key:value, } массив описывающий торговое предложение в битриксе, на основе которого формируется описание для вк
 * @param \VKapi\Market\Good\Export\Item $goodExportItem - объект класса, формирующий описание товара и вызывающий это событие, для доступа к методам класса, использовать с осторожностью
 * Вернется должен объект с новыми данными - new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['arOffer' => $arOffer]);
 */
    public const EVENT_ON_AFTER_PREPARE_OFFER_DATA = 'onAfterPrepareOfferData';
    /**
 * Вызывается перед созданием заказа в битриксе, когда уже все поля заполнены, свойства, осталось только сохранить
 * @param \Bitrix\Sale\Order $order - объект класса
 * @param \VKapi\Market\Sale\Order\Item $item - объект класса заказа вк, содержащий поля заказа вк подготовленные и оригинальный массив с данными
 */
    public const EVENT_ON_BEFORE_ORDER_CREATE = 'onBeforeOrderCreate';
    /**
 * Вызывается перед сохранением ИЗМЕННОГО заказа в битриксе, когда из вк пришли новые данные по callback или при ручном импорте заказа
 * @param \Bitrix\Sale\Order $order - объект класса
 * @param \VKapi\Market\Sale\Order\Item $item - объект класса заказа вк, содержащий поля заказа вк подготовленные и оригинальный массив с данными
 */
    public const EVENT_ON_BEFORE_ORDER_UPDATE = 'onBeforeOrderUpdate';
    /**
 * Вызывается после создания заказа битриксе
 * @param \Bitrix\Sale\Order $order - объект класса
 * @param \VKapi\Market\Sale\Order\Item $item - объект класса заказа вк, содержащий поля заказа вк подготовленные и оригинальный массив с данными
 */
    public const EVENT_ON_AFTER_ORDER_CREATE = 'onAfterOrderCreate';
    /**
 * вызывается перед началом формирования описания товара
 * @param array $arExportData - массив описывающий парамтеры выгрузки
 * @param string $filter - фильтр, объект класса \Bitrix\Main\ORM\Query\Filter\ConditionTree для пердачи в \Bitrix\Iblock\ElementTable::getCount, \Bitrix\Iblock\ElementTable::getList
 * Вернется должен объект с новыми данными - new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['filter' => $filter]);
 */
    public const EVENT_ON_GET_FILTER_FOR_PREPARE_LIST = 'onGetFilterForPrepareList';
}
?>