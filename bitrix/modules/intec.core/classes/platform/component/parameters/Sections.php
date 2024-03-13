<?php

namespace intec\core\platform\component\parameters;

use CIBlockSection;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с списком разделов инфоблока
 * @package intec\core\platform\component\parameters
 * @author imber228@gmail.com
 */
class Sections extends Collection
{
    /**
     * Формирует индексированный объект списка разделов инфоблока по заданному фильтру
     * @param array $filter Фильтр выборки разделов
     * @param array $sort Сортировка выборки
     * @param string $indexBy Ключ массива раздела для индексации списка
     * @return Sections
     */
    public static function getList($filter, $sort = [], $indexBy = 'ID')
    {
        if (empty($filter) || !Type::isArray($filter))
            return new static([]);

        $filter = ArrayHelper::merge([
            'ACTIVE' => 'Y',
            'GLOBAL_ACTIVE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R'
        ], $filter);

        if (empty($sort) || !Type::isArray($sort))
            $sort = ['SORT' => 'ASC'];

        if (empty($indexBy) || !Type::isString($indexBy))
            $indexBy = 'ID';

        $sections = CIBlockSection::GetList($sort, $filter);
        $result = [];

        while ($section = $sections->GetNext(false, false)) {
            if (ArrayHelper::keyExists($indexBy, $section))
                $result[$section[$indexBy]] = $section;
            else
                $result[$section['ID']] = $section;
        }

        return new static($result);
    }

    /**
     * Возвращает форматированный массив-список разделов инфоблока
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