<?php

namespace VKapi\Market\Album;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Хранит локальные подборки, добавляемые в админке
 * Class ItemTable
 * + ID:int
 * + VK_NAME :string
 * + NAME :string
 * + PICTURE :int
 * + PARAMS :array
 * 
 * @package VKapi\Market\Album
 */
class ItemTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_album_item';
    }
    /**
 * @return array
 * @throws \Bitrix\Main\SystemException
 */
    public static function getMap()
    {
        return [new \Bitrix\Main\Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]), new \Bitrix\Main\Entity\StringField('VK_NAME', [
            // название подборки в вк
            'required' => false,
            'validator' => function () {
                return [new \Bitrix\Main\Entity\Validator\Range(1, 255)];
            },
        ]), new \Bitrix\Main\Entity\StringField('NAME', [
            // внутренее название в битриксе
            'required' => false,
            'validator' => function () {
                return [new \Bitrix\Main\Entity\Validator\Range(1, 255)];
            },
        ]), new \Bitrix\Main\Entity\IntegerField('PICTURE'), new \Bitrix\Main\Entity\TextField('PARAMS', ['required' => true, 'serialized' => true, 'default_value' => []]), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')];
    }
    /**
 * Удалим картинки альбомов при удалении альбома
 * 
 * @param \Bitrix\Main\Entity\Event $event
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $primary = $event->getParameter("primary");
        if (!isset($primary['ID'])) {
            return true;
        }
        $albumId = $primary['ID'];
        $arData = self::getById($albumId)->fetch();
        if (!$arData) {
            return true;
        }
        if ($arData['PICTURE']) {
            // удалим из таблицы файлов
            \CFile::Delete(intval($arData['PICTURE']));
            // удалим информацию о выгруженных в вк картинках
            \VKapi\Market\Export\PhotoTable::deleteByFileId(intval($arData['PICTURE']));
        }
        // удалим записи о выгруженом альбоме в вк
        \VKapi\Market\Album\ExportTable::deleteAllByAlbumId($albumId);
        // удалим связи между товарами и альбомом
        \VKapi\Market\Good\Reference\AlbumTable::deleteAllByAlbumId($albumId);
    }
}
/**
 * Работа с локльаными подборками, коорые добавляются в админке
 * Class Item
 * 
 * @package VKapi\Market\Album
 */
class Item
{
    /**
 * @var \VKapi\Market\Album\ItemTable
 */
    private $oTable = null;
    public function __construct()
    {
    }
    /**
 * поля:
 * + ID:int
 * + VK_NAME :string
 * + NAME :string
 * + PICTURE :int
 * + PARAMS :array
 * @return \VKapi\Market\Album\ItemTable
 */
    public function table()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Album\ItemTable();
        }
        return $this->oTable;
    }
    /**
 * @param $name
 * @param null $arReplace
 * 
 * @return string
 */
    protected function getMessage($name, $arReplace = null)
    {
        return \VKapi\Market\Manager::getInstance()->getMessage('ALBUM.ITEM.' . $name, $arReplace);
    }
    /**
 * Вернет все доступные категории для товаров
 * из ВКонтакте, кэш на сутки
 */
    public function getAllCategories()
    {
        $result = new \Bitrix\Main\Result();
        $oCache = \Bitrix\Main\Data\Cache::createInstance();
        $cacheTime = 120;
        $cacheId = 'getAllCategories';
        $cacheDir = 'vkapi.market/vk/categories';
        if ($oCache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $arCategory = $oCache->getVars();
        } elseif ($oCache->startDataCache()) {
            $arCategory = [];
            $oManager = \VKapi\Market\Manager::getInstance();
            // получаем аккаунты
            $bFind = false;
            $resultCategories = false;
            $dbrAccount = \VKapi\Market\ConnectTable::getList();
            $arExceptoins = [];
            while ($arAccount = $dbrAccount->fetch()) {
                try {
                    $conn = $oManager->getConnection($arAccount['ID']);
                    if (!is_null($conn)) {
                        $resultCategories = $conn->method('market.getCategories');
                        if ($resultCategories->isSuccess()) {
                            $resultCategoriesData = $resultCategories->getData('response');
                            $arCategory = $resultCategoriesData['items'];
                            $bFind = true;
                            break;
                        }
                    }
                } catch (\VKapi\Market\Exception\BaseException $ex) {
                    $arExceptoins[] = $ex;
                }
            }
            if (!$bFind) {
                if (count($arExceptoins)) {
                    $ex = $arExceptoins[0];
                    $result->addError(new \Bitrix\Main\Error($ex->getMessage(), $ex->getCode(), $ex->getCustomData()));
                } else {
                    $result->addError(new \Bitrix\Main\Error($this->getMessage('EMPTY_ACCOUNT_LIST'), 'EMPTY_ACCOUNT_LIST'));
                }
                $oCache->abortDataCache();
            } elseif (empty($arCategory)) {
                if ($resultCategories instanceof \VKapi\Market\Result && !$resultCategories->isSuccess()) {
                    $result->addError($resultCategories->getBitrixError());
                } else {
                    $result->addError(new \Bitrix\Main\Error($this->getMessage('EMPTY_CATEGORIES_LIST'), 'EMPTY_CATEGORIES_LIST'));
                }
                $oCache->abortDataCache();
            }
            $oCache->endDataCache($arCategory);
        }
        if ($result->isSuccess()) {
            $result->setData(['items' => $arCategory]);
        }
        return $result;
    }
    /**
 * Вернет html с выпадающим списком категорий из ВКонтакте
 * 
 * @param $name
 * @param string $val
 * @return string
 * @internal
 */
    public function getCategorySelectHtml($name, $val = '')
    {
        $categories = $this->getAllCategories();
        $html = '';
        if ($categories->isSuccess()) {
            $data = $categories->getData();
            $arCategory = $data['items'];
            $html = '<select name="' . $name . '" class="vkapi-market-select vkapi-market-select--groups" >';
            $lastGroup = null;
            if (!empty($arCategory)) {
                // foreach ($arCategory as $item) {
                // if (is_null($lastGroup)) {
                // $html .= '<optgroup label="' . $item['section']['name'] . '" >';
                // $lastGroup = $item['section']['id'];
                // }
                
                // if ($item['section']['id'] != $lastGroup) {
                // $html .= '</optgroup><optgroup label="' . $item['section']['name'] . '" >';
                // $lastGroup = $item['section']['id'];
                // }
                
                // if ('' == $val) {
                // $val = $item['id'];
                // }
                
                // $html .= '<option value="' . $item['id'] . '" ' . ($item['id'] == $val ? ' selected="selected" ' : '') . ' >' . $item['name'] . '</option>';
                
                // }
                foreach ($arCategory as $item) {
                    $html .= '<optgroup label="' . $item['name'] . '" >';
                    foreach ($item['children'] as $child) {
                        $html .= '<option value="' . $child['id'] . '" ' . ($child['id'] == $val ? ' selected="selected" ' : '') . ' >' . $child['name'] . '</option>';
                    }
                    $html .= '</optgroup>';
                }
            }
            if (!is_null($lastGroup)) {
                $html .= '</optgroup>';
            }
            $html .= '</select>';
        } else {
            $html = '<div class="vkapi-market-message vkapi-market-message--error">' . implode(',', $categories->getErrorMessages()) . '</div>';
        }
        return $html;
    }
    /**
 * Вренет массив с описанием альбомов для js
 * 
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getItemsForJs()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            $dbrItem = $this->table()->getList(['order' => ['VK_NAME' => 'ASC']]);
            while ($arItem = $dbrItem->fetch()) {
                $src = false;
                if ($arItem['PICTURE']) {
                    $arImg = \CFile::ResizeImageGet($arItem['PICTURE'], ['width' => 200, 'height' => 200]);
                    $src = $arImg['src'];
                }
                $arReturn[] = ['id' => $arItem['ID'], 'name' => $arItem['VK_NAME'] . ' (' . $arItem['NAME'] . ') [' . $arItem['ID'] . ']', 'img' => $src];
            }
        }
        return $arReturn;
    }
    /**
 * Вернет массив с описанием подборок
 * 
 * @param int[] $arId
 * @return array [id : {}, id: {} ]
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getItemsById($arId)
    {
        $arReturn = [];
        $dbrItem = $this->table()->getList(['filter' => ['ID' => $arId]]);
        while ($arItem = $dbrItem->fetch()) {
            $arReturn[$arItem['ID']] = $arItem;
        }
        return $arReturn;
    }
}
?>