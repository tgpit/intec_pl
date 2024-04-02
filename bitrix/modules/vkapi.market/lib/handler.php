<?php

namespace VKapi\Market;

use Bitrix\Main\Entity;
use Bitrix\Main\Loader;

class Handler
{
    /**
     * Глобавльное меню
     * 
     * @param $arGlobalMenu
     * @param $arModuleMenu
     */
    public static function main_onBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
    {
        if (!\Bitrix\Main\Loader::includeModule('vkapi.market')) {
            return null;
        }
        $arGlobalMenu['global_menu_vkapi'] = ['menu_id' => 'global_menu_vkapi', 'text' => 'VK', 'title' => 'VK', 'sort' => '250', 'items_id' => 'global_menu_vkapi', 'help_section' => 'VK', 'items' => []];
    }
    // событие изменения заказа
    public static function saleOnSaleOrderSaved(\Bitrix\Main\Event $event)
    {
        try {
            /**
 * @var \Bitrix\Sale\Order $order
 */
            $order = $event->getParameter("ENTITY");
            $oldValues = $event->getParameter("VALUES");
            $isNew = $event->getParameter("IS_NEW");
            if (!$isNew) {
                // ищем связь
                $arRef = \VKapi\Market\Sale\Order\Sync\RefTable::getList(['filter' => ['ORDER_ID' => (int) $order->getId()], 'limit' => 1])->fetch();
                if ($arRef) {
                    $oImport = new \VKapi\Market\Sale\Order\Import\Item($arRef['SYNC_ID']);
                    $oImport->sendOrderChangesToVK($order, $arRef);
                }
            }
        } catch (\Throwable $ex) {
            \AddMessage2Log($ex->getMessage() . ' | ' . $ex->getFile() . ':' . $ex->getLine() . ' | ' . $ex->getTraceAsString(), "vkapi.market");
        }
    }
    // зменениезаказа - отменен, изменен статус и тп
    public static function onSaleOrderChanged(\Bitrix\Main\Event $event)
    {
        try {
            /**
 * @var \Bitrix\Sale\Order $order
 */
            $order = $event->getParameter("ENTITY");
            // ищем связь
            $arRef = \VKapi\Market\Sale\Order\Sync\RefTable::getList(['filter' => ['ORDER_ID' => (int) $order->getId()], 'limit' => 1])->fetch();
            if ($arRef) {
                $oImport = new \VKapi\Market\Sale\Order\Import\Item($arRef['SYNC_ID']);
                $oImport->sendOrderChangesToVK($order, $arRef);
            }
        } catch (\Throwable $ex) {
            \AddMessage2Log($ex->getMessage() . ' | ' . $ex->getFile() . ':' . $ex->getLine() . ' | ' . $ex->getTraceAsString(), "vkapi.market");
        }
    }
}
?>