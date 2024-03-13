<?php
namespace intec\core\platform\main;

use intec\core\base\Collection;

/**
 * Класс, представляющий коллекцию файлов.
 * Class Files
 * @package intec\core\platform\main
 * @author apocalypsisdimon@gmail.com
 */
class Files extends Collection
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return $item instanceof File;
    }
}
