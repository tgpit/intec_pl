<?php
namespace Avito\Export\Structure\Transportation\WaterTransport;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class JetSkis implements Structure\Category, Structure\CategoryLevel
{
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function categoryLevel() : ?string
	{
		return Structure\CategoryLevel::VEHICLE_TYPE;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		$dictionaryFactory = new Structure\DictionaryFactory(self::getLocalePrefix());

		return new Dictionary\Compound(
			$dictionaryFactory->make([
				'all' => [
					'other_tags' => 'transportation/watertransport/jetskis/gidrocikly_tags.xml',
					'make_and_model' => 'transportation/watertransport/jetskis/gidrocikly.xml',
					'Year' => 'transportation/watertransport/jetskis/gidrocikly_year.xml',
				]
			])
		);
	}

	public function children() : array
	{
		return [];
	}
}