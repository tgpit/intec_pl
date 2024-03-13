<?php
namespace intec\regionality\services\locator\extensions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Service;
use intec\regionality\services\locator\Extension;

Loc::loadMessages(__FILE__);

/**
 * Представляет расширение сервиса локаций.
 * Class BitrixGeoIPManager
 * @inheritdoc
 * @package intec\regionality\services\locator\extensions
 * @author apocalypsisdimon@gmail.com
 */
class BitrixGeoIPManager extends Extension
{
    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'bitrixGeoIPManager';
    }

    /**
     * @inheritdoc
     */
    public function getName($language = LANGUAGE_ID)
    {
        return Loc::getMessage('intec.regionality.services.locator.extensions.bitrixGeoIPManager.name', null, $language);
    }

    /**
     * @inheritdoc
     */
    public function getIsAvailable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function resolve($address, $fullData = false)
    {
        $result = null;
        $data = Service\GeoIp\Manager::getDataResult($address, 'en', ['countryName', 'cityName']);

        if ($fullData) {
            $result = $data;
        } else if (!empty($data)) {
            $data = $data->getGeoData();

            if (!empty($data->cityName))
                $result = $data->cityName;
        }

        return $result;
    }
}