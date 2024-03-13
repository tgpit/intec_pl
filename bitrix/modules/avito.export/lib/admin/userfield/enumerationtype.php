<?php
/** @noinspection PhpUnused */
namespace Avito\Export\Admin\UserField;

use Bitrix\Main;
use Avito\Export\Admin\View;
use Avito\Export\Concerns as GlobalConcerns;

class EnumerationType
{
	use GlobalConcerns\HasLocale;
	use Concerns\HasCompatibleExtends;

	public static function getCommonExtends() : string
	{
		return Main\UserField\Types\EnumType::class;
	}

	public static function getCompatibleExtends() : string
	{
		/** @noinspection PhpDeprecationInspection */
		return \CUserTypeEnum::class;
	}

	public static function getUserTypeDescription() : array
	{
		$result = static::callParent('getUserTypeDescription');

		if (!empty($result['USE_FIELD_COMPONENT']))
		{
			$result['USE_FIELD_COMPONENT'] = false;
		}

		return $result;
	}

	public static function GetList($userField) : \CDBResult
	{
		$result = new \CDBResult();
		$result->InitFromArray($userField['VALUES']);

		return $result;
	}

	public static function GetEditFormHTML($userField, $htmlControl) : string
	{
		if (isset($userField['SETTINGS']['DISPLAY']) && $userField['SETTINGS']['DISPLAY'] === 'CHECKBOX')
		{
			return static::callParent('GetEditFormHTML', [$userField, $htmlControl]);
		}

		return static::editSelect($userField, $htmlControl);
	}

	public static function GetEditFormHTMLMulty($userField, $htmlControl) : string
	{
		if (isset($userField['SETTINGS']['DISPLAY']) && $userField['SETTINGS']['DISPLAY'] === 'CHECKBOX')
		{
			return static::callParent('GetEditFormHTMLMulty', [$userField, $htmlControl]);
		}

		$layout = $userField['SETTINGS']['LAYOUT'] ?? 'DEFAULT';

		$values = Helper\Value::asMultiple($userField, $htmlControl);
		$values = array_filter($values, static function($value) { return (string)$value !== ''; });
		$values = array_unique($values);
		$inputName = preg_replace('/\[]$/', '', $htmlControl['NAME']);
		$result = sprintf(
			'<table id="table_%s" style="%s">',
			$inputName,
			$layout === 'INLINE' ? 'display: inline-table; vertical-align: top;' : ''
		);

		if (empty($values)) { $values[] = null; }

		foreach ($values as $index => $value)
		{
			$result .= '<tr><td>';
			$result .= static::editSelect($userField, [
				'VALUE' => $value,
				'NAME' => $inputName . '[' . (int)$index . ']',
			]);
			$result .= '</td></tr>';
		}

		$result .= '<tr><td></td></tr>';
		$result .= '</table>';
		$result .= sprintf(
			'<input type="button" value="%s" onclick=\'%s\' style="%s" />',
			self::getLocale('ADD_' . $layout),
			sprintf(
				'addNewRow("table_%1$s", "%2$s|%3$s|%3$s_old_id")',
				$inputName,
				str_replace('[', '\\\\[', Helper\Attributes::nameToId($inputName)),
				str_replace('[', '\\\\[', $inputName)
			),
			$layout === 'INLINE' ? 'margin-top: 1px; height: 27px;' : ''
		);

		return $result;
	}

	protected static function editSelect($userField, $htmlControl, array $attributes = []) : string
	{
		$query = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getList'], $userField);
		$variants = Helper\Variants::toArray($query);
		$attributes += [
			'name' => $htmlControl['NAME'],
			'disabled' => $userField['EDIT_IN_LIST'] !== 'Y',
			'style' => 'max-width: 300px;',
			'onchange' => $userField['SETTINGS']['ONCHANGE'] ?? null,
		];
		$attributes += $userField['SETTINGS']['ATTRIBUTES'] ?? [];

		if ($userField['SETTINGS']['LIST_HEIGHT'] > 1)
		{
			$attributes['size'] = $userField['SETTINGS']['LIST_HEIGHT'];
		}
		else
		{
			$htmlControl['VALIGN'] = 'middle';
		}

		if (
			(string)$htmlControl['VALUE'] === ''
			&& (!isset($userField['ENTITY_VALUE_ID']) || (int)$userField['ENTITY_VALUE_ID'] < 1)
		)
		{
			$htmlControl['VALUE'] = static::searchDefaultValue($userField, $variants);
		}

		if (!isset($userField['SETTINGS']['ALLOW_NO_VALUE']))
		{
			$userField['SETTINGS']['ALLOW_NO_VALUE'] = ($userField['MANDATORY'] !== 'Y');
		}
		else if (is_string($userField['SETTINGS']['ALLOW_NO_VALUE']))
		{
			$userField['SETTINGS']['ALLOW_NO_VALUE'] = ($userField['SETTINGS']['ALLOW_NO_VALUE'] === 'Y');
		}

		return View\Select::edit($variants, $htmlControl['VALUE'], $attributes, $userField['SETTINGS'] ?? []);
	}

	public static function GetAdminListViewHTML($userField, $htmlControl) : string
	{
		if (empty($htmlControl['VALUE'])) { return '&nbsp;'; }

		$result = '[' . htmlspecialcharsbx($htmlControl['VALUE']) . ']';
		$query = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getList'], $userField);

		while ($option = $query->Fetch())
		{
			if ((string)$option['ID'] === (string)$htmlControl['VALUE'])
			{
				$result = htmlspecialcharsbx($option['VALUE']);
				break;
			}
		}

		return $result;
	}

	/** @noinspection PhpUnused */
	public static function GetAdminListViewHTMLMulty($userField, $htmlControl) : string
	{
		if (empty($htmlControl['VALUE'])) { return '&nbsp;'; }

		$partials = [];
		$query = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getList'], $userField);
		$valueList = (array)$htmlControl['VALUE'];
		$valueMap = array_flip($valueList);

		while ($option = $query->Fetch())
		{
			if (isset($valueMap[$option['ID']]))
			{
				$partials[] = htmlspecialcharsbx($option['VALUE']);
			}
		}

		return !empty($partials) ? implode(' / ', $partials) : '&nbsp;';
	}

	protected static function searchDefaultValue(array $userField, array $variants)
	{
		if (isset($userField['SETTINGS']['DEFAULT_VALUE']) && (string)$userField['SETTINGS']['DEFAULT_VALUE'] !== '')
		{
			$result = $userField['SETTINGS']['DEFAULT_VALUE'];
		}
		else
		{
			$result = null;

			foreach ($variants as $variant)
			{
				if ($variant['DEF'] === 'Y')
				{
					$result = $variant['ID'];
					break;
				}
			}
		}

		return $result;
	}
}
