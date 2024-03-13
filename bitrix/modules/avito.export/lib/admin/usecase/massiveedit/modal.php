<?php
namespace Avito\Export\Admin\UseCase\MassiveEdit;

use Bitrix\Main;
use Bitrix\Iblock;
use Avito\Export\Admin;
use Avito\Export\Admin\Property\FormCategory;
use Avito\Export\Concerns;
use Avito\Export\Assert;
use Avito\Export\Utils\Word;
use Avito\Export\Config;

class Modal extends Admin\Page\Page
{
	use Concerns\HasLocale;

	protected const NO_CATEGORY = 'NO_CATEGORY';
	protected const SHOW_DETAILS_LIMIT = 1000;
	protected const CHUNK_SIZE = 1000;

	public function hasRequest() : bool
	{
		return $this->request->getPost('massiveEditAction') !== null;
	}

	public function processRequest() : void
	{
		$action = $this->request->getPost('massiveEditAction');

		if ($action === 'save')
		{
			$this->save();
		}
		else
		{
			throw new Main\ArgumentException(sprintf('unknown %s action', $action));
		}
	}

	/** @noinspection JSUnresolvedReference */
	public function save() : void
	{
		global $APPLICATION;

		$values = $this->request->getPost('VALUES');
		$propertyId = $this->request->getPost('property');

		Assert::notNull($propertyId, '$_POST[property]');
		Assert::isArray($values, '$_POST[VALUES]');

		$characteristicProperty = $this->characteristicProperty($propertyId);
		$categoryProperties = $this->categoryProperties($characteristicProperty['IBLOCK_ID']);
		[$firstCategoryPropertyType, $firstCategoryPropertyId] = reset($categoryProperties);
		$categoryAdapter = FormCategory\Registry::make($firstCategoryPropertyType);

		foreach ($values as $incoming)
		{
			$elementIds = explode(',', $incoming['ELEMENTS']);

			$this->checkElementsWriteAccess($characteristicProperty['IBLOCK_ID'], $elementIds);

			// category

			if ($incoming['CATEGORY_ORIGIN'] !== $incoming['CATEGORY'])
			{
				$categoryAdapter->saveValues($firstCategoryPropertyId, $elementIds, $incoming['CATEGORY']);
			}

			// characteristic

			$characteristicValues = $this->characteristicValues($characteristicProperty, $elementIds);
			$characteristicCommon = $this->characteristicsCommonValues($elementIds, $characteristicValues);

			foreach ($elementIds as $elementId)
			{
				$storedValues = $characteristicValues[$elementId] ?? [];
				$incomingValues = $this->normalizeIncomingCharacteristic($characteristicProperty, $incoming['CHARACTERISTICS']);
				$incomingValues += array_diff_key($storedValues, $characteristicCommon);

				/** @noinspection TypeUnsafeComparisonInspection */
				if ($incomingValues == $storedValues) { continue; }

				if ($characteristicProperty['MULTIPLE'] === 'Y')
				{
					$storageValue = $this->rebuildCharacteristicValuesToMultiple($incomingValues);
				}
				else
				{
					$storageValue = [ 'VALUE' => $incomingValues ];
				}

				\CIBlockElement::SetPropertyValuesEx($elementId, $characteristicProperty['IBLOCK_ID'], [ $propertyId => $storageValue ]);
			}
		}

		$APPLICATION->RestartBuffer();
		echo '<script> top.BX.onCustomEvent("avitoExportMassiveEditDone"); </script>';
		die();
	}

	protected function normalizeIncomingCharacteristic(array $property, $values) : array
	{
		if (!is_array($values)) { return []; }
		if ($property['MULTIPLE'] !== 'Y') { return $values; }

		$result = [];

		foreach ($values as $item)
		{
			$result[$item['DESCRIPTION']] = $item['VALUE'];
		}

		return $result;
	}

	public function getRequiredModules() : array
	{
		return [
			'iblock',
		];
	}

	public function show() : void
	{
		global $APPLICATION;

		$propertyId = $this->request->get('property');
		$elementIds = $this->request->get('selected');
		$iblockId = $this->request->get('iblockId');

		Assert::notNull($propertyId, '$_POST[property]');
		Assert::isArray($elementIds, '$_POST[selected]');
		Assert::notNull($elementIds, '$_POST[iblockId]');

		$this->checkIblockReadAccess($iblockId);

		$elementIds = $this->selectedElements($elementIds, $iblockId);
		$isElementsLimited = false;

		if (count($elementIds) > self::elementsLimit())
		{
			$elementIds = array_slice($elementIds, 0, self::elementsLimit(), true);
			$isElementsLimited = true;
		}

		$characteristicProperty = $this->characteristicProperty($propertyId);
		$characteristicValues = $this->characteristicValues($characteristicProperty, $elementIds);
		$parentCharacteristicValues = $this->parentCharacteristicValues($characteristicProperty, $elementIds);

		$categoryProperties = $this->categoryProperties($iblockId);
		$categoryValues = $this->categoryValues($categoryProperties, $elementIds);
		[$firstCategoryPropertyType, $firstCategoryPropertyId] = reset($categoryProperties);
		$firstCategoryProperty = $this->categoryProperty($iblockId, $firstCategoryPropertyType, $firstCategoryPropertyId);

		$elementGroups = $this->groupByCategory($elementIds, $categoryValues);
		$elementNames = $this->elementNames($elementIds);

		?>
		<form action="<?= htmlspecialcharsbx($APPLICATION->GetCurPageParam()) ?>" method="post" name="form_avito_massive_edit" id="form_avito_massive_edit">
			<input type="hidden" name="massiveEditAction" value="save" />
			<input type="hidden" name="property" value="<?= $propertyId ?>" />
			<?php
			if ($isElementsLimited)
			{
				echo BeginNote('style="margin-top: -10px;"');
				echo self::getLocale('ELEMENTS_LIMITED', ['#COUNT#' => self::elementsLimit()]);
				echo EndNote();
			}

			$index = 0;

			foreach ($elementGroups as $category => $groupIds)
			{
				if ($category === self::NO_CATEGORY) { $category = null; }

				$groupCharacteristics = $this->characteristicsCommonValues($groupIds, $characteristicValues);
				$groupParentCharacteristics = $this->characteristicsCommonValues($groupIds, $parentCharacteristicValues);
				$groupCount = count($groupIds);

				?>
				<div class="bx-avito-massive-edit-panel">
					<?php
					echo sprintf(
						'<span class="bx-avito-massive-edit-panel__title">%s</span>',
						$category ?? self::getLocale('NO_CATEGORY')
					);
					?>
					<input type="hidden" name="VALUES[<?= $index ?>][ELEMENTS]" value="<?= implode(',', $groupIds) ?>" />
					<input type="hidden" name="VALUES[<?= $index ?>][CATEGORY_ORIGIN]" value="<?= htmlspecialcharsbx($category) ?>">
					<div class="bx-avito-massive-edit-field">
						<?php
						if (
							$firstCategoryPropertyType === FormCategory\Registry::ELEMENT
							|| $firstCategoryPropertyType === FormCategory\Registry::PRODUCT_ELEMENT
						)
						{
							echo Admin\Property\CategoryProperty::getPropertyFieldHtml(
								$firstCategoryProperty,
								[ 'VALUE' => $category ],
								[ 'VALUE' => "VALUES[$index][CATEGORY]" ]
							);
						}
						else
						{
							echo Admin\Property\CategoryField::getEditFormHTML(
								$firstCategoryProperty,
								[ 'NAME' => "VALUES[$index][CATEGORY]", 'VALUE' => $category ]
							);
						}
						?>
					</div>
					<div class="bx-avito-massive-edit-field">
						<span class="bx-avito-massive-edit-field__label">
							<?= $characteristicProperty['NAME'] ?>
						</span>
						<?php
						if ($characteristicProperty['MULTIPLE'] === 'Y')
						{
							echo Admin\Property\CharacteristicProperty::getPropertyFieldHtmlMulty(
								$characteristicProperty,
								$this->rebuildCharacteristicValuesToMultiple($groupCharacteristics),
								[
									'VALUE' => "VALUES[$index][CHARACTERISTICS]",
									'PARENT_VALUE' => $groupParentCharacteristics,
									'CATEGORY_PROPERTIES' => [
										[ FormCategory\Registry::MASSIVE_EDIT, $index ],
									],
								]
							);
						}
						else
						{
							echo Admin\Property\CharacteristicProperty::getPropertyFieldHtml(
								$characteristicProperty,
								[ 'VALUE' => $groupCharacteristics ],
								[
									'VALUE' => "VALUES[$index][CHARACTERISTICS]",
									'PARENT_VALUE' => $groupParentCharacteristics,
									'CATEGORY_PROPERTIES' => [
										[ FormCategory\Registry::MASSIVE_EDIT, $index ],
									],
								]
							);
						}
						?>
					</div>
					<details>
						<summary><?= Word::declension($groupCount, [
							self::getLocale('FOR_ELEMENTS_1', [ '#COUNT#' => $groupCount ]),
							self::getLocale('FOR_ELEMENTS_2', [ '#COUNT#' => $groupCount ]),
							self::getLocale('FOR_ELEMENTS_5', [ '#COUNT#' => $groupCount ]),
						]) ?></summary>
						<ul>
							<?php
							$elements = array_slice($groupIds, 0, self::SHOW_DETAILS_LIMIT);
							foreach ($elements as $elementId)
							{
								echo sprintf('<li>[%s] %s</li>', $elementId, $elementNames[$elementId]);
							}
							if ($groupCount > self::SHOW_DETAILS_LIMIT)
							{
								echo '<li>...</li>';
							}
							?>
						</ul>
					</details>
				</div>
				<?php

				$index++;
			}
			?>
		</form>
		<?php
	}

	protected function characteristicProperty(int $propertyId) : array
	{
		$queryProperty = Iblock\PropertyTable::getList([
			'filter' => [ '=ID' => $propertyId ],
			'limit' => 1,
		]);
		$characteristicProperty = $queryProperty->fetch();

		if ($characteristicProperty === false)
		{
			throw new Main\ObjectNotFoundException('characteristic property not found');
		}

		if ($characteristicProperty['USER_TYPE'] !== Admin\Property\CharacteristicProperty::USER_TYPE)
		{
			throw new Main\ArgumentException('characteristic property not valid user type');
		}

		if ($characteristicProperty['MULTIPLE'] === 'Y' && $characteristicProperty['WITH_DESCRIPTION'] !== 'Y')
		{
			throw new Main\SystemException(sprintf('%s: %s',
				$characteristicProperty['NAME'],
				self::getLocale('CHARACTERISTICS_PROPERTY_NEED_DESCRIPTION')
			));
		}

		$characteristicProperty['USER_TYPE_SETTINGS'] = $characteristicProperty['USER_TYPE_SETTINGS_LIST'];

		return $characteristicProperty;
	}

	protected function categoryProperties(int $iblockId) : array
	{
		$result = Admin\Property\ValueInherit\Category::properties($iblockId);

		if (empty($result))
		{
			throw new Main\SystemException(self::getLocale('CATEGORY_PROPERTY_MISSING'));
		}

		return $result;
	}

	protected function categoryValues(array $categoryProperties, array $elementIds) : array
	{
		$leftElements = array_flip($elementIds);
		$result = [];

		foreach ($categoryProperties as [$categoryType, $categoryPropertyId])
		{
			if (empty($leftElements)) { break; }

			$formCategory = FormCategory\Registry::make($categoryType);

			foreach (array_chunk($leftElements, static::CHUNK_SIZE, true) as $leftChunk)
			{
				$stored = $formCategory->elementValues($categoryPropertyId, array_keys($leftChunk));

				$result += array_filter($stored);
			}

			$leftElements = array_diff_key($leftElements, $result);
		}

		return $result;
	}

	protected function categoryProperty(int $iblockId, string $propertyType, string $propertyId) : array
	{
		if (
			$propertyType === FormCategory\Registry::ELEMENT
			|| $propertyType === FormCategory\Registry::PRODUCT_ELEMENT
		)
		{
			$result = $this->elementCategoryProperty($propertyId);
		}
		else if ($propertyType === FormCategory\Registry::SECTION)
		{
			$result = $this->sectionCategoryField($iblockId, $propertyId);
		}
		else if ($propertyType === FormCategory\Registry::PRODUCT_SECTION)
		{
			if (!Main\Loader::includeModule('catalog'))
			{
				throw new Main\SystemException('cant load catalog module');
			}

			$catalog = \CCatalogSku::GetInfoByIBlock($iblockId);

			if (empty($catalog['PRODUCT_IBLOCK_ID']))
			{
				throw new Main\SystemException('product iblock not linked for offers');
			}

			$result = $this->sectionCategoryField((int)$catalog['PRODUCT_IBLOCK_ID'], $propertyId);
		}
		else
		{
			throw new Main\ArgumentException(self::getLocale('CATEGORY_PROPERTY_WRONG_TYPE'));
		}

		return $result;
	}

	protected function elementCategoryProperty(int $categoryPropertyId) : array
	{
		$queryProperty = Iblock\PropertyTable::getList([
			'filter' => [ '=ID' => $categoryPropertyId ],
			'limit' => 1,
		]);

		$categoryProperty = $queryProperty->fetch();

		if ($categoryProperty === false)
		{
			throw new Main\ObjectNotFoundException(self::getLocale('CATEGORY_PROPERTY_NOT_FOUND', ['#ID#' => $categoryPropertyId]));
		}

		if ($categoryProperty['USER_TYPE'] !== Admin\Property\CategoryProperty::USER_TYPE)
		{
			throw new Main\ArgumentException(self::getLocale('CATEGORY_PROPERTY_WRONG_TYPE'));
		}

		$categoryProperty['USER_TYPE_SETTINGS'] = $categoryProperty['USER_TYPE_SETTINGS_LIST'];

		return $categoryProperty;
	}

	protected function sectionCategoryField(int $iblockId, string $fieldCode) : array
	{
		global $USER_FIELD_MANAGER;

		$entityId = sprintf('IBLOCK_%d_SECTION', $iblockId);
		$result = null;

		foreach ($USER_FIELD_MANAGER->GetUserFields($entityId, 0, LANGUAGE_ID) as $field)
		{
			if ($field['FIELD_NAME'] === $fieldCode)
			{
				$result = $field;
				break;
			}
		}

		if ($result === null)
		{
			throw new Main\ObjectNotFoundException(self::getLocale('CATEGORY_PROPERTY_NOT_FOUND', ['#ID#' => $fieldCode]));
		}

		if ($result['USER_TYPE_ID'] !== Admin\Property\CategoryField::USER_TYPE_ID)
		{
			throw new Main\ObjectNotFoundException(self::getLocale('CATEGORY_PROPERTY_WRONG_TYPE'));
		}

		return $result;
	}

	protected function selectedElements(array $elementIds, int $iblockId) : array
	{
		$result = [];

		foreach ($elementIds as $id)
		{
			if (is_numeric($id))
			{
				$elementId = (int)$id;

				$result[$elementId] = true;
			}
			else if (mb_strpos($id, 'E') === 0)
			{
				$elementId = (int)mb_substr($id, 1);

				$result[$elementId] = true;
			}
			else if (mb_strpos($id, 'S') === 0)
			{
				$sectionId = (int)mb_substr($id, 1);

				$result += array_fill_keys(
					$this->sectionElements($sectionId, $iblockId),
					true
				);
			}
		}

		if (empty($result))
		{
			throw new Main\ArgumentException(self::getLocale('ELEMENTS_NOT_FOUND'));
		}

		return array_keys($result);
	}

	protected function sectionElements(int $sectionId, int $iblockId) : array
	{
		if ($sectionId <= 0) { return []; }

		$result = [];

		$queryElements = \CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => $iblockId,
				'SECTION_ID' => $sectionId,
				'INCLUDE_SUBSECTIONS' => 'Y'
			],
			false,
			[ 'nTopCount' => self::elementsLimit() + 1 ],
			[ 'ID' ]
		);

		while ($row = $queryElements->Fetch())
		{
			$result[] = (int)$row['ID'];
		}

		return $result;
	}

	protected function characteristicValues(array $property, array $elementIds) : array
	{
		$result = [];
		$isMultiple = ($property['MULTIPLE'] === 'Y');

		foreach (array_chunk($elementIds, static::CHUNK_SIZE) as $chunkIds)
		{
			$queryValue = \CIBlockElement::GetPropertyValues(
				$property['IBLOCK_ID'],
				[ '=ID' => $chunkIds ],
				$isMultiple,
				[ 'ID' => $property['ID'] ]
			);

			while ($row = $queryValue->Fetch())
			{
				if (empty($row[$property['ID']])) { continue; }

				if ($isMultiple)
				{
					if (empty($row['DESCRIPTION'][$property['ID']])) { continue; }

					$result[$row['IBLOCK_ELEMENT_ID']] = array_combine(
						(array)$row['DESCRIPTION'][$property['ID']],
						(array)$row[$property['ID']]
					);
				}
				else
				{
					$propertyValue = Admin\Property\CharacteristicProperty::convertFromDB($property, [ 'VALUE' => $row[$property['ID']] ]);

					$result[$row['IBLOCK_ELEMENT_ID']] = $propertyValue['VALUE'];
				}
			}
		}

		return $result;
	}

	protected function parentCharacteristicValues(array $property, array $elementIds) : array
	{
		$result = [];
		foreach (array_chunk($elementIds, static::CHUNK_SIZE) as $chunkIds)
		{
			$result += Admin\Property\ValueInherit\Characteristic::parentValues($chunkIds, $property['IBLOCK_ID']);
		}
		return $result;
	}

	protected function groupByCategory(array $elementIds, array $categoryValues) : array
	{
		$result = [];

		foreach ($elementIds as $elementId)
		{
			$category = $categoryValues[$elementId] ?? self::NO_CATEGORY;

			if (!isset($result[$category]))
			{
				$result[$category] = [];
			}

			$result[$category][] = $elementId;
		}

		return $result;
	}

	protected function characteristicsCommonValues(array $elementIds, array $characteristicValues) : array
	{
		$characteristicValues = array_intersect_key($characteristicValues, array_flip($elementIds));

		if (empty($characteristicValues))
		{
			return [];
		}

		if (count($characteristicValues) > 1)
		{
			return array_intersect_assoc(...$characteristicValues);
		}

		return reset($characteristicValues);
	}

	protected function elementNames(array $elementIds) : array
	{
		if (empty($elementIds)) { return []; }

		$result = [];

		foreach (array_chunk($elementIds, static::CHUNK_SIZE) as $chunkIds)
		{
			$elements = Iblock\ElementTable::getList([
				'filter' => [ 'ID' => $chunkIds ],
				'select' => [ 'ID', 'NAME' ]
			])->fetchAll();

			$result += array_column($elements, 'NAME', 'ID');
		}

		return $result;
	}

	protected function rebuildCharacteristicValuesToMultiple(array $values) : array
	{
		$result = [];
		$index = 1;

		foreach ($values as $key => $value)
		{
			$result["n$index"] = [
				'VALUE' => $value,
				'DESCRIPTION' => $key
			];
			$index++;
		}

		return $result;
	}

	protected static function elementsLimit() : int
	{
		return (int)Config::getOption('massive_edit_elements_limit', 20000);
	}

	public function checkIblockReadAccess($iblockId) : void
	{
		if (!\CIBlockRights::UserHasRightTo($iblockId, $iblockId, "iblock_admin_display"))
		{
			throw new Main\AccessDeniedException(self::getLocale('READ_ACCESS_DENIED'));
		}
	}

	protected function checkElementsWriteAccess(int $iblockId, array $elementIds) : void
	{
		if (\CIBlock::GetArrayByID($iblockId, 'RIGHTS_MODE') === 'E')
		{
			foreach ($elementIds as $elementId)
			{
				if (!\CIBlockElementRights::UserHasRightTo($iblockId, $elementId, "element_edit"))
				{
					throw new Main\AccessDeniedException(self::getLocale('WRITE_ACCESS_DENIED'));
				}
			}
		}
		else if (\CIBlock::GetPermission($iblockId) < 'W')
		{
			throw new Main\AccessDeniedException(self::getLocale('WRITE_ACCESS_DENIED'));
		}
	}
}