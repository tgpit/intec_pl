<?php

namespace intec\core\platform\component\parameters;

use CComponentUtil;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс для работы с списком шаблонов компонета
 * @package intec\core\platform\component\parameters
 * @author imber228@mail.com
 */
class Templates extends Collection
{
    /**
     * Определение втроенного шаблона
     * @var string
     */
    const SITE_TEMPLATE_DEFAULT = '';

    /**
     * Формирует объект списка шаблонов
     * @param string $component Название компонента
     * @param string|bool $template Идентификатор шаблона сайта
     * @return Templates
     */
    public static function getList($component, $template = false)
    {
        $result = new static([]);

        if (!empty($component)) {
            $templates = CComponentUtil::GetTemplatesList($component, $template);

            foreach ($templates as $template)
                $result->set($template['NAME'], $template);
        }

        return $result;
    }

    /**
     * Возвращает форматированный массив-список шаблонов
     * @param array|string|null $filter Идентификатор(ы) шаблона сайта, по которому будут отфильтрованы шаблоны
     * @return array
     */
    public function asArrayFormatted($filter = null) {
        return $this->asArray(function ($key, $value) use ($filter) {
            if ((
                    Type::isString($filter) &&
                    $value['TEMPLATE'] !== $filter
                ) || (
                    Type::isArray($filter) &&
                    !ArrayHelper::isIn($value['TEMPLATE'], $filter, true)
                )
            )
                return ['skip' => true];

            return [
                'key' => $key,
                'value' => $value['NAME']
            ];
        });
    }
}