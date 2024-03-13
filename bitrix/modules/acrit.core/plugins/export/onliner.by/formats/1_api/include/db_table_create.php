<?
/**
 * Acrit Core: create tables for onliner.by api
 * @documentation https://github.com/onlinerby/onliner-b2b-api/
 */

namespace Acrit\Core\Export\Plugins\OnlinerbyHelpers;

use
	\Acrit\Core\Helper;

$obDatabase = \Bitrix\Main\Application::getConnection();

$arTables = [
	'acrit_onliner_attribute' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'CATEGORY_ID' => 'int(11) NOT NULL',
		'ATTRIBUTE_ID' => 'int(11) NOT NULL',
		'DICTIONARY_ID' => 'int(11) DEFAULT NULL',
		'NAME' => 'VARCHAR(255) NOT NULL',
		'CODE' => 'VARCHAR(255) NOT NULL',
		//
		'DESCRIPTION' => 'TEXT DEFAULT NULL',
		'TYPE' => 'VARCHAR(50) NOT NULL',
		'MULTIVALUED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		//
		'IS_COLLECTION' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'IS_REQUIRED' => 'CHAR(1) NOT NULL DEFAULT \'N\'', //MANDATORY
		//
		'GROUP_ID' => 'int(11) DEFAULT NULL',
		//
		'GROUP_NAME' => 'VARCHAR(255) DEFAULT NULL',
		'LAST_VALUES_COUNT' => 'int(11) DEFAULT NULL',
		'LAST_VALUES_DATETIME' => 'DATETIME DEFAULT NULL',
		'LAST_VALUES_ELAPSED_TIME' => 'int(11) DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_onliner_attribute_1` (`ATTRIBUTE_ID`)',
	],
	'acrit_onliner_attribute_value' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'CATEGORY_ID' => 'int(11) NOT NULL',
		'ATTRIBUTE_ID' => 'int(11) NOT NULL',
		'ATTRIBUTE_CODE' => 'VARCHAR(255) NOT NULL',
		'DICTIONARY_ID' => 'int(11) NOT NULL',
		'VALUE_ID' => 'int(11) NOT NULL',
		'VALUE' => 'VARCHAR(255) NOT NULL',
		'CODE' => 'VARCHAR(255) NOT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_onliner_attribute_value_1` (`VALUE_ID`)',
	],
	'acrit_onliner_category' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'CATEGORY_ID' => 'int(11) NOT NULL',
		'NAME' => 'TEXT NOT NULL',
		'CODE' => 'TEXT NOT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_onliner_category_1` (`CATEGORY_ID`)',
	],	
	'acrit_onliner_task' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'TASK_ID' => 'int(11) NOT NULL',
		'PRODUCTS_COUNT' => 'int(11) NOT NULL',
		'JSON' => 'LONGTEXT DEFAULT NULL',
		'RESPONSE' => 'TEXT DEFAULT NULL',
		'STATUS' => 'TEXT DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'STATUS_DATETIME' => 'DATETIME DEFAULT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_onliner_task_1` (`TASK_ID`)',
		'KEY `ix_perf_acrit_onliner_task_2` (`PROFILE_ID`)',
	],	
	'acrit_onliner_history' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'TASK_ID' => 'int(11) NOT NULL',
		'TASK_ID_ONLINER' => 'int(11) NOT NULL',
		'OFFER_ID' => 'VARCHAR(255) NOT NULL',
		'PRODUCT_ID' => 'int(11) NOT NULL',
		'ELEMENT_ID' => 'int(11) DEFAULT NULL',
		'STOCK_VALUE' => 'int(11) DEFAULT NULL',
		'JSON' => 'LONGTEXT NOT NULL',
		'RESPONSE' => 'TEXT DEFAULT NULL',
		'STOCK_UPDATED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'STOCK_ERRORS' => 'VARCHAR(255) DEFAULT NULL',
		'STATUS' => 'TEXT DEFAULT NULL',
		'STATUS_DATETIME' => 'DATETIME DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_onliner_history_1` (`PROFILE_ID`, `MODULE_ID`)',
		'KEY `ix_perf_acrit_onliner_history_2` (`TASK_ID_ONLINER`)',
		'KEY `ix_perf_acrit_onliner_history_3` (`TASK_ID`, `OFFER_ID`)',
	],
	'acrit_onliner_history_stock' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'HISTORY_ID' => 'int(11) NOT NULL',
		'OFFER_ID' => 'VARCHAR(255) NOT NULL',
		'PRODUCT_ID' => 'int(11) DEFAULT NULL',
		'WAREHOUSE_ID' => 'bigint DEFAULT NULL',
		'STOCK' => 'int(11) DEFAULT NULL',
		'UPDATED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'ERRORS' => 'VARCHAR(255) DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
	],
	'acrit_onliner_access_token' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'ACCESS_TOKEN' => 'VARCHAR(255) NOT NULL',
		'EXPIRES_IN' => 'int(11) NOT NULL',
		'TIME' => 'TIMESTAMP NOT NULL',
		'PRIMARY KEY (ID)',
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
		//file_put_contents(__DIR__.'/a2.txt', var_export($strSql, true));
		
	}
}

$strSql = "SHOW COLUMNS FROM acrit_onliner_history LIKE 'TASK_ID_ONLINER'";
if(!$obDatabase->query($strSql)->fetch()){
	$obDatabase->startTransaction();
	$obDatabase->query("ALTER TABLE acrit_onliner_history CHANGE TASK_ID TASK_ID_ONLINER int(11) NOT NULL;");
	$obDatabase->query("ALTER TABLE acrit_onliner_history ADD TASK_ID int(11) NOT NULL AFTER PROFILE_ID;");
	//$this->doMigrateTaskId();
	$obDatabase->commitTransaction();
}
