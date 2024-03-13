<?php
namespace Avito\Export\Structure\ForHomeAndGarden\RepairAndConstruction;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Custom;

class Tools extends Custom
{
	use Concerns\HasLocale;

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_TYPE;
	}

	/** @noinspection SpellCheckingInspection */
	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Compound([
			new Dictionary\XmlTree('forhomeandgarden/tools.xml'),
			new Dictionary\Decorator(
				new Dictionary\XmlCascade('forhomeandgarden/repairandconstruction/brendy_instrumentov.xml'),
				[
					'rename' => [
						'brand' => 'Brand',
					],
				]
			),
		]);
	}
}