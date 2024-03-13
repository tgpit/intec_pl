<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Bitrix\Main\ORM\EntityError,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field as Field,
	\Acrit\Core\Export\Backup,
	\Acrit\Core\Cli,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

/**
 * Class ProfileGroupTable
 * @package Acrit\Core\Export
 */

abstract class ProfileGroupTable extends Entity\DataManager {

	const LANG = 'ACRIT_EXP_PROFILE_GROUP_FIELD_';
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!

	protected static $bResortingTree = false;
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return static::TABLE_NAME;
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		$strField = 'FIELD_';
		$arResult = [
			'ID' => new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage(static::LANG.$strField.'ID'),
			]),
			'GROUP_ID' => new Entity\IntegerField('GROUP_ID', [
				'title' => Helper::getMessage(static::LANG.$strField.'GROUP_ID'),
			]),
			'ACTIVE' => new Entity\StringField('ACTIVE', [
				'title' => Helper::getMessage(static::LANG.$strField.'ACTIVE'),
			]),
			'NAME' => new Entity\StringField('NAME', [
				'title' => Helper::getMessage(static::LANG.$strField.'NAME'),
				'required' => true,
			]),
			'DESCRIPTION' => new Entity\TextField('DESCRIPTION', [
				'title' => Helper::getMessage(static::LANG.$strField.'DESCRIPTION'),
			]),
			'SORT' => new Entity\IntegerField('SORT', [
				'title' => Helper::getMessage(static::LANG.$strField.'SORT'),
			]),
			'DEPTH_LEVEL' => new Entity\IntegerField('DEPTH_LEVEL', [
				'title' => Helper::getMessage(static::LANG.$strField.'DEPTH_LEVEL'),
			]),
			'LEFT_MARGIN' => new Entity\IntegerField('LEFT_MARGIN', [
				'title' => Helper::getMessage(static::LANG.$strField.'LEFT_MARGIN'),
			]),
			'RIGHT_MARGIN' => new Entity\IntegerField('RIGHT_MARGIN', [
				'title' => Helper::getMessage(static::LANG.$strField.'RIGHT_MARGIN'),
			]),
			#
			'DATE_CREATED' => new Entity\DatetimeField('DATE_CREATED', [
				'title' => Helper::getMessage(static::LANG.$strField.'DATE_CREATED'),
				'default_value' => new \Bitrix\Main\Type\DateTime,
			]),
			'DATE_MODIFIED' => new Entity\DatetimeField('DATE_MODIFIED', [
				'title' => Helper::getMessage(static::LANG.$strField.'DATE_MODIFIED'),
				'default_value' => new \Bitrix\Main\Type\DateTime,
			]),
		];
		return $arResult;
	}
	
	/**
	 *	Delete item
	 */
	public static function delete($primary) {
		$bCanDelete = true;
		# 1. Delete children groups
		$arQuery = [
			'order' => ['LEFT_MARGIN' => 'ASC'],
			'filter' => ['GROUP_ID' => $primary],
			'select' => ['ID'],
		];
		$resChildrenGroups = static::getList($arQuery);
		while($arChildrenGroup = $resChildrenGroups->fetch()){
			$obResult = static::delete($arChildrenGroup['ID']);
			if(!$obResult->isSuccess()){
				$bCanDelete = false;
			}
			unset($obResult);
		}
		# 2. Cancel delete if group has groups of profile
		$arQuery = [
			'filter' => [
				'GROUP_ID' => $primary,
			],
			'limit' => 1,
		];
		$resData = Helper::call(static::MODULE_ID, 'Profile', 'getList', [$arQuery]);
		if($arProfile = $resData->fetch()){
			$bCanDelete = false;
		}
		# 3. Delete this group
		if($bCanDelete){
			$obResult = parent::delete($primary);
		}
		else{
			$obResult = new \Bitrix\Main\ORM\Data\DeleteResult();
			$obResult->addError(new EntityError(Loc::getMessage('ACRIT_EXP_PROFILE_GROUP_FIELD_ERROR_CANNOT_DELETE_GROUP')));
		}
		return $obResult;
	}
	
	public static function add(array $data){
		Helper::encodeEmojii($data);
		$obResult = parent::add($data);
		return $obResult;
	}
	
	/**
	*	Update item
	*/
	public static function update($primary, array $data) {
		Helper::encodeEmojii($data);
		if($arGroup = static::getList(['filter' => ['ID' => $primary]])->fetch()){
			if(isset($data['GROUP_ID']) && is_numeric($data['GROUP_ID']) && $data['GROUP_ID'] > 0){
				if($arTargetGroup = static::getList(['filter' => ['ID' => $data['GROUP_ID']]])->fetch()){
					if($arTargetGroup['LEFT_MARGIN'] >= $arGroup['LEFT_MARGIN']){
						if($arTargetGroup['RIGHT_MARGIN'] <= $arGroup['RIGHT_MARGIN']){
							$strError = Loc::getMessage('ACRIT_EXP_PROFILE_GROUP_FIELD_ERROR_CANNOT_MOVE_INTO_SELF');
							$obResult = new \Bitrix\Main\ORM\Data\AddResult();
							$obResult->setId($primary);
							$obResult->addError(new \Bitrix\Main\Error($strError));
							return $obResult;
						}
					}
				}
			}
		}
		return parent::update($primary, $data);
	}

	public static function getList(array $parameters=array()){
		$obResult = parent::getList($parameters);
		$obResult->addFetchDataModifier(function($data){
			Helper::decodeEmojii($data);
			return $data;
		});
		return $obResult;
	}

	/**
	 * Resort nested tree like a b_iblock_section
	 */
	public static function resortTree($intId=0, $intCount=0, $intDepth=0){
		$intId = intVal($intId);
		$intCount = intVal($intCount);
		$intDepth = intVal($intDepth);
		if(static::$bResortingTree && !$intId){
			return;
		}
		static::$bResortingTree = true;
		if($intId > 0){
			static::update($intId, [
				'LEFT_MARGIN' => $intCount,
				'RIGHT_MARGIN' => $intCount,
			]);
		}
		$intCount++;
		$resGroups = static::getList([
			'order' => ['SORT', 'NAME', 'ID'],
			'filter' => ['GROUP_ID' => $intId],
			'select' => ['ID'],
		]);
		while($arGroup = $resGroups->fetch()){
			$intCount = static::resortTree($arGroup['ID'], $intCount, $intDepth + 1);
		}
		if(!$intId){
			static::$bResortingTree = false;
			return true;
		}
		static::update($intId, [
			'DEPTH_LEVEL' => $intDepth,
			'RIGHT_MARGIN' => $intCount,
		]);
		return $intCount + 1;
	}

	protected static function callOnAfterAddEvent($object, $fields, $intId){
		static::resortTree();
	}

	protected static function callOnAfterUpdateEvent($object, $fields){
		static::resortTree();
	}

	protected static function callOnUpdateEvent($object, $fields, $ufdata){
		static::resortTree();
	}

	protected static function callOnAfterDeleteEvent($primary, $entity){
		static::resortTree();
	}

	/**
	 * Get list of groups
	 */
	public static function getTree(){
		$arResult = [];
		$resGroups = static::getList([
			'order' => ['LEFT_MARGIN' => 'ASC'],
			'filter' => ['ACTIVE' => 'Y'],
		]);
		while($arGroup = $resGroups->fetch()){
			$arResult[] = $arGroup;
		}
		return $arResult;
	}

	public static function makeTreeMenu($arTree=null, $intParentId=null){
		$strModuleCode = str_replace('.', '_', static::MODULE_ID);
		$arResult = [];
		$bRootLevel = is_null($arTree);
		if($bRootLevel){
			$arTree = static::getTree();
			$intParentId = 0;
		}
		foreach($arTree as $key => $arGroup){
			if($arGroup['GROUP_ID'] == $intParentId){
				$arResult[] = [
					'text' => $arGroup['NAME'],
					'url' => static::adminUrl($strModuleCode.'_new_list.php', [
						'group_id' => $arGroup['ID'],
					]),
					'more_url' => [
						static::adminUrl($strModuleCode.'_new_list.php', [
							'find_GROUP_ID' => $arGroup['ID'],
						]),
						static::adminUrl($strModuleCode.'_new_edit.php', [
							'group_id' => $arGroup['ID'],
						]),
						static::adminUrl($strModuleCode.'_new_group.php', [
							'group_id' => $arGroup['ID'],
						]),
					],
					'icon' => 'iblock_menu_icon_sections',
					'items_id' => 'acrit_'.$strModuleCode.'_group_'.$arGroup['ID'],
					'items' => static::makeTreeMenu($arTree, $arGroup['ID']),
				];
				unset($arTree[$key]);
			}
		}
		return $arResult;
	}

	public static function getNavChain($intGroupId){
		$arResult = [];
		$intMax = 20;
		$intIndex = 0;
		while(true){
			$arQuery = [
				'filter' => ['ID' => $intGroupId],
			];
			if($arChain = static::getList($arQuery)->fetch()){
				$arResult[] = $arChain;
				$intGroupId = $arChain['GROUP_ID'];
			}
			else{
				$intGroupId = false;
			}
			if(!$intGroupId || $intIndex++ >= $intMax){
				break;
			}
		}
		return array_reverse($arResult);
	}

	public static function adminUrl(string $strFile, array $arParams=[], $bAddLang=true):string{
		if($bAddLang && !isset($arParams['lang'])){
			$arParams['lang'] = LANGUAGE_ID;
		}
		return sprintf('/bitrix/admin/%s?%s', $strFile, http_build_query($arParams));
	}
	
}
?>