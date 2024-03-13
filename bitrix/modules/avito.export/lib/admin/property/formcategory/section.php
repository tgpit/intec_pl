<?php
namespace Avito\Export\Admin\Property\FormCategory;

use Avito\Export\Assert;
use Avito\Export\Feed\Source;
use Bitrix\Main;
use Bitrix\Iblock;
use Avito\Export\Concerns;
use Avito\Export\Admin\Property\CategoryField;

class Section implements Behavior
{
	use Concerns\HasLocale;

	private $parents = [];
	private $propertyName;
	private $iblockId;
	private $sectionMap = [];

	public function title() : string
	{
		return self::getLocale('TITLE');
	}

	protected function setPropertyName(string $name) : void
	{
		$this->propertyName = $name;
	}

	protected function getPropertyName() : string
	{
		return $this->propertyName;
	}

	protected function setIblockId(int $id) : void
	{
		$this->iblockId = $id;
	}

	protected function getIblockId() : int
	{
		return $this->iblockId;
	}

	private function initParams(array $form) : void
	{
		Assert::notNull($form['property'], 'property');

		$iblockId = $this->formIblockId($form);

		$this->setIblockId($iblockId);
		$this->setPropertyName($form['property']);

		$this->sectionMap = $this->formSections($form);

		$this->findParentsIds();
	}

	public function variants(array $property) : array
	{
		global $USER_FIELD_MANAGER;

		$iblockId = $this->propertyIblockId($property);

		if ($iblockId <= 0 || !Main\Loader::includeModule('iblock')) { return []; }

		$typeId = sprintf('IBLOCK_%s_SECTION', $iblockId);
		$result = [];

		foreach ($USER_FIELD_MANAGER->GetUserFields($typeId, 0, LANGUAGE_ID) as $field)
		{
			if ($field['USER_TYPE_ID'] !== CategoryField::USER_TYPE_ID) { continue; }

			$result[] = [
				'ID' => $field['FIELD_NAME'],
				'NAME' => $field['EDIT_FORM_LABEL'],
			];
		}

		return $result;
	}

	public function options(array $property, string $field) : array
	{
		return [
			'primaryName' => 'IBLOCK_ELEMENT_SECTION_ID',
			'selectName' => 'IBLOCK_SECTION',
			'iblockId' => $property['IBLOCK_ID'],
			'property' => $field,
		];
	}

	public function value(array $form) : string
	{
		$this->initParams($form);

		$result = null;

		foreach ($this->sectionMap as $sectionId)
		{
			$value = $this->findValueInParent($sectionId, $this->parents[$sectionId]);

			if (is_array($value))
			{
				$value = reset($value);
			}

			if ((string)$value !== '')
			{
				$result = (string)$value;
				break;
			}
		}

		if ($result === null)
		{
			throw new Main\ArgumentException(self::getLocale('SECTION_CATEGORY_REQUIRED'));
		}

		return $result;
	}

	private function findParentsIds() : void
	{
		if (empty($this->sectionMap) || !Main\Loader::includeModule('iblock')) { return; }

		foreach ($this->sectionMap as $section)
		{
			$list = \CIBlockSection::GetNavChain($this->getIblockId(), $section, [
				'ID',
				'IBLOCK_SECTION_ID',
			], true);

			foreach ($list as $arSectionPath)
			{
				$this->parents[$section][$arSectionPath['ID']] = [
					'IBLOCK_SECTION_ID' => (int)$arSectionPath['IBLOCK_SECTION_ID']
				];
			}
		}
	}

	private function findValueInParent($sectionId, $parents)
	{
		global $USER_FIELD_MANAGER;

		$value = $USER_FIELD_MANAGER->GetUserFieldValue(
			sprintf('IBLOCK_%s_SECTION', $this->getIblockId()),
			$this->getPropertyName(),
			$sectionId
		);

		if (empty($value) && isset($parents[$sectionId]))
		{
			$sectionId = $parents[$sectionId]['IBLOCK_SECTION_ID'];
			if ($sectionId <= 0) { return null; }
			$value = $this->findValueInParent($sectionId, $parents);
		}

		return $value;
	}

	protected function formIblockId(array $form) : int
	{
		Assert::notNull($form['iblockId'], 'iblockId');

		return $form['iblockId'];
	}

	protected function formSections(array $form) : array
	{
		$sections = array_merge(
			[ $form['iblockSectionId'] ?? null ],
			$form['iblockSection'] ?? []
		);

		Main\Type\Collection::normalizeArrayValuesByInt($sections, false);

		if (empty($sections))
		{
			throw new Main\ArgumentException(self::getLocale('FORM_SECTION_REQUIRED'));
		}

		return $sections;
	}

	protected function propertyIblockId(array $property) : int
	{
		return (int)($property['IBLOCK_ID'] ?? 0);
	}

	public function elementValues(string $propertyId, array $elementIds) : array
	{
		if (empty($elementIds) || !Main\Loader::includeModule('iblock')) { return []; }

		$result = [];
		$elements = $this->findElements($elementIds);

		if (empty($elements)) { return []; }

		$iblockId = $this->elementsIblockId($elements);
		$primarySections = $this->primarySections($elements);
		$additionalSections = $this->additionalSections($elementIds);
		$usedSections = $this->usedSections($primarySections, $additionalSections);
		$sectionValues = (new Source\Section\Fetcher())->querySectionValues($iblockId, $usedSections, [ $propertyId ]);

		foreach ($primarySections as $elementId => $primarySection)
		{
			if (!empty($sectionValues[$primarySection][$propertyId]))
			{
				$result[$elementId] = $sectionValues[$primarySection][$propertyId];
				continue;
			}

			$elementAdditional = $additionalSections[$elementId] ?? [];

			foreach ($elementAdditional as $sectionId)
			{
				if (!empty($sectionValues[$sectionId][$propertyId]))
				{
					$result[$elementId] = $sectionValues[$sectionId][$propertyId];
					break;
				}
			}
		}

		return $result;
	}

	protected function findElements(array $elementIds) : array
	{
		if (empty($elementIds)) { return []; }

		$query = Iblock\ElementTable::getList([
			'filter' => ['ID' => $elementIds],
			'select' => ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID']
		]);

		return $query->fetchAll();
	}

	protected function elementsIblockId(array $elements) : int
	{
		$element = reset($elements);
		$iblockId = $element['IBLOCK_ID'] ?? null;

		Assert::notNull($iblockId, '$iblockId');

		return (int)$iblockId;
	}

	protected function primarySections(array $elements) : array
	{
		return array_column($elements, 'IBLOCK_SECTION_ID', 'ID');
	}

	protected function additionalSections(array $elementIds) : array
	{
		if (empty($elementIds)) { return []; }

		$result = [];

		$query = \CIBlockElement::GetElementGroups($elementIds, false, ['IBLOCK_ELEMENT_ID', 'ID']);

		while ($row = $query->Fetch())
		{
			if (!isset($result[$row['IBLOCK_ELEMENT_ID']]))
			{
				$result[$row['IBLOCK_ELEMENT_ID']] = [];
			}

			$result[$row['IBLOCK_ELEMENT_ID']][] = $row['ID'];
		}

		return $result;
	}

	protected function usedSections(array $primarySections, array $additionalSections) : array
	{
		$result = array_flip($primarySections);

		foreach ($additionalSections as $elementSections)
		{
			$result += array_flip($elementSections);
		}

		return array_keys($result);
	}

	public function saveValues(string $propertyId, array $elementIds, string $value) : void
	{
		if (!Main\Loader::includeModule('iblock')) { return; }

		$elements = $this->findElements($elementIds);

		if (empty($elements)) { return; }

		$primarySections = $this->primarySections($elements);
		$primarySections = array_unique($primarySections);

		foreach ($primarySections as $sectionId)
		{
			$updateProvider = new \CIBlockSection();
			$updated = $updateProvider->Update($sectionId, [ $propertyId => $value ], false, false);

			if ($updated === false)
			{
				throw new Main\SystemException(self::getLocale('SAVE_VALUE_FAILED', [
					'#MESSAGE#' => $updateProvider->LAST_ERROR,
				]));
			}
		}
	}
}