<?php
namespace Avito\Export\Structure;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;

class Electronics implements Category
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
			'AdType' => new Electronics\Properties\AdType()
		]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			return [
				new Electronics\Phone(),
				new Electronics\AudioVideo(),
				new Electronics\ComputerProducts(),
				new Electronics\PhotoEquipment(),
				new Electronics\GameConsolePrograms(),
				new Electronics\OfficeEquipmentAndSupplies(),
				new Electronics\TabletsAndEBooks(),
				new Electronics\Laptops(),
				new Electronics\DesktopComputers(),
			];
		});
	}
}