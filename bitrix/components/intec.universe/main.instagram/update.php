<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

if (!Loader::includeModule('intec.core'))
    return;

include_once('class.php');

$backUrl = ArrayHelper::getValue($_REQUEST, 'BACK_URL');
$token = ArrayHelper::getValue($_REQUEST, 'ACCESS_TOKEN');
$cachePath = ArrayHelper::getValue($_REQUEST, 'CACHE_FILE');
$clientId = 'self';

$data = null;

$oUpdate = new IntecInstagramComponent();
$oUpdate->setToken($token);
$oUpdate->setFileCachePath($cachePath);
$oUpdate->clearResultCache();
$oUpdate->refreshToken();
$data = $oUpdate->getData();
$oUpdate->storeCache($data);

LocalRedirect($backUrl);