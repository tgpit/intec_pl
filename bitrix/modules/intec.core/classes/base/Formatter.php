<?php
namespace intec\core\base;

/**
 * Класс, представляющий форматер.
 * Class Formatter
 * @package intec\core\base
 * @author apocalypsisdimon@gmail.com
 */
abstract class Formatter extends BaseObject implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public abstract function format($object);
}
