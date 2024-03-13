<?php

namespace intec\core\platform\iblock;

use intec\core\helpers\Type;

/**
 * Примесь для реализации выборки по элементам
 * @package intec\core\platform\iblock
 * @author imber228@gmail.com
 */
trait QueryElementsTrait
{
    /**
     * Код(ы) элемента(ов)
     * @var string|string[]|false|null
     */
    protected $_elementsCode;

    /**
     * Идентификатор(ы) элемента(ов)
     * @var integer|integer[]|false|null
     */
    protected $_elementsId;

    /**
     * Шаблон URL элемента
     * @var string|null
     */
    protected $_iBlockElementUrlTemplate;

    /**
     * Возвращает установленные коды элементов
     * @return string|string[]|false|null
     */
    public function getElementsCode()
    {
        return $this->_elementsCode;
    }

    /**
     * Возвращает установленные идентификаторы элементов
     * @return int|integer[]|false|null
     */
    public function getElementsId()
    {
        return $this->_elementsId;
    }

    /**
     * Возвращает установленный шаблон URL элемента
     * @return string|null
     */
    public function getIBlockElementUrlTemplate()
    {
        return $this->_iBlockElementUrlTemplate;
    }

    /**
     * Устанавливает коды элементов
     * @param string|string[]|false|null $value
     * @return $this
     */
    public function setElementsCode($value)
    {
        if ($value !== null)
            if (Type::isArray($value)) {
                $value = array_filter($value);

                if (empty($value))
                    $value = null;
            } else if ($value !== false) {
                $value = Type::toString($value);
            }

        $this->_elementsCode = $value;

        return $this;
    }

    /**
     * Устанавливает идентификаторы элементов
     * @param int|integer[]|false|null $value
     * @return $this
     */
    public function setElementsId($value)
    {
        if ($value !== null)
            if (Type::isNumeric($value)) {
                $value = Type::toInteger($value);
                $value = $value > 0 ? $value : null;
            } else if (Type::isArray($value)) {
                $value = array_filter($value);

                if (empty($value))
                    $value = null;
            } else if ($value !== false) {
                $value = null;
            }

        $this->_elementsId = $value;

        return $this;
    }

    /**
     * Устанавливает шаблон URL элемента
     * @param string|null $value
     * @return $this
     */
    public function setIBlockElementUrlTemplate($value)
    {
        if ($value !== null)
            $value = Type::toString($value);

        $this->_iBlockElementUrlTemplate = $value;

        return $this;
    }
}