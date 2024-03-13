<?php
namespace intec\core\platform\iblock;

use intec\core\base\HandlerInterface;

/**
 * Интерфейс, представляющий обработчик значения свойства элемента инфоблока.
 * Interface ElementPropertyValueHandlerInterface
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
interface ElementPropertyValueHandlerInterface extends HandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handle($property, &$key = null);
}
