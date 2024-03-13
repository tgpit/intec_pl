<?php

namespace intec\core\bitrix\iblock;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Class Query
 * @property integer $iBlockId Идентификатор инфоблока.
 * @property string $iBlockType Тип инфоблока.
 * @package intec\core\bitrix\iblock
 * @deprecated 
 */
abstract class Query extends \intec\core\bitrix\Query
{
    /**
     * Фильтр.
     * @var array|null
     */
    protected $_filter;
    /**
     * Идентификатор инфоблока.
     * @var integer|null
     */
    protected $_iBlockId;
    /**
     * Тип инфоблока.
     * @var string|null
     */
    protected $_iBlockType;
    /**
     * Поля выборки.
     * @var array|null
     */
    protected $_select;
    /**
     * Сортировка.
     * @var array|null
     */
    protected $_sort;

    /**
     * Расширяет уже существующий фильтр.
     * @param array $value
     * @return $this
     */
    public function extendFilter($value)
    {
        if (Type::isArrayable($value)) {
            if (!Type::isArrayable($this->_filter))
                $this->_filter = [];

            $this->_filter = ArrayHelper::merge($this->_filter, $value);
        }

        return $this;
    }

    /**
     * Возвращает фильтр.
     * @return array|null
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * Возвращает идентификатор инфоблока.
     * @return integer|null
     */
    public function getIBlockId()
    {
        return $this->_iBlockId;
    }

    /**
     * Возвращает тип инфоблока.
     * @return integer|null
     */
    public function getIBlockType()
    {
        return $this->_iBlockType;
    }

    /**
     * Возвращает значение, является ли запрос глобальным.
     * @return boolean
     */
    public function getIsGlobal()
    {
        return $this->_iBlockId === null;
    }

    /**
     * Возвращает поля выборки.
     * @return array|null
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * Возвращает сортировку.
     * @return array|null
     */
    public function getSort()
    {
        return $this->_sort;
    }

    /**
     * Устанавливает фильтр.
     * @param array|null $value
     * @return $this
     */
    public function setFilter($value)
    {
        if (!Type::isArrayable($value))
            $value = null;

        $this->_filter = $value;

        return $this;
    }

    /**
     * Устанавливает идентификатор инфоблока.
     * @param integer|null $value
     * @return $this
     */
    public function setIBlockId($value)
    {
        if ($value !== null) {
            $value = Type::toInteger($value);
            $value = $value > 0 ? $value : null;
        }

        $this->_iBlockId = $value;

        return $this;
    }

    /**
     * Устанавливает тип инфоблока.
     * @param string|null $value
     * @return $this
     */
    public function setIBlockType($value)
    {
        if ($value !== null)
            $value = Type::toString($value);

        $this->_iBlockType = $value;

        return $this;
    }

    /**
     * Устанавливает поля выборки.
     * @param array|null $value
     * @return $this
     */
    public function setSelect($value)
    {
        if (!Type::isArrayable($value))
            $value = null;

        $this->_select = $value;

        return $this;
    }

    /**
     * Устанавливает сортировку.
     * @param array|null $value
     * @return $this
     */
    public function setSort($value)
    {
        if (!Type::isArrayable($value))
            $value = null;

        $this->_sort = $value;

        return $this;
    }
}
