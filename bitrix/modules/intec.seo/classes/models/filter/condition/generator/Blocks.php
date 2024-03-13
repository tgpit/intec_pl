<?php
namespace intec\seo\models\filter\condition\generator;

use intec\core\base\Collection;
use intec\core\helpers\Type;
use intec\seo\models\filter\condition\generator\block\Property;

/**
 * Класс, представляющий коллекцию блоков.
 * Class Blocks
 * @package intec\seo\models\filter\condition\generator
 * @author apocalypsisdimon@gmail.com
 */
class Blocks extends Collection
{
    /**
     * Создает коллекцию блоков из массива.
     * @param array $blocks
     * @return static
     */
    public static function create($blocks)
    {
        $result = new static();

        if (!Type::isArray($blocks))
            return $result;

        foreach ($blocks as $block) {
            if (Type::isArray($block))
                $block = new Block($block);

            $result->add($block);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return ($item instanceof Block);
    }

    /**
     * Возвращает комбинации свойств.
     * @return array
     */
    public function getCombinations()
    {
        $combinations = [[]];

        /**
         * @var integer $id
         * @var Block $block
         */
        foreach ($this->items as $id => $block) {
            $collection = [];

            foreach ($combinations as $combination) {
                $properties = $block->getProperties();

                /** @var Property $property */
                foreach ($properties as $property) {
                    $combination[$id] = $property;
                    $collection[] = $combination;
                }
            }

            $combinations = $collection;
        }

        return $combinations;
    }

    /**
     * Возвращает список блоков в виде массива.
     * @return array
     */
    public function export()
    {
        $result = [];

        /** @var Block $item */
        foreach ($this->items as $key => $item)
            $result[$key] = $item->export();

        return $result;
    }
}