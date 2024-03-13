<?php
namespace intec\regionality\services\locator\extensions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Service;
use intec\regionality\services\locator\Extension;

Loc::loadMessages(__FILE__);

/**
 * Представляет расширение сервиса локаций с помощью расширения GeoIP.
 * Class GeoIP
 * @inheritdoc
 * @package intec\regionality\services\locator\extensions
 * @author apocalypsisdimon@gmail.com
 */
class GeoIP extends Extension
{
    /**
     * Доступность расширения.
     * @var boolean
     */
    protected $_isAvailable;

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'geoIP';
    }

    /**
     * @inheritdoc
     */
    public function getName($language = LANGUAGE_ID)
    {
        return Loc::getMessage('intec.regionality.services.locator.extensions.geoIP.name', null, $language);
    }

    /**
     * @inheritdoc
     */
    public function getIsAvailable()
    {
        if ($this->_isAvailable === null)
            $this->_isAvailable = extension_loaded('geoip');

        return $this->_isAvailable;
    }

    /**
     * @inheritdoc
     */
    public function resolve($address, $fullData = false)
    {
        $result = null;
        $data = geoip_record_by_name($address);

        if ($fullData) {
            $result = $data;
        } else if (!empty($data) && !empty($data['city'])) {
            $result = $data['city'];
        }

        return $result;
    }
}