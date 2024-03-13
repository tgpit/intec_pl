<?php
namespace intec\core\platform\iblock;

/**
 * Класс, представляющий преобразователь массивов в разделы инфоблока.
 * Class SectionArrayConverter
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class SectionArrayConverter extends SectionConverter
{
    /**
     * @inheritdoc
     */
    public function convert($item)
    {
        return new Section($item);
    }
}
