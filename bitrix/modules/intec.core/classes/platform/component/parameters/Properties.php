<?php

namespace intec\core\platform\component\parameters;

use CIBlockProperty;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с списком пользовательских свойств инфоблока
 * @package intec\core\platform\component\parameters
 * @author imber228@gmail.com
 */
class Properties extends Collection
{
    /**
     * Обычные и множественные свойства
     * @var bool
     */
    const TYPE_BOTH = false;

    /**
     * Множественные свойства
     * @var string
     */
    const TYPE_MULTIPLE = 'Y';

    /**
     * Обычные свойства
     * @var string
     */
    const TYPE_SINGLE = 'N';

    /**
     * Формирует индексированный объект списка пользовательских свойств по заданному фильтру
     * @param array $filter Фильтр выборки свойств
     * @param array $sort Сортировка выборки
     * @param string $indexBy Ключ массива свойства для индексации списка
     * @return Properties
     */
    public static function getList($filter, $sort = [], $indexBy = 'CODE')
    {
        if (empty($filter) || !Type::isArray($filter))
            return new static([]);

        $filter = ArrayHelper::merge([
            'ACTIVE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R'
        ], $filter);

        if (empty($sort) || !Type::isArray($sort))
            $sort = ['SORT' => 'ASC'];

        if (empty($indexBy) || !Type::isString($indexBy))
            $indexBy = 'CODE';

        $properties = CIBlockProperty::GetList($sort, $filter);
        $result = [];

        while ($property = $properties->GetNext(false, false)) {
            if (ArrayHelper::keyExists($indexBy, $property))
                $result[$property[$indexBy]] = $property;
            else
                $result[$property['CODE']] = $property;
        }

        return new static($result);
    }

    /**
     * Возвращает форматированный массив-список свойств
     * @param array $filter Массив фильтрации списка
     * @param bool $strict Режим строгого сответствия фильтру
     * @return array
     */
    public function asArrayFormatted($filter = [], $strict = false)
    {
        if (!Type::isArray($filter))
            $filter = [];

        return $this->asArray(function ($key, $value) use (&$filter, &$strict) {
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

            return !$filtered ? ['skip' => true] : [
                'key' => $key,
                'value' => '[' . $key . '] ' . $value['NAME']
            ];
        });
    }

    /**
     * Возвращает форматированный список свойств типа "Чекбокс"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getCheckboxType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('L', 'C', null, $mode);
    }

    /**
     * Возвращает форматированный и отфильтрованный список пользовательских свойств
     * @param string $propertyType Тип свойства
     * @param string|bool $listType Вид отображения свойства
     * @param string|bool|null $userType Пользовательский тип свойства
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getCustomType($propertyType, $listType = false, $userType = false, $mode = self::TYPE_BOTH)
    {
        if ($this->isEmpty() || empty($propertyType))
            return [];

        return $this->asArray(function ($key, $value) use ($propertyType, $listType, $userType, $mode) {
            if (
                $value['PROPERTY_TYPE'] !== $propertyType ||
                ($listType !== false && $value['LIST_TYPE'] !== $listType) ||
                ($userType !== false && $value['USER_TYPE'] !== $userType)
            )
                return ['skip' => true];

            if ($mode !== self::TYPE_BOTH) {
                if (
                    ($mode === self::TYPE_MULTIPLE && $value['MULTIPLE'] !== $mode) ||
                    ($mode === self::TYPE_SINGLE && $value['MULTIPLE'] !== $mode)
                )
                    return ['skip' => true];
            }

            return [
                'key' => $key,
                'value' => '[' . $key . '] ' . $value['NAME']
            ];
        });
    }

    /**
     * Возвращает форматированный список свойств типа "Дата/Время"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getDateTimeType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'DateTime', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Дата"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getDateType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'Date', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Справочник"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getEAutocompleteType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'directory', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к элементам"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getElementType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('E', 'L', null, $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к элементам по XML_ID"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getElementXmlIDType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'ElementXmlID', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к элементам в виде списка"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getEListType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('E', 'L', 'EList', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к файлу (на сервере)"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getFileManType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'FileMan', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Файл"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getFileType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('F', 'L', null, $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "HTML/Текст"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getHtmlType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'HTML', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к Google Maps"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getGoogleMapType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'map_google', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Список"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getListType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('L', 'L', null, $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Деньги"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getMoneyType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'Money', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Число"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getNumberType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('N', 'L', null, $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к разделам с автозаполнением"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getSectionAutoType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('G', 'L', 'SectionAuto', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к разделам"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getSectionType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('G', 'L', null, $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Счетчик"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getSequenceType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('N', 'L', 'Sequence', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Текст"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getStringType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', null, $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к пользователю"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getUserType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'UserID', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Видео"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getVideoType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'video', $mode);
    }

    /**
     * Возвращает форматированный список свойств типа "Привязка к Яндекс.Карте"
     * @param string|bool $mode Режим отбора свойства (простые/множественные/все)
     * @return array
     */
    public function getYandexMapType($mode = self::TYPE_BOTH)
    {
        return $this->getCustomType('S', 'L', 'map_yandex', $mode);
    }
}