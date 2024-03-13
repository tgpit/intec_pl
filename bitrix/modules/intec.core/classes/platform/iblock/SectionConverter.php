<?php
namespace intec\core\platform\iblock;

use intec\core\base\BaseObject;

/**
 * Класс, представляющий конвертер в разделы инфоблока.
 * Class SectionConverter
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
abstract class SectionConverter extends BaseObject implements SectionConverterInterface
{
    /**
     * @inheritdoc
     */
    public function convertAll($items)
    {
        $result = new Sections();

        foreach ($items as $item)
            $result->add($this->convert($item));

        return $result;
    }
}
