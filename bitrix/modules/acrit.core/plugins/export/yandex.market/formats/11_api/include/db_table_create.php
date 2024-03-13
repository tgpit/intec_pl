<?
/**
 * Acrit Core: Yandex Market Api tables
 */

namespace Acrit\Core\Export\Plugins\YandexMarketApiHelpers;

use
	\Acrit\Core\Helper;

$obDatabase = \Bitrix\Main\Application::getConnection();

$arTables = [
	'acrit_yandex_market_api_stocks' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'ELEMENT_ID' => 'int(11) DEFAULT NULL',
		'SKU' => 'VARCHAR(255) NOT NULL',
		'WAREHOUSE_ID' => 'VARCHAR(50) NOT NULL',
		'TYPE' => 'VARCHAR(50) NOT NULL',
		'COUNT' => 'int(11) DEFAULT NULL',
		'UPDATED_AT' => 'VARCHAR(25) NOT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'DATE_RESET' => 'DATETIME DEFAULT NULL',
		'PRIMARY KEY (ID)',
		'KEY `acrit_yandex_mp_stocks_1` (`PROFILE_ID`, `MODULE_ID`)',
		'KEY `acrit_yandex_mp_stocks_2` (`SKU`)',
		'KEY `acrit_yandex_mp_stocks_3` (`SKU`, `WAREHOUSE_ID`)',
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
