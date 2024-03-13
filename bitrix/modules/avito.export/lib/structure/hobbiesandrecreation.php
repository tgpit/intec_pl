<?php
namespace Avito\Export\Structure;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;

class HobbiesAndRecreation implements Category
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'AdType' => new HobbiesAndRecreation\Properties\AdType()
		]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			return [
				new HobbiesAndRecreation\HuntingAndFishing(),
				new HobbiesAndRecreation\TicketsAndTravel(),
				new HobbiesAndRecreation\MusicalInstruments(),
				new HobbiesAndRecreation\Bicycles(),
				new HobbiesAndRecreation\BooksAndMagazines(),
				new HobbiesAndRecreation\Collecting(),
				new HobbiesAndRecreation\SportAndRecreation(),
			];
		});
	}
}