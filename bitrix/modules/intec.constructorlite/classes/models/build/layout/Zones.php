<?php
namespace intec\constructor\models\build\layout;

use intec\core\base\Collection;

class Zones extends Collection
{
    /**
     * @inheritdoc
     */
    public function verify($item)
    {
        return $item instanceof Zone;
    }
}