<?php
namespace intec\core\platform\iblock;

use intec\core\base\BaseObject;

/**
 * Класс, представляющий конвертер в элементы инфоблока.
 * Class ElementConverter
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
abstract class ElementConverter extends BaseObject implements ElementConverterInterface
{
    /**
     * @inheritdoc
     */
    public function convertAll($items)
    {
        $result = new Elements();

        foreach ($items as $item)
            $result->add($this->convert($item));

        return $result;
    }
}
