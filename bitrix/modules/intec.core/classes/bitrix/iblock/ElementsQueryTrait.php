<?php

namespace intec\core\bitrix\iblock;

use intec\core\helpers\Type;

/**
 * Trait SectionsQueryTrait
 * @property string|null $iBlockElementUrlTemplate Шаблон URL элемента.
 * @property integer|integer[] $iBlockElementsId Идентификатор(ы) элемента(ов) инфоблока.
 * @property string|string[] $iBlockElementsCode Код(ы) элемента(ов) инфоблока.
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
trait ElementsQueryTrait
{
    /**
     * Шаблон URL элемента.
     * @var string
     */
    protected $_iBlockElementUrlTemplate;
    /**
     * Код(ы) элемента(ов).
     * @var string|string[]|null
     */
    protected $_iBlockElementsCode;
    /**
     * Идентификатор(ы) элемента(ов).
     * @var integer|integer[]|null
     */
    protected $_iBlockElementsId;

    /**
     * Возвращает шаблон URL элемента инфоблока.
     * @return string|null
     */
    public function getIBlockElementUrlTemplate()
    {
        return $this->_iBlockElementUrlTemplate;
    }

    /**
     * Получает коды элементов.
     * @return string|string[]|false|null
     */
    public function getIBlockElementsCode()
    {
        return $this->_iBlockElementsCode;
    }

    /**
     * Получает идентификаторы элементов.
     * @return integer|integer[]|false|null
     */
    public function getIBlockElementsId()
    {
        return $this->_iBlockElementsId;
    }

    /**
     * Устанавливает шаблон URL элемента инфоблока.
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

    /**
     * Устанавливает коды элементов.
     * @param string|string[]|false|null $value
     * @return $this
     */
    public function setIBlockElementsCode($value)
    {
        if ($value !== null)
            if (Type::isArray($value)) {
                $value = array_filter($value);

                if (empty($value))
                    $value = null;
            } else if ($value !== false) {
                $value = Type::toString($value);
            }

        $this->_iBlockElementsCode = $value;

        return $this;
    }

    /**
     * Устанавливает идентификаторы элементов.
     * @param integer|integer[]|false|null $value
     * @return $this
     */
    public function setIBlockElementsId($value)
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

        $this->_iBlockElementsId = $value;

        return $this;
    }
}