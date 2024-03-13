<?php
namespace intec\core\helpers;

/**
 * Класс, содержащий в себе математические функции.
 * Class Math
 * @package intec\core\helpers
 * @author apocalypsisdimon@gmail.com
 */
class Math
{
    /**
     * Приводит число к кратности по основанию.
     * @param float $value Значение.
     * @param float $base Основание
     * @return integer|float
     */
    public static function multiplicity($value, $base)
    {
        return $base * round($value / $base);
    }
}
