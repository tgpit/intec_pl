<?php
namespace Avito\Export\Structure\HobbiesAndRecreation;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class Collecting implements Structure\Category, Structure\CategoryLevel
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function categoryLevel() : ?string
	{
		return Structure\CategoryLevel::CATEGORY;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([ 'Condition' => new Dictionary\Listing\Condition() ]);
	}

	public function children() : array
	{
		return $this->once('children', function() {
			self::includeLocale();

			return (new Structure\Factory(self::getLocalePrefix()))
				->make($this->getDictionaryRootVariants());
		});
	}

	private function getDictionaryRootVariants() : array
	{
		return ( new Dictionary\XmlTree('hobbiesandrecreation/collecting/goodstype.xml') )
			->variants('GoodsType');
	}
}