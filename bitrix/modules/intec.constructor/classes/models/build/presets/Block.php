<?php
namespace intec\constructor\models\build\presets;

use intec\core\helpers\Type;
use intec\constructor\models\build\Preset;
use intec\constructor\models\build\preset\Group;

/**
 * Class Component
 * @property string $code
 * @property string $name
 * @property string $picturePath
 * @property Group|null $group
 * @property integer $sort
 * @package intec\constructor\models\build\preset
 */
class Block extends Preset
{
    protected $_code;

    protected $_name;

    protected $_picturePath;

    protected $_group;

    protected $_sort;

    public function getCode()
    {
        return $this->_code;
    }

    public function setCode($value)
    {
        $this->_code = $value;
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
        return 'block';
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

    public function getConfiguration()
    {
        return [
            'code' => $this->_code
        ];
    }
}