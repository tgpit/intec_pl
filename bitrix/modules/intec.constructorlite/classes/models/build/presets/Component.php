<?php
namespace intec\constructor\models\build\presets;

use intec\core\helpers\Type;
use intec\constructor\models\build\Preset;
use intec\constructor\models\build\preset\Group;

/**
 * Class Component
 * @property string $code
 * @property string $template
 * @property string $name
 * @property string $picturePath
 * @property Group|null $group
 * @property integer $sort
 * @property array $properties
 * @package intec\constructor\models\build\preset
 */
class Component extends Preset
{
    protected $_code;

    protected $_template;

    protected $_name;

    protected $_picturePath;

    protected $_group;

    protected $_sort;

    protected $_properties;

    public function getCode()
    {
        return $this->_code;
    }

    public function setCode($value)
    {
        $this->_code = $value;
    }

    public function getTemplate()
    {
        return $this->_template;
    }

    public function setTemplate($value)
    {
        $this->_template = $value;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($value)
    {
        $this->_name = $value;
    }

    public function getType()
    {
        return 'component';
    }

    public function getPicturePath()
    {
        return $this->_picturePath;
    }

    public function setPicturePath($value)
    {
        $this->_picturePath = $value;
    }

    public function getGroup()
    {
        return $this->_group;
    }

    public function setGroup($value)
    {
        if ($value instanceof Group) {
            $this->_group = $value;
        } else {
            $this->_group = null;
        }
    }

    public function getSort()
    {
        return $this->_sort;
    }

    public function setSort($value)
    {
        $this->_sort = $value;
    }

    public function getProperties()
    {
        return $this->_properties;
    }

    public function setProperties($value)
    {
        if (Type::isArray($value)) {
            $this->_properties = $value;
        } else {
            $this->_properties = [];
        }
    }

    public function getConfiguration()
    {
        return [
            'code' => $this->_code,
            'template' => $this->_template,
            'properties' => $this->_properties
        ];
    }
}