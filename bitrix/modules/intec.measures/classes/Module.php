<?php
namespace intec\measures;

use CCatalogMeasure;
use intec\core\base\BaseObject;
use intec\core\collections\Arrays;

/**
 * Класс модуля.
 * Class Module
 * @package intec\regionality
 * @author apocalypsisdimon@gmail.com
 */
class Module extends BaseObject
{
    /**
     * Единицы измерения системы.
     * @var array
     */
    protected static $_measures;

    /**
     * Возвращает единицы измерения системы.
     * @param boolean $reset Сбросить.
     * @return array
     */
    public static function getMeasures($reset = false)
    {
        if (static::$_measures === null || $reset) {
            static::$_measures = Arrays::fromDBResult(CCatalogMeasure::getList())
                ->indexBy('ID')
                ->asArray();
        }

        return static::$_measures;
    }
}