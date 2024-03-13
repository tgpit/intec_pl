<?
/**
 * Acrit Core: Aliexpress Local tasks
 * @documentation https://business.aliexpress.ru/docs/category/open-api
 */

namespace Acrit\Core\Export\Plugins\AliexpressComApiLocalHelpers;

use Acrit\Core\Export\Plugins\AliHelpers\Products;
use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class TaskTable extends Entity\DataManager {
	const TYPE_ADD = 1;
	const TYPE_UPDATE = 2;

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_export_aliloc_task';
	}
	
	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../../../class.php'), $strLang, $strHead, 
			$strName, $strHint);
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage($strLang.'ID'),
			)),
			'MODULE_ID' => new Entity\StringField('MODULE_ID', array(
				'title' => Helper::getMessage($strLang.'MODULE_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Helper::getMessage($strLang.'PROFILE_ID'),
			)),
			'PRODUCT_ID' => new Entity\IntegerField('PRODUCT_ID', array(
				'title' => Helper::getMessage($strLang.'PRODUCT_ID'),
			)),
			'GROUP_ID' => new Entity\IntegerField('GROUP_ID', array(
				'title' => Helper::getMessage($strLang.'GROUP_ID'),
			)),
			'TASK_ID' => new Entity\StringField('TASK_ID', array(
				'title' => Helper::getMessage($strLang.'TASK_ID'),
			)),
			'TYPE' => new Entity\IntegerField('TYPE', array(
				'title' => Helper::getMessage($strLang.'TYPE'),
			)),
			'STATUS_ID' => new Entity\IntegerField('STATUS_ID', array(
				'title' => Helper::getMessage($strLang.'STATUS_ID'),
			)),
			'ERRORS' => new Entity\TextField('ERRORS', array(
				'title' => Helper::getMessage($strLang.'ERRORS'),
			)),
			'SESSION_ID' => new Entity\StringField('SESSION_ID', array(
				'title' => Helper::getMessage($strLang.'SESSION_ID'),
			)),
			'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
				'title' => Helper::getMessage($strLang.'TIMESTAMP_X'),
			)),
		);
	}

	/**
	 * Get list with additional data
	 *
	 * @return array
	 */
	public static function getListData($query) {
		$tasks_list = [];
		// Get base list
		$tasks = self::getList($query);
		while ($task = $tasks->fetch()){
			$tasks_list[] = $task;
		}
		// Get additional info
		foreach ($tasks_list as $k => $item) {
			// Product ID
			$product = \Bitrix\Iblock\ElementTable::getByPrimary($item['PRODUCT_ID'], [
				'select' => ['ID', 'NAME'],
			])->fetch();
			$item['PRODUCT_NAME'] = $product['NAME'];
			// Task status
			$item['STATUS_NAME'] = Products::getStatusName($item['STATUS_ID']);
			// Errors
			$item['ERRORS'] = json_decode($item['ERRORS'], true);
			$tasks_list[$k] = $item;
		}
		return $tasks_list;
	}

	/**
	 * Get list with additional data
	 *
	 * @return array
	 */
	public static function updateTaskStatus($task_id, $status, $error) {
		$result = false;
		$task = self::getList([
			'filter' => [
				'TASK_ID' => $task_id
			]
		])->fetch();
		if (isset($task['ID'])) {
			self::update($task['ID'], [
				'STATUS_ID' => $status,
				'ERRORS' => $error,
			]);
			$result = true;
		}
		return $result;
	}

	/**
	 * Delete by filter
	 *
	 * @return array
	 */
	public static function deleteByFilter($arFilter=null) {
		$strTable = static::getTableName();
		$strSql = "DELETE FROM `{$strTable}` WHERE 1=1";
		if(is_array($arFilter)){
			foreach($arFilter as $strField => $strValue){
				$strEqual = '=';
				if(preg_match('#^(.*?)([A-z0-9_]+)(.*?)$#', $strField, $arMatch)){
					$strField = $arMatch[2];
					if($arMatch[1] == '!'){
						$strEqual = '!=';
					}
				}
				$strField = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strField);
				$strValue = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strValue);
				if(is_numeric($strField)){
					$strSql .= " AND ({$strValue})";
				}
				else{
					$strSql .= " AND (`{$strField}`{$strEqual}'{$strValue}')";
				}
			}
			$strSql .= ';';
		}
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}

}
