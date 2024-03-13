<?
/**
 * Acrit Core: create tables for VK
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper;

$obDatabase = \Bitrix\Main\Application::getConnection();

$arTables = [
	'acrit_export_vk_photos' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'HASH' => 'VARCHAR(255) NOT NULL',
		'VK_PHOTO_ID' => 'int(11) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
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
	}
}
