<?php

namespace VKapi\Market\Ajax;

use VKapi\Market\Result;
use VKapi\Market\Exception\BaseException;
/**
 * ����� ��� ������ ������� �� ajax ������
 * Class Error
 * 
 * @package VKapi\Market
 */
class JsonResponse
{
    protected $arError = null;
    protected $arResponse = array();
    /**
     * �������� �������� �����
     * @param array $arData - ������
     */
    public function setResponse($arData)
    {
        $this->arResponse = $arData;
    }
    /**
     * �������� ������ � ������������ ���� ��������� ������
     * @param string $code
     * @param mixed $data
     */
    public function setResponseField($code, $data)
    {
        $this->arResponse[$code] = $data;
    }
    /**
     * �������� ������ � �����
     * @param $msg - ����� ������
     * @param $code - ��� ������
     * @param array $arData - �������������� ������
     */
    public function setError($msg, $code, $arData = [])
    {
        $this->arError = ['msg' => $msg, 'code' => $code, 'more' => $arData];
    }
    /**
     * �������� ������ � ����� �� ������� Result
     */
    public function setErrorFromResult(\VKapi\Market\Result $result)
    {
        $this->setError($result->getFirstError()->getMessage(), $result->getFirstError()->getCode(), $result->getFirstError()->getMore());
    }
    /**
     * �������� � �������� ������ ����������
     * @param \Throwable $exception
     */
    public function setException(\Throwable $exception)
    {
        $trace = '';
        if (defined('VKAPI_MARKET_DEBUG') && constant('VKAPI_MARKET_DEBUG') == true) {
            $trace .= ' | ' . $exception->getFile() . ':' . $exception->getLine();
            $trace .= ' | ' . $exception->getTraceAsString();
        }
        if ($exception instanceof \VKapi\Market\Exception\BaseException) {
            $this->setError($exception->getMessage() . $trace, $exception->getCustomCode(), $exception->getCustomData());
        } else {
            $this->setError($exception->getMessage() . $trace, 'UNKNOWN_EXCEPTION');
        }
    }
    /**
     * ������ ����� �� ���������� �������
     * @throws \Bitrix\Main\ArgumentException
     */
    public function output()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');
        if (!is_null($this->arError)) {
            echo \Bitrix\Main\Web\Json::encode(['error' => $this->arError]);
        } else {
            echo \Bitrix\Main\Web\Json::encode(['response' => $this->arResponse]);
        }
        self::finish();
    }
    /**
     * ���������� �� ����� ������ json, ����� ������ ������� �� ����������
     */
    public static function finish()
    {
        if (\Bitrix\Main\Loader::includeModule("compression")) {
            \CCompress::DisableCompression();
        }
        \Bitrix\Main\Context::getCurrent()->getResponse()->writeHeaders();
        \Bitrix\Main\Application::getConnection()->disconnect();
        die;
    }
}
?>