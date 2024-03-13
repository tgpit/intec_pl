<?php
namespace intec\core\web;

/**
 * Интерфейс, представляющий преобразователь ответа в какой-либо формат.
 * Interface ResponseFormatterInterface
 * @package intec\core\web
 */
interface ResponseFormatterInterface
{
    /**
     * Преобразует ответ в какой-либо формат.
     * @param Response $response
     * @return mixed
     */
    public function format($response);
}
