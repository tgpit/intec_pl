<?php
/**
 *    Settings
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
    Bitrix\Main\SystemException,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\DB\Exception,
    Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

class  OrderNumber
{

    public static function generateOrderNumber($orderId, $type)
    {
        if (!Loader::IncludeModule('sale')) {
            return false;
        }
        $arr_order = self::checkIsOrderFromTraidingPlatform($orderId);
        if (!$arr_order || !is_array($arr_order)) {
            return false;
        }
        $exp = '/(?<=ACRIT_NUMBER\(~)(.+)(?=~\))/';
        $arr_param = preg_match($exp, $arr_order['ADDITIONAL_INFO'], $matches);
        if ($matches[0]) {
            $param = unserialize($matches[0]);
            if (is_array($param)){
                return self::makeNumber($param, $arr_order);
            } else {
                return false;
            }
        } else {
            return false;
        }
        return false;
    }

    public static function checkIsOrderFromTraidingPlatform($orderId)
    {
        $filter = [
            'ID' => $orderId,
        ];
        $orders = \Bitrix\Sale\Order::getList([
            'select' => ['ID', 'STATUS_ID', 'ADDITIONAL_INFO', 'XML_ID'],
            'filter' => $filter,
            'order' => ['ID' => 'ASC'],
        ]);
        return  $orders->fetch();
    }
    public  static function makeNumber($param, $arr_order) {
        $number = false;
        $I = $arr_order['ID'];
        $E = $arr_order['XML_ID'];
        $P = $param['prefix'] ?: '';
        $separator = $param['separator'] ?: '.';
        $scheme = $param['scheme'] ?: 'I';
        $arr_id = str_split($scheme);
        $i = 1;
        foreach ($arr_id as $item) {
            $number .= $$item;
            if ($i < count($arr_id) ) {
                $number .= $separator;
            }
            $i++;
        }
        return $number;
    }
}
