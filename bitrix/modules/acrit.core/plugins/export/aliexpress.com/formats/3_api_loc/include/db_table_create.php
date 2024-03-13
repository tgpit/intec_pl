<?
/**
 * Acrit Core: create tables for Aliexpress Local
 * @documentation https://business.aliexpress.ru/docs/category/open-api
 */

namespace Acrit\Core\Export\Plugins\AliexpressComApiLocalHelpers;

use
	\Acrit\Core\Helper;

$obDatabase = \Bitrix\Main\Application::getConnection();

$arTables = [
	'acrit_export_aliloc_task' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'PRODUCT_ID' => 'int(11) NOT NULL',
		'GROUP_ID' => 'int(11) NOT NULL',
		'TASK_ID' => 'VARCHAR(255) NOT NULL',
		'TYPE' => 'int(11) NOT NULL',
		'STATUS_ID' => 'int(11) NOT NULL',
		'ERRORS' => 'TEXT DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_export_aliloc_task_1` (`TASK_ID`)',
		'KEY `ix_perf_acrit_export_aliloc_task_2` (`PROFILE_ID`)',
	],
];
foreach($arTables as $strTableName => $arFields){
	$strSql = sprintf("SHOW TABLES LIKE '%s';", $strTableName);
	if(!$obDatabase->query($strSql)->fetch()){
		foreach($arFields as $key => $strValue){
			if(!is_numeric($key)){
				$arFields[$key] = sprintf('%s %s', $key, $strValue);
			}
		}
		$strSql = sprintf('CREATE TABLE IF NOT EXISTS `%s`(%s);', $strTableName,
			PHP_EOL.implode(','.PHP_EOL, $arFields).PHP_EOL);
			$obDatabase->query($strSql);
	}
}
