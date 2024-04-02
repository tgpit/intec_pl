<?php

namespace VKapi\Market\Sale\Order;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Connect;
use VKapi\Market\Manager;
use VKapi\Market\Exception\BaseException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для импортирования заказов
 */
class Import
{
    public function __construct()
    {
    }
    /**
     * Вернет ссылку на Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * Вернет ссылку на Manager
     */
    public function sync()
    {
        if (!isset($this->oSync)) {
            $this->oSync = new \VKapi\Market\Sale\Order\Sync();
        }
        return $this->oSync;
    }
    /**
     * Вернет сообщение
     */
    public function getMessage($name, $arReplace = null)
    {
        return $this->manager()->getMessage('LIB.SALE.ORDER.IMPORT.' . $name, $arReplace);
    }
    /**
     * Выведет HTML ручного импорта заказов
     * @throws \Bitrix\Main\ArgumentException
     */
    public function showImportByHand()
    {
        \CUtil::InitJSCore('jquery');
        $rand = \Bitrix\Main\Security\Random::getString(10);
        $container = 'vkapi-market-order-import--' . $rand;
        // размещаем блок
        echo '<div class="vkapi-market-order-import" id="' . $container . '"></div>';
        // формируем даныне
        $arData = ['items' => $this->getSyncSettingsListForJs()];
        // размещаем js
        ?>
        <script type="text/javascript" class="vkapi-market-data">
            (function () {
                window.VKapiMarketOrderImportParams = <?php 
        echo \Bitrix\Main\Web\Json::encode($arData);
        ?>;
                window.VKapiMarketOrderImportJs = window.VKapiMarketOrderImportJs || {};
                window.VKapiMarketOrderImportJs['<?php 
        echo $container;
        ?>'] = new VKapiMarketOrderImport('<?php 
        echo $container;
        ?>', window.VKapiMarketOrderImportParams);
            })();
        </script>
        <?php 
    }
    /**
     * Вернет массив настроек синхронизации заказов [[id:int, name:string, groupId:int], ...]
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getSyncSettingsListForJs()
    {
        $arReturn = [];
        $dbr = $this->sync()->table()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['ACTIVE' => true]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = ['id' => (int) $ar['ID'], 'name' => sprintf('[%s] %s (%s)', $ar['ID'], $ar['GROUP_NAME'], $ar['GROUP_ID']), 'groupId' => (int) $ar['GROUP_ID']];
        }
        return $arReturn;
    }
    /**
     * Вернет объект для импорта из под конкретной настройки синхронизации
     * @return \VKapi\Market\Sale\Order\Import\Item
     */
    public function item($syncId)
    {
        $syncId = intval($syncId);
        if (!isset($this->arItems[$syncId])) {
            $this->arItems[$syncId] = new \VKapi\Market\Sale\Order\Import\Item($syncId);
        }
        return $this->arItems[$syncId];
    }
}
?>