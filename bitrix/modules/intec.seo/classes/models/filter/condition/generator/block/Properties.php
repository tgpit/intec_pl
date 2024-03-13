<?php
namespace intec\seo\models\filter\condition\generator\block;

use intec\core\base\Collection;
use intec\core\helpers\Type;

/**
 * Класс, представляющий коллекцию свойств блоков.
 * Class Properties
 * @package intec\seo\models\filter\condition\generator\block
 * @author apocalypsisdimon@gmail.com
 */
class Properties extends Collection
{
    /**
     * Создает коллекцию свойств блока из массива.
     * @param array $properties
     * @return static
     */
    public static function create($properties)
    {
        $result = new static();

        if (!Type::isArray($properties))
            return $result;

        foreach ($properties as $property) {
            if (Type::isArray($property))
                $property = new Property($property);

            $result->add($property);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return ($item instanceof Property);
    }

    /**
     * Возвращает список свойств блоков в виде массива.
     * @return array
     */
    public function export()
    {
        $result = [];

        /** @var Property $item */
        foreach ($this->items as $key => $item)
            $result[$key] = $item->export();

        return $result;
    }
}