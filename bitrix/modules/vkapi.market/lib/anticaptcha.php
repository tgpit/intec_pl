<?php

namespace VKapi\Market;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
class AntiCaptchaTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_anticaptcha_list';
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)), new \Bitrix\Main\Entity\IntegerField('CID', array('required' => true)), new \Bitrix\Main\Entity\StringField('WORD', array()), new \Bitrix\Main\Entity\StringField('STATUS', array('required' => true, 'validator' => function () {
            return array(new \Bitrix\Main\Entity\Validator\Range(1, 1));
        }, 'default_value' => 0)), new \Bitrix\Main\Entity\DatetimeField('TIME_CREATE', array('required' => true, 'default_value' => new \Bitrix\Main\Type\DateTime())), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'));
    }
}
final class AntiCaptcha
{
    private static $instance = null;
    /**
         * 
         * @var HttpClient $this ->oHTTP
         */
    protected $bDebug = false;
    protected $antigate_key = '';
    private function __construct()
    {
        $this->oTable = new \VKapi\Market\AntiCaptchaTable();
        $this->bDebug = \VKapi\Market\Manager::getInstance()->getParam('DEBUG', 'N') == 'Y';
        $this->antigate_key = \VKapi\Market\Manager::getInstance()->getParam('ANTIGATE_KEY', '');
    }
    private function __clone()
    {
    }
    /**
         * 
         * @return AntiCaptcha
         */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }
    /**
         * 
         * Îòïğàâêà êàï÷è íà ğàñïîçíàâàíèå
         * @param $img_content
         * @return Result
         * @throws \Exception
         */
    public function sendImageContent($img_content)
    {
        $oResult = new \VKapi\Market\Result();
        $oHTTP = new \Bitrix\Main\Web\HttpClient();
        $oHTTP->post('http://antigate.com/in.php', array('method' => 'base64', 'key' => $this->antigate_key, 'body' => base64_encode($img_content), 'is_russian' => 1));
        if ($oHTTP->getStatus() == 200) {
            $result = explode('|', $oHTTP->getResult());
            if ($result[0] == 'OK') {
                $resAdd = $this->oTable->add(array('CID' => $result[1]));
                if ($resAdd->isSuccess()) {
                    $oResult->setData('ID', $resAdd->getId());
                } else {
                    $oResult->setError(new \VKapi\Market\Error('ANTIGATE_UNKNOWN_RESPONSE', 0));
                }
            } else {
                $oResult->setError(new \VKapi\Market\Error($oHTTP->getResult(), 0));
            }
        } else {
            $oResult->setError(new \VKapi\Market\Error('ANTIGATE_ERROR_RESPONSE_STATUS', 'ANTIGATE_ERROR_RESPONSE_STATUS', array('HTTP' => $oHTTP)));
        }
        return $oResult;
    }
    /**
         * 
         * get result
         * @param $id
         * @return null|false|string
         * @throws \Bitrix\Main\ArgumentException
         */
    public function getWord($id)
    {
        $resGet = $this->oTable->getList(array('filter' => array('ID' => $id)));
        if ($ar = $resGet->fetch()) {
            return $ar['WORD'];
        } else {
            return false;
        }
    }
    /**
         * 
         * Àãåíò êîòîğûé ïğâîåğÿåò åñòü ëè îòâåòû íà îòïğàâëåííûå captcha
         * @return mixed|string
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Bitrix\Main\ObjectPropertyException
         * @throws \Bitrix\Main\SystemException
         */
    public function checkResult()
    {
        $oHTTP = new \Bitrix\Main\Web\HttpClient();
        $resGet = $this->oTable->getList(array('filter' => array('STATUS' => 0)));
        while ($ar = $resGet->fetch()) {
            $oHTTP->get('http://antigate.com/res.php?key=' . $this->antigate_key . '&action=get&id=' . $ar['CID']);
            if ($oHTTP->getStatus() == 200) {
                $res_word = explode('|', $oHTTP->getResult());
                if ($res_word[0] == 'OK') {
                    $this->oTable->update($ar['ID'], array('WORD' => $res_word[1], 'STATUS' => 1));
                } elseif ($res_word['0'] == 'CAPCHA_NOT_READY') {
                    // 
                } else {
                    $this->oTable->update($ar['ID'], array('WORD' => '', 'STATUS' => 3));
                }
            }
            return $ar['WORD'];
        }
        return __METHOD__ . '();';
    }
    /**
         * 
         * Àãåíò óäàëÿşùéè ñòàğûå çàïğîñû íà ğàñïîçíàâàíèå captcha
         * @return string
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Bitrix\Main\ObjectPropertyException
         * @throws \Bitrix\Main\SystemException
         */
    public static function clearAgent()
    {
        $oTable = new \VKapi\Market\AntiCaptchaTable();
        $resGet = $oTable->getList(array('filter' => array('<TIME_CREATE' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time() - 60 * 5))));
        while ($ar = $resGet->fetch()) {
            $oTable->delete($ar['ID']);
        }
        return __METHOD__ . '();';
    }
}
?>