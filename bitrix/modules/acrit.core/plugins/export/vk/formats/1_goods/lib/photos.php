<?
/**
 * Acrit Core: VK photos hashes
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\VkHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class PhotosTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_export_vk_photos';
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
			'HASH' => new Entity\TextField('HASH', array(
				'title' => Helper::getMessage($strLang.'HASH'),
			)),
			'VK_PHOTO_ID' => new Entity\StringField('VK_PHOTO_ID', array(
				'title' => Helper::getMessage($strLang.'VK_PHOTO_ID'),
			)),
			'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
				'title' => Helper::getMessage($strLang.'TIMESTAMP_X'),
			)),
		);
	}

	public static function addPhoto($photo_url, $vk_photo_id) {
		if ($vk_photo_id) {
			self::add([
				'HASH'        => md5($photo_url),
				'VK_PHOTO_ID' => $vk_photo_id,
				'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
			]);
		}
	}

	public static function find($photo_key) {
		$result = false;
		if ($item = self::getList([
				'select' =>[
					'VK_PHOTO_ID',
				],
				'filter' =>[
					'HASH' => md5($photo_key),
				],
			])->fetch()) {
			if ($item['VK_PHOTO_ID']) {
				$result = $item['VK_PHOTO_ID'];
			}
		}
		return $result;
	}
}
