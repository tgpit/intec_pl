<?php

namespace intec\core\bitrix\iblock;

use intec\core\helpers\Type;

/**
 * Trait LimitQueryTrait
 * @property integer|null $limit Лимит выборки.
 * @property integer|null $offset Смещение выборки.
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
trait LimitQueryTrait
{
    /**
     * Лимит выборки.
     * @var integer|null
     */
    protected $_limit;
    /**
     * Смещение выборки.
     * @var integer|null
     */
    protected $_offset;

    /**
     * Возвращает лимит выборки.
     * @return integer|null
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Возвращает смещение выборки.
     * @return integer|null
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Устанавливает лимит выборки.
     * @param integer|null $value
     * @return $this
     */
    public function setLimit($value)
    {
        if ($value !== null) {
            $value = Type::toInteger($value);
            $value = $value > 0 ? $value : null;
        }

        $this->_limit = $value;

        return $this;
    }

    /**
     * Устанавливает смещение выборки.
     * @param integer|null $value
     * @return $this
     */
    public function setOffset($value)
    {
        if ($value !== null) {
            $value = Type::toInteger($value);
            $value = $value > 0 ? $value : null;
        }

        $this->_offset = $value;

        return $this;
    }
}