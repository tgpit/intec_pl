<?php

namespace VKapi\Market;

/**
 * ����� ������
 * Class Error
 * @package VKapi\Market
 */
class Error
{
    private $code = '';
    private $message = '';
    private $arMore = array();
    /**
 * ����������� ��� �������� ������
 * Error constructor.
 * 
 * @param $message - ����� ������
 * @param string $code - ��� ������
 * @param array $arMore - ������ �������������� ������
 */
    public function __construct($message, $code = '', $arMore = array())
    {
        $this->message = is_array($message) ? serialize($message) : $message;
        $this->code = $code;
        $this->arMore = $arMore;
    }
    /**
 * ������ ����� ������
 * @return string
 */
    public function getMessage()
    {
        return $this->message;
    }
    /**
 * ������ ��� ������
 * @return string
 */
    public function getCode()
    {
        return $this->code;
    }
    /**
 * ������ ����� �������������� ������ �� ������
 * @return array
 */
    public function getMore()
    {
        return $this->arMore;
    }
    /**
 * ������ ����� �������������� ������ �� ������
 * @param string $name - ����
 * @param mixed $value - ��������
 */
    public function setMore($name, $value)
    {
        $this->arMore[$name] = $value;
    }
}
?>