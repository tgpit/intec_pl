<?php
namespace Avito\Export\Structure\ForHomeAndGarden\RepairAndConstruction;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Custom;

class PlumbingWaterAndSauna extends Custom
{
	use Concerns\HasLocale;

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_TYPE;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\XmlCascade('forhomeandgarden/repairandconstruction/plumbingwaterandsauna.xml');
	}
}