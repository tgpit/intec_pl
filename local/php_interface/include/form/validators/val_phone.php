<?
// Листинг файла /bitrix/php_interface/include/form/validators/val_num_ex.php
class CFormCustomValidatorNumberEx
{
	function GetDescription()
	{
		return array(
			"NAME"            => "mnumber_phone",                                   // идентификатор
			"DESCRIPTION"     => "Номер телефона",		                               // наименование
			"TYPES"           => array("text"),                            // типы полей
			"SETTINGS"        => array("CFormCustomValidatorNumberEx", "GetSettings"), // метод, возвращающий массив настроек
			"CONVERT_TO_DB"   => array("CFormCustomValidatorNumberEx", "ToDB"),        // метод, конвертирующий массив настроек в строку
			"CONVERT_FROM_DB" => array("CFormCustomValidatorNumberEx", "FromDB"),      // метод, конвертирующий строку настроек в массив
			"HANDLER"         => array("CFormCustomValidatorNumberEx", "DoValidate")   // валидатор
		);
	}
	function GetSettings()
	{
		return array(
			"NUMBER_FROM" => array(
				"TITLE"   => "Нижний порог числа",
				"TYPE"    => "TEXT",
				"DEFAULT" => "0",
			),
			"NUMBER_TO" => array(
				"TITLE"   => "Верхний порог числа",
				"TYPE"    => "TEXT",
				"DEFAULT" => "100",
			),
      
			"NUMBER_FLOAT" => array(
				"TITLE"   => "Не только целое",
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y",
			),
		);
	}
	function ToDB($arParams)
	{
		// проверка переданных параметров
		$arParams["NUMBER_FLOAT"] = $arParams["NUMBER_FLOAT"] == "Y" ? "Y" : "N";
		$arParams["NUMBER_FROM"]  = $arParams["NUMBER_FLOAT"] == "Y" ? floatval($arParams["NUMBER_FROM"]) : intval($arParams["NUMBER_FROM"]);
		$arParams["NUMBER_TO"]    = $arParams["NUMBER_FLOAT"] == "Y" ? floatval($arParams["NUMBER_TO"]) : intval($arParams["NUMBER_TO"]);
    
		// перестановка значений порогов, если требуется
		if ($arParams["NUMBER_FROM"] > $arParams["NUMBER_TO"])
		{
			$tmp                     = $arParams["NUMBER_FROM"];
			$arParams["NUMBER_FROM"] = $arParams["NUMBER_TO"];
			$arParams["NUMBER_TO"]   = $tmp;
		}
    
		// возвращаем сериализованную строку
		return serialize($arParams);
	}
	function FromDB($strParams)
	{
		// никаких преобразований не требуется, просто вернем десериализованный массив
		return unserialize($strParams);
	}
	
	function DoValidate($arParams, $arQuestion, $arAnswers, $arValues)
	{
		global $APPLICATION;
    
		foreach ($arValues as $value)
		{
			// пустые значения пропускаем
			if (strlen($value) <= 0) continue;
      
			// приведем значение к числу
			$value = $arParams["NUMBER_FLOAT"] == "Y" ? floatval($value) : intval($value);
      
			// проверим нижний порог числа
			if (strlen($arParams["NUMBER_FROM"]) > 0 && $value < intval($arParams["NUMBER_FROM"]))
			{
				// вернем ошибку
				$APPLICATION->ThrowException("#FIELD_NAME#: слишком маленькое значение");
				return false;
			}
      
			// проверим верхний порог числа
			if (strlen($arParams["NUMBER_TO"]) > 0 && $value > intval($arParams["NUMBER_TO"]))
			{
				// вернем ошибку
				$APPLICATION->ThrowException("#FIELD_NAME#: слишком большое значение");
				return false;
			}
		}
    
		// все значения прошли валидацию, вернем true
		return true;
	}
}
// установим метод CFormCustomValidatorNumberEx в качестве обработчика события
AddEventHandler("form", "onFormValidatorBuildList", array("CFormCustomValidatorNumberEx", "GetDescription"));
?>