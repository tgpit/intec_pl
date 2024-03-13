<?php
namespace intec\core\base;

/**
 * Интерфейс, представляющий конвертер.
 * Interface ConverterInterface
 * @package intec\core\base
 * @author apocalypsisdimon@gmail.com
 */
interface ConverterInterface
{
    /**
     * Преобразует объект.
     * @param $object
     * @return mixed
     */
    public function convert($object);
}
