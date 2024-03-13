<?php

namespace intec\core\platform\iblock;

use intec\core\helpers\Type;

/**
 * Примесь для реализации группировки результата в выборке
 * @package intec\core\platform\iblock
 * @author imber228@gmail.com
 */
trait QueryGroupTrait
{
    /**
     * Группировка выборки
     * @var array|null
     */
    protected $_group;

    /**
     * Возвращает установленную группировку выборки
     * @return array|null
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Устанавливает группировку выборки
     * @param array $value
     * @return $this
     */
    public function setGroup($value)
    {
        if (!Type::isArrayable($value))
            $value = null;

        $this->_group = $value;

        return $this;
    }
}