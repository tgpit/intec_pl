<?php

namespace intec\core\platform\component\parameters;

use CUserTypeManager;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с пользовательсикими полями
 * @package intec\core\platform\component\parameters
 * @author imber228@gmail.com
 * @version 1.0.0
 */
class UserFields extends Collection
{
    /**
     * Тип поля "Да/Нет"
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Тип поля "Привязка к элементамм CRM"
     */
    const TYPE_CRM = 'crm';

    /**
     * Тип поля "Привязка к справочникам CRM"
     */
    const TYPE_CRM_STATUS = 'crm_status';

    /**
     * Тип поля "Дата/Время"
     */
    const TYPE_DATETIME = 'datetime';

    /**
     * Тип поля "Число"
     */
    const TYPE_DOUBLE = 'double';

    /**
     * Тип поля "Список"
     */
    const TYPE_ENUMERATION = 'enumeration';

    /**
     * Тип поля "Файл"
     */
    const TYPE_FILE = 'file';

    /**
     * Тип поля "Привязка к элементам инфоблока"
     */
    const TYPE_IBLOCK_ELEMENT = 'iblock_element';

    /**
     * Тип поля "Привязка к разделам инфоблока"
     */
    const TYPE_IBLOCK_SECTION = 'iblock_section';

    /**
     * Тип поля "Целое число"
     */
    const TYPE_INTEGER = 'integer';

    /**
     * Тип поля "Строка"
     */
    const TYPE_STRING = 'string';

    /**
     * Тип поля "Шаблон"
     */
    const TYPE_STRING_FORMATTED = 'string_formatted';

    /**
     * Тип поля "Видео"
     */
    const TYPE_VIDEO = 'video';

    /**
     * Множественное свойство
     */
    const TYPE_MULTIPLE = 'Y';

    /**
     * Единичное свойство
     */
    const TYPE_SINGLE = 'N';

    /**
     * Без учета множественности
     */
    const TYPE_BOTH = false;

    /**
     * Получает коллекцию пользовательских полей по указанной сущности
     * @param string $entity
     * @param int| $valueId
     * @param bool|string $languageId
     * @param bool|int $userId
     * @return UserFields
     */
    public static function getFields($entity, $valueId = 0, $languageId = false, $userId = false)
    {
        return new static((new CUserTypeManager())->getUserFields($entity, $valueId, $languageId, $userId));
    }

    /**
     * Возвращает коллекцию пользовательских полей разделов инфоблока
     * @param $iBlockId
     * @return UserFields
     */
    public static function getSectionsFields($iBlockId)
    {
        return self::getFields(
            'IBLOCK_' . $iBlockId . '_SECTION',
            0,
            LANGUAGE_ID
        );
    }

    /**
     * Возвращает коллекцию пользовательских полей HighLoad-блоков
     * @param $blockId
     * @return UserFields
     */
    public static function getHighLoadBlockFields($blockId)
    {
        return self::getFields(
            'HLBLOCK_' . $blockId,
            0,
            LANGUAGE_ID
        );
    }

    /**
     * Возвращает форматированный массив из коллекции пользовательских полей
     * @param array $filter
     * @param bool $strict
     * @return array
     */
    public function asArrayFormatted($filter = [], $strict = false)
    {
        if (!Type::isArray($filter))
            $filter = [];

        return $this->asArray(function ($key, $value) use ($filter, $strict) {
            $filtered = true;

            if (!empty($filter)) {
                foreach ($filter as $property => $condition) {
                    if (!ArrayHelper::keyExists($property, $value)) {
                        $filtered = false;

                        break;
                    }

                    if (Type::isArray($condition)) {
                        $isVariant = false;

                        if (empty($condition)) {
                            $filtered = true;

                            continue;
                        }

                        foreach ($condition as $variant) {
                            if (Type::isArray($value[$property]))
                                $isVariant = ArrayHelper::isIn($variant, $value[$property], $strict);
                            else
                                $isVariant = $strict ? $variant === $value[$property] : $variant == $value[$property];

                            if ($isVariant)
                                break;
                        }

                        $filtered = $isVariant;
                    } else {
                        if (Type::isArray($value[$property]))
                            $filtered = ArrayHelper::isIn($condition, $value[$property], $strict);
                        else
                            $filtered = $strict ? $condition === $value[$property] : $condition == $value[$property];
                    }

                    if (!$filtered)
                        break;
                }
            }

            if ($filtered) {
                $name = $value['EDIT_FORM_LABEL'];

                if (empty($name))
                    $name = $key;
                else
                    $name = '[' . $key . '] ' . $name;

                return [
                    'key' => $key,
                    'value' => $name
                ];
            } else {
                return ['skip' => true];
            }
        });
    }

    /**
     * Возвращает указанный тип полей из коллекции
     * @param $type
     * @param $multiple
     * @return array
     */
    public function getCustomType($type, $multiple = self::TYPE_BOTH)
    {
        return $this->asArray(function ($key, $value) use ($type, $multiple) {
            if ($value['USER_TYPE_ID'] !== $type)
                return ['skip' => true];

            if ($multiple !== self::TYPE_BOTH) {
                if ($value['MULTIPLE'] !== $multiple)
                    return ['skip' => true];
            }

            $name = $value['EDIT_FORM_LABEL'];

            if (empty($name))
                $name = $key;
            else
                $name = '[' . $key . '] ' . $name;

            return [
                'key' => $key,
                'value' => $name
            ];
        });
    }

    /**
     * Возвращает тип полей "Да/Нет"
     * @param bool $multiple
     * @return array
     */
    public function getBooleanType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_BOOLEAN, $multiple);
    }

    /**
     * Возвращает тип полей "Привязка к элементам CRM"
     * @param bool $multiple
     * @return array
     */
    public function getCrmType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_CRM, $multiple);
    }

    /**
     * Возвращает тип полей "Привязка к справочникам CRM"
     * @param bool $multiple
     * @return array
     */
    public function getCrmStatusType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_CRM_STATUS, $multiple);
    }

    /**
     * Возвращает тип полей "Дата/Время"
     * @param bool $multiple
     * @return array
     */
    public function getDateTimeType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_DATETIME, $multiple);
    }

    /**
     * Возвращает тип полей "Число"
     * @param bool $multiple
     * @return array
     */
    public function getDoubleType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_DOUBLE, $multiple);
    }

    /**
     * Возвращает тип полей "Список"
     * @param bool $multiple
     * @return array
     */
    public function getEnumerationType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_ENUMERATION, $multiple);
    }

    /**
     * Возвращает тип полей "Файл"
     * @param bool $multiple
     * @return array
     */
    public function getFileType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_FILE, $multiple);
    }

    /**
     * Возвращает тип полей "Привязка к элементам инфоблока"
     * @param bool $multiple
     * @return array
     */
    public function getIBlockElementType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_IBLOCK_ELEMENT, $multiple);
    }

    /**
     * Возвращает тип полей "Привязка к разделам инфоблока"
     * @param bool $multiple
     * @return array
     */
    public function getIBlockSectionType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_IBLOCK_SECTION, $multiple);
    }

    /**
     * Возвращает тип полей "Целое число"
     * @param bool $multiple
     * @return array
     */
    public function getIntegerType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_INTEGER, $multiple);
    }

    /**
     * Возвращает тип полей "Строка"
     * @param bool $multiple
     * @return array
     */
    public function getStringType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_STRING, $multiple);
    }

    /**
     * Возвращает тип полей "Шаблон"
     * @param bool $multiple
     * @return array
     */
    public function getStringFormattedType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_STRING_FORMATTED, $multiple);
    }

    /**
     * Возвращает тип полей "Видео"
     * @param bool $multiple
     * @return array
     */
    public function getVideoType($multiple = self::TYPE_BOTH)
    {
        return $this->getCustomType(self::TYPE_VIDEO, $multiple);
    }
}