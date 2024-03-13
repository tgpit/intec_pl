<?php
namespace intec\core\handling\controllers;

use intec\core\base\Event;

/**
 * Класс, представляющий событие разрешения роутером.
 * Class ResolveEvent
 * @property string $path Путь. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class ResolveEvent extends Event
{
    /**
     * Путь.
     * @var string
     * @since 1.0.0
     */
    protected $_path;

    /**
     * Маршрут.
     * @var Route
     * @since 1.0.0
     */
    public $route;

    /**
     * Маршрут верный.
     * @var boolean
     * @since 1.0.0
     */
    public $isValid = true;

    /**
     * Возвращает путь.
     * @return string
     * @since 1.0.0
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @inheritdoc
     * @param string $path Путь.
     * @since 1.0.0
     */
    public function __construct($path, $config = [])
    {
        $this->_path = $path;
        parent::__construct($config);
    }
}