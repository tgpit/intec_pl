<?php

namespace intec\core\platform\component\parameters;

use CFormField;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с списком полей web-формы
 * @package intec\core\platform\component\parameters
 * @author imber228@gmail.com
 */
class FormFields extends Collection
{
    /**
     * Все типы полей
     */
    const TYPE_ALL = null;

    /**
     * Тип поля "текст"
     */
    const TYPE_TEXT = 'text';

    /**
     * Тип поля "многострочный текст"
     */
    const TYPE_TEXT_AREA = 'textarea';

    /**
     * Тип поля "радиокнопка"
     */
    const TYPE_RADIO = 'radio';

    /**
     * Тип поля "чекбокс"
     */
    const TYPE_CHECKBOX = 'checkbox';

    /**
     * Тип поля "выпадающий список"
     */
    const TYPE_DROP_DOWN = 'dropdown';

    /**
     * Тип поля "множественный список"
     */
    const TYPE_MULTI_SELECT = 'multiselect';

    /**
     * Тип поля "дата"
     */
    const TYPE_DATE = 'date';

    /**
     * Тип поля "изображение"
     */
    const TYPE_IMAGE = 'image';

    /**
     * Тип поля "файл"
     */
    const TYPE_FILE = 'file';

    /**
     * Тип поля "эл. почта"
     */
    const TYPE_EMAIL = 'email';

    /**
     * Тип поля "ссылка"
     */
    const TYPE_URL = 'url';

    /**
     * Тип поля "пароль"
     */
    const TYPE_PASSWORD = 'password';

    /**
     * Тип поля "скрытое"
     */
    const TYPE_HIDDEN = 'hidden';

    /**
     * Формирует объект списка полей web-формы по заданному фильтру
     * @param int $form Идентификатор формы
     * @param array $filter Массив фильтрации полей
     * @return FormFields
     */
    public static function getList($form, $filter = [])
    {
        $form = Type::toInteger($form);

        if ($form < 1)
            return new static([]);

        if (empty($filter) || !Type::isArray($filter))
            $filter = [];

        $filter = ArrayHelper::merge([
            'ACTIVE' => 'Y'
        ], $filter);

        $fields = (new CFormField())->GetList(
            $form,
            'N',
            $by = 's_sort',
            $order = 'asc',
            $filter,
            $filtered = false
        );
        $result = [];

        while ($field = $fields->GetNext(true, false))
            $result[$field['ID']] = $field;

        return new static($result);
    }

    /**
     * Возвращает форматированный массив-список полей web-форм
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
                'key' => 'form_' . $value['FIELD_TYPE'] . '_' . $value['ID'],
                'value' => '[' . $key . '] ' . $value['TITLE']
            ];
        });
    }
}