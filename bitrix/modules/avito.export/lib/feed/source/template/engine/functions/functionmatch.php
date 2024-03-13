<?php
namespace Avito\Export\Feed\Source\Template\Engine\Functions;

use Bitrix\Iblock;
use Bitrix\Main;

if (!Main\Loader::includeModule('iblock')) { return; }

class FunctionMatch extends Iblock\Template\Functions\FunctionBase
{
	public function calculate(array $parameters)
	{
		$needed = (string)array_shift($parameters);
		$result = null;

		if (count($parameters) % 2 === 1)
		{
			$result = array_pop($parameters);
		}

		foreach ($parameters as $index => $param)
		{
			if ($index % 2 !== 0) { continue; }

			if ((string)$param === $needed)
			{
				$result = $parameters[$index + 1] ?? null;
				break;
			}
		}

		return $result;
	}
}