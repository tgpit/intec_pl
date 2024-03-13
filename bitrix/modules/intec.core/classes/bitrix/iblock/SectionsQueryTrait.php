<?php

namespace intec\core\bitrix\iblock;

use intec\core\helpers\Type;

/**
 * Trait SectionsQueryTrait
 * @property string|null $iBlockRootUrlTemplate Шаблон URL корня. Только для чтения.
 * @property string|null $iBlockSectionUrlTemplate Шаблон URL раздела. Только для чтения.
 * @property integer|integer[] $iBlockSectionsId Идентификатор(ы) раздела(ов) инфоблока.
 * @property string|string[] $iBlockSectionsCode Код(ы) раздела(ов) инфоблока.
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
trait SectionsQueryTrait
{
    /**
     * Шаблон URL раздела.
     * @var string
     */
    protected $_iBlockSectionUrlTemplate;
    /**
     * Код(ы) раздела(ов).
     * @var string|string[]|null
     */
    protected $_iBlockSectionsCode;
    /**
     * Идентификатор(ы) раздела(ов).
     * @var integer|integer[]|null
     */
    protected $_iBlockSectionsId;
    /**
     * Шаблон URL корня.
     * @var string
     */
    protected $_iBlockRootUrlTemplate;

    /**
     * Возвращает шаблон URL корня инфоблока.
     * @return string|null
     */
    public function getIBlockRootUrlTemplate()
    {
        return $this->_iBlockRootUrlTemplate;
    }

    /**
     * Возвращает шаблон URL раздела инфоблока.
     * @return string|null
     */
    public function getIBlockSectionUrlTemplate()
    {
        return $this->_iBlockSectionUrlTemplate;
    }

    /**
     * Получает коды разделов.
     * @return string|string[]|false|null
     */
    public function getIBlockSectionsCode()
    {
        return $this->_iBlockSectionsCode;
    }

    /**
     * Получает идентификаторы разделов инфоблока.
     * @return integer|integer[]|false|null
     */
    public function getIBlockSectionsId()
    {
        return $this->_iBlockSectionsId;
    }

    /**
     * Устанавливает шаблон URL корня инфоблока.
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
     * Устанавливает шаблон URL раздела инфоблока.
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
     * Устанавливает коды разделов.
     * @param string|string[]|false|null $value
     * @return $this
     */
    public function setIBlockSectionsCode($value)
    {
        if ($value !== null)
            if (Type::isArray($value)) {
                $value = array_filter($value);

                if (empty($value))
                    $value = null;
            } else if ($value !== false) {
                $value = Type::toString($value);
            }

        $this->_iBlockSectionsCode = $value;

        return $this;
    }

    /**
     * Устанавливает идентификаторы разделов.
     * @param integer|integer[]|false|null $value
     * @return $this
     */
    public function setIBlockSectionsId($value)
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

        $this->_iBlockSectionsId = $value;

        return $this;
    }
}