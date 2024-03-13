<?php
namespace Avito\Export\Admin\Property;

use Bitrix\Main;
use Avito\Export\Concerns;

class CharacteristicProperty implements FeedPropertyExtension
{
	use Concerns\HasLocale;

	public const USER_TYPE = 'avito_characteristic';

	public static function getUserTypeDescription() : array
	{
		return [
			'PROPERTY_TYPE'             => 'S',
			'USER_TYPE'                 => static::USER_TYPE,
			'DESCRIPTION'               => self::getLocale('DESCRIPTION', null, 'Avito: Characteristic'),
			'GetPropertyFieldHtml'      => [static::class, 'getPropertyFieldHtml'],
			'GetPropertyFieldHtmlMulty' => [static::class, 'getPropertyFieldHtmlMulty'],
			'PrepareSettings'           => [static::class, 'prepareSettings'],
			'GetSettingsHTML'           => [static::class, 'getSettingsHTML'],
			'GetAdminListViewHTML'      => [static::class, 'getAdminListViewHTML'],

			'ConvertToDB'		        => [static::class, 'convertToDB'],
			'ConvertFromDB'		        => [static::class, 'convertFromDB'],

			'avitoExportFeedFields' => [static::class, 'avitoExportFeedFields'],
			'avitoExportFeedValue' => [static::class, 'avitoExportFeedValue'],
		];
	}

	public static function avitoExportFeedFields($property) : array
	{
		return [
			[
				'ID' => 'self',
				'TITLE' => self::getLocale('EXPORT_FIELD_SELF'),
				'TYPE' => static::USER_TYPE,
			],
		];
	}

	public static function avitoExportFeedValue($property, $value, $field)
	{
		$isMultiple = ($property['MULTIPLE'] === 'Y');
		$characteristics = static::combineValues($value, $isMultiple);

		if ($field === 'self')
		{
			$result = $characteristics;
		}
		else
		{
			$result = $characteristics[$field] ?? null;
		}

		return $result;
	}

	protected static function combineValues($value, $isMultiple) : ?array
	{
		if ($isMultiple)
		{
			if (!is_array($value['VALUE']) || !is_array($value['DESCRIPTION'])) { return null; }

			$result = [];

			foreach ($value['VALUE'] as $key => $oneValue)
			{
				if (!isset($value['DESCRIPTION'][$key])) { continue; }

				$name = $value['DESCRIPTION'][$key];
				$result[$name] = $oneValue;
			}
		}
		else
		{
			if (!is_array($value['VALUE'])) { return null; }

			$result = $value['VALUE'];
		}

		return $result;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function prepareSettings($property) : array
	{
		return [];
	}

	public static function convertToDB(array $property, array $value) : array
	{
		if ($property['MULTIPLE'] === 'Y') { return $value; }

		if (isset($value['VALUE']) && is_array($value['VALUE']))
		{
			$value['VALUE'] = serialize($value['VALUE']);
		}

		return $value;
	}

	public static function convertFromDB(array $property, array $value) : array
	{
		if ($property['MULTIPLE'] === 'Y') { return $value; }

		if (isset($value['VALUE']) && is_string($value['VALUE']) && $value['VALUE'] !== '')
		{
			$unserialized = unserialize($value['VALUE'], [
				'allowed_classes' => false,
			]);

			$value['VALUE'] = is_array($unserialized) ? $unserialized : [];
		}
		else
		{
			$value['VALUE'] = [];
		}

		return $value;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function getSettingsHTML($property, $controlSettings, &$propertyFields) : string
	{
		$propertyFields['HIDE'] = [
			'MULTIPLE_CNT',
		];

		return '';
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function getAdminListViewHTML($property, $value, $htmlControl) : string
	{
		if ($property['MULTIPLE'] === 'N')
		{
			$partials = [];
			$valueParts = [
				self::parentValue($property, $htmlControl),
				$value['VALUE'] ?? [],
			];

			foreach ($valueParts as $valuePart)
			{
				if (!is_array($valuePart)) { continue; }

				foreach ($valuePart as $name => $one)
				{
					if (isset($partials[$name])) { continue; }

					$partials[$name] = sprintf('%s: %s', $name, $one);
				}
			}

			$result = implode('<br />', $partials);
		}
		else if (isset($value['VALUE']) && !empty($value['DESCRIPTION']))
		{
			$result = sprintf('%s: %s', $value['DESCRIPTION'], $value['VALUE']);
		}
		else
		{
			$result = '';
		}

		return $result;
	}

	public static function getPropertyFieldHtml($property, $value, $controlName) : string
	{
		try
		{
			$result = self::editComponent(static::editDefaults($property, $controlName) + [
				'MULTIPLE' => 'N',
				'VALUE' => $value['VALUE'],
			]);
		}
		catch (Main\SystemException $exception)
		{
			$result = $exception->getMessage();
		}

		return $result;
	}

	public static function getPropertyFieldHtmlMulty($property, $values, $controlName) : string
	{
		try
		{
			if ($property['WITH_DESCRIPTION'] !== 'Y')
			{
				throw new Main\SystemException(self::getLocale('MULTIPLE_WITH_DESCRIPTION_REQUIRED'));
			}

			$result = self::editComponent(static::editDefaults($property, $controlName) + [
				'MULTIPLE' => 'Y',
				'VALUE' => $values,
			]);
		}
		catch (Main\SystemException $exception)
		{
			$result = $exception->getMessage();
		}

		return $result;
	}

	protected static function editDefaults($property, $controlName) : array
	{
		$categoryOptions = self::categoryOptions($property, $controlName);

		return [
			'PROPERTY' => $property,
			'CONTROL' => $controlName,
			'CATEGORY_OPTIONS' => $categoryOptions,
			'PARENT_VALUE' => array_key_exists('PARENT_VALUE', $controlName) ? $controlName['PARENT_VALUE'] : self::parentValue($property, $controlName)
		];
	}

	protected static function editComponent(array $parameters) : string
	{
		global $APPLICATION;

		return (string)$APPLICATION->IncludeComponent(
			'avito.export:admin.property.characteristic',
			'',
			$parameters,
			false,
			[
				'HIDE_ICONS' => 'Y',
			]
		);
	}

	protected static function categoryOptions($property, $controlName) : array
	{
		$categoryProperties = $controlName['CATEGORY_PROPERTIES'] ?? ValueInherit\Category::properties($property['IBLOCK_ID']);
		$options = [];

		foreach ($categoryProperties as [$type, $categoryPropertyId])
		{
			$behavior = FormCategory\Registry::make($type);

			$options[] = [ 'type' => $type ] + $behavior->options($property, $categoryPropertyId);
		}

		return $options;
	}

	protected static function parentValue(array $property,  array $controlName)
	{
		$elementId = Utils\PropertyElement::findElementId($controlName);

		if ($elementId === null) { return null; }

		return ValueInherit\Characteristic::parentValue($elementId, $property['IBLOCK_ID']);
	}
}