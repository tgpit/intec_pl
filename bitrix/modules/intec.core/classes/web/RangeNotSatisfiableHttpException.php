<?php
namespace intec\core\web;

/**
 * Класс, представляющий исключение неудовлетворительного диапазона HTTP.
 * Class RangeNotSatisfiableHttpException
 * @package intec\core\web
 */
class RangeNotSatisfiableHttpException extends HttpException
{
    /**
     * Конструктор.
     * RangeNotSatisfiableHttpException constructor.
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(416, $message, $code, $previous);
    }
}
