<?php

namespace intec\core\platform\iblock;
use intec\core\helpers\Type;

/**
 * Примесь для реализации выборки по разделам
 * @package intec\core\platform\iblock
 * @author imber228@gmail.com
 */
trait QuerySectionsTrait
{
    /**
     * Шаблон URL корня
     * @var string|null
     */
    protected $_iBlockRootUrlTemplate;

    /**
     * Шаблон URL раздела
     * @var string|null
     */
    protected $_iBlockSectionUrlTemplate;

    /**
     * Код(ы) раздела(ов)
     * @var string|string[]|false|null
     */
    protected $_sectionsCode;

    /**
     * Идентификатор(ы) раздела(ов)
     * @var integer|integer[]|null
     */
    protected $_sectionsId;

    /**
     * Возвращает шаблон URL корня инфоблока
     * @return string|null
     */
    public function getIBlockRootUrlTemplate()
    {
        return $this->_iBlockRootUrlTemplate;
    }

    /**
     * Возвращает шаблон URL раздела инфоблока
     * @return string|null
     */
    public function getIBlockSectionUrlTemplate()
    {
        return $this->_iBlockSectionUrlTemplate;
    }

    /**
     * Возвращает установленные коды разделов
     * @return string|string[]|null
     */
    public function getSectionsCode()
    {
        return $this->_sectionsCode;
    }

    /**
     * Возвращает установленные идентификаторы разделов
     * @return int|integer[]|null
     */
    public function getSectionsId()
    {
        return $this->_sectionsId;
    }

    /**
     * Устанавливает шаблон URL корня инфоблока
     * @param string|null $value
     * @return $this
     */
    public function setIBlockRootUrlTemplate($value)
    {
        if ($value !== null)
            $value = Type::toString($value);

        $this->_iBlockRootUrlTemplate = $value;

        return $this;
    }

    /**
     * Устанавливает шаблон URL раздела инфоблока
     * @param string|null $value
     * @return $this
     */
    public function setIBlockSectionUrlTemplate($value)
    {
        if ($value !== null)
            $value = Type::toString($value);

        $this->_iBlockSectionUrlTemplate = $value;

        return $this;
    }

    /**
     * Устанавливает коды разделов
     * @param string|string[]|false|null $value
     * @return $this
     */
    public function setSectionsCode($value)
    {
        if ($value !== null)
            if (Type::isArray($value)) {
                $value = array_filter($value);

                if (empty($value))
                    $value = null;
            } else if ($value !== false) {
                $value = Type::toString($value);
            }

        $this->_sectionsCode = $value;

        return $this;
    }

    /**
     * Устанавливает идентификаторы разделов
     * @param integer|integer[]|false|null $value
     * @return $this
     */
    public function setSectionsId($value)
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

        $this->_sectionsId = $value;

        return $this;
    }
}