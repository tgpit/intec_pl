<?php
namespace Avito\Export\Data;

class Number
{
	public static function cast($value) : ?int
	{
		return is_numeric($value) ? (int)$value : null;
	}
}