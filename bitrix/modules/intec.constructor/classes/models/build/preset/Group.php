<?php
namespace intec\constructor\models\build\preset;

use intec\core\base\BaseObject;

class Group extends BaseObject
{
    public $name;

    public $code;

    public $sort = 500;

    public function getStructure()
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'sort' => $this->sort
        ];
    }
}