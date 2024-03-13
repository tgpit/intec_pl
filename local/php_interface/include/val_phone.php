<?php

class CFormValidatorPhone
{
	public static function GetDescription()
	{
		return array(
			"NAME" => "Phone", // validator string ID
			"DESCRIPTION" => 'Номер телефона', // validator description
			"TYPES" => array("text", "textarea"), //  list of types validator can be applied.
			"SETTINGS" => array("CFormValidatorPhone", "GetSettings"), // method returning array of validator settings, optional
			"CONVERT_TO_DB" => array("CFormValidatorPhone", "ToDB"), // method, processing validator settings to string to put to db, optional
			"CONVERT_FROM_DB" => array("CFormValidatorPhone", "FromDB"), // method, processing validator settings from string from db, optional
			"HANDLER" => array("CFormValidatorPhone", "DoValidate") // main validation method
		);
	}

	public static function GetSettings()
	{
		return [];
	}

	public static function ToDB($arParams)
	{
		// возвращаем сериализованную строку
		return serialize($arParams);
	}

	public static function FromDB($strParams)
	{
		return unserialize($strParams, ['allowed_classes' => false]);
	}

	public static function DoValidate($arParams, $arQuestion, $arAnswers, $arValues)
	{
		global $APPLICATION;
 
		foreach ($arValues as $value)
		{
			$value = preg_replace('/[^0-9]/', '', $value);
			// проверяем на пустоту
			if (strlen($value) < 10)
			{
				// вернем ошибку
				$APPLICATION->ThrowException('Не верно заполнен "Номер телефона"');
				
				return false;
			}
 
		}
 
		// все значения прошли валидацию, вернем true
		return true;
	}

}

AddEventHandler("form", "onFormValidatorBuildList", array("CFormValidatorPhone", "GetDescription"));