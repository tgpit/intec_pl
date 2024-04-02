<?php

namespace VKapi\Market;


class Result
{
    private $arErrors = array();
    private $result = null;
    private $arMore = array();
    /**
 * ����������� ��� ����������, ����������� �������� �� ���������
 * Result constructor.
 * 
 * @param null $result
 */
    public function __construct($result = null)
    {
        if ($result instanceof \VKapi\Market\Error) {
            $this->setError($result);
            return;
        }
        $this->result = $result;
    }
    /**
 * ������� ������ ���������� � ��������� � ���� �������������� ������
 * 
 * @param array $arData
 * @return \VKapi\Market\Result
 */
    public static function create(array $arData)
    {
        $result = new self();
        return $result->setDataArray($arData);
    }
    /**
 * @return bool
 */
    public function isSuccess()
    {
        return empty($this->arErrors);
    }
    /**
 * �������� ������� �� ������ � ����������� �������� �� ���������� ��������
 * @return bool
 */
    public function isTimeoutError()
    {
        return $this->getFirstErrorCode() == 'ERROR_TIMEOUT';
    }
    /**
 * ��������� ������ � ���������
 * 
 * @param $message
 * @param string $code
 * @param array $arMore
 * @return $this
 */
    public function addError($message, $code = '', $arMore = array())
    {
        $this->arErrors[] = new \VKapi\Market\Error($message, $code, $arMore);
        return $this;
    }
    /**
 * @return \VKapi\Market\Error[]
 */
    public function getErrors()
    {
        return $this->arErrors;
    }
    /**
 * ������ ������, ��������� ��� ������ �� ajax ������
 * 
 * @return array
 */
    public function getErrorForJsonAnswer()
    {
        $error = reset($this->arErrors);
        return array('code' => $error->getCode(), 'msg' => $error->getMessage(), 'more' => $error->getMore());
    }
    /**
 * @return Error
 */
    public function getFirstError()
    {
        if (isset($this->arErrors[0])) {
            return $this->arErrors[0];
        } else {
            return new \VKapi\Market\Error(' - ', 0);
        }
    }
    /**
 * ������ ��������� � ������ ������
 * @return string
 */
    public function getFirstErrorMessage()
    {
        $error = $this->getFirstError();
        return $error->getMessage();
    }
    /**
 * ������ ��� ������ ������
 * @return string
 */
    public function getFirstErrorCode()
    {
        $error = $this->getFirstError();
        return $error->getCode();
    }
    /**
 * ������ �������������� ������ �� ������ ������
 * @return array
 */
    public function getFirstErrorMore()
    {
        $error = $this->getFirstError();
        return $error->getMore();
    }
    public function getErrorMessages()
    {
        $ar = array();
        /**
 * @var Error $error
 */
        foreach ($this->arErrors as $error) {
            $ar[] = (!!$error->getCode() ? '[' . $error->getCode() . '] ' : '') . $error->getMessage();
        }
        return $ar;
    }
    public function getErrorMessagesString($glue = ', ')
    {
        return implode($glue, $this->getErrorMessages());
    }
    /**
 * ���������� ������� ������ � ���������
 * 
 * @param \VKapi\Market\Error $error
 * @return $this
 */
    public function setError(\VKapi\Market\Error $error)
    {
        $this->arErrors[] = $error;
        return $this;
    }
    /**
 * ������������� ���������
 * 
 * @deprecated
 * @param $result
 * @return $this
 */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
    /**
 * ������ �������� ����������
 * 
 * @deprecated
 * @return mixed
 */
    public function getResult()
    {
        return $this->result;
    }
    public function setMore($name, $value)
    {
        $this->setData($name, $value);
        return $this;
    }
    /**
 * ������ ����� ������� �������������� ������
 * @param array $arData
 * 
 * @return $this
 */
    public function setDataArray(array $arData)
    {
        foreach ($arData as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }
    /**
 * ���������� ������ � ���������
 * 
 * @param $name - ����
 * @param $value - ��������
 * @return \VKapi\Market\Result
 */
    public function setData($name, $value)
    {
        $this->arMore[$name] = $value;
        return $this;
    }
    /**
 * ������ �������������� ������ �� ����������, ���� ������� ���� �������������� ������
 * �� � ������ �� ������� �������� ������ �� ����� ��� null
 * 
 * @param null $name - ����, ��� ��������� �������� �� ������ �������������� ������
 * @return mixed|null
 */
    public function getData($name = null)
    {
        if ($name === null) {
            return $this->arMore;
        }
        return isset($this->arMore[$name]) ? $this->arMore[$name] : null;
    }
    /**
 * @deprecated
 * @param null $name
 * 
 * @return mixed
 */
    public function getMore($name = null)
    {
        return $this->getData($name);
    }
    /**
 * ������ ������ ����������� ������ ��������
 * 
 * @return mixed
 */
    public function getBitrixError()
    {
        return new \Bitrix\Main\Error($this->getFirstErrorMessage(), $this->getFirstErrorCode(), $this->getFirstErrorMore());
    }
}
?>