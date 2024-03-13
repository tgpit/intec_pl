<?php
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);
define('NOT_CHECK_PERMISSIONS', true);

use intec\Core;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule("intec.core");

Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');

$params = $_REQUEST['params'];

if (!empty($_REQUEST['cityID'])) {
    $params['CITY_ID'] = $_REQUEST['cityID'];
    $_SESSION['CATALOG_DELIVERY_CITY_ID'] = $_REQUEST['cityID'];
}

if (intval($_REQUEST['quantity'])>0)
    $params['PRODUCT_QUANTITY_VALUE'] = intval($_REQUEST['quantity']);

$params['USE_BASKET'] = ($_REQUEST['useBasket'] == 'y')?'Y':'N';

$componentTemplate = (!empty($_REQUEST['template']))?$_REQUEST['template']:'';

global $APPLICATION;

$APPLICATION->IncludeComponent(
    'intec.universe:catalog.delivery',
    $componentTemplate,
    $params
);