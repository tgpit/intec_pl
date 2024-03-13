<?php

namespace intec\core\platform\iblock;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Класс базовых свойств и методов выборки из инфоблоков
 * @package intec\core\platform\iblock
 * @author imber228@gmail.com
 */
abstract class Query extends \intec\core\base\Query
{
    /**
     * Фильтр выборки
     * @var array|null
     */
    protected $_filter;

    /**
     * Информационный блок выборки
     * @var int|null
     */
    protected $_iBlockId;

    /**
     * Тип информационного блока выборки
     * @var string|null
     */
    protected $_iBlockType;

    /**
     * Лимит элементов выборки
     * @var int|null
     */
    protected $_limit;

    /**
     * Смещение выборки
     * @var int|null
     */
    protected $_offset;

    /**
     * Список возвращаемых полей выборки
     * @var array|null
     */
    protected $_select;

    /**
     * Сортировка выборки
     * @var array|null
     */
    protected $_sort;

    /**
     * Автоматическое преобразовывать свойства HTML/Text
     * @var bool
     */
    protected $_textToHtmlAuto = true;

    /**
     * Получать необработанные значения (~)
     * @var bool
     */
    protected $_useTilda = false;

    /**
     * Дополняет уже существующий фильтр выборки
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
     * Возвращает установленный фильтр выборки
     * @return array|null
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * Возвращает установленный идентификатор инфоблока
     * @return int|null
     */
    public function getIBlockId()
    {
        return $this->_iBlockId;
    }

    /**
     * Возвращает установленный тип инфоблока
     * @return null|string
     */
    public function getIBlockType()
    {
        return $this->_iBlockType;
    }

    /**
     * Возвращает глобальность выборки
     * @return bool
     */
    public function getIsGlobal()
    {
        return $this->_iBlockId === null;
    }

    /**
     * Возвращает лимит элементов выборки
     * @return int|null
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Возвращает смещение выборки
     * @return int|null
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * ВОзвращает установленный список возвращаемых полей выборки
     * @return array|null
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * Возвращает установленную сортировку выборки
     * @return array|null
     */
    public function getSort()
    {
        return $this->_sort;
    }

    /**
     * Возвращает активность автоматического преобразования свойств HTML/Text
     * @return bool
     */
    public function getTextToHtmlAuto()
    {
        return $this->_textToHtmlAuto;
    }

    /**
     * Возвращает активность дополнительного получения необработанных значений
     * @return bool
     */
    public function getUseTilda()
    {
        return $this->_useTilda;
    }

    /**
     * Устанавливает фильтр выборки
     * @param array $value
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
     * Устанавливает идентификатор инфоблока
     * @param int $value
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
     * Устанавливает тип инфоблока
     * @param string $value
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
     * Устанавливает лимит элементов выборки
     * @param int $value
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
     * Устанавливает смещение выборки
     * @param int $value
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

    /**
     * Устанавливает список возвращаемых полей выборки
     * @param array $value
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
     * Устанавливает сортировку выборки
     * @param array $value
     * @return $this
     */
    public function setSort($value)
    {
        if (!Type::isArrayable($value))
            $value = null;

        $this->_sort = $value;

        return $this;
    }

    /**
     * Устанавливает активность дополнительного получения необработанных значений
     * @param $value
     * @return $this
     */
    public function setUseTilda($value)
    {
        $this->_useTilda = Type::toBoolean($value);

        return $this;
    }
}