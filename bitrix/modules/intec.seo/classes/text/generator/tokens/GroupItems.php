<?php
namespace intec\seo\text\generator\tokens;

use intec\core\base\Collection;

/**
 * Класс, представляющий коллекцию элементов группы.
 * Class GroupItems
 * @package intec\seo\text\generator\tokens
 * @author apocalypsisdimon@gmail.com
 */
class GroupItems extends Collection
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return ($item instanceof GroupItem);
    }
}