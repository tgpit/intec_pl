<?php

\define("PUBLIC_AJAX_MODE", \true);
\define('BX_NO_ACCELERATOR_RESET', \true);
// ����� �� ������� �� VMBitrix 3.1 ��-�� Zend ��� �������� ������ � ������.
\define('BX_SECURITY_SHOW_MESSAGE', \false);
\define("STOP_STATISTICS", \true);
\define("NO_KEEP_STATISTIC", "Y");
\define("NO_AGENT_STATISTIC", "Y");
// define("NOT_CHECK_PERMISSIONS", true);
// define("NOT_CHECK_FILE_PERMISSIONS", true);
\define('CHK_EVENT', \false);
\define("NO_AGENT_CHECK", \true);
\define("DisableEventsCheck", \true);
// define('BX_SECURITY_SESSION_READONLY', true);
// define('BX_SECURITY_SESSION_VIRTUAL', true);
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter());
if (\Bitrix\Main\Loader::includeModule('vkapi.market')) {
    \VKapi\Market\Manager::getInstance()->adminPageAjaxHandler();
} else {
    echo \json_encode(['status' => 'error', 'error' => ['msg' => 'Module vkapi.market is not installed', 'code' => 'MODULE_NOT_INSTALLED']]);
}
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_after.php";