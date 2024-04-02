<?php
namespace Avito\Export\Structure\Transportation\WaterTransport;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class MotorBoatsAndMotors implements Structure\Category, Structure\CategoryLevel
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
					'make_and_model' => 'transportation/watertransport/motorboatsandmotors/motornye_lodki.xml',
					'other_tags' => 'transportation/watertransport/motorboatsandmotors/boats_tags.xml',
				],
				'Type-->TYPE_PVC_BOAT_INFLATABLE' => [
					'Year' => 'transportation/watertransport/motorboatsandmotors/boats_year.xml',
				],
				'Type-->TYPE_RIB_COMBINATION_BOAT' => [
					'Year' => 'transportation/watertransport/motorboatsandmotors/boats_year.xml',
				],
				'Type-->TYPE_RIGID_HULL_BOAT' => [
					'Year' => 'transportation/watertransport/motorboatsandmotors/boats_year.xml',
				],
				'EngineIncluded-->ENGINE_INCLUDED_YES' => [
					'EngineMake' => 'transportation/watertransport/motorboatsandmotors/engine_make.xml',
					'EngineYear' => 'transportation/watertransport/motorboatsandmotors/engine_year.xml',
				],
			])
		);
	}

	public function children() : array
	{
		return [];
	}
}