<?php
namespace Avito\Export\Dictionary\Listing;

use Avito\Export\Concerns;

class Condition implements Listing
{
	use Concerns\HasLocale;

	public function values() : array
	{
		return [
			self::getLocale('NEW'),
			self::getLocale('USED'),
		];
	}
}