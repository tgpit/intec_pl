<?php
namespace intec\regionality\services\locator\extensions;

use CCity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Service;
use intec\regionality\services\locator\Extension;

Loc::loadMessages(__FILE__);

/**
 * Представляет расширение сервиса локаций с помощью модуля статистики Bitrix.
 * Class BitrixStatistic
 * @inheritdoc
 * @package intec\regionality\services\locator\extensions
 * @author apocalypsisdimon@gmail.com
 */
class BitrixStatistic extends Extension
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
        return 'bitrixStatistic';
    }

    /**
     * @inheritdoc
     */
    public function getName($language = LANGUAGE_ID)
    {
        return Loc::getMessage('intec.regionality.services.locator.extensions.bitrixStatistic.name', null, $language);
    }

    /**
     * @inheritdoc
     */
    public function getIsAvailable()
    {
        if ($this->_isAvailable === null)
            $this->_isAvailable = Loader::includeModule('statistic');

        return $this->_isAvailable;
    }

    /**
     * @inheritdoc
     */
    public function resolve($address, $fullData = false)
    {
        $result = null;
        $city = new CCity();
        $city = $city->GetFullInfo();

        if ($fullData) {
            $result = $city;
        } else if (
            !empty($city) &&
            !empty($city['CITY_NAME']) &&
            !empty($city['CITY_NAME']['VALUE'])
        ) $result = $city['CITY_NAME']['VALUE'];

        return $result;
    }
}