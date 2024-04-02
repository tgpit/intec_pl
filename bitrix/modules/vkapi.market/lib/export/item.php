<?php

namespace VKapi\Market\Export;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
/**
 * Класс для удобной работы с конкретной выгрузкой, получение полей, настройек, условий отбора товаров и тп
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
 * вернет все данные описывающие выгрузку
 * @return array|null
 */
    public function getData()
    {
        return $this->arExportData;
    }
    /**
 * Установит новый набор полей описывающих выгрузку, используется
 * для формирвоания предпросмотра на странице редактирования выгрузки
 */
    public function setData($arExportData)
    {
        $this->arExportData = $arExportData;
    }
    /**
 * Вернет объект дял запросов к вк
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
 * Вернет объект дял запросов к вк
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
 * Устанвока режима предпросмотра, когда не выгружаются кратиник
 * @param $flag
 */
    public function setPreviewMode($flag)
    {
        $this->previewMode = (bool) $flag;
    }
    /**
 * Првоерка режима предпросмотра, когда не выгружаются картинки
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
 * Вернете идентификатор сайта для которого делаем выгрузку
 * @return string
 */
    public function getSiteId()
    {
        return $this->arExportData['SITE_ID'];
    }
    /**
 * Вернете привязанный идентфиикатор аккаунта доабвленого
 * @return int
 */
    public function getAccountId()
    {
        return (int) $this->arExportData['ACCOUNT_ID'];
    }
    /**
 * Вернеn идентификатор категории вк по умолчанию
 * @return int
 */
    public function getCategoryId()
    {
        return (int) $this->arExportData['PARAMS']['CATEGORY_ID'];
    }
    /**
 * Вернете идентфиикаторы локлаьных подборок для выгрузки в вк
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
 * Вернете ключи правил для редактирвоания описания, напрмире для вырезания таблиц и тп
 * @return string[]
 */
    public function getDescriptionDeleteRules()
    {
        return (array) $this->arExportData['PARAMS']['DESCRIPTION_DELETE'];
    }
    /**
 * Вернет ID файла водного знака
 * @return int
 */
    public function getWatermark()
    {
        return (int) $this->arExportData['PARAMS']['WATERMARK'];
    }
    /**
 * Вернет степень прозрачности водного знака от 0 до 100
 * @return int
 */
    public function getWatermarkOpacity()
    {
        return max(0, min(100, (int) $this->arExportData['PARAMS']['WATERMARK_OPACITY']));
    }
    /**
 * Вернет коэффициент масштабирования 0.1, 0.2, ..., 1
 * @return string
 */
    public function getWatermarkCoefficient()
    {
        return $this->arExportData['PARAMS']['WATERMARK_COEFFICIENT'];
    }
    /**
 * Вернет позицию водного знака, tl(top left), tc(top center), ...
 * @return string
 */
    public function getWatermarkPosition()
    {
        return $this->arExportData['PARAMS']['WATERMARK_POSITION'];
    }
    /**
 * Вернет идентификаторы свойст для выгрузки
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
 * Вернет идентфикиаторы товара для предпросмотра
 * @return int
 */
    public function getProductIdForPreview()
    {
        return (int) $this->arExportData['PARAMS']['PREVIEW_IN_VK_PRODUCT_ID'];
    }
    /**
 * Вернет идентфикиаторы ТП для предпросмотра
 * @return int
 */
    public function getOfferIdForPreview()
    {
        return (int) $this->arExportData['PARAMS']['PREVIEW_IN_VK_OFFER_ID'];
    }
    /**
 * Вернет идентфикиаторы инфоблока товаров
 * @return int
 */
    public function getProductIblockId()
    {
        return (int) $this->arExportData['PARAMS']['CATALOG_IBLOCK_ID'];
    }
    /**
 * Вернет идентфикиаторы инфоблока тп
 * @return int
 */
    public function getOfferIblockId()
    {
        return (int) $this->arExportData['PARAMS']['OFFER_IBLOCK_ID'];
    }
    /**
 * Вернет идентификатор свойства привязки торговых предложений
 * 
 * @return int
 */
    public function getLinkPropertyId()
    {
        return (int) $this->arExportData['PARAMS']['LINK_PROPERTY_ID'];
    }
    /**
 * Вернет код валюты, по умолчанию вернет RUB
 * 
 * @return string
 */
    public function getCurrencyId()
    {
        return $this->arExportData['PARAMS']['CURRENCY_ID'] ?? 'RUB';
    }
    /**
 * Вернет описнаие условия отбора товаров
 * 
 * @return string
 */
    public function getConditions()
    {
        return $this->arExportData['PARAMS']['CONDITIONS'];
    }
    /**
 * Вернет поле из которого брать название товара товара
 * 
 * @return string
 */
    public function getProductName()
    {
        return $this->arExportData['PARAMS']['PRODUCT_NAME'];
    }
    /**
 * Вернет поле из которого брать картинку товара
 * 
 * @return string
 */
    public function getProductPhoto()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PICTURE'];
    }
    /**
 * Вернет поле из которого брать дополнительные кратинки товара
 * 
 * @return string
 */
    public function getProductMorePhoto()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PICTURE_MORE'];
    }
    /**
 * Вернет поле из которого брать цену товара
 * 
 * @return string
 */
    public function getProductPrice()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PRICE'];
    }
    /**
 * Вернет массив гурпп пользователей для которых расчитывать скидку на цену
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
 * Вернет поле из которого брать старую цену товара
 * 
 * @return string
 */
    public function getProductPriceOld()
    {
        return $this->arExportData['PARAMS']['PRODUCT_PRICE_OLD'];
    }
    /**
 * Вернет поле из которого брать вес товара
 * 
 * @return string
 */
    public function getProductWeight()
    {
        return $this->arExportData['PARAMS']['PRODUCT_WEIGHT'];
    }
    /**
 * Вернет поле из которого брать количество товара
 * 
 * @return string
 */
    public function getProductQuantity()
    {
        return $this->arExportData['PARAMS']['PRODUCT_QUANTITY'];
    }
    /**
 * Вернет поле из которого брать длину товара
 * 
 * @return string
 */
    public function getProductLength()
    {
        return $this->arExportData['PARAMS']['PRODUCT_LENGTH'];
    }
    /**
 * Вернет поле из которого брать высоту товара
 * 
 * @return string
 */
    public function getProductHeight()
    {
        return $this->arExportData['PARAMS']['PRODUCT_HEIGHT'];
    }
    /**
 * Вернет поле из которого брать ширину товара
 * 
 * @return string
 */
    public function getProductWidth()
    {
        return $this->arExportData['PARAMS']['PRODUCT_WIDTH'];
    }
    /**
 * Вернет поле из которого брать артикул товара
 * 
 * @return string
 */
    public function getProductSku()
    {
        return $this->arExportData['PARAMS']['PRODUCT_SKU'];
    }
    /**
 * Вернет текст по умолчанию для описнаия товара
 * 
 * @return string
 */
    public function getProductDefaultText()
    {
        return $this->arExportData['PARAMS']['PRODUCT_DEFAULT_TEXT'];
    }
    /**
 * Вернет шаблон описания простого товара
 * 
 * @return string
 */
    public function getProductTemplate()
    {
        return $this->arExportData['PARAMS']['PRODUCT_TEMPLATE'];
    }
    /**
 * Вернет поле из которого брать название тп
 * 
 * @return string
 */
    public function getOfferName()
    {
        return $this->arExportData['PARAMS']['OFFER_NAME'];
    }
    /**
 * Вернет поле из которого брать кратинку торгового предложения
 * 
 * @return string
 */
    public function getOfferPhoto()
    {
        return $this->arExportData['PARAMS']['OFFER_PICTURE'];
    }
    /**
 * Вернет поле из которого брать дополнительные картиник торгового предложения
 * 
 * @return string
 */
    public function getOfferMorePhoto()
    {
        return $this->arExportData['PARAMS']['OFFER_PICTURE_MORE'];
    }
    /**
 * Вернет поле из которого брать цену торгового предложения
 * 
 * @return string
 */
    public function getOfferPrice()
    {
        return $this->arExportData['PARAMS']['OFFER_PRICE'];
    }
    /**
 * Вернет массив гурпп пользователей для которых расчитывать скидку на цену
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
 * Вернет поле из которого брать старую цену торгового предложения
 * 
 * @return string
 */
    public function getOfferPriceOld()
    {
        return $this->arExportData['PARAMS']['OFFER_PRICE_OLD'];
    }
    /**
 * Вернет поле из которого брать вес тп
 * 
 * @return string
 */
    public function getOfferWeight()
    {
        return $this->arExportData['PARAMS']['OFFER_WEIGHT'];
    }
    /**
 * Вернет поле из которого брать количество
 * 
 * @return string
 */
    public function getOfferQuantity()
    {
        return $this->arExportData['PARAMS']['OFFER_QUANTITY'];
    }
    /**
 * Вернет поле из которого брать длину тп
 * 
 * @return string
 */
    public function getOfferLength()
    {
        return $this->arExportData['PARAMS']['OFFER_LENGTH'];
    }
    /**
 * Вернет поле из которого брать высоту тп
 * 
 * @return string
 */
    public function getOfferHeight()
    {
        return $this->arExportData['PARAMS']['OFFER_HEIGHT'];
    }
    /**
 * Вернет поле из которого брать ширину тп
 * 
 * @return string
 */
    public function getOfferWidth()
    {
        return $this->arExportData['PARAMS']['OFFER_WIDTH'];
    }
    /**
 * Вернет поле из которого брать артикул тп
 * 
 * @return string
 */
    public function getOfferSku()
    {
        return $this->arExportData['PARAMS']['OFFER_SKU'];
    }
    /**
 * Вернет тект по умолчанию для описания тп
 * 
 * @return string
 */
    public function getOfferDefaultText()
    {
        return $this->arExportData['PARAMS']['OFFER_DEFAULT_TEXT'];
    }
    /**
 * Вернет шаблон описания тп
 * 
 * @return string
 */
    public function getOfferTemplate()
    {
        return (string) $this->arExportData['PARAMS']['OFFER_TEMPLATE'];
    }
    /**
 * Вернет шаблон описания до итерируемой части
 * 
 * @return string
 */
    public function getOfferTemplateBefore()
    {
        return (string) $this->arExportData['PARAMS']['OFFER_TEMPLATE_BEFORE'];
    }
    /**
 * Вернет шаблон описания после итерируемой части
 * 
 * @return string
 */
    public function getOfferTemplateAfter()
    {
        return (string) $this->arExportData['PARAMS']['OFFER_TEMPLATE_AFTER'];
    }
    /**
 * Проверка отключено ли удаление старых товаров
 * @return bool
 */
    public function isDisabledOldItemDeleting()
    {
        return (bool) $this->arExportData['PARAMS']['DISABLED_OLD_ITEM_DELETING'];
    }
    /**
 * Проверка отключено ли удаление старых альбомов
 */
    public function isDisabledOldAlbumDeleting()
    {
        return (bool) $this->arExportData['PARAMS']['DISABLED_OLD_ALBUM_DELETING'];
    }
    /**
 * Включено ли приведение картинок к квадрату
 */
    public function isEnabledImageToSquare()
    {
        return (bool) $this->arExportData['PARAMS']['IMAGE_TO_SQUARE'];
    }
    /**
 * Включен ли режим расширенные товары
 */
    public function isEnabledExtendedGoods()
    {
        return (bool) $this->arExportData['PARAMS']['EXTENDED_GOODS'];
    }
    /**
 * Включено ли объединение тп
 */
    public function isEnabledOfferCombine()
    {
        return (bool) $this->arExportData['PARAMS']['OFFER_COMBINE'];
    }
    /**
 * Проверяет возможны ли оферы в этой выгрузке
 * 
 * @return bool
 */
    public function hasOffers()
    {
        return $this->getOfferIblockId() && $this->getLinkPropertyId();
    }
}
?>