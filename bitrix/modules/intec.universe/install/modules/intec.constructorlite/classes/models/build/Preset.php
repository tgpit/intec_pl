<?php
namespace intec\constructor\models\build;

use intec\core\base\BaseObject;
use intec\constructor\models\build\preset\Group;

abstract class Preset extends BaseObject
{
    /**
     * @return string
     */
    public abstract function getName();

    /**
     * @return string
     */
    public abstract function getType();

    /**
     * @return string
     */
    public abstract function getPicturePath();

    /**
     * @return Group
     */
    public abstract function getGroup();

    /**
     * @return integer
     */
    public abstract function getSort();

    /**
     * @return array
     */
    public abstract function getConfiguration();

    /**
     * @return array
     */
    public function getStructure()
    {
        $group = $this->getGroup();

        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'picture' => $this->getPicturePath(),
            'group' => !empty($group) ? $group->getStructure() : null,
            'sort' => $this->getSort(),
            'configuration' => $this->getConfiguration()
        ];
    }
}