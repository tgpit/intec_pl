<?php

namespace intec\core\bitrix\component\parameters;

use intec\core\base\Collection;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * Class Templates
 * @package intec\core\bitrix\component\parameters
 * @deprecated
 */
class Templates extends Collection
{
    /**
     * Создает коллекцию шаблонов указанного компонента
     * @param string $component
     * @return Templates
     */
    public static function getList($component)
    {
        if (empty($component) || !Type::isString($component)) {
            return new static([]);
        }

        $result = [];
        $templates = \CComponentUtil::GetTemplatesList($component);

        if (!empty($templates)) {
            foreach ($templates as $template) {
                $result[$template['NAME']] = $template['NAME'];
            }
        }

        return new static($result);
    }

    /**
     * Возвращает отфильтрованный список по заданному индексу в начале строки названия шаблока
     * @param string $index
     * @return array
     */
    public function getByIndex($index)
    {
        if (empty($index) || !Type::isString($index)) {
            return [];
        }

        return $this->asArray(function ($key, $value) use ($index) {
            if (StringHelper::startsWith($key, $index)) {
                $value = StringHelper::cut($value, StringHelper::length($index));

                return [
                    'key' => $key,
                    'value' => $value
                ];
            }

             return ['skip' => true];
        });
    }
}