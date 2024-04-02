<?php

namespace VKapi\Market;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для сохранения часто повторяемх действия
 * при проверки причин не работоспособности модуля на том или ином сайте
 */
final class Check
{
    public $exportId = 0;
    /**
 * @param int $exportId
 */
    public function __construct(int $exportId)
    {
        $this->exportId = $exportId;
    }
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    public function exportItem()
    {
        if (!isset($this->oExportItem)) {
            $this->oExportItem = new \VKapi\Market\Export\Item($this->exportId);
            $this->oExportItem->load();
        }
        return $this->oExportItem;
    }
    /**
 * Вернет объект для работы с выгружаемыми картинками
 * 
 * @return \VKapi\Market\Export\Photo
 */
    public function exportPhoto()
    {
        if (is_null($this->oPhoto)) {
            $this->oPhoto = new \VKapi\Market\Export\Photo();
            $this->oPhoto->setExportItem($this->exportItem());
        }
        return $this->oPhoto;
    }
    /**
 * 
 * @return \VKapi\Market\Album\Export
 */
    public function albumExport()
    {
        if (is_null($this->oAlbumExport)) {
            $this->oAlbumExport = new \VKapi\Market\Album\Export($this->exportItem());
        }
        return $this->oAlbumExport;
    }
    /**
 * 
 * @return \VKapi\Market\Good\Export
 */
    public function goodExport()
    {
        if (is_null($this->oGoodExport)) {
            $this->oGoodExport = new \VKapi\Market\Good\Export($this->exportItem());
        }
        return $this->oGoodExport;
    }
    /**
 * @return \VKapi\Market\Property\Export
 */
    public function propertyExport()
    {
        if (is_null($this->oPropertyExport)) {
            $this->oPropertyExport = new \VKapi\Market\Property\Export($this->exportItem());
        }
        return $this->oPropertyExport;
    }
    public function deleteItemPhotoByFields($arFields)
    {
        $arPhotoId = (array) $arFields['main_photo_id'];
        $arPhotoId = array_merge($arPhotoId, explode(',', $arFields['photo_ids']));
        $this->exportPhoto()->deleteByPhotoId($arPhotoId, $this->exportItem()->getGroupId());
    }
    /**
 * Проверка соовтетствует ли товар условиям
 * @param $exportId
 * @param $elementId
 * @return void
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\LoaderException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function checkConditon($elementId)
    {
        \CModule::IncludeModule('vkapi.market');
        echo '<pre>';
        $arResult = $this->manager()->checkExportConditionsForElementId($this->exportId, $elementId);
        if ($arResult['valid']) {
            echo 'Is valid';
        } else {
            echo 'Is not valid';
        }
        echo PHP_EOL;
        print_r($arResult);
        echo '</pre>';
    }
    /**
 * Вывод сформирвоанных данных по товару в выгрузке
 * @param $exportId
 * @param $productId
 * @param $offerId
 * @return void
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ArgumentTypeException
 * @throws \Bitrix\Main\LoaderException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \VKapi\Market\Exception\BaseException
 */
    public function checkProductData($productId, $offerId = 0)
    {
        \CModule::IncludeModule('vkapi.market');
        echo '<pre>';
        $offer = new \VKapi\Market\Good\Export\Item($productId, $offerId, $this->exportItem());
        echo PHP_EOL;
        echo '\\VKapi\\Market\\Good\\Export\\Item::getFields ' . PHP_EOL;
        var_export($offer->getFields());
        echo PHP_EOL;
        echo '\\VKapi\\Market\\Good\\Export\\Item::getProductData ' . PHP_EOL;
        var_export($offer->getProductData());
        echo PHP_EOL;
        echo 'isOffer ' . PHP_EOL;
        var_dump($offer->isOffer());
        echo PHP_EOL;
        echo '\\VKapi\\Market\\Export\\Item ' . PHP_EOL;
        var_export($offer->exportItem());
        echo '</pre>';
    }
}
?>