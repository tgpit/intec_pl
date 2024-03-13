<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->restartBuffer();

CModule::IncludeModule("acrit.core");

use Acrit\Core\Orders\Controller,
	Acrit\Core\Orders\Rest,
	Acrit\Core\Orders\OrdersInfo;

Controller::setModuleId($strModuleId);

$action = trim($_REQUEST['action'] ?? '');
$params = $_REQUEST['params'];
$result = [];
$result['status'] = 'error';
$result['log'] = [];

switch ($action) {
	// Users search
	case 'find_users':
		$result = [];
		if (strlen($_REQUEST['q']) > 3) {
			$list = OrdersInfo::getUsers($_REQUEST['q']);
			foreach ($list as $item) {
				$result[$item['id']] = $item['name'] . ', ' . $item['code'] . ' [' . $item['id'] . ']';
			}
		}
		break;
    case 'find_companies':
        $result = [];
        if (strlen($_REQUEST['q']) > 3) {
            try {
                $list = OrdersInfo::getCompanies($_REQUEST['q']);
            }  catch ( \Throwable  $e ) {
                $errors = [
                    'error_php' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'stek' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                ];
            }
            foreach ($list as $item) {
                $result[$item['id']] = $item['name'] . ', ' . ' [' . $item['id'] . ']';
            }
        }
        break;
    case 'find_contacts':
        $result = [];
        if (strlen($_REQUEST['q']) > 3) {
            try {
                $list = OrdersInfo::getContacts($_REQUEST['q']);
            }  catch ( \Throwable  $e ) {
                $errors = [
                    'error_php' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'stek' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                ];
            }
            foreach ($list as $item) {
                $result[$item['id']] = $item['name'] . ', ' . $item['code'] . ' [' . $item['id'] . ']';
            }
        }
        break;

}

echo \Bitrix\Main\Web\Json::encode($result);
