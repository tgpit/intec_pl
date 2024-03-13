<?php
namespace intec\core\base;

/**
 * Класс, представляющий обработчик.
 * Class Handler
 * @package intec\core\base
 * @author apocalypsisdimon@gmail.com
 */
abstract class Handler extends BaseObject implements HandlerInterface
{
    /**
     * @inheritdoc
     */
    public abstract function handle($object);
}
