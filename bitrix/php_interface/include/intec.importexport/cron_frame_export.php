<?
if(!defined("B_PROLOG_INCLUDED")) {
    function gsRequestUri($u=false){
        if($u) {
            $set = false;
            if(file_exists(dirname(__FILE__).'/.u') && file_get_contents(dirname(__FILE__).'/.u')=='0') $set = true;
            if(!array_key_exists('REQUEST_URI', $_SERVER) && $set)
            {
                $_SERVER["REQUEST_URI"] = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));
                define("SET_REQUEST_URI", true);
            }
        } else {
            if(!defined('BITRIX_INCLUDED'))
            {
                file_put_contents(dirname(__FILE__).'/.u', (defined("SET_REQUEST_URI") ? '1' : '0'));
            }
        }
    }
    register_shutdown_function('gsRequestUri');
    @set_time_limit(0);
    if(!defined('NOT_CHECK_PERMISSIONS')) define('NOT_CHECK_PERMISSIONS', true);
    if(!defined('NO_AGENT_CHECK')) define('NO_AGENT_CHECK', true);
    if(!defined('BX_CRONTAB')) define("BX_CRONTAB", true);
    if(!defined('ADMIN_SECTION')) define("ADMIN_SECTION", true);
    if(!ini_get('date.timezone') && function_exists('date_default_timezone_set')) {
        @date_default_timezone_set("Europe/Moscow");
    }
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/../../../..');
    gsRequestUri(true);
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    require_once(dirname(__FILE__).'/../../../modules/intec.importexport/classes/models/excel/export/Export.php');
    if(!defined('BITRIX_INCLUDED')) define("BITRIX_INCLUDED", true);
}

@set_time_limit(0);

\Bitrix\Main\Loader::includeModule("iblock");
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule("currency");
\Bitrix\Main\Loader::includeModule("intec.core");
\Bitrix\Main\Loader::includeModule("intec.importexport");


$sess = $_SESSION;
session_write_close();
$_SESSION = $sess;

$templateIds = htmlspecialcharsbx($argv[1]);
$templateIds = array_map('trim', explode(',', $templateIds));

foreach($templateIds as $templateId) {

    if(strlen($templateId) <= 0) {
        echo date('Y-m-d H:i:s').": template id is not set \r\n \r\n";
        continue;
    }

    $template = intec\importexport\models\excel\export\Template::findOne($templateId);

    if (empty($template)) {
        echo date('Y-m-d H:i:s') . ": template with id - " . $templateId . " not found \r\n \r\n";
        continue;
    }

    echo date('Y-m-d H:i:s') . ": start of template export with id " . $templateId .  "\r\n";

    try {
        $parameters = intec\core\helpers\Json::decode($template->getAttribute('params'));
    } catch (intec\core\base\InvalidParamException $exception) {
        $parameters = [];
    }

    $export = new Export();
    $result = $export->generateExcelCron($templateId);

    if (empty($result) || $result['error']['is']) {
        echo date('Y-m-d H:i:s').": template export error \r\n";
        if (!empty($result['error']['errors'])) {
            foreach ($result['error']['errors'] as $error) {
                if (!empty($error['message'])) {
                    echo "Error: " . $error['message'] . "\r\n";
                }
            }
        }
        echo "\r\n";
        continue;
    }

    echo date('Y-m-d H:i:s').": template export with id " . $templateId . " completed \r\n \r\n";
}
?>