<?php
namespace Avito\Export\Feed\Source\Routine;

use Avito\Export\Feed\Source;

class QueryFilter
{
	public static function make(array $conditions, array $fields) : array
	{
		/** @var array<string, Source\Field\Field> $fieldMap */
		$fieldMap = array_combine(
			array_map(static function(Source\Field\Field $field) { return $field->id(); }, $fields),
			$fields
		);
		$result = [];

		foreach ($conditions as $condition)
		{
			if (!isset($fieldMap[$condition['FIELD']])) { continue; }

			$field = $fieldMap[$condition['FIELD']];
			$fieldFilter = $field->filter($condition['COMPARE'], $condition['VALUE']);

			foreach ($fieldFilter as $queryField => $queryValue)
			{
				if (!isset($result[$queryField]))
				{
					$result[$queryField] = $queryValue;
				}
				else
				{
					if ($result[$queryField] === $queryValue) { continue; }

					if (!is_array($result[$queryField]))
					{
						$result[$queryField] = [$result[$queryField]];
					}

					if (!is_array($queryValue))
					{
						$result[$queryField][] = $queryValue;
					}
					else
					{
						$result[$queryField] = array_merge($result[$queryField], $queryValue);
					}
				}
			}
		}

		return $result;
	}
}