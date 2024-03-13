<?php

namespace intec\core\platform\component\parameters;

use CIBlockElement;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с списком элементов инфоблока
 * @package intec\core\platform\component\parameters
 * @author imber228@gmail.com
 */
class Elements extends Collection
{
    /**
     * Формирует индексированный объект списка элементов инфоблока по заданному фильтру
     * @param array $filter Фильтр выборки элементов
     * @param array $sort Сортировка выборки
     * @param string $indexBy Ключ массива элемента для индексации списка
     * @return Elements
     */
    public static function getList($filter, $sort = [], $indexBy = 'ID')
    {
        if (empty($filter) || !Type::isArray($filter))
            return new static([]);

        $filter = ArrayHelper::merge([
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R'
        ], $filter);

        if (empty($sort) || !Type::isArray($sort))
            $sort = ['SORT' => 'ASC'];

        if (empty($indexBy) || !Type::isString($indexBy))
            $indexBy = 'ID';

        $elements = CIBlockElement::GetList($sort, $filter);
        $result = [];

        while ($element = $elements->GetNext(false, false)) {
            if (ArrayHelper::keyExists($indexBy, $element))
                $result[$element[$indexBy]] = $element;
            else
                $result[$element['ID']] = $element;
        }

        return new static($result);
    }

    /**
     * Возвращает форматированный массив-список элементов инфоблока
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
}