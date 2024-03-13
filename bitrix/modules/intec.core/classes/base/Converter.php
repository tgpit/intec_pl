<?php
namespace intec\core\base;

/**
 * Класс, представляющий конвертер.
 * Class Converter
 * @package intec\core\base
 */
abstract class Converter extends BaseObject implements ConverterInterface
{
    /**
     * @inheritdoc
     */
    public abstract function convert($object);
}
