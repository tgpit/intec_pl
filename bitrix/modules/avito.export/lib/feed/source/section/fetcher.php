<?php
namespace Avito\Export\Feed\Source\Section;

use Avito\Export\Concerns;
use Avito\Export\Feed\Source;

class Fetcher extends Source\FetcherSkeleton
{
	use Concerns\HasLocale;
	use Concerns\HasOnce;

	protected $typeMap = [
		'string' => 'S',
		'integer' => 'N',
		'double' => 'N',
		'enumeration' => 'L',
		'hlblock' => 'L',
		'url' => 'S',
		'file' => 'F',
		'iblock_element' => 'E',
		'iblock_section' => 'G',
	];

	public function listener() : Source\Listener
	{
		return new Source\NoValue\Listener();
	}

	public function title() : string
	{
		return self::getLocale('TITLE');
	}

	public function modules() : array
	{
		return [ 'iblock' ];
	}

	public function fields(Source\Context $context) : array
	{
		return $this->once('fields', function() use ($context) {
			global $USER_FIELD_MANAGER;

			$result = [];
			$factory = new Source\Field\Factory();
			$userFields = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . $context->iblockId() . '_SECTION', 0, LANGUAGE_ID);

			foreach ($userFields as $userField)
			{
				$userClassName = $userField['USER_TYPE']['CLASS_NAME'] ?? null;
				$commonField = [
					'ID' => $userField['FIELD_NAME'],
					'NAME' => sprintf('[%s] %s', $userField['FIELD_NAME'], $userField['EDIT_FORM_LABEL']),
					'TYPE' => $this->typeMap[$userField['USER_TYPE_ID']] ?? 'S',
					'FILTERABLE' => false,
				];

				if ($userClassName !== null && class_exists($userClassName) && method_exists($userClassName, 'avitoExportFeedFields'))
				{
					$embeddedFields = $userClassName::avitoExportFeedFields($userField);

					if (!is_array($embeddedFields)) { continue; }

					foreach ($embeddedFields as $embeddedField)
					{
						$field = $embeddedField + $commonField;
						$field['ID'] = $commonField['ID'] . '.' . $embeddedField['ID'];
						$field['NAME'] = $commonField['NAME'] . sprintf(' (%s)', $embeddedField['TITLE']);

						$result[] = $factory->make($field);
					}
				}
				else
				{
					$result[] = $factory->make($commonField);
				}
			}

			return $result;
		});
	}

	public function select(array $fields) : array
	{
		return [
			'ELEMENT' => [ 'IBLOCK_SECTION_ID' ],
		];
	}

	public function values(array $elements, array $parents, array $siblings, array $select, Source\Context $context) : array
	{
		$catalog = Source\Routine\Values::catalogElements($elements, $parents);
		$primarySections = array_column($catalog, 'IBLOCK_SECTION_ID', 'IBLOCK_SECTION_ID');
		$linkedSections = $this->linkedSections(array_values(array_column($catalog, 'ID', 'ID')));
		$sectionIds = $this->usedSections($primarySections, $linkedSections);
		$sectionValues = $this->sectionValues($context->iblockId(), $sectionIds, $select);
		$result = [];

		foreach ($catalog as $targetId => $element)
		{
			$result[$targetId] = $this->mergeValues(
				$sectionValues,
				(int)$element['IBLOCK_SECTION_ID'],
				$linkedSections[$element['ID']] ?? []
			);
		}

		return $result;
	}

	protected function linkedSections(array $elementIds) : array
	{
		if (empty($elementIds)) { return []; }

		$result = [];
		$query = \CIBlockElement::GetElementGroups($elementIds, true, [
			'IBLOCK_ELEMENT_ID',
			'ID',
		]);

		while ($group = $query->Fetch())
		{
			$elementId = $group['IBLOCK_ELEMENT_ID'];

			if (!isset($result[$elementId])) { $result[$elementId] = []; }

			$result[$elementId][] = $group['ID'];
		}

		return $result;
	}

	protected function usedSections(array $primarySections, array $linkedSections) : array
	{
		$map = $primarySections;

		foreach ($linkedSections as $linked)
		{
			$map += array_flip($linked);
		}

		return array_keys($map);
	}

	protected function mergeValues(array $sectionValues, int $primaryId, array $linkedIds) : array
	{
		$filter = static function($value) { return is_scalar($value) ? (string)$value !== '' : !empty($value); };
		$result = array_filter($sectionValues[$primaryId] ?? [], $filter);

		foreach ($linkedIds as $linkedId)
		{
			if (!isset($sectionValues[$linkedId])) { continue; }

			$result += array_filter($sectionValues[$linkedId], $filter);
		}

		return $result;
	}

	protected function sectionValues(int $iblockId, array $sectionIds, array $select) : array
	{
		$embeddedMap = $this->propertiesEmbeddedMap($select);
		$plainSelect = array_diff($select, array_keys($embeddedMap));
		$plainSelect = array_unique(array_merge($plainSelect, array_column($embeddedMap, 0)));

		$sectionValues = $this->querySectionValues($iblockId, $sectionIds, $plainSelect);
		$sectionValues = $this->extendEmbeddedValues($sectionValues, $iblockId, $embeddedMap);

		return $sectionValues;
	}

	protected function propertiesEmbeddedMap($select) : array
	{
		$result = [];

		foreach ($select as $name)
		{
			$dotPosition = mb_strpos($name, '.');

			if ($dotPosition === false) { continue; }

			$result[$name] = [
				mb_substr($name, 0, $dotPosition),
				mb_substr($name, $dotPosition + 1),
			];
		}

		return $result;
	}

	public function querySectionValues(int $iblockId, array $sectionIds, array $select) : array
	{
		$select = array_unique($select);
		$result = array_fill_keys($sectionIds, []);
		$nextIds = $sectionIds;
		$fetched = [];

		while (!empty($nextIds))
		{
			$nextTargetMap = [];

			$query = \CIBlockSection::GetList(
				[],
				[ 'IBLOCK_ID' => $iblockId, 'ID' => $nextIds ],
				false,
				array_merge(
					[ 'IBLOCK_ID', 'ID', 'IBLOCK_SECTION_ID' ],
					$select
				)
			);

			while ($row = $query->Fetch())
			{
				$sectionId = (int)$row['ID'];
				$fetched[$sectionId] = $row;
				$targetIds = $targetMap[$sectionId] ?? [];

				if (isset($result[$sectionId]))
				{
					$targetIds[] = $sectionId;
				}

				foreach ($targetIds as $targetId)
				{
					$sourceId = $sectionId;

					while ($sourceId > 0 && isset($fetched[$sourceId]))
					{
						$sourceRow = $fetched[$sourceId];
						$parentId = (int)$sourceRow['IBLOCK_SECTION_ID'];
						$isAllFilled = true;

						foreach ($select as $name)
						{
							if (isset($result[$targetId][$name])) { continue; }

							$value = $sourceRow[$name] ?? null;

							if (Source\Routine\Values::isEmpty($value))
							{
								$isAllFilled = false;
								continue;
							}

							$result[$targetId][$name] = $value;
						}

						if ($parentId > 0 && !$isAllFilled && !isset($fetched[$parentId]))
						{
							if (!isset($nextTargetMap[$parentId]))
							{
								$nextTargetMap[$parentId] = [];
							}

							$nextTargetMap[$parentId][] = $targetId;
							break;
						}

						$sourceId = $parentId;
					}
				}
			}

			$targetMap = $nextTargetMap;
			$nextIds = array_keys($targetMap);
		}

		return $result;
	}

	protected function extendEmbeddedValues(array $sectionValues, int $iblockId, array $embeddedMap) : array
	{
		global $USER_FIELD_MANAGER;

		$entityType = 'IBLOCK_' . $iblockId . '_SECTION';
		$userFields = $USER_FIELD_MANAGER->GetUserFields($entityType, 0, LANGUAGE_ID);

		foreach ($sectionValues as &$values)
		{
			foreach ($embeddedMap as $embeddedSign => [$propertyCode, $embeddedField])
			{
				if (!isset($userFields[$propertyCode])) { continue; }

				$userField = $userFields[$propertyCode];
				$userClassName = $userField['USER_TYPE']['CLASS_NAME'] ?? null;

				if (
					$userClassName === null
					|| !class_exists($userClassName)
					|| !method_exists($userClassName, 'avitoExportFeedValue')
				)
				{
					continue;
				}

				$values[$embeddedSign] = $userClassName::avitoExportFeedValue(
					$userField,
					[ 'VALUE' => $values[$propertyCode] ],
					$embeddedField
				);
			}
		}
		unset($values);

		return $sectionValues;
	}
}