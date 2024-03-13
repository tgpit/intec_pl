<?php

namespace intec\core\bitrix\iblock;

use intec\core\helpers\Type;

/**
 * Trait GroupQueryTrait
 * @property integer|null $group Группировка выборки.
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
trait GroupQueryTrait
{
    /**
     * Группировка выборки.
     * @var integer|null
     */
    protected $_group;

    /**
     * Возвращает группировку выборки.
     * @return integer|null
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Устанавливает группировку выборки.
     * @param integer|null $value
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