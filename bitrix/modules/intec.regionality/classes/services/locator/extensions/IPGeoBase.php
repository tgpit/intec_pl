<?php
namespace intec\regionality\services\locator\extensions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Service;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\net\http\Request;
use intec\core\net\Url;
use intec\regionality\services\locator\Extension;

Loc::loadMessages(__FILE__);

/**
 * Представляет расширение сервиса локаций с помощью онлайн сервиса ipgeobase.ru.
 * Class IPGeoBase
 * @inheritdoc
 * @package intec\regionality\services\locator\extensions
 * @author apocalypsisdimon@gmail.com
 */
class IPGeoBase extends Extension
{
    /**
     * Доступность расширения.
     * @var boolean
     */
    protected $_isAvailable;

    /**
     * Таймаут соединения.
     * @var integer
     */
    public $timeout = 10;

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'ipGeoBase';
    }

    /**
     * @inheritdoc
     */
    public function getName($language = LANGUAGE_ID)
    {
        return Loc::getMessage('intec.regionality.services.locator.extensions.ipGeoBase.name', null, $language);
    }

    /**
     * @inheritdoc
     */
    public function getIsAvailable()
    {
        if ($this->_isAvailable === null) {
            $this->_isAvailable = false;
            /*$request = new Request();
            $request->setTimeout($this->timeout);
            $response = $request->send($this->getUrl()->build());
            $this->_isAvailable = !empty($response->content);*/
        }

        return $this->_isAvailable;
    }

    /**
     * Возвращает Url адрес сервиса.
     * @return Url
     */
    public function getUrl()
    {
        $result = new Url();
        $result
            ->setScheme('http')
            ->setHost('ipgeobase.ru')
            ->setPort(7020);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function resolve($address, $fullData = false)
    {
        $result = null;
        $url = $this->getUrl();
        $url->setPathString('geo');
        $url->getQuery()
            ->set('ip', $address);

        $request = new Request();
        $request->setTimeout($this->timeout);
        $response = $request->send($url->build());
        $content = $response->content;

        if (empty($content))
            return $result;

        if ($fullData) {
            $result = $content;
        } else {
            $startPosition = StringHelper::position('<city>', $content);
            $endPosition = StringHelper::position('</city>', $content);

            if ($startPosition === false || $endPosition === false)
                return $result;

            $startPosition += 6;
            $result = StringHelper::cut($content, $startPosition, $endPosition - $startPosition, Encoding::Windows1251);

            if (!empty($result)) {
                $result = Encoding::convert($result, null, Encoding::Windows1251);
            } else {
                $result = null;
            }
        }

        return $result;
    }
}