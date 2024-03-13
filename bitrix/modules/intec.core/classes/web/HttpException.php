<?php
namespace intec\core\web;

use intec\core\base\Exception;

/**
 * Класс, представляющий ошибку HTTP.
 * Class HttpException
 * @package intec\core\web
 */
class HttpException extends Exception
{
    /**
     * Статус.
     * @var integer
     */
    public $statusCode;

    /**
     * Конструктор.
     * HttpException constructor.
     * @param $status
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Возвращает наименование статуса.
     * @return string
     */
    public function getName()
    {
        if (isset(Response::$httpStatuses[$this->statusCode]))
            return Response::$httpStatuses[$this->statusCode];

        return 'Error';
    }
}
