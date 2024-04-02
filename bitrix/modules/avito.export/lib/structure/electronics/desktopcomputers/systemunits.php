<?php
namespace Avito\Export\Structure\Electronics\DesktopComputers;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;

class SystemUnits implements Category, CategoryLevel
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_SUB_TYPE;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Compound([
			new Dictionary\Fixed([
				'Brand' => [
					'Apple',
					self::getLocale('BRAND_OTHER'),
				],

			]),
			new Dictionary\Decorator(new Dictionary\Compound([
				new Dictionary\Fixed([
					'Type' => [
						self::getLocale('TYPE_GAME'),
						self::getLocale('TYPE_OFFICE'),
					],
				]),
				// todo https://catalogs.avito.ru/feed/processors_pc.xml
				new Dictionary\Fixed([
					'RamSize' => explode('|', self::getLocale('RAM_VARIANTS')),
				]),
				// todo https://catalogs.avito.ru/feed/materinskie_platy_pc.xml
				// todo https://catalogs.avito.ru/feed/gpus_pc.xml
			]), [
				'wait' => [ 'Brand' => self::getLocale('BRAND_OTHER') ],
			])
		]);
	}

	public function children() : array
	{
		return [];
	}
}