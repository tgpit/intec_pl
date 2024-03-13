<?php
namespace intec\core\platform\iblock;

use intec\core\base\ConverterInterface;

/**
 * Интерфейс, представляющий конвертер в разделы инфоблока.
 * Interface SectionConverterInterface
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
interface SectionConverterInterface extends ConverterInterface
{
    /**
     * Преобразует несколько элементов.
     * @param array $items Разделы.
     * @return Sections
     */
    public function convertAll($items);

    /**
     * Преобразует один элемент.
     * @param array $item Раздел.
     * @return Section
     */
    public function convert($item);
}
