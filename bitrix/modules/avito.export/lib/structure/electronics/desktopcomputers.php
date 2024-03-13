<?php
namespace Avito\Export\Structure\Electronics;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;

class DesktopComputers implements Category, CategoryLevel
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function categoryLevel() : ?string
	{
		return CategoryLevel::CATEGORY;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'Condition' => new Dictionary\Listing\Condition(),
			'GoodsSubType' => [
				self::getLocale('GOODS_SUB_TYPE_SYSTEM_UNITS'),
				self::getLocale('GOODS_SUB_TYPE_MONOBLOCKS'),
				self::getLocale('GOODS_SUB_TYPE_OTHER'),
			],
		]);
	}

	public function children() : array
	{
		return [];
	}
}