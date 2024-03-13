<?php

namespace intec\core\platform\component\parameters;

use CForm;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с списком web-форм
 * @package intec\core\platform\component\parameters
 * @author imber228@gmail.com
 */
class Forms extends Collection
{
    /**
     * Формирует объект списка web-форм по заданному фильтру
     * @param array $filter
     * @return Forms
     */
    public static function getList($filter = [])
    {
        if (empty($filter) || !Type::isArray($filter))
            $filter = [];

        $forms = CForm::GetList(
            $by = 'sort',
            $order = 'asc',
            $filter,
            $filtered = false
        );
        $result = [];

        while ($form = $forms->GetNext(true, false))
            $result[$form['ID']] = $form;

        return new static($result);
    }

    /**
     * Возвращает форматированный массив-список web-форм
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