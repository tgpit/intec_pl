<?php
namespace intec\core\web;

/**
 * Класс, представляющий исключение неприемлимой ошибки.
 * Class NotAcceptableHttpException
 * @package intec\core\web
 */
class NotAcceptableHttpException extends HttpException
{
    /**
     * Конструктор.
     * NotAcceptableHttpException constructor.
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(406, $message, $code, $previous);
    }
}
