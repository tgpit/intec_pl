<?php
namespace intec\core\web;

/**
 * Класс, представляющий исключение о не найденой странице.
 * Class NotFoundHttpException
 * @package intec\core\web
 */
class NotFoundHttpException extends HttpException
{
    /**
     * Конструктор.
     * NotFoundHttpException constructor.
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(404, $message, $code, $previous);
    }
}
