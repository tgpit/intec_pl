<?php
namespace intec\core\base;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Serializable;

/**
 * Класс, представляющий модель-массив.
 * Class ArrayModel
 * @package intec\core\base
 * @author apocalypsisdimon@gmail.com
 */
class ArrayModel extends BaseObject implements IteratorAggregate, ArrayAccess, Countable//, Serializable
{
    /**
     * Коллекция полей.
     * @var Collection
     */
    protected $_fields;

    /**
     * Конструктор.
     * ArrayModel constructor.
     * @param array $fields
     * @param array $config
     */
    public function __construct($fields = [], array $config = [])
    {
        $this->_fields = new Collection($fields);

        parent::__construct($config);
    }

    /**
     * Возвращает поля в виде массива.
     * @return array
     */
    public function asArray()
    {
        return $this->_fields->asArray();
    }

    /**
     * Возвращает коллекцию полей.
     * @return Collection
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $this->_fields->getIterator();
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->_fields->offsetExists($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->_fields->offsetGet($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->_fields->offsetSet($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->_fields->offsetUnset($offset);
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return $this->_fields->serialize();
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $this->_fields->unserialize($serialized);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->_fields->count();
    }
}
