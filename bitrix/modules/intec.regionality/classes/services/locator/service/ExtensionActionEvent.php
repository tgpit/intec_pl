<?php
namespace intec\regionality\services\locator\service;

use intec\core\base\Event;
use intec\regionality\services\locator\Extension;

/**
 * Представляет событие действия с расширением.
 * Class ServiceExtensionActionEvent
 * @package intec\regionality\services\locator\service
 * @author apocalypsisdimon@gmail.com
 */
class ExtensionActionEvent extends Event
{
    /**
     * Расширение сервиса.
     * @var Extension
     */
    public $extension;
}