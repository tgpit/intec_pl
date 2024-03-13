<?php
namespace intec\core\platform\iblock;

/**
 * Класс, представляющий преобразователь массивов в элементы инфоблока.
 * Class ElementArrayConverter
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class ElementArrayConverter extends ElementConverter
{
    /**
     * Преобразует массив в элемент инфоблока.
     * @param array $item Элемент.
     * @return Element
     */
    public function convert($item)
    {
        $properties = [];

        if (!empty($item['PROPERTIES']))
            foreach ($item['PROPERTIES'] as $property) {
                $property = new ElementProperty($property);
                $properties[$property->getSId()] = $property;
            }

        unset($item['PROPERTIES']);

        return new Element($item, $properties);
    }
}
