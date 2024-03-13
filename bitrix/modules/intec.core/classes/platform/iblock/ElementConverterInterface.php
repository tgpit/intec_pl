<?php
namespace intec\core\platform\iblock;

use intec\core\base\ConverterInterface;

/**
 * Интерфейс, представляющий конвертер в элементы инфоблока.
 * Interface ElementConverterInterface
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
interface ElementConverterInterface extends ConverterInterface
{
    /**
     * Преобразует несколько элементов.
     * @param array $items Элементы.
     * @return Elements
     */
    public function convertAll($items);

    /**
     * Преобразует один элемент.
     * @param array $item Элемент.
     * @return Element
     */
    public function convert($item);
}
