<?php
namespace intec\core\platform\iblock;

use intec\core\base\ArrayModel;
use intec\core\helpers\Type;

/**
 * Класс, представляющий свойство инфоблока.
 * Class Property
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class Property extends ArrayModel
{
    /**
     * Возвращает код.
     * @return mixed
     */
    public function getCode()
    {
        return $this->_fields['CODE'];
    }

    /**
     * Возвращает идентификатор.
     * @return integer
     */
    public function getId()
    {
        return Type::toInteger($this->_fields['ID']);
    }

    /**
     * Возвращает значение, указывающее на активность свойства.
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->_fields['ACTIVE'] === 'Y';
    }

    /**
     * Возвращает значение, указывающее на множественность свойства.
     * @return boolean
     */
    public function getIsMultiple()
    {
        return $this->_fields['MULTIPLE'] === 'Y';
    }

    /**
     * Возвращает значение, указывающее на то, является ли свойство пользовательским.
     * @return boolean
     */
    public function getIsUserProperty()
    {
        return $this->_fields['USER_TYPE'] !== null;
    }

    /**
     * Возвращает тип списка свойства списка.
     * @return string
     */
    public function getListType()
    {
        return $this->_fields['LIST_TYPE'];
    }

    /**
     * Возвращает специальный идентификатор.
     * @return integer|string
     */
    public function getSId()
    {
        $result = $this->getCode();

        if (Type::isEmpty($result))
            $result = $this->getId();

        return $result;
    }

    /**
     * Возвращает сортировку.
     * @return integer
     */
    public function getSort()
    {
        return Type::toInteger($this->_fields['SORT']);
    }

    /**
     * Возвращает тип.
     * @return string
     */
    public function getType()
    {
        return $this->_fields['PROPERTY_TYPE'];
    }

    /**
     * Возвращает пользовательский тип.
     * @return string
     */
    public function getUserType()
    {
        return $this->_fields['USER_TYPE'];
    }
}
