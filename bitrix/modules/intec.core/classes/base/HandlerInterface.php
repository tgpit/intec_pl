<?php
namespace intec\core\base;

/**
 * Интерфейс, представляющий обработчик.
 * Interface HandlerInterface
 * @package intec\core\base
 * @author apocalypsisdimon@gmail.com
 */
interface HandlerInterface
{
    /**
     * Обрабатывает объект и возвращает результат.
     * @param mixed $object Объект.
     * @return mixed
     */
    public function handle($object);
}
