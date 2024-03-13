<?php
namespace intec\regionality\services\locator;

use intec\core\base\Collection;

/**
 * Представляет коллекцию расширений.
 * Class Extensions
 * @package intec\regionality\services\locator
 * @author apocalypsisdimon@gmail.com
 */
class Extensions extends Collection
{
    /**
     * @inheritdoc
     */
    public function verify($item)
    {
        return $item instanceof Extension;
    }
}