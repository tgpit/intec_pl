<?php
namespace Pecom\Delivery\Core\Entity;

/**
 * Trait FieldsContainer
 * @package Pecom\Delivery\Core
 * @subpackage Entity
 */
trait FieldsContainer
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @return array
     */
    public function getContainer()
    {
        if (!is_array($this->fields))
            $this->fields = [];

        return $this->fields;
    }

    /**
     * @param string $code
     * @param mixed $val
     * @return $this
     */
    public function setField($code, $val)
    {
        $this->fields[$code] = $val;

        return $this;
    }

    /**
     * @param array $arFields
     * @return $this
     */
    public function setFields(array $arFields)
    {
        foreach ($arFields as $field => $val) {
            $this->setField($field, $val);
        }
        return $this;
    }

    /**
     * @param string $code
     * @return mixed|null
     */
    public function getField($code)
    {
        return (array_key_exists($code, $this->getContainer())) ? $this->getContainer()[$code] : null;
    }
}