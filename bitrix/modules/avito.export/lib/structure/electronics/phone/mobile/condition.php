<?php
namespace Avito\Export\Structure\Electronics\Phone\Mobile;

use Avito\Export\Concerns;
use Avito\Export\Dictionary\Listing\Listing;

class Condition implements Listing
{
	use Concerns\HasLocale;

	public function values() : array
	{
		return [
			self::getLocale('NEW'),
			self::getLocale('EXCELLENT'),
			self::getLocale('GOOD'),
			self::getLocale('SATISFACTORY'),
			self::getLocale('NEEDS_REPAIR'),
		];
	}
}