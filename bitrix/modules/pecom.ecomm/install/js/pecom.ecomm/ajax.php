<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Pec\Delivery\PecomEcommDb;
use Pec\Delivery\Tools;
use Bitrix\Main\Text\Encoding;

$module_id = "pecom.ecomm";

Loader::includeModule('sale');
Loader::includeModule($module_id);

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$method = $request->getPost("method");
$orderId = $request->getPost("orderId");

$result = '';

switch ($method) {
    case 'saveProperty':
        $widgetData = serialize(json_decode($_REQUEST['pec_widget_data']));
        PecomEcommDb::AddOrderData($orderId, 'WIDGET', $widgetData);
        break;
    case 'savePecId':
        $result = Tools::savePecId($orderId, $request->getPost("pecId"));
        break;
    case 'getPecStatus':
        $result = Tools::getAndSavePecStatus($orderId, $request->getPost("pecId"));
        break;
    case 'getTag':
        $result = Tools::getTag($orderId, $request->getPost("pecId"));
        break;
    case 'cancelOrder':
        $result = Tools::cancelOrder($orderId, $request->getPost("pecId"));
        break;
    case 'preRegistration':
        $result = Tools::preRegistration($orderId, $request->getPost("positionCount"));
        break;
    case 'pickupSubmit':
        // $result = Tools::pickupNetworkSubmit($orderId, $request->getPost("positionCount"));
        $result = Tools::pickupSubmit($orderId, $request->getPost("positionCount"), $request->getPost("pickupDate"));
        break;
    case 'orderParams':
        $result = $_SESSION['pec_post']['arParams'];
        break;
    case 'checkApi':
        try {
            $result = Tools::checkApi($_POST['login'], $_POST['password'], $_POST['apiUrl']);
            $result = !empty($result->zoneId);
        } catch (Throwable $throwable) {
            $result = false;
        }
        break;
    case 'dopSklad':
        echo $result = Tools::otherWarehouse();
        exit;
}


if (is_array($result)) {
    if (isset($result['ADDRESS']))
        $result['ADDRESS'] = Encoding::convertEncoding($result['ADDRESS'], SITE_CHARSET, 'utf-8');
    if (isset($result['FROM_ADDRESS']))
        $result['FROM_ADDRESS'] = Encoding::convertEncoding($result['FROM_ADDRESS'], SITE_CHARSET, 'utf-8');
}
echo \Bitrix\Main\Web\Json::encode($result);
