<?php
namespace intec\core\base;

/**
 * Интерфейс, представляющий запрос.
 * Interface QueryInterface
 * @package intec\core\base
 */
interface QueryInterface
{
    /**
     * Выполняет запрос и возвращает результат.
     * @return mixed
     */
    public function execute();
}
