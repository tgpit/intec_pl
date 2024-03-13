<? // if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/lheader.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main;
use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\RegExp;
use intec\core\helpers\StringHelper;
use intec\template\Properties;
use intec\core\helpers\FileHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;
use intec\core\io\Path;

global $USER, $APPLICATION;
define("START_EXEC_PROLOG_AFTER_1", microtime(true));
$GLOBALS["BX_STATE"] = "PA";

?>

<?
//$APPLICATION->SetPageProperty("description", "-=-");
//$APPLICATION->SetTitle("Тест Запись на мотосервис");
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	"zapserv",
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"CONSENT_URL" => "",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "Y",
		"LIST_URL" => "",
		"SEF_MODE" => "N",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "Y",
		"WEB_FORM_ID" => "24",
		"COMPONENT_TEMPLATE" => "zapserv",
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		)
	),
	false
);?>
