<?php

namespace VKapi\Market;

/**
 * Класс ошибок
 * Class Error
 * @package VKapi\Market
 */
class Error
{
    private $code = '';
    private $message = '';
    private $arMore = array();
    /**
 * Конструктор для создания ошибки
 * Error constructor.
 * 
 * @param $message - текст ошибки
 * @param string $code - код ошибки
 * @param array $arMore - массив дополнительных данных
 */
    public function __construct($message, $code = '', $arMore = array())
    {
        $this->message = is_array($message) ? serialize($message) : $message;
        $this->code = $code;
        $this->arMore = $arMore;
    }
    /**
 * Вернет текст ошибки
 * @return string
 */
    public function getMessage()
    {
        return $this->message;
    }
    /**
 * Вернет код ошибки
 * @return string
 */
    public function getCode()
    {
        return $this->code;
    }
    /**
 * Вернет масси дополнительных данных по ошибке
 * @return array
 */
    public function getMore()
    {
        return $this->arMore;
    }
    /**
 * Вернет масси дополнительных данных по ошибке
 * @param string $name - ключ
 * @param mixed $value - значение
 */
    public function setMore($name, $value)
    {
        $this->arMore[$name] = $value;
    }
}
?>