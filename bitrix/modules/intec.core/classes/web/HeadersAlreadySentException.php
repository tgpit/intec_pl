<?php
namespace intec\core\web;

use intec\core\base\Exception;

/**
 * Класс, представляющий исключение о том, что заголовки были отправлены.
 * Class HeadersAlreadySentException
 * @package intec\core\web
 */
class HeadersAlreadySentException extends Exception
{
    /**
     * Конструктор.
     * HeadersAlreadySentException constructor.
     * @param $file
     * @param $line
     */
    public function __construct($file, $line)
    {
        parent::__construct("Headers already sent in {$file} on line {$line}.");
    }
}
